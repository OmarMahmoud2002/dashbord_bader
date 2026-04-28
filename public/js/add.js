// Modern JavaScript for Excel Upload with Progress
class ExcelUploader {
  constructor() {
    this.initializeElements()
    this.bindEvents()
    this.maxFileSize = 50 * 1024 * 1024 // 50MB
    this.allowedTypes = [
      "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
      "application/vnd.ms-excel",
    ]
  }

  initializeElements() {
    this.uploadZone = document.getElementById("uploadZone")
    this.fileInput = document.getElementById("excelFile")
    this.fileInfo = document.getElementById("fileInfo")
    this.fileName = document.getElementById("fileName")
    this.fileSize = document.getElementById("fileSize")
    this.removeFileBtn = document.getElementById("removeFile")
    this.uploadBtn = document.getElementById("uploadBtn")
    this.btnText = document.getElementById("btnText")
    this.uploadForm = document.getElementById("uploadForm")
    this.progressContainer = document.getElementById("progressContainer")
    this.progressBar = document.getElementById("progressBar")
    this.progressPercent = document.getElementById("progressPercent")
    this.progressStatus = document.getElementById("progressStatus")
  }

  bindEvents() {
    // Upload zone events
    this.uploadZone.addEventListener("click", () => this.fileInput.click())
    this.uploadZone.addEventListener("dragover", this.handleDragOver.bind(this))
    this.uploadZone.addEventListener("dragleave", this.handleDragLeave.bind(this))
    this.uploadZone.addEventListener("drop", this.handleDrop.bind(this))

    // File input events
    this.fileInput.addEventListener("change", this.handleFileSelect.bind(this))

    // Remove file button
    this.removeFileBtn.addEventListener("click", this.removeFile.bind(this))

    // Form submission
    this.uploadForm.addEventListener("submit", this.handleFormSubmit.bind(this))
  }

  handleDragOver(e) {
    e.preventDefault()
    this.uploadZone.classList.add("dragover")
  }

  handleDragLeave(e) {
    e.preventDefault()
    this.uploadZone.classList.remove("dragover")
  }

  handleDrop(e) {
    e.preventDefault()
    this.uploadZone.classList.remove("dragover")

    const files = e.dataTransfer.files
    if (files.length > 0) {
      this.processFile(files[0])
    }
  }

  handleFileSelect(e) {
    const file = e.target.files[0]
    if (file) {
      this.processFile(file)
    }
  }

  processFile(file) {
    // Validate file type
    if (!this.allowedTypes.includes(file.type)) {
      this.showError("نوع الملف غير مدعوم. يرجى اختيار ملف Excel (.xlsx أو .xls)")
      return
    }

    // Validate file size
    if (file.size > this.maxFileSize) {
      this.showError("حجم الملف كبير جداً. الحد الأقصى المسموح: 50 ميجابايت")
      return
    }

    // Display file info
    this.displayFileInfo(file)
    this.enableUploadButton()
  }

  displayFileInfo(file) {
    this.fileName.textContent = file.name
    this.fileSize.textContent = this.formatFileSize(file.size)

    this.fileInfo.classList.remove("d-none")
    this.uploadZone.style.display = "none"
  }

  formatFileSize(bytes) {
    if (bytes === 0) return "0 بايت"

    const k = 1024
    const sizes = ["بايت", "كيلوبايت", "ميجابايت", "جيجابايت"]
    const i = Math.floor(Math.log(bytes) / Math.log(k))

    return Number.parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + " " + sizes[i]
  }

  enableUploadButton() {
    this.uploadBtn.disabled = false
    this.btnText.textContent = "رفع وتحديث المخزون"
    this.uploadBtn.classList.add("btn-gradient-primary")
  }

  removeFile() {
    this.fileInput.value = ""
    this.fileInfo.classList.add("d-none")
    this.uploadZone.style.display = "block"
    this.uploadBtn.disabled = true
    this.btnText.textContent = "اختر ملف أولاً"
    this.uploadBtn.classList.remove("btn-gradient-primary")
  }

