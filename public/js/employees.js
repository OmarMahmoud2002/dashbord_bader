$(".app-content-headerButton").click(()=>{
  $(".popup-add-employee").fadeIn(200)
})
$(".products-row .actions .edit-btn").click(()=>{
  $(".popup-edit-employee").fadeIn(200)
})
$(".products-row .actions .add-oh").click(()=>{
  $(".popup-add-ohda").fadeIn(200)
})

$('.del-btn').click(() => {
    let main_url = window.location.origin
    let user_custody_id = $(event.target).closest('.d').attr('id');
    $.post(main_url + '/admin/products/remove_custody', {'custody_id' : user_custody_id}, (data) => {
        location.reload()
    })
})


function add_serial() {
    let value = $('#SerialInputAdder').val()
    if (value == '') {
        return null
    }
    let element = document.getElementById('serials')
    $('.serials_number').text(parseInt($('.serials_number').text()) + 1)
    document.querySelector('#serials').innerHTML += '<div class = "serial" data-serial = "' + value + '"><span>' + value + '</span><input type = "hidden" name = "serials[]" value = "' + value + '" readonly><i class = "fa fa-trash delete" onclick = "delete_serial()"></i></div>'
    $('#SerialInputAdder').val('')
    element.scrollTop = -element.scrollHeight
}

$('#serialAdder').click((event) => {
    add_serial()
})

function delete_serial(){
    $(event.target).closest('.serial').remove()
    $('.serials_number').text(parseInt($('.serials_number').text()) - 1)
}

function send_add_request() {
    if (confirm('هل تريد حفظ البيانات')) {
        $('.popup-add-prod-serial form').submit()
    }
}

// Disable form submission through enter

$('form').on('keydown', (e) => {
    if (e.keyCode == 13) {
        e.preventDefault()
    }
})

$('.popup-add-ohda form').on('keydown', (e) => {
    if (e.keyCode == 13) {
        add_serial()
    }
})