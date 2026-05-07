// تحميل النموذج كملف PDF
document.addEventListener('DOMContentLoaded', function() {
    const cancelDateInput = document.getElementById('serviceCancelDate');
    const syncedDateInputs = document.querySelectorAll('[data-sync-from-cancel-date]');
    let syncingCancelDate = false;
    
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
            return;
        }

        input.value = value;
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
            
            // الحصول على اسم العميل للملف
            const customerNameInput = document.querySelector('.form-table input[type="text"]');
            const customerName = customerNameInput ? customerNameInput.value.trim() : 'عميل';
            const sanitizedName = customerName.replace(/[^a-zA-Z0-9\u0600-\u06FF]/g, '_');
            
            // تعيين عنوان المستند لاسم ملف PDF
            const originalTitle = document.title;
            document.title = `نموذج_تسوية_غرامة_${sanitizedName}` || 'نموذج_تسوية_غرامة';
            
            // استخدام وظيفة الطباعة إلى PDF في المتصفح
            window.print();
            
            // استعادة العنوان الأصلي
            setTimeout(() => {
                document.title = originalTitle;
            }, 1000);
        });
    }
    
    console.log('النموذج جاهز للاستخدام');
});
