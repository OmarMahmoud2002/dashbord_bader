
$('.del-btn').click((event) => {
    $(".popup-remove-serial").css('display', 'block');
    $('#itemid').val($(event.target).closest('.products-row').attr('id'))
})

$('.close').click((event) => {
    $(event.target).closest('.popup').css('display', 'none');
})