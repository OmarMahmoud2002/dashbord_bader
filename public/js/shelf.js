$(".app-content-headerButton").click(()=>{
  $(".popup-add-shelf").fadeIn(200)
})

$('.orderShelf').click((event) => {
    $(".popup-movein-serials").css('display', 'block');
})

function add_serial() {
    let serial = $('#SerialInputAdder').val()
    if (serial == '') {
        return null
    }
    let poster_number = $('#PosterInputAdder').val()
    if (poster_number == '') {
        poster_number = '';
    }

    let element = document.getElementById('serials')
    $('.serials_number').text(parseInt($('.serials_number').text()) + 1)
    document.querySelector('#serials').innerHTML += '<div class = "serial" data-serial = "' + serial + '"><span>' + serial + '</span><input type = "hidden" name = "serials[]" value = "' + serial + '" readonly><span>' + poster_number + '</span><input type = "hidden" name = "posters[]" value = "' + poster_number + '" readonly><i class = "fa fa-trash delete" onclick = "delete_serial()"></i></div>'
    $('#SerialInputAdder').val('')
    $('#PosterInputAdder').val('')
    element.scrollTop = -element.scrollHeight
}

$('#serialAdder').click((event) => {
    add_serial()
})

$('.popup-movein-serials form').on('keydown', (e) => {
    if (e.keyCode == 13) {
        add_serial()
        e.preventDefault()
    }
})

function delete_serial(){
    $(event.target).closest('.serial').remove()
    $('.serials_number').text(parseInt($('.serials_number').text()) - 1)
}

function send_change_request() {
    if (confirm('هل تريد حفظ البيانات')) {
        $('.popup-movein-serials form').submit()
    }
}

$('.del-btn').click((event) => {
    if (confirm('هل تريد حذف الرف؟')) {
        id = $(event.target).parent().parent().attr('id')
        $.ajax({
            url: './shelves/delete_shelf?id=' + id,
            type: 'get',
            success: (data) => {
                window.location = window.location.href;
            }
        })
    }
    
})