  handleFormSubmit(e) {
    e.preventDefault()

    if (!this.fileInput.files[0]) {
      this.showError("يرجى اختيار ملف أولاً")
      return
    }

    this.startUpload()
  }

  startUpload() {
    // Show progress container
    this.progressContainer.classList.remove("d-none")
    this.uploadBtn.disabled = true
    this.btnText.innerHTML = '<span class="loading-spinner me-2"></span>جاري الرفع...'

    // Create FormData
    const formData = new FormData()
    formData.append("excel_file", this.fileInput.files[0])

    // Simulate upload progress (since PHP doesn't provide real-time progress)
    this.simulateProgress()

    // Send request
    fetch(window.location.href, {
      method: "POST",
      body: formData,
    })
      .then((response) => response.text())
      .then((html) => {
        // Parse response to check for success/error
        const parser = new DOMParser()
        const doc = parser.parseFromString(html, "text/html")
        const alert = doc.querySelector(".alert")

        if (alert) {
          this.completeUpload()
          // Reload page to show result
          setTimeout(() => {
            window.location.reload()
          }, 1000)
        } else {
          throw new Error("استجابة غير متوقعة من الخادم")
        }
      })
      .catch((error) => {
        this.handleUploadError(error.message)
      })
  }

  simulateProgress() {
    let progress = 0
    const interval = setInterval(() => {
      progress += Math.random() * 15

      if (progress >= 95) {
        progress = 95
        this.progressStatus.textContent = "جاري معالجة البيانات..."
        clearInterval(interval)
        return
      }

      this.updateProgress(progress)

      // Update status messages
      if (progress < 30) {
        this.progressStatus.textContent = "جاري رفع الملف..."
      } else if (progress < 60) {
        this.progressStatus.textContent = "جاري قراءة البيانات..."
      } else if (progress < 90) {
        this.progressStatus.textContent = "جاري تحديث قاعدة البيانات..."
      }
    }, 200)
  }

  updateProgress(percent) {
    const roundedPercent = Math.round(percent)
    this.progressBar.style.width = roundedPercent + "%"
    this.progressPercent.textContent = roundedPercent + "%"
  }

  completeUpload() {
    this.updateProgress(100)
    this.progressStatus.textContent = "تم الانتهاء بنجاح!"

    // Add success animation
    this.progressContainer.classList.add("success-animation")

    setTimeout(() => {
      this.btnText.innerHTML = '<i class="bi bi-check-circle-fill me-2"></i>تم بنجاح'
      this.uploadBtn.classList.add("btn-success")
    }, 500)
  }

  handleUploadError(message) {
    this.progressContainer.classList.add("d-none")
    this.uploadBtn.disabled = false
    this.btnText.textContent = "إعادة المحاولة"
    this.showError("حدث خطأ أثناء الرفع: " + message)
  }

  showError(message) {
    // Create and show error alert
    const alertDiv = document.createElement("div")
    alertDiv.className = "alert alert-danger alert-dismissible fade show shadow-sm"
    alertDiv.innerHTML = `
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `

    // Insert at the top of the container
    const container = document.querySelector(".container .row .col-lg-8")
    container.insertBefore(alertDiv, container.firstChild)

    // Auto dismiss after 5 seconds
    setTimeout(() => {
      if (alertDiv.parentNode) {
        alertDiv.remove()
      }
    }, 5000)
  }
}

// Initialize uploader when DOM is loaded
document.addEventListener("DOMContentLoaded", () => {
  new ExcelUploader()
})

// Add smooth scrolling for better UX
document.querySelectorAll('a[href^="#"]').forEach((anchor) => {
  anchor.addEventListener("click", function (e) {
    e.preventDefault()
    const target = document.querySelector(this.getAttribute("href"))
    if (target) {
      target.scrollIntoView({
        behavior: "smooth",
        block: "start",
      })
    }
  })
})

// Add loading state to page
window.addEventListener("beforeunload", () => {
  document.body.style.opacity = "0.7"
})
