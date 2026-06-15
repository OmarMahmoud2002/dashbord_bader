let shelfQrScanner = null;
let shelfBarcodeReader = null;
let shelfQuaggaActive = false;
let shelfQuaggaStarting = false;
let shelfQuaggaDetectionHandler = null;
let shelfSerialScannerRunning = false;
let activeShelfScannerMode = null;
let shelfScannerSessionId = 0;
let shelfOcrWorker = null;
let shelfOcrWorkerReady = false;
let shelfOcrWorkerPromise = null;
let shelfOcrTimer = null;
let shelfOcrRunning = false;
let shelfLastOcrAt = 0;
let shelfNativeBarcodeDetector = null;
let shelfNativeBarcodeTimer = null;
let shelfNativeBarcodeRunning = false;
let shelfBarcodeScannerStartedAt = 0;
let lastScannedSerial = '';
let lastScannedSerialAt = 0;
let lastDuplicateWarningAt = 0;
let lastInvalidScanValue = '';
let lastInvalidScanAt = 0;
let shelfBarcodeCandidateSerial = '';
let shelfBarcodeCandidateCount = 0;
let shelfBarcodeCandidateAt = 0;
let shelfSerialScannerMode = 'barcode';
let shelfBarcodeTestMode = 'full';
const scanRepeatCooldownMs = 1500;
const duplicateWarningCooldownMs = 1500;
const invalidScanWarningCooldownMs = 1800;
const ocrScanIntervalMs = 1600;
const ocrFallbackDelayMs = 6500;
const nativeBarcodeScanIntervalMs = 220;
const barcodeCandidateWindowMs = 2200;
const barcodeConfirmationsRequired = 2;
const shelfBarcodeScanDebug = true;
const shelfSerialCheckCache = Object.create(null);

$(".app-content-headerButton:not(.orderShelf)").click(() => {
  $(".popup-add-shelf").fadeIn(200)
})

$('.orderShelf').click((event) => {
    $(".popup-movein-serials").css('display', 'block');
})

function showShelfToast(type, message) {
    if (window.toastr && typeof toastr[type] === 'function') {
        toastr[type](message)
        return
    }

    if (type === 'warning' || type === 'error') {
        alert(message)
    }
}

function getShelfSerialCheckUrl() {
    return window.shelfSerialCheckUrl || '/admin/shelves/check_serial'
}

function getSerialStatusText(state) {
    if (state === 'valid') {
        return 'السيريال موجود'
    }

    if (state === 'invalid') {
        return 'السيريال غير موجود'
    }

    if (state === 'error') {
        return 'تعذر التحقق'
    }

    return 'جاري التحقق'
}

function setSerialCheckState(serialItem, state, message) {
    if (!serialItem) {
        return
    }

    const status = state || 'pending'
    const statusText = message || getSerialStatusText(status)
    const statusBadge = serialItem.querySelector('.serial-status-badge')
    const serialInput = serialItem.querySelector('input[name="serials[]"]')
    const posterInput = serialItem.querySelector('input[name="posters[]"]')
    const canSubmit = status === 'valid'

    serialItem.dataset.checkStatus = status
    serialItem.classList.remove('serial-check-pending', 'serial-check-valid', 'serial-check-invalid', 'serial-check-error')
    serialItem.classList.add('serial-check-' + status)

    if (statusBadge) {
        statusBadge.dataset.state = status
        statusBadge.textContent = status === 'valid' ? '✓' : (status === 'pending' ? '…' : '×')
        statusBadge.setAttribute('title', statusText)
        statusBadge.setAttribute('aria-label', statusText)
    }

    if (serialInput) {
        serialInput.disabled = !canSubmit
    }

    if (posterInput) {
        posterInput.disabled = !canSubmit
    }
}

function checkSerialExists(serial) {
    if (Object.prototype.hasOwnProperty.call(shelfSerialCheckCache, serial)) {
        return Promise.resolve(shelfSerialCheckCache[serial])
    }

    return new Promise((resolve, reject) => {
        $.ajax({
            url: getShelfSerialCheckUrl(),
            type: 'POST',
            dataType: 'json',
            data: { serial: serial }
        }).done((response) => {
            const exists = !!(response && response.exists)
            shelfSerialCheckCache[serial] = exists
            resolve(exists)
        }).fail((xhr) => {
            reject(xhr)
        })
    })
}

function checkSerialItem(serialItem) {
    if (!serialItem) {
        return Promise.resolve(false)
    }

    const serial = serialItem.dataset.serial
    if (!serial) {
        setSerialCheckState(serialItem, 'invalid', 'السيريال فارغ')
        return Promise.resolve(false)
    }

    setSerialCheckState(serialItem, 'pending')

    return checkSerialExists(serial).then((exists) => {
        if (!document.body.contains(serialItem)) {
            return exists
        }

        setSerialCheckState(
            serialItem,
            exists ? 'valid' : 'invalid',
            exists ? 'السيريال موجود' : 'السيريال غير موجود'
        )

        return exists
    }).catch(() => {
        if (document.body.contains(serialItem)) {
            setSerialCheckState(serialItem, 'error', 'تعذر التحقق من السيريال')
        }

        return false
    })
}

function getShelfSerialRows() {
    return Array.from(document.querySelectorAll('#serials .serial'))
}

function getShelfSerialStatusCounts() {
    return getShelfSerialRows().reduce((counts, row) => {
        const status = row.dataset.checkStatus || 'pending'
        counts.total += 1
        counts[status] = (counts[status] || 0) + 1
        return counts
    }, { total: 0, valid: 0, invalid: 0, pending: 0, error: 0 })
}

function syncSerializableSerialInputs() {
    getShelfSerialRows().forEach((row) => {
        const canSubmit = row.dataset.checkStatus === 'valid'
        const serialInput = row.querySelector('input[name="serials[]"]')
        const posterInput = row.querySelector('input[name="posters[]"]')

        if (serialInput) {
            serialInput.disabled = !canSubmit
        }

        if (posterInput) {
            posterInput.disabled = !canSubmit
        }
    })
}

function verifyPendingShelfSerials() {
    const pendingRows = getShelfSerialRows().filter((row) => {
        const status = row.dataset.checkStatus
        return status === 'pending' || status === 'error' || !status
    })

    if (!pendingRows.length) {
        syncSerializableSerialInputs()
        return Promise.resolve(getShelfSerialStatusCounts())
    }

    return Promise.all(pendingRows.map((row) => checkSerialItem(row))).then(() => {
        syncSerializableSerialInputs()
        return getShelfSerialStatusCounts()
    })
}

function getExistingSerial(serial) {
    const serialItems = document.querySelectorAll('#serials .serial')
    return Array.from(serialItems).find((item) => item.dataset.serial === serial)
}

function updateSerialsCount(delta) {
    const current = parseInt($('.serials_number').text(), 10) || 0
    $('.serials_number').text(Math.max(0, current + delta))
}

