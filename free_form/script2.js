// تحميل النموذج كملف PDF
document.addEventListener('DOMContentLoaded', function() {
    
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
