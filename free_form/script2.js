// تحميل النموذج كملف PDF
document.addEventListener('DOMContentLoaded', function() {
    const cancelDateInput = document.getElementById('serviceCancelDate');
    const syncedDateInputs = document.querySelectorAll('[data-sync-from-cancel-date]');
    let syncingCancelDate = false;

    // START settlement empty field color additions
    const updateWritableFieldColor = (field) => {
        const cell = field.closest('.input-cell');
        if (!cell || cell.classList.contains('form-default-cell')) {
            return;
        }

        cell.classList.toggle('has-written-value', field.value.trim() !== '');
    };

    const updateAllWritableFieldColors = () => {
        document.querySelectorAll('.input-cell:not(.form-default-cell) input[type="text"], .input-cell:not(.form-default-cell) textarea').forEach(updateWritableFieldColor);
    };

    document.querySelectorAll('.input-cell:not(.form-default-cell) input[type="text"], .input-cell:not(.form-default-cell) textarea').forEach((field) => {
        ['input', 'change'].forEach((eventName) => {
            field.addEventListener(eventName, () => updateWritableFieldColor(field));
        });
        updateWritableFieldColor(field);
    });

    document.addEventListener('forms:writable-fields-updated', updateAllWritableFieldColors);
    // END settlement empty field color additions
    
    // تفعيل flatpickr لحقول التاريخ
    if (typeof flatpickr !== 'undefined') {
        flatpickr(".date-picker", {
            dateFormat: "d/m/Y",
            locale: "ar",
            allowInput: true,
            closeOnSelect: true,
            enableTime: false,
            mode: "single"
        });
        console.log('تم تفعيل حقول التاريخ بنجاح');
    } else {
        console.error('مكتبة flatpickr غير محملة');
    }

    const setDateInputValue = (input, value) => {
        if (input._flatpickr) {
            input._flatpickr.setDate(value, false, "d/m/Y");
            // START settlement empty field color additions
            updateWritableFieldColor(input);
            // END settlement empty field color additions
            return;
        }

        input.value = value;
        // START settlement empty field color additions
        updateWritableFieldColor(input);
        // END settlement empty field color additions
    };

    const syncCancelDateToSignatureDates = () => {
        if (!cancelDateInput || !cancelDateInput.value.trim()) {
            return;
        }

        syncingCancelDate = true;
        syncedDateInputs.forEach((input) => {
            if (!input.value.trim() || input.dataset.syncedFromCancelDate === '1') {
                setDateInputValue(input, cancelDateInput.value);
                input.dataset.syncedFromCancelDate = '1';
            }
        });
        syncingCancelDate = false;
    };

    if (cancelDateInput && syncedDateInputs.length) {
        ['change', 'input'].forEach((eventName) => {
            cancelDateInput.addEventListener(eventName, syncCancelDateToSignatureDates);
        });

        syncedDateInputs.forEach((input) => {
            ['change', 'input'].forEach((eventName) => {
                input.addEventListener(eventName, () => {
                    if (!syncingCancelDate) {
                        input.dataset.syncedFromCancelDate = '0';
                    }
                });
            });
        });

        syncCancelDateToSignatureDates();
    }
    
    const downloadBtn = document.getElementById('downloadBtn');
    
    if (downloadBtn) {
        downloadBtn.addEventListener('click', function() {
            // التحقق من ملء جميع الحقول المطلوبة
            const allInputs = document.querySelectorAll('input[type="text"], textarea');
            let hasEmptyFields = false;
            
            allInputs.forEach((input) => {
                if (!input.value.trim()) {
                    hasEmptyFields = true;
                }
            });
            
            if (hasEmptyFields) {
                alert('يرجى ملء جميع الحقول المطلوبة قبل التحميل');
                return;
            }
            
            // START settlement PDF filename additions
            const customerNameInput = document.querySelector('.form-table input[type="text"]');
            const customerName = customerNameInput && customerNameInput.value.trim() ? customerNameInput.value.trim() : 'عميل';
            const sanitizedName = customerName.replace(/[<>:"/\\|?*]/g, '_').replace(/\s+/g, ' ').trim();
            const printTitle = `نموذج تسوية الغرامة_${sanitizedName}`;
            // END settlement PDF filename additions

            // تعيين عنوان المستند لاسم ملف PDF
            const originalTitle = document.title;
            const originalTopTitle = window.top && window.top.document ? window.top.document.title : null;
            // START settlement PDF filename additions
            document.title = printTitle;
            if (window.top && window.top.document) {
                window.top.document.title = printTitle;
            }
            // END settlement PDF filename additions
            
            // استخدام وظيفة الطباعة إلى PDF في المتصفح
            window.print();
            
            // استعادة العنوان الأصلي
            setTimeout(() => {
                document.title = originalTitle;
                if (window.top && window.top.document && originalTopTitle !== null) {
                    window.top.document.title = originalTopTitle;
                }
            }, 1000);
        });
    }
    
    console.log('النموذج جاهز للاستخدام');
});