function setScanStartButtonState(state) {
    const button = document.getElementById('shelfSerialScanStart')
    if (!button) {
        return
    }

    const icon = button.querySelector('i')
    const text = button.querySelector('span')
    button.dataset.state = state || 'idle'

    if (state === 'opening') {
        button.disabled = true
        if (icon) icon.className = 'bi bi-hourglass-split'
        if (text) text.textContent = 'جاري الفتح'
        return
    }

    if (state === 'running') {
        button.disabled = true
        if (icon) icon.className = 'bi bi-camera-video'
        if (text) text.textContent = 'الكاميرا مفتوحة'
        return
    }

    button.disabled = false
    if (icon) icon.className = 'bi bi-qr-code-scan'
    if (text) text.textContent = 'Scan'
}

function setScannerModeButtonState() {
    const button = document.getElementById('shelfSerialScanQrMode')
    const panel = document.getElementById('shelfSerialScannerPanel')
    if (!button) {
        return
    }

    const icon = button.querySelector('i')
    const text = button.querySelector('span')
    const isQrMode = shelfSerialScannerMode === 'qr'

    button.dataset.mode = shelfSerialScannerMode
    if (panel) panel.dataset.mode = shelfSerialScannerMode
    if (icon) icon.className = isQrMode ? 'bi bi-upc-scan' : 'bi bi-qr-code-scan'
    if (text) text.textContent = isQrMode ? 'Scan Serial' : 'Scan QR Code'
}

function setBarcodeTestModeButtonState() {
    const panel = document.getElementById('shelfSerialScannerPanel')
    if (panel) {
        panel.dataset.barcodeTestMode = shelfBarcodeTestMode
    }

    document.querySelectorAll('#shelfBarcodeTestModes button').forEach((button) => {
        button.dataset.active = button.dataset.mode === shelfBarcodeTestMode ? 'true' : 'false'
    })
}

function add_serial(serialValue, posterValue, source) {
    const isScanned = source === 'scan'
    let serial = typeof serialValue === 'string' ? serialValue : $('#SerialInputAdder').val()
    serial = serial.trim()

    if (serial == '') {
        return false
    }

    if (getExistingSerial(serial)) {
        const now = Date.now()
        if (!isScanned || now - lastDuplicateWarningAt > duplicateWarningCooldownMs) {
            showShelfToast('warning', 'السيريال مضاف بالفعل')
            lastDuplicateWarningAt = now
        }
        return false
    }

    let poster_number = typeof posterValue === 'string' ? posterValue : $('#PosterInputAdder').val()
    poster_number = poster_number ? poster_number.trim() : ''

    const element = document.getElementById('serials')
    const serialItem = document.createElement('div')
    serialItem.className = 'serial'
    serialItem.dataset.serial = serial

    const serialText = document.createElement('span')
    serialText.textContent = serial

    const serialInput = document.createElement('input')
    serialInput.type = 'hidden'
    serialInput.name = 'serials[]'
    serialInput.value = serial
    serialInput.readOnly = true

    const posterInput = document.createElement('input')
    posterInput.type = 'text'
    posterInput.name = 'posters[]'
    posterInput.value = poster_number
    posterInput.className = 'serial-poster-input'
    posterInput.autocomplete = 'off'
    posterInput.placeholder = 'رقم الملصق'
    posterInput.setAttribute('aria-label', 'رقم الملصق')

    const statusBadge = document.createElement('em')
    statusBadge.className = 'serial-status-badge'
    statusBadge.setAttribute('role', 'img')
    statusBadge.setAttribute('aria-label', 'جاري التحقق')

    const deleteButton = document.createElement('i')
    deleteButton.className = 'fa fa-trash delete'
    deleteButton.setAttribute('role', 'button')
    deleteButton.setAttribute('tabindex', '0')
    deleteButton.setAttribute('aria-label', 'حذف السيريال')
    deleteButton.addEventListener('click', () => delete_serial(deleteButton))
    deleteButton.addEventListener('keydown', (e) => {
        if (e.key === 'Enter' || e.key === ' ') {
            e.preventDefault()
            delete_serial(deleteButton)
        }
    })

    serialItem.appendChild(serialText)
    serialItem.appendChild(serialInput)
    serialItem.appendChild(posterInput)
    serialItem.appendChild(statusBadge)
    serialItem.appendChild(deleteButton)
    setSerialCheckState(serialItem, 'pending')
    element.appendChild(serialItem)
    checkSerialItem(serialItem)

    updateSerialsCount(1)

    if (!isScanned) {
        $('#SerialInputAdder').val('')
        $('#PosterInputAdder').val('')
    }

    element.scrollTop = -element.scrollHeight
    return true
}

$('#serialAdder').click((event) => {
    add_serial()
})

$('.popup-movein-serials form').on('keydown', (e) => {
    if (e.keyCode == 13 && $(e.target).is('.serial-poster-input')) {
        e.preventDefault()
        return
    }

    if (e.keyCode == 13) {
        add_serial()
        e.preventDefault()
    }
})

function delete_serial(target) {
    const source = target || (window.event ? window.event.target : null)
    if (!source) {
        return
    }

    $(source).closest('.serial').remove()
    updateSerialsCount(-1)
}

function setScannerStatus(message, type) {
    const status = document.getElementById('shelfSerialScannerStatus')
    if (!status) {
        return
    }

    status.textContent = message || ''
    status.dataset.type = type || ''
}

function getShelfScannerFormats() {
    if (typeof Html5QrcodeSupportedFormats === 'undefined') {
        return undefined
    }

    return [
        Html5QrcodeSupportedFormats.QR_CODE
    ].filter((format) => typeof format !== 'undefined')
}

function getShelfSerialBarcodeFormats() {
    if (typeof Html5QrcodeSupportedFormats === 'undefined') {
        return undefined
    }

    return [
        Html5QrcodeSupportedFormats.CODE_128,
        Html5QrcodeSupportedFormats.CODE_39,
        Html5QrcodeSupportedFormats.CODE_93,
        Html5QrcodeSupportedFormats.ITF
    ].filter((format) => typeof format !== 'undefined')
}

function getZxingBarcodeHints() {
    if (typeof ZXing === 'undefined') {
        return undefined
    }

    const formats = [
        ZXing.BarcodeFormat.CODE_128,
        ZXing.BarcodeFormat.ITF,
        ZXing.BarcodeFormat.CODE_39
    ].filter((format) => typeof format !== 'undefined')

    const hints = new Map()
    hints.set(ZXing.DecodeHintType.POSSIBLE_FORMATS, formats)
    hints.set(ZXing.DecodeHintType.TRY_HARDER, true)
    return hints
}

function getShelfBarcodeVideoConstraints() {
    return {
        facingMode: { ideal: 'environment' },
        width: { ideal: 1920 },
        height: { ideal: 1080 },
        aspectRatio: { ideal: 1.777778 },
        advanced: [
            { focusMode: 'continuous' },
            { exposureMode: 'continuous' }
        ]
    }
}

