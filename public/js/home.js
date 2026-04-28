
$(".app-content-headerButton").click(()=>{
  $(".popup-add-product").fadeIn(200)
})
$(".products-row .actions .edit-btn").click(()=>{
  $(".popup-edit-product").fadeIn(200)
})

$('.del-btn').click((event) => {

    var itemID = $(event.target).parent().parent().attr('id');
    if (confirm('هل تريد حذف المنتج ؟')) {
        $.get('./delete_product?id=' + itemID, () => {
            window.location = window.location.href
        })
    }
    
})

document.querySelector(".grid").addEventListener("click", function () {
  document.querySelector(".list").classList.remove("active");
  document.querySelector(".grid").classList.add("active");
  document.querySelector(".products-area-wrapper").classList.add("gridView");
  document
    .querySelector(".products-area-wrapper")
    .classList.remove("tableView");
});

document.querySelector(".list").addEventListener("click", function () {
  document.querySelector(".list").classList.add("active");
  document.querySelector(".grid").classList.remove("active");
  document.querySelector(".products-area-wrapper").classList.remove("gridView");
  document.querySelector(".products-area-wrapper").classList.add("tableView");
});



$('input[name="dates"]').daterangepicker({
  "autoApply": true,
  "alwaysShowCalendars": true,
  "startDate": new Date(),
  "endDate": new Date(),
}, function(start, end, label) {
console.log('New date range selected: ' + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD') + ' (predefined range: ' + label + ')');
});

// Valid
$(".products-row .actions .valid-btn").click(()=>{
  $(".popup-valid").fadeIn(200)
})

// Add Prod
$(".products-row .actions .add-prod").click((event)=>{
  let product = $(event.target).closest('.products-row');
  

  if (product.attr('data-unified') == '1') {
    $('#barcodeproduct_id').val(product.attr('id'));
    $(".popup-add-prod-barcode").fadeIn(200)
  } else {
    $('#serialproduct_id').val(product.attr('id'));
    $(".popup-add-prod-serial").fadeIn(200)
  }
})

function add_serial() {
    let serial = $('#SerialInputAdder').val()
    if (serial == '') {
        return null
    }
    let poster_number = $('#PosterInputAdder').val()
    if (poster_number == '') {
        poster_number = 0;
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

function delete_serial(){
    $(event.target).closest('.serial').remove()
    $('.serials_number').text(parseInt($('.serials_number').text()) - 1)
}


$('#brandsSelector').change((e) => {
    index = e.target.selectedIndex
    brand = e.target.options[index].innerText;
    
    $.get('./request_categories_by_brand?brand=' + brand, (data) => {
        data = JSON.parse(data);
        document.getElementById('categoriesSelector').innerHTML = '';
        for (category of data) {
            document.getElementById('categoriesSelector').innerHTML += `<option value = "${category['category']}">${category['category']}</option>`
        }
    })
})

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

$('.popup-add-prod-serial form').on('keydown', (e) => {
    if (e.keyCode == 13) {
        add_serial()
    }
})