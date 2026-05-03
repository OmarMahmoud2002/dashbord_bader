document.getElementById('replacementForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    // Validate form
    if (!this.checkValidity()) {
        alert('الرجاء ملء جميع الحقول المطلوبة\nPlease fill all required fields');
        return;
    }
    
    // Get customer name for PDF filename
    const customerName = document.querySelector('input[name="customerName"]').value;
    const sanitizedName = customerName.replace(/[^a-zA-Z0-9\u0600-\u06FF]/g, '_');
    
    // Set document title for PDF filename
    const originalTitle = document.title;
    document.title = `نموذج_استبدال_${sanitizedName}` || 'نموذج_استبدال';
    
    // Use browser's print to PDF functionality
    window.print();
    
    // Restore original title
    setTimeout(() => {
        document.title = originalTitle;
    }, 1000);
});

// تهيئة Flatpickr لحقل التاريخ
window.addEventListener('load', function() {
    const today = new Date();
    const year = today.getFullYear();
    const month = String(today.getMonth() + 1).padStart(2, '0');
    const day = String(today.getDate()).padStart(2, '0');
    const dateValue = new Date(today.getFullYear(), today.getMonth(), today.getDate());
    
    flatpickr("#dateField", {
        dateFormat: "d/m/Y",  // يوم / شهر / سنة (DD/MM/YYYY)
        locale: "ar",         // اللغة العربية
        defaultDate: dateValue, // التاريخ الافتراضي (اليوم)
        enableTime: false,    // بدون وقت
        mode: "single",       // اختيار تاريخ واحد فقط
        closeOnSelect: true   // إغلاق التقويم عند الاختيار
    });
});