function getShelfBarcodeMediaConstraints() {
    return {
        video: getShelfBarcodeVideoConstraints(),
        audio: false
    }
}

function isValidImeiSerial(serial) {
    if (!/^\d{15}$/.test(serial || '')) {
        return false
    }

    const digitCounts = serial.split('').reduce((counts, digit) => {
        counts[digit] = (counts[digit] || 0) + 1
        return counts
    }, {})
    const uniqueDigits = Object.keys(digitCounts).length
    const maxRepeatedDigit = Math.max.apply(null, Object.values(digitCounts))

    if (uniqueDigits < 4 || maxRepeatedDigit > 10) {
        return false
    }

    return isLuhnValidImeiSerial(serial)
}

function isLuhnValidImeiSerial(serial) {
    if (!/^\d{15}$/.test(serial || '')) {
        return false
    }

    let sum = 0
    for (let i = 0; i < serial.length; i++) {
        let digit = Number(serial[serial.length - 1 - i])
        if (i % 2 === 1) {
            digit *= 2
            if (digit > 9) {
                digit -= 9
            }
        }
        sum += digit
    }

    return sum % 10 === 0
}

function normalizeOcrText(value) {
    return String(value || '')
        .toUpperCase()
        .replace(/[|!]/g, 'I')
        .replace(/[’'`]/g, '')
        .replace(/\s+/g, ' ')
        .trim()
}

function digitsFromText(value) {
    return String(value || '').replace(/\D/g, '')
}

function isFifteenDigitSerial(serial) {
    return /^\d{15}$/.test(serial || '')
}

function getFifteenDigitSerialCandidate(value) {
    const trimmed = String(value || '').trim()
    if (isFifteenDigitSerial(trimmed)) {
        return trimmed
    }

    const digits = digitsFromText(trimmed)
    return isFifteenDigitSerial(digits) ? digits : null
}

function logShelfBarcodeScanDebug(rawDecodedText, extractedSerial) {
    if (!shelfBarcodeScanDebug || !(window.console && typeof console.info === 'function')) {
        return
    }

    const serial = extractedSerial || ''
    console.info('[Shelf Serial Scan]', {
        rawDecodedText: String(rawDecodedText || ''),
        extractedSerial: serial || null,
        fifteenDigitValid: isFifteenDigitSerial(serial),
        luhnValid: isLuhnValidImeiSerial(serial),
        testMode: shelfBarcodeTestMode
    })
}

function getImeiSerialFromScan(decodedText, options) {
    const settings = Object.assign({
        requireKeyword: false,
        requireLuhn: true
    }, options || {})
    const trimmed = String(decodedText || '').trim()
    const normalized = normalizeOcrText(trimmed)

    if (settings.requireKeyword) {
        const keywordMatch = normalized.match(/(?:IMEI|IME1|MEID|ME1D|IMEL)(?:\s*\/\s*(?:MEID|ME1D))?[^0-9]{0,30}((?:\d[\s\-:.]*){15,20})/)
        if (!keywordMatch) {
            return null
        }

        const digits = digitsFromText(keywordMatch[1])
        const serial = digits.length >= 15 ? digits.slice(0, 15) : null
        if (!serial) {
            return null
        }

        return !settings.requireLuhn || isValidImeiSerial(serial) ? serial : null
    }

    if (/^\d{15}$/.test(trimmed)) {
        return !settings.requireLuhn || isValidImeiSerial(trimmed) ? trimmed : null
    }

    const digitGroups = normalized.match(/\d{15}/g)
    if (digitGroups && digitGroups.length) {
        for (let i = digitGroups.length - 1; i >= 0; i--) {
            if (!settings.requireLuhn || isValidImeiSerial(digitGroups[i])) {
                return digitGroups[i]
            }
        }
    }

    const digitsOnly = digitsFromText(normalized)
    if (digitsOnly.length >= 15) {
        for (let start = 0; start <= digitsOnly.length - 15; start++) {
            const candidate = digitsOnly.slice(start, start + 15)
            if (!settings.requireLuhn || isValidImeiSerial(candidate)) {
                return candidate
            }
        }
    }

    return null
}

function getImeiSerialFromBarcodeScan(decodedText) {
    const trimmed = String(decodedText || '').trim()
    if (!trimmed) {
        logShelfBarcodeScanDebug(decodedText, null)
        return null
    }

    const serial = getFifteenDigitSerialCandidate(trimmed)
    logShelfBarcodeScanDebug(decodedText, serial)
    return serial
}

function warnInvalidImeiScan(decodedText) {
    const scanValue = String(decodedText || '').trim()
    const now = Date.now()

    if (scanValue === lastInvalidScanValue && now - lastInvalidScanAt < invalidScanWarningCooldownMs) {
        return
    }

    lastInvalidScanValue = scanValue
    lastInvalidScanAt = now
    showShelfToast('warning', 'الكود المقروء ليس IMEI صحيح')
    setScannerStatus('الكود المقروء ليس IMEI صحيح', 'warning')
}

function setBarcodeSearchingStatus() {
    setScannerStatus('ضع السيريال داخل الشريط العرضي. جاري البحث عن IMEI...', 'ready')
}

function getSerialFromScan(decodedText, options) {
    const settings = Object.assign({
        source: shelfSerialScannerMode
    }, options || {})
    const trimmed = String(decodedText || '').trim()
    if (!trimmed) {
        return null
    }

    if (settings.source === 'barcode') {
        return getImeiSerialFromBarcodeScan(trimmed)
    }

    if (settings.source === 'ocr') {
        return getImeiSerialFromScan(trimmed, {
            requireKeyword: true,
            requireLuhn: true
        })
    }

    const imeiSerial = getImeiSerialFromScan(trimmed, {
        requireKeyword: false,
        requireLuhn: settings.source !== 'qr'
    })
    if (settings.source === 'qr') {
        return imeiSerial || trimmed
    }

    return imeiSerial
}

function commitShelfSerial(serial) {
    serial = String(serial || '').trim()
    if (!serial) {
        return false
    }

    const now = Date.now()
    if (serial === lastScannedSerial && now - lastScannedSerialAt < scanRepeatCooldownMs) {
        return true
    }

    lastScannedSerial = serial
    lastScannedSerialAt = now

    if (add_serial(serial, '', 'scan')) {
        showShelfToast('success', 'تم إضافة السيريال')
        setScannerStatus('تم إضافة السيريال: ' + serial, 'success')
    } else if (getExistingSerial(serial)) {
        setScannerStatus('السيريال مضاف بالفعل: ' + serial, 'warning')
    }

    return true
}

function handleShelfSerialScan(decodedText, options) {
    const settings = Object.assign({
        source: shelfSerialScannerMode,
        warnInvalid: false
    }, options || {})
    const serial = getSerialFromScan(decodedText, settings)
    if (!serial) {
        if (settings.source !== 'qr') {
            setBarcodeSearchingStatus()
            return false
        }
        if (settings.warnInvalid) {
            warnInvalidImeiScan(decodedText)
        }
        return false
    }

    return commitShelfSerial(serial)
}

function resetShelfBarcodeCandidate() {
    shelfBarcodeCandidateSerial = ''
    shelfBarcodeCandidateCount = 0
    shelfBarcodeCandidateAt = 0
}

function handleStableShelfBarcodeScan(decodedText) {
    const serial = getSerialFromScan(decodedText, { source: 'barcode' })
    if (!serial) {
        return false
    }

    const now = Date.now()
    if (serial === shelfBarcodeCandidateSerial && now - shelfBarcodeCandidateAt < barcodeCandidateWindowMs) {
        shelfBarcodeCandidateCount += 1
    } else {
        shelfBarcodeCandidateSerial = serial
        shelfBarcodeCandidateCount = 1
    }
    shelfBarcodeCandidateAt = now

    if (shelfBarcodeCandidateCount < barcodeConfirmationsRequired) {
        setScannerStatus('تم التقاط IMEI، ثبّت السيريال داخل الشريط للحظة...', 'loading')
        return true
    }

    resetShelfBarcodeCandidate()
    handleShelfSerialScan(serial)
    return true
}

function getShelfSerialStripCrop(video) {
    const sourceWidth = video.videoWidth || video.clientWidth
    const sourceHeight = video.videoHeight || video.clientHeight
    if (!sourceWidth || !sourceHeight) {
        return null
    }

    const stripHeight = sourceHeight * 0.48
    const stripY = (sourceHeight - stripHeight) / 2

    return {
        x: sourceWidth * 0.02,
        y: stripY,
        width: sourceWidth * 0.96,
        height: stripHeight
    }
}

function clampShelfCrop(crop, sourceWidth, sourceHeight) {
    const x = Math.max(0, Math.min(sourceWidth - 1, crop.x))
    const y = Math.max(0, Math.min(sourceHeight - 1, crop.y))
    const width = Math.max(1, Math.min(crop.width, sourceWidth - x))
    const height = Math.max(1, Math.min(crop.height, sourceHeight - y))

    return { x, y, width, height }
}

function createShelfOcrCanvas(video, crop, options) {
    const sourceWidth = video.videoWidth || video.clientWidth
    const sourceHeight = video.videoHeight || video.clientHeight
    if (!sourceWidth || !sourceHeight) {
        return null
    }

    const rawCrop = crop || {
        x: 0,
        y: 0,
        width: sourceWidth,
        height: sourceHeight
    }
    const safeCrop = clampShelfCrop(rawCrop, sourceWidth, sourceHeight)
    const canvas = document.createElement('canvas')
    const targetWidth = options && options.targetWidth ? options.targetWidth : 1800
    const scale = targetWidth / safeCrop.width
    canvas.width = targetWidth
    canvas.height = Math.max(180, Math.round(safeCrop.height * scale))

    const context = canvas.getContext('2d', { willReadFrequently: true })
    context.drawImage(video, safeCrop.x, safeCrop.y, safeCrop.width, safeCrop.height, 0, 0, canvas.width, canvas.height)

    if (options && options.preprocess === false) {
        return canvas
    }

    const imageData = context.getImageData(0, 0, canvas.width, canvas.height)
    const data = imageData.data
    for (let i = 0; i < data.length; i += 4) {
        const gray = (data[i] * 0.299) + (data[i + 1] * 0.587) + (data[i + 2] * 0.114)
        let value = ((gray - 128) * 1.75) + 128
        value = Math.max(0, Math.min(255, value))

        if (options && options.binary) {
            value = value > (options.threshold || 135) ? 255 : 0
        }

        data[i] = value
        data[i + 1] = value
        data[i + 2] = value
    }
    context.putImageData(imageData, 0, 0)

    return canvas
}

function createShelfBarcodeStripCanvas(video) {
    const stripCrop = getShelfSerialStripCrop(video)
    if (!stripCrop) {
        return null
    }

    return createShelfOcrCanvas(video, stripCrop, {
        targetWidth: 1800,
        preprocess: false
    })
}

function getShelfOcrCanvases(video) {
    const stripCrop = getShelfSerialStripCrop(video)
    if (!stripCrop) {
        return []
    }

    const topTextCrop = {
        x: stripCrop.x,
        y: stripCrop.y + (stripCrop.height * 0.04),
        width: stripCrop.width,
        height: stripCrop.height * 0.38
    }
    const bottomTextCrop = {
        x: stripCrop.x,
        y: stripCrop.y + (stripCrop.height * 0.70),
        width: stripCrop.width,
        height: stripCrop.height * 0.26
    }

    return [
        createShelfOcrCanvas(video, topTextCrop, { targetWidth: 2400, binary: false }),
        createShelfOcrCanvas(video, topTextCrop, { targetWidth: 2400, binary: true, threshold: 150 }),
        createShelfOcrCanvas(video, bottomTextCrop, { targetWidth: 2200, binary: false }),
        createShelfOcrCanvas(video, bottomTextCrop, { targetWidth: 2200, binary: true, threshold: 150 })
    ].filter(Boolean)
}

function getShelfOcrWorker() {
    if (shelfOcrWorkerReady && shelfOcrWorker) {
        return Promise.resolve(shelfOcrWorker)
    }

    if (shelfOcrWorkerPromise) {
        return shelfOcrWorkerPromise
    }

    if (typeof Tesseract === 'undefined' || typeof Tesseract.createWorker !== 'function') {
        return Promise.reject(new Error('Tesseract is not available'))
    }

    setScannerStatus('جاري تجهيز قراءة نص IMEI...', 'loading')

    shelfOcrWorkerPromise = Tesseract.createWorker('eng', 1, {
        workerPath: 'https://cdn.jsdelivr.net/npm/tesseract.js@5.1.1/dist/worker.min.js',
        corePath: 'https://cdn.jsdelivr.net/npm/tesseract.js-core@5.1.1',
        langPath: 'https://tessdata.projectnaptha.com/4.0.0',
        logger: () => {}
    }).then((worker) => {
        shelfOcrWorker = worker
        return worker.setParameters({
            tessedit_char_whitelist: 'IME/MD0123456789 ',
            tessedit_pageseg_mode: Tesseract.PSM ? Tesseract.PSM.SINGLE_LINE : '7'
        }).then(() => {
            shelfOcrWorkerReady = true
            return worker
        })
    }).catch((error) => {
        shelfOcrWorker = null
        shelfOcrWorkerReady = false
        shelfOcrWorkerPromise = null
        throw error
    })

    return shelfOcrWorkerPromise
}

function startShelfOcrFallback(video) {
    stopShelfOcrFallback()

    shelfOcrTimer = window.setInterval(() => {
        if (shelfSerialScannerMode !== 'barcode' || !shelfSerialScannerRunning || shelfOcrRunning) {
            return
        }

        if (Date.now() - shelfLastOcrAt < ocrScanIntervalMs) {
            return
        }

        if (Date.now() - shelfBarcodeScannerStartedAt < ocrFallbackDelayMs) {
            return
        }

        if (typeof Tesseract === 'undefined' || !video.videoWidth) {
            return
        }

        const canvases = getShelfOcrCanvases(video)
        if (!canvases.length) {
            return
        }

        shelfOcrRunning = true
        shelfLastOcrAt = Date.now()

        getShelfOcrWorker().then((worker) => {
            let chain = Promise.resolve(null)
            setScannerStatus('جاري قراءة نص IMEI من الملصق...', 'loading')

            canvases.forEach((canvas) => {
                chain = chain.then((foundSerial) => {
                    if (foundSerial) {
                        return foundSerial
                    }

                    return worker.recognize(canvas).then((result) => {
                        const text = result && result.data ? result.data.text : ''
                        const confidence = result && result.data && typeof result.data.confidence === 'number'
                            ? result.data.confidence
                            : 0
                        if (confidence && confidence < 35) {
                            return null
                        }
                        return getImeiSerialFromScan(text, {
                            requireKeyword: true,
                            requireLuhn: true
                        })
                    }).catch(() => null)
                })
            })

            return chain
        }).then((serial) => {
            if (serial) {
                commitShelfSerial(serial)
            } else {
                setBarcodeSearchingStatus()
            }
        }).catch(() => {
            setScannerStatus('تعذر تشغيل OCR، جاري محاولة قراءة الباركود فقط...', 'warning')
        }).finally(() => {
            shelfOcrRunning = false
        })
    }, 500)
}

function stopShelfOcrFallback() {
    if (shelfOcrTimer) {
        window.clearInterval(shelfOcrTimer)
        shelfOcrTimer = null
    }
    shelfOcrRunning = false
}

function getNativeBarcodeDetector() {
    if (!('BarcodeDetector' in window)) {
        return null
    }

    if (shelfNativeBarcodeDetector) {
        return shelfNativeBarcodeDetector
    }

    try {
        shelfNativeBarcodeDetector = new BarcodeDetector({
            formats: ['code_128', 'code_39', 'itf']
        })
    } catch (e) {
        shelfNativeBarcodeDetector = null
    }

    return shelfNativeBarcodeDetector
}

function startNativeBarcodeFallback(video) {
    stopNativeBarcodeFallback()

    const detector = getNativeBarcodeDetector()
    if (!detector) {
        return
    }

    shelfNativeBarcodeTimer = window.setInterval(() => {
        if (shelfSerialScannerMode !== 'barcode' || !shelfSerialScannerRunning || shelfNativeBarcodeRunning || !video.videoWidth) {
            return
        }

        const canvas = createShelfBarcodeStripCanvas(video)
        if (!canvas) {
            return
        }

        shelfNativeBarcodeRunning = true
        detector.detect(canvas).then((barcodes) => {
            for (const barcode of barcodes) {
                const value = barcode.rawValue || barcode.rawText || ''
                if (handleShelfSerialScan(value, { source: 'barcode' })) {
                    break
                }
            }
        }).catch(() => {}).finally(() => {
            shelfNativeBarcodeRunning = false
        })
    }, nativeBarcodeScanIntervalMs)
}

function stopNativeBarcodeFallback() {
    if (shelfNativeBarcodeTimer) {
        window.clearInterval(shelfNativeBarcodeTimer)
        shelfNativeBarcodeTimer = null
    }
    shelfNativeBarcodeRunning = false
}

function terminateShelfOcrWorker() {
    stopShelfOcrFallback()

    if (shelfOcrWorker) {
        shelfOcrWorker.terminate().catch(() => {})
    }

    shelfOcrWorker = null
    shelfOcrWorkerReady = false
    shelfOcrWorkerPromise = null
}

function getShelfScannerStartConfig() {
    const config = {
        fps: 15,
        aspectRatio: shelfSerialScannerMode === 'qr' ? 1 : 1.777778
    }

    if (shelfSerialScannerMode === 'qr') {
        config.qrbox = { width: 280, height: 280 }
    } else {
        config.qrbox = { width: 520, height: 180 }
    }

    return config
}

function getShelfBarcodeScannerConfig() {
    const config = {
        fps: 15,
        disableFlip: true
    }

    if (shelfBarcodeTestMode === 'wide') {
        config.qrbox = (viewfinderWidth, viewfinderHeight) => {
            const width = Math.floor(viewfinderWidth * 0.95)
            const height = Math.floor(Math.max(160, Math.min(180, viewfinderHeight * 0.36)))
            return { width, height }
        }
    }

    return config
}

function clearShelfScannerReader() {
    const reader = document.getElementById('shelfSerialScannerReader')
    if (reader) {
        reader.innerHTML = ''
    }
    return reader
}

function configureShelfBarcodeVideo(video) {
    if (!video) {
        return null
    }

    video.classList.add('shelf-serial-barcode-video')
    video.setAttribute('playsinline', 'true')
    video.setAttribute('muted', 'true')
    video.muted = true
    video.autoplay = true
    return video
}

function createShelfBarcodeVideo() {
    const reader = clearShelfScannerReader()
    if (!reader) {
        return null
    }

    const video = document.createElement('video')
    video.id = 'shelfSerialBarcodeVideo'
    configureShelfBarcodeVideo(video)
    reader.appendChild(video)
    return video
}

function getBackCameraDeviceId(devices) {
    const backCamera = devices.find((device) => /back|rear|environment|خلف/i.test(device.label || ''))
    return backCamera ? backCamera.deviceId : null
}

function isCameraPermissionError(error) {
    const name = error && error.name ? error.name : ''
    const message = error && error.message ? error.message : String(error || '')
    return /NotAllowedError|PermissionDeniedError|NotReadableError/i.test(name)
        || /permission|denied|not allowed/i.test(message)
}

function getQuaggaBarcodeReaders() {
    return [
        'code_128_reader',
        'i2of5_reader',
        'code_39_reader'
    ]
}

function getQuaggaConfig(reader) {
    return {
        inputStream: {
            name: 'ShelfSerialScanner',
            type: 'LiveStream',
            target: reader,
            constraints: getShelfBarcodeVideoConstraints()
        },
        locator: {
            patchSize: 'large',
            halfSample: false
        },
        decoder: {
            readers: getQuaggaBarcodeReaders(),
            multiple: false
        },
        locate: true,
        frequency: 15,
        numOfWorkers: Math.max(0, Math.min(2, navigator.hardwareConcurrency || 1))
    }
}

function getQuaggaDecodedText(result) {
    if (!result || !result.codeResult) {
        return ''
    }

    return String(result.codeResult.code || '').trim()
}

function isQuaggaResultReliable(result) {
    const decodedCodes = result && result.codeResult && Array.isArray(result.codeResult.decodedCodes)
        ? result.codeResult.decodedCodes
        : []
    const errors = decodedCodes
        .map((code) => typeof code.error === 'number' ? code.error : null)
        .filter((error) => error !== null)

    if (!errors.length) {
        return true
    }

    const averageError = errors.reduce((sum, error) => sum + error, 0) / errors.length
    const maxError = Math.max.apply(null, errors)
    return averageError < 0.35 && maxError < 0.75
}

function getQuaggaVideoElement() {
    const reader = document.getElementById('shelfSerialScannerReader')
    return reader ? reader.querySelector('video') : null
}

function stopQuaggaBarcodeScanner() {
    if (typeof Quagga !== 'undefined') {
        if (shelfQuaggaDetectionHandler && typeof Quagga.offDetected === 'function') {
            try {
                Quagga.offDetected(shelfQuaggaDetectionHandler)
            } catch (e) {}
        }

        if (shelfQuaggaActive || shelfQuaggaStarting) {
            try {
                Quagga.stop()
            } catch (e) {}
        }
    }

    shelfQuaggaActive = false
    shelfQuaggaStarting = false
    shelfQuaggaDetectionHandler = null
}

function startQuaggaBarcodeScanner(allowZxingFallback) {
    const shouldFallbackToZxing = allowZxingFallback !== false

    if (typeof Quagga === 'undefined') {
        return false
    }

    const reader = clearShelfScannerReader()
    if (!reader) {
        setScannerStatus('تعذر تجهيز مساحة الكاميرا.', 'error')
        setScanStartButtonState('idle')
        return true
    }

    const scannerSessionId = shelfScannerSessionId
    activeShelfScannerMode = 'barcode'
    shelfQuaggaStarting = true
    resetShelfBarcodeCandidate()
    setScannerStatus('جاري فتح كاميرا السيريال...', 'loading')

    const onQuaggaReady = (error) => {
        shelfQuaggaStarting = false

        if (scannerSessionId !== shelfScannerSessionId || shelfSerialScannerMode !== 'barcode') {
            stopQuaggaBarcodeScanner()
            return
        }

        if (error) {
            stopQuaggaBarcodeScanner()
            if (isCameraPermissionError(error)) {
                const message = 'تم رفض إذن الكاميرا.'
                setScannerStatus(message, 'error')
                showShelfToast('error', message)
                setScanStartButtonState('idle')
                return
            }

            if (shouldFallbackToZxing) {
                setScannerStatus('تعذر تشغيل قارئ السيريال الأساسي، جاري تجربة قارئ احتياطي...', 'warning')
                startZxingBarcodeScanner(false)
                return
            }

            setScannerStatus('تعذر تشغيل قارئ السيريال. جرّب QR أو متصفح آخر.', 'error')
            showShelfToast('error', 'تعذر تشغيل قارئ السيريال')
            setScanStartButtonState('idle')
            return
        }

        shelfQuaggaDetectionHandler = (result) => {
            if (!shelfQuaggaActive || shelfSerialScannerMode !== 'barcode') {
                return
            }

            const decodedText = getQuaggaDecodedText(result)
            if (!decodedText || !isQuaggaResultReliable(result)) {
                return
            }

            handleStableShelfBarcodeScan(decodedText)
        }

        if (typeof Quagga.onDetected === 'function') {
            Quagga.onDetected(shelfQuaggaDetectionHandler)
        }

        try {
            Quagga.start()
        } catch (startError) {
            stopQuaggaBarcodeScanner()
            if (shouldFallbackToZxing) {
                setScannerStatus('تعذر تشغيل قارئ السيريال، جاري تجربة قارئ احتياطي...', 'warning')
                startZxingBarcodeScanner(false)
                return
            }

            setScannerStatus('تعذر تشغيل قارئ السيريال. جرّب QR أو متصفح آخر.', 'error')
            showShelfToast('error', 'تعذر تشغيل قارئ السيريال')
            setScanStartButtonState('idle')
            return
        }

        shelfQuaggaActive = true
        shelfSerialScannerRunning = true
        shelfBarcodeScannerStartedAt = Date.now()
        setScanStartButtonState('running')
        setScannerStatus('ضع باركود IMEI داخل الشريط العرضي وثبّت الكاميرا لحظة.', 'ready')

        window.setTimeout(() => {
            if (!shelfQuaggaActive || scannerSessionId !== shelfScannerSessionId) {
                return
            }

            const video = configureShelfBarcodeVideo(getQuaggaVideoElement())
            if (video) {
                startNativeBarcodeFallback(video)
                startShelfOcrFallback(video)
            }
        }, 250)
    }

    try {
        Quagga.init(getQuaggaConfig(reader), onQuaggaReady)
    } catch (error) {
        shelfQuaggaStarting = false
        if (isCameraPermissionError(error)) {
            const message = 'تم رفض إذن الكاميرا.'
            setScannerStatus(message, 'error')
            showShelfToast('error', message)
            setScanStartButtonState('idle')
        } else if (shouldFallbackToZxing) {
            setScannerStatus('تعذر تشغيل قارئ السيريال الأساسي، جاري تجربة قارئ احتياطي...', 'warning')
            startZxingBarcodeScanner(false)
        } else {
            setScannerStatus('تعذر تشغيل قارئ السيريال. جرّب QR أو متصفح آخر.', 'error')
            showShelfToast('error', 'تعذر تشغيل قارئ السيريال')
            setScanStartButtonState('idle')
        }
    }

    return true
}

function startZxingBarcodeScanner(allowQuaggaFallback) {
    const shouldFallbackToQuagga = allowQuaggaFallback !== false

    if (typeof ZXing === 'undefined') {
        if (shouldFallbackToQuagga && startQuaggaBarcodeScanner(false)) {
            return
        }

        setScannerStatus('تعذر تحميل مكتبة قراءة الباركود. حاول تحديث الصفحة.', 'error')
        showShelfToast('error', 'تعذر تحميل مكتبة قراءة الباركود')
        setScanStartButtonState('idle')
        return
    }

    const video = createShelfBarcodeVideo()
    if (!video) {
        setScannerStatus('تعذر تجهيز مساحة الكاميرا.', 'error')
        setScanStartButtonState('idle')
        return
    }

    shelfBarcodeReader = new ZXing.BrowserMultiFormatReader(getZxingBarcodeHints(), 150)
    activeShelfScannerMode = 'barcode'
    const scannerSessionId = shelfScannerSessionId

    const onDecoded = (result) => {
        if (scannerSessionId !== shelfScannerSessionId || shelfSerialScannerMode !== 'barcode') {
            return
        }

        if (!result) {
            return
        }

        const decodedText = typeof result.getText === 'function' ? result.getText() : String(result.text || result)
        handleShelfSerialScan(decodedText, { source: 'barcode' })
    }

    const onStarted = () => {
        if (scannerSessionId !== shelfScannerSessionId || shelfSerialScannerMode !== 'barcode') {
            return
        }

        shelfSerialScannerRunning = true
        shelfBarcodeScannerStartedAt = Date.now()
        setScanStartButtonState('running')
        setScannerStatus('ضع باركود IMEI داخل الشريط العرضي. سيتم استخدام قراءة النص فقط إذا لم يظهر الباركود.', 'ready')
        startNativeBarcodeFallback(video)
        startShelfOcrFallback(video)
    }

    const onStartError = (error) => {
        if (scannerSessionId !== shelfScannerSessionId) {
            return
        }

        if (!isCameraPermissionError(error) && shouldFallbackToQuagga && startQuaggaBarcodeScanner(false)) {
            setScannerStatus('تعذر تشغيل القارئ السريع، جاري تجربة قارئ احتياطي...', 'warning')
            return
        }

        const message = isCameraPermissionError(error)
            ? 'تم رفض إذن الكاميرا.'
            : 'تعذر فتح كاميرا الباركود. جرّب QR أو متصفح آخر.'
        setScannerStatus(message, 'error')
        showShelfToast('error', message)
        setScanStartButtonState('idle')
    }

    const startDecode = (deviceId) => {
        shelfBarcodeReader.decodeFromVideoDevice(deviceId, video, onDecoded).then(onStarted).catch(onStartError)
    }

    if (typeof shelfBarcodeReader.decodeFromConstraints === 'function') {
        shelfBarcodeReader.decodeFromConstraints(getShelfBarcodeMediaConstraints(), video, onDecoded)
            .then(onStarted)
            .catch(onStartError)
        return
    }

    shelfBarcodeReader.listVideoInputDevices()
        .then((devices) => startDecode(getBackCameraDeviceId(devices)))
        .catch(() => startDecode(null))
}

function startHtml5BarcodeScanner() {
    if (typeof Html5Qrcode === 'undefined') {
        setScannerStatus('تعذر تحميل مكتبة قراءة السيريال. حاول تحديث الصفحة.', 'error')
        showShelfToast('error', 'تعذر تحميل مكتبة قراءة السيريال')
        setScanStartButtonState('idle')
        return
    }

    clearShelfScannerReader()
    activeShelfScannerMode = 'barcode'
    const scannerSessionId = shelfScannerSessionId
    const formatsToSupport = getShelfSerialBarcodeFormats()
    const scannerConfig = {
        useBarCodeDetectorIfSupported: true
    }
    if (formatsToSupport && formatsToSupport.length) {
        scannerConfig.formatsToSupport = formatsToSupport
    }

    shelfQrScanner = new Html5Qrcode('shelfSerialScannerReader', scannerConfig)
    setScannerStatus('جاري فتح قارئ السيريال السريع...', 'loading')

    const onDecoded = (decodedText) => {
        if (scannerSessionId === shelfScannerSessionId && shelfSerialScannerMode === 'barcode') {
            handleShelfSerialScan(decodedText, { source: 'barcode' })
        }
    }

    const onStarted = () => {
        if (scannerSessionId !== shelfScannerSessionId || shelfSerialScannerMode !== 'barcode') {
            return
        }

        shelfSerialScannerRunning = true
        shelfBarcodeScannerStartedAt = Date.now()
        setScanStartButtonState('running')
        setScannerStatus('وجه باركود IMEI داخل المستطيل. وضع الاختبار: ' + shelfBarcodeTestMode + '.', 'ready')
    }

    const onFailed = (error) => {
        if (scannerSessionId !== shelfScannerSessionId) {
            return
        }

        if (window.console && typeof console.warn === 'function') {
            console.warn('Shelf serial scanner failed to start:', error)
        }

        shelfQrScanner = null
        const message = isCameraPermissionError(error)
            ? 'تم رفض إذن الكاميرا.'
            : 'تعذر فتح قارئ السيريال. تأكد من الإذن أو جرّب متصفح آخر.'
        setScannerStatus(message, 'error')
        showShelfToast('error', message)
        setScanStartButtonState('idle')
    }

    const startWithCamera = (cameraConfig) => shelfQrScanner.start(
        cameraConfig,
        getShelfBarcodeScannerConfig(),
        onDecoded,
        () => {}
    )

    startWithCamera({ facingMode: 'environment' }).then(onStarted).catch((error) => {
        if (scannerSessionId !== shelfScannerSessionId || isCameraPermissionError(error)) {
            onFailed(error)
            return
        }

        setScannerStatus('جاري تجربة الكاميرا الافتراضية للسيريال...', 'loading')
        startWithCamera({ facingMode: { ideal: 'environment' } }).then(onStarted).catch(onFailed)
    })
}

function startHtml5QrScanner() {
    if (typeof Html5Qrcode === 'undefined') {
        setScannerStatus('تعذر تحميل مكتبة قراءة QR. حاول تحديث الصفحة.', 'error')
        showShelfToast('error', 'تعذر تحميل مكتبة قراءة QR')
        setScanStartButtonState('idle')
        return
    }

    clearShelfScannerReader()
    activeShelfScannerMode = 'qr'
    const scannerSessionId = shelfScannerSessionId

    const formatsToSupport = getShelfScannerFormats()
    const scannerConfig = {}
    if (formatsToSupport && formatsToSupport.length) {
        scannerConfig.formatsToSupport = formatsToSupport
    }
    shelfQrScanner = new Html5Qrcode('shelfSerialScannerReader', scannerConfig)

    shelfQrScanner.start(
        { facingMode: 'environment' },
        getShelfScannerStartConfig(),
        (decodedText) => {
            if (scannerSessionId === shelfScannerSessionId && shelfSerialScannerMode === 'qr') {
                handleShelfSerialScan(decodedText)
            }
        },
        () => {}
    ).then(() => {
        if (scannerSessionId !== shelfScannerSessionId || shelfSerialScannerMode !== 'qr') {
            return
        }

        shelfSerialScannerRunning = true
        setScanStartButtonState('running')
        setScannerStatus('الكاميرا مفتوحة لوضع QR. ضع QR داخل المربع.', 'ready')
    }).catch((error) => {
        if (scannerSessionId !== shelfScannerSessionId) {
            return
        }

        const message = isCameraPermissionError(error)
            ? 'تم رفض إذن الكاميرا.'
            : 'تعذر فتح كاميرا QR. تأكد من الإذن أو جرّب متصفح آخر.'
        setScannerStatus(message, 'error')
        showShelfToast('error', message)
        setScanStartButtonState('idle')
    })
}

function startShelfSerialScanner() {
    const panel = document.getElementById('shelfSerialScannerPanel')

    if (panel) {
        panel.hidden = false
        panel.dataset.mode = shelfSerialScannerMode
        panel.scrollIntoView({ behavior: 'smooth', block: 'nearest' })
    }
    setScannerModeButtonState()
    setBarcodeTestModeButtonState()

    if (!window.isSecureContext) {
        setScannerStatus('الكاميرا تحتاج HTTPS حتى تعمل في المتصفح.', 'error')
        showShelfToast('error', 'الكاميرا تحتاج HTTPS')
        return
    }

    if (shelfSerialScannerRunning || shelfQuaggaStarting) {
        return
    }

    setScanStartButtonState('opening')
    setScannerStatus('جاري فتح الكاميرا...', 'loading')
    lastScannedSerial = ''
    lastScannedSerialAt = 0
    resetShelfBarcodeCandidate()
    shelfScannerSessionId += 1
    shelfBarcodeScannerStartedAt = 0

    if (shelfSerialScannerMode === 'qr') {
        startHtml5QrScanner()
    } else {
        startHtml5BarcodeScanner()
    }
}

function stopShelfSerialScanner(hidePanel) {
    const panel = document.getElementById('shelfSerialScannerPanel')
    const shouldHidePanel = hidePanel !== false
    shelfScannerSessionId += 1
    shelfBarcodeScannerStartedAt = 0

    if (!shelfSerialScannerRunning && !shelfQrScanner && !shelfBarcodeReader && !shelfQuaggaActive && !shelfQuaggaStarting) {
        if (panel && shouldHidePanel) {
            panel.hidden = true
        }
        setScanStartButtonState('idle')
        setScannerStatus('', '')
        return Promise.resolve()
    }

    const stopActions = []
    stopNativeBarcodeFallback()
    terminateShelfOcrWorker()
    stopQuaggaBarcodeScanner()
    resetShelfBarcodeCandidate()

    if (shelfQrScanner) {
        const qrScanner = shelfQrScanner
        stopActions.push(qrScanner.stop().catch(() => {}).then(() => {
            if (typeof qrScanner.clear === 'function') {
                try {
                    const clearResult = qrScanner.clear()
                    if (clearResult && typeof clearResult.catch === 'function') {
                        return clearResult.catch(() => {})
                    }
                } catch (e) {
                    return undefined
                }
            }
            return undefined
        }))
        shelfQrScanner = null
    }

    if (shelfBarcodeReader) {
        shelfBarcodeReader.reset()
        shelfBarcodeReader = null
    }

    const barcodeVideo = document.getElementById('shelfSerialBarcodeVideo')
    if (barcodeVideo && barcodeVideo.srcObject) {
        barcodeVideo.srcObject.getTracks().forEach((track) => track.stop())
        barcodeVideo.srcObject = null
    }

    return Promise.all(stopActions).then(() => {
        shelfSerialScannerRunning = false
        activeShelfScannerMode = null
        if (panel && shouldHidePanel) {
            panel.hidden = true
        }
        setScanStartButtonState('idle')
        setScannerStatus('', '')
        clearShelfScannerReader()
    }).catch(() => {
        shelfSerialScannerRunning = false
        activeShelfScannerMode = null
        if (panel && shouldHidePanel) {
            panel.hidden = true
        }
        setScanStartButtonState('idle')
        clearShelfScannerReader()
    })
}

function restartShelfSerialScannerWithMode(mode) {
    shelfSerialScannerMode = mode
    setScannerModeButtonState()

    if (!shelfSerialScannerRunning && !shelfQuaggaStarting && !shelfQuaggaActive && !shelfQrScanner && !shelfBarcodeReader) {
        startShelfSerialScanner()
        return
    }

    setScannerStatus('جاري تغيير وضع القراءة...', 'loading')
    stopShelfSerialScanner(false).then(() => {
        startShelfSerialScanner()
    })
}

function restartShelfBarcodeScannerWithTestMode(mode) {
    if (mode !== 'full' && mode !== 'wide') {
        return
    }

    if (shelfBarcodeTestMode === mode) {
        setBarcodeTestModeButtonState()
        return
    }

    shelfBarcodeTestMode = mode
    setBarcodeTestModeButtonState()

    if (shelfSerialScannerMode !== 'barcode') {
        return
    }

    if (!shelfSerialScannerRunning && !shelfQuaggaStarting && !shelfQuaggaActive && !shelfQrScanner && !shelfBarcodeReader) {
        return
    }

    setScannerStatus('جاري تغيير وضع اختبار الباركود...', 'loading')
    stopShelfSerialScanner(false).then(() => {
        startShelfSerialScanner()
    })
}

$('#shelfSerialScanStart').click(() => {
    shelfSerialScannerMode = 'barcode'
    startShelfSerialScanner()
})

$('#shelfSerialScanStop').click(() => {
    stopShelfSerialScanner()
})

$('#shelfSerialScanQrMode').click(() => {
    const nextMode = shelfSerialScannerMode === 'qr' ? 'barcode' : 'qr'
    restartShelfSerialScannerWithMode(nextMode)
})

$('#shelfBarcodeModeFull, #shelfBarcodeModeWide').click((event) => {
    restartShelfBarcodeScannerWithTestMode(event.currentTarget.dataset.mode)
})

$('.popup-movein-serials .close').click(() => {
    stopShelfSerialScanner()
})

window.addEventListener('pagehide', () => {
    stopShelfSerialScanner()
})

function submitShelfReorderForm() {
    syncSerializableSerialInputs()
    stopShelfSerialScanner().finally(() => {
        $('.popup-movein-serials form').submit()
    })
}

function getShelfSaveConfirmationMessage(counts) {
    const skipped = counts.total - counts.valid

    if (skipped > 0) {
        return 'هيتم حفظ ' + counts.valid + ' سيريال موجود فقط، وتجاهل ' + skipped + ' سيريال غير موجود.'
    }

    return 'هيتم حفظ ' + counts.valid + ' سيريال موجود.'
}

function send_change_request() {
    verifyPendingShelfSerials().then((counts) => {
        if (counts.valid < 1) {
            showShelfToast('warning', 'لا يوجد سيريالات موجودة للحفظ')
            return
        }

        const message = getShelfSaveConfirmationMessage(counts)

        if (typeof window.adminUiConfirm === 'function') {
            window.adminUiConfirm({
                title: 'تأكيد الحفظ',
                message: message,
                confirmText: 'حفظ الموجود فقط',
                cancelText: 'إلغاء',
                icon: 'bi-check2-circle'
            }).then((confirmed) => {
                if (confirmed) {
                    submitShelfReorderForm()
                }
            })
            return
        }

        if (confirm(message)) {
            submitShelfReorderForm()
        }
    })
}

$('.del-btn').click((event) => {
    let row = $(event.target).closest('.products-row');
    let id = row.attr('id');
    let shelfNumber = row.find('.product-cell.num').clone().children().remove().end().text().trim();

    $('#deleteShelfNumber').val(shelfNumber);
    $('#confirmDeleteShelf').attr('href', './shelves/delete_shelf?id=' + id);
    $('.popup-delete-shelf').fadeIn(200);
})
