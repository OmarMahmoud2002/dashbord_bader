
$('#reset-btn').click(function() {
    $('#from-date').val('');
    $('#to-date').val('');
});

// تأكيد البيع
function confirmSale(productId) {
    if (confirm('هل تريد تأكيد بيع هذا الجهاز؟')) {
        alert('تم تأكيد بيع المنتج بنجاح');
    }
}

// إرجاع المنتج
function returnProduct(productId) {
    if (confirm('هل تريد إرجاع هذا المنتج؟')) {
        $.post('request_product_return', {'id': productId}, function() {
            alert('تم إرسال طلب إرجاع المنتج بنجاح');
        });
    }
}

// تهيئة التطبيق
function initApp() {    
    $('.table-responsive').css('display', 'block')
    $(".btn-return").click(function() {
        var productId = $(this).data('product-id');
        returnProduct(productId);
    });
}

// بدء التطبيق
document.addEventListener('DOMContentLoaded', initApp);