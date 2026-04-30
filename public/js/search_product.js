let serialNumber = document.getElementById("serialNumberInput");
let verificationInput = document.getElementById("verificationInput");
let verification = document.getElementById("verification");
let checkResult = document.querySelector(".checkResult");
let checkBtn = document.getElementById("checkBtn");
let nextBtn = document.getElementById("nextBtn");
let startOverBtn = document.getElementById("startOverBtn");
let deliveryBtn = document.getElementById("deliveryBtn");
let delivery_btn = document.getElementsByClassName("delivery_btn");
let prod_infos = document.querySelector(".prod_infos");
let prod_form = document.querySelector(".admin-ui-product-search .prod_form");
let select_type = document.querySelector(".select_type");
let radios_enabled = false
let radios = [];
let redirected = false;
let checkResultMsgAccepted = '<span style="color:green">تم التحقق و المطابقة<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--!Font Awesome Free 6.5.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path d="M256 512A256 256 0 1 0 256 0a256 256 0 1 0 0 512zM369 209L241 337c-9.4 9.4-24.6 9.4-33.9 0l-64-64c-9.4-9.4-9.4-24.6 0-33.9s24.6-9.4 33.9 0l47 47L335 175c9.4-9.4 24.6-9.4 33.9 0s9.4 24.6 0 33.9z"/></svg></span>';
let checkResultMsgRejected = '<span style="color:red">لم يتم التحقق<svg  style="fill:red" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--!Font Awesome Free 6.5.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path d="M256 512A256 256 0 1 0 256 0a256 256 0 1 0 0 512zM175 175c9.4-9.4 24.6-9.4 33.9 0l47 47 47-47c9.4-9.4 24.6-9.4 33.9 0s9.4 24.6 0 33.9l-47 47 47 47c9.4 9.4 9.4 24.6 0 33.9s-24.6 9.4-33.9 0l-47-47-47 47c-9.4 9.4-24.6 9.4-33.9 0s-9.4-24.6 0-33.9l47-47-47-47c-9.4-9.4-9.4-24.6 0-33.9z"/></svg></span>';
let product_data = {};
var selected_items = [];
let verified_items = []
let process_type = '';
let accepted_cart_item;
//
const main_url = window.location.origin


serialNumber.focus()

function sync_product_search_layout() {
    if (!prod_form || !prod_infos) return;
    prod_form.classList.toggle('has-result', !prod_infos.classList.contains('hide'));
}

if (prod_form && prod_infos) {
    sync_product_search_layout();
    new MutationObserver(sync_product_search_layout).observe(prod_infos, {
        attributes: true,
        attributeFilter: ['class']
    });
}

function set_data(data) {
    var image_path = "/public/img/products/" + 'default_product_image.png';
    $('#product_name').text(data['name']);
    
    if (data['shelf'].includes('movedToEmployee')) {
        $('#item_shelf').text('تم تسليمه');
    } else {
        $('#item_shelf').text(data['shelf']);
    }
    
    $('#item_code').text(data['code']);
    $('#subinventory_code').text(data['subinventory_code']);
    $('#total_quantity').text(data['total_quantity']);
    $('#product_image').attr('href', image_path);
    $('#product_image img').attr('src', image_path);
    $('#poster_number').text(data['poster_number']);
    
    
    product_data['name'] = data['name'];
    product_data['id'] = data['id'];
    product_data['serial_control'] = data['serial_control'];

    if (data['serial_control'] == 'no') {
      product_data['barcode'] = data['barcode'];
      $('#item_barcode').text(product_data['barcode']);
    } else {
      product_data['serial'] = data['serial_number'];
      $('#item_barcode').text('لا يوجد');
    }

}

function get_product_by_serial(serial) {
  $.get(main_url + '/admin/products/get_product_by_serial?serial=' + serial, function(data) {
    data = JSON.parse(data);

    if (data['status'] == 'ok') {
        set_data(data);
        checkBtn.classList.remove("hide");
        prod_infos.classList.remove("hide");
        verification.classList.remove("hide");
        verificationInput.focus()
        nextBtn.classList.add("hide");

    } else if (data['status'] == 'custody_product' || data['status'] == 'delivered_product' || data['status'] == 'in_cart') {
        set_data(data);
        prod_infos.classList.remove("hide");
        nextBtn.classList.add("hide");
        document.querySelector('#serialNumberError').innerHTML = data['description'];
        document.querySelector('#serialNumberError').classList.remove('hide');
        $('#serialNumberHistory a').attr('href', './operations?serial=' + product_data['serial'])
        $('#serialNumberHistory').removeClass('hide')
        
    } else {
        document.querySelector('#serialNumberError').innerText = data['description'];
        document.querySelector('#serialNumberError').classList.remove('hide');
        serialNumber.disabled = false;
    }
  })
}

function get_item_categories_by_serial_part(serial_part) {
  $.get(main_url + '/admin/products/get_item_categories_by_serial_part?serial_part=' + serial_part, function(data) {

    if (data === '[]') {
      document.querySelector('#serialNumberError').innerText = 'السيريال غير موجود';
      document.querySelector('#serialNumberError').classList.remove('hide');
      serialNumber.disabled = false;
      return;
    }

    data = JSON.parse(data);
    if (data.length == 1) {
      serialNumber.disabled = true;
      redirected = true;
      radios_enabled = true;

      document.querySelector('.serialAdd').classList.remove('hide')
      get_product_by_serial(data[0][0]);

    } else {
      for (serial_category of data) {
        var item = `<div class="bt">
            <input type="radio" name="productType" value="${serial_category[0]}">
            <button type="button">(${serial_category[1]}) ---> ${serial_category[0]}</button>
        </div>`
        $(".select_type").html($(".select_type").html() + item);
      }
      enable_radios();
      select_type.classList.remove("hide");
      serialNumber.disabled = true;
    }
  })
}

function nextBtn_handler() {
    if(serialNumber.value.length < 6){
        document.getElementById("serialNumberError").classList.remove("hide");
        document.querySelector('#serialNumberHistory').classList.add('hide');
    } else {
        document.getElementById("serialNumberError").classList.add("hide");
        document.querySelector('#serialNumberHistory').classList.add('hide')

        if (radios_enabled == true && checkRadio()) {
            disableRadios();
            verification.classList.remove("hide");
            verificationInput.focus()
            checkBtn.classList.remove("hide");
            nextBtn.classList.add("hide");

        } else if (serialNumber.value.length < 7) {
            get_item_categories_by_serial_part(serialNumber.value);

        } else {
            document.querySelector('.serialAdd').classList.remove('hide')
            get_product_by_serial(serialNumber.value);
        }
    }
    
}

nextBtn.onclick = () => {
    nextBtn_handler()
}

$('#serialNumberInput').on('keypress', (event) => {
    let key = event.key;
    if (key == 'Enter') {
        event.preventDefault()
        nextBtn_handler();
    }
})

function enable_radios() {
    radios = document.getElementsByName('productType');
    radios_enabled = true;
    radios.forEach(radioInput => {
        radioInput.addEventListener('click', function() {
            document.querySelector('.serialAdd').classList.remove('hide')
            get_product_by_serial(radioInput.value);
        });
    });
}

function checkRadio() {
  if (redirected) {
    return true;
  }
  let isChecked = false;
  for (let i = 0; i < radios.length; i++) {
    if (radios[i].checked) {
      isChecked = true;
      break;
    }
  }
  return isChecked;
}

function disableRadios() {
  radios = document.querySelectorAll('input[type="radio"][name="productType"]');
  radios.forEach(function(radio) {
      radio.disabled = true;
  });
}

function enable_delivery_step() {
    checkBtn.classList.add("hide");
    deliveryBtn.classList.remove("hide");
    startOverBtn.classList.remove("hide");
    verificationInput.disabled = true;
    serialNumber.disabled = true;
}

function checkBtn_handler() {
    if((serialNumber.value.toUpperCase() == verificationInput.value.toUpperCase() || 'S' +  serialNumber.value.toUpperCase() == verificationInput.value.toUpperCase()) && !checkRadio()) {
        enable_delivery_step();
        checkResult.innerHTML = checkResultMsgAccepted;
        $('#serialHistory').removeClass('hide')
    } else if (serialNumber.value.toUpperCase() == verificationInput.value.toUpperCase()) {
        enable_delivery_step();
        checkResult.innerHTML = checkResultMsgAccepted;
        $('#serialHistory').removeClass('hide')
    } else {
        checkResult.innerHTML = checkResultMsgRejected
    }
}

checkBtn.onclick = () => {
  checkBtn_handler()
}

$('#verificationInput').on('keypress', (event) => {
    let key = event.key;
    if (key == 'Enter') {
        event.preventDefault()
        checkBtn_handler();
    }
})

deliveryBtn.onclick = () => {
    process_type = 'addProductSearched'
    delivery_popup.classList.remove("hide");
}



//-----
let itemCode = document.getElementById("itemCodeInput");
let newVerificationInput = document.getElementById("newVerificationInput");
let newVerification = document.getElementById("newVerification");
let newCheckResult = document.querySelector(".newCheckResult");
let newCheckBtn = document.getElementById("newCheckBtn");
let newNextBtn = document.getElementById("newNextBtn");
let newStartOverBtn = document.getElementById("newStartOverBtn");
let newDeliveryBtn = document.getElementById("newDeliveryBtn");
let newDelivery_btn = document.getElementsByClassName("newDelivery_btn");
let newProd_infos = document.querySelector(".prod_infos");
let newSelect_type = document.querySelector(".newSelect_type");

function newNextBtn_handler() {
    if (itemCode.value.length < 1){
        document.querySelector('.newserialAdd').classList.add('hide')
        document.getElementById("itemCodeError").classList.remove("hide");
    } else {
        document.getElementById("itemCodeError").classList.add("hide");

        $.get('./get_product_by_item_code?item_code=' + itemCode.value, function(data) {
            data = JSON.parse(data);

            
            if (data['status'] == 'ok') {
                
                set_data(data);
                newVerification.classList.remove("hide");
                newCheckBtn.classList.remove("hide");
                itemCode.disabled = true;
                
                document.querySelector('.newserialAdd').classList.remove('hide')
                newProd_infos.classList.remove("hide");
                
            } else {
                document.getElementById("itemCodeError").innerText = data['description'];
                document.getElementById("itemCodeError").classList.remove("hide");
            }

            newNextBtn.classList.add("hide");
        })
    }
}

newNextBtn.onclick = () => {
    newNextBtn_handler()
}

$('#itemCodeInput').on('keypress', (event) => {
    let key = event.key;
    if (key == 'Enter') {
        event.preventDefault()
        newNextBtn_handler();
    }
})

function newCheckBtn_handler() {
    if(product_data['barcode'].toUpperCase() == newVerificationInput.value.toUpperCase()) {
        newCheckBtn.classList.add("hide");
        newProd_infos.classList.remove("hide");
        newDeliveryBtn.classList.remove("hide");
        newVerificationInput.disabled = true;
        newCheckResult.innerHTML = checkResultMsgAccepted;
    } else {
        newCheckResult.innerHTML = checkResultMsgRejected;
    }
}

newCheckBtn.onclick = () => {
    newCheckBtn_handler()
}

$('#newVerification').on('keypress', (event) => {
    let key = event.key;
    if (key == 'Enter') {
        event.preventDefault()
        newCheckBtn_handler();
    }
})

newDeliveryBtn.onclick = () => {
  delivery_popup.classList.remove("hide");
  process_type = 'addProductSearched';
}

let delivery_popup = document.querySelector(".delivery_popup");
let delivery_popup_btns = document.querySelector(".delivery_popup .btns");
let delivery_popup_close = document.querySelector(".delivery_popup .close");
let delivery_popup_title = document.getElementById("delivery_popup_title");

delivery_btn[0].onclick = () => {
  delivery_popup_btns.classList.add("hide");
  document.querySelector(`${delivery_btn[0].dataset.type}`).classList.remove("hide");
  delivery_popup_title.textContent = delivery_btn[0].dataset.title;
  reset_employee_selection();
}

function reset_employee_selection() {
  let selectEl = document.getElementById('selectElement');
  if (selectEl) {
    selectEl.value = "";
    selectEl.dispatchEvent(new Event('change', { bubbles: true }));
  }
}

delivery_btn[1].onclick = () => {
  delivery_popup_btns.classList.add("hide");
  document.querySelector(`${delivery_btn[1].dataset.type}`).classList.remove("hide");
  delivery_popup_title.textContent = delivery_btn[1].dataset.title;
}

delivery_popup_close.onclick = () => {
  document.querySelector(`${delivery_btn[1].dataset.type}`).classList.add("hide");
  document.querySelector(`${delivery_btn[0].dataset.type}`).classList.add("hide");
  delivery_popup_btns.classList.remove("hide");
  delivery_popup.classList.add("hide");
}

startOverBtn.onclick = () => {
  location.reload();
}

async function register_process(emp_id, item_id, order_number) {
    cartElements = document.getElementsByClassName('cart-item')
    
    if (process_type == 'addProductSearched') {
        register_custody(emp_id, item_id, order_number)
    } else {
        await add_cart_custodies(emp_id, order_number)
    }
}

async function register_custody(emp_id, item_id, order_number) {
  await $.post('./add_item_as_custody', {
    'emp_id' : emp_id, 
    'item_id' : item_id, 
    'order_number': order_number,
  }, (data) => {
    data = JSON.parse(data);
    if (data['status'] == 'ok') {
        document.querySelector(".popup_employee .content").classList.add("hide");
        document.querySelector(".popup_employee .done").classList.remove("hide");
        document.getElementById("popEmplError").classList.add("hide");
        setTimeout(()=>{
            delivery_popup.classList.add("hide");
            delivery_popup_btns.classList.remove("hide");
            document.querySelector(".popup_employee .done").classList.add("hide");
            location.reload()
        },3000)
    } else if (data['status'] == 'wrong') {
      alert(data['msg']);
    }
  });
}

async function add_cart_custodies(emp_id, order_num) {

    if (selected_items.length > 0) {
        items_to_remove = [];
        for (let cartItemId of selected_items) {
            if (!verified_items.includes(cartItemId)) {
                alert('يرجي التحقق من الأرقام الخاصة بالمنتجات')
                return;
            }
            
        }

        for (let cartItemId of selected_items) {
          let cartitem = $('#' + cartItemId);
          let item_id = cartitem.attr('data-item-id')
          items_to_remove.push(cartItemId)

          await register_custody(emp_id, item_id, order_num)
        }

        $.post('./delete_selected_cart_items', {'selected': items_to_remove}, () => {})
    } else {
        if (!verified_items.includes($(accepted_cart_item).attr('id'))) {
            alert('يرجي التحقق من رقم المنتج')
            return;
        }

        let item_id = $(accepted_cart_item).attr('data-item-id')

        await register_custody(emp_id, item_id, order_num)
        $.post('./delete_selected_cart_items', {'selected': [$(accepted_cart_item).attr('id')]}, () => {})
    }
    

    
}

document.getElementById("popup_employee_btn").onclick = () => {
  let emp_id = document.getElementById("selectElement").value;
  let order_num = document.getElementById("empoo").value;

  if (emp_id) {
    register_process(emp_id, product_data['id'], order_num)
  } else {
    document.getElementById("popEmplError").classList.remove("hide");
  }
}

document.querySelectorAll(".list li").forEach(elm => {
  elm.addEventListener('click', function() {
    prod_infos.classList.add("hide");
    if(elm.dataset.elm==".conts"){
      document.querySelector(".conts").classList.remove("hide");
      document.querySelector(".conts2").classList.add("hide");
    } else {
      document.querySelector(".conts").classList.add("hide");
      document.querySelector(".conts2").classList.remove("hide");
    }
    document.querySelectorAll(".list li").forEach(function(otherItem) {
      if (otherItem !== elm) {
        otherItem.classList.remove('active');
      }
    });
    elm.classList.toggle('active');
  });
});



// Image
Fancybox.bind("[data-fancybox]", {});


// Cart
function register_item_to_cart() {

    $.post('./add_item_to_cart', {'item_id' : product_data['id'], 'serial_control' : product_data['serial_control']}, (data) => {
        if (data['status'] == 'error') {
            alert('المنتج مسجل بالفعل أو الكمية كلها مسجلة في السلة')
        } else {
            location.reload()
        }
        
    }, 'json')
}

function remove_cart_item(event) {
  if (selected_items.length > 0) {
    for (let cartItemId of selected_items) {
      $.post('./remove_cart_item', {'id' : cartItemId}, (itemId) => {
        $('#' + itemId).remove();
        $('.cart p').text(parseInt($('.cart p').text()) - 1)
      })
    }
  } else {
    cartItem = $(event.target).closest('.cart-item')
    $.post('./remove_cart_item', {'id' : $(cartItem).attr('id')}, (itemId) => {
        $('#' + itemId).remove();
        $('.cart p').text(parseInt($('.cart p').text()) - 1)
    })
  }
  
}

function accept(event) {
    let item = $(event.target).closest('.cart-item')
    process_type = 'addProductsInCart';
    accepted_cart_item = item
    delivery_popup.classList.remove("hide");
}

$('.rem-btn').click(remove_cart_item)

$('.acc-btn').click(accept)

$(".cart").click(function(){
  $(".cart-popup").fadeToggle();
})

document.querySelectorAll('.toggle-input-image').forEach(image => {
  image.addEventListener('click', function () {
    const inputField = this.parentElement.querySelector('.toggle-input-field');
    inputField.classList.toggle('show');
  });
});

let serial = document.getElementsByClassName('.serial');

document.querySelectorAll(".toggle-input-field").forEach((inputField) => {
  const validateInput = function () {
    const correctMark = this.parentElement.querySelector(
      ".verification-result.correct"
    );
    const incorrectMark = this.parentElement.querySelector(
      ".verification-result.incorrect"
    );
    const topPart = document.getElementById("top_part");
    const toggleInputField = this.closest(".toggle-input-field"); 
    const cartItem = this.closest(".cart-item"); 
    

    this.classList.remove("input-success", "input-error");

    if (this.value === $(cartItem).find('.item_number').attr('data-number')) {
      if (!verified_items.includes($(cartItem).attr('id'))) {
        verified_items.push($(cartItem).attr('id'));
      }
      
      correctMark.style.visibility = "visible";
      incorrectMark.style.visibility = "hidden";
      this.classList.add("input-success");

      if (topPart) {
        topPart.style.display = "none";
      }

      this.disabled = true;

      if (toggleInputField && toggleInputField.classList.contains("show")) {
        toggleInputField.classList.remove("show");
      }

      if (cartItem) {
        cartItem.style.backgroundColor = "#d4edda"; 
      }
    } else {
      correctMark.style.visibility = "hidden";
      incorrectMark.style.visibility = "visible";
      this.classList.add("input-error");
      this.select();

      if (cartItem) {
        cartItem.style.backgroundColor = "#f8d7da";
      }
    }
  };

  inputField.addEventListener("blur", validateInput);

  inputField.addEventListener("keydown", function (e) {
    if (e.key === "Enter") {
      e.preventDefault(); 
      validateInput.call(this);
    }
  });
});


function toggleItemSelection(checkbox) {
  const cartItem = checkbox.closest('.cart-item');
  if (checkbox.checked) {
    cartItem.classList.add('selected');
    selected_items.push($(cartItem).attr('id'))
  } else {

    cartItem.classList.remove('selected');
    selected_items = selected_items.filter(item => item !== $(cartItem).attr('id'));
  }

}

function toggleAllSelections(selectAllCheckbox) {
  const isChecked = selectAllCheckbox.checked;
  const checkboxes = document.querySelectorAll('.cart-item input[type="checkbox"]');
  checkboxes.forEach(checkbox => {
    checkbox.checked = isChecked;
    toggleItemSelection(checkbox);
  });
}



// remove custody

function remove_custody(user_custody_id) {
    if (confirm('هل تريد إرجاع المنتج إلي المخزون')) {
        $.post('./remove_custody', {'custody_id' : user_custody_id}, (data) => {
            location.reload()
        })
    }
}


function remove_request(request_id) {
    if (confirm('هل تريد إرجاع المنتج إلي المخزون')) {
        $.post('./product_delivery_return', {'id' : request_id}, (data) => {
            location.reload()
        })
    }
}



// delivery

function product_delivery_confirm(e) {
    let order_number = $('#delivery_order_number').val()
    if (!order_number) {
        let confirm_ordernumber = confirm('خانة رقم الطلب فارغة, هل تريد الاستمرار؟')
        if (confirm_ordernumber) {
          product_delivery(product_data['id'], order_number)
        }
    } else {
      product_delivery(product_data['id'], order_number)
    }

}

function product_delivery(item_id,  order_number) {
  if (process_type == 'addProductSearched') {
    single_product_delivery(item_id, order_number)
  } else {
    multi_product_delivery(order_number)
  }
}

function single_product_delivery(item_id, order_number) {

  $.post('./add_product_to_delivery', {
    'item_id' : item_id, 
    'order_number': order_number,
  }, (data) => {

    data = JSON.parse(data);
    if (data['status'] == 'ok') {
        document.querySelector(".popup_delivery .content").classList.add("hide");
        document.querySelector(".popup_delivery .done").classList.remove("hide");

        setTimeout(()=>{
            delivery_popup.classList.add("hide");
            delivery_popup_btns.classList.remove("hide");
            document.querySelector(".popup_delivery .done").classList.add("hide");
            location.reload()
        },3000)
    } else if (data['status'] == 'wrong') {
      alert(data['msg']);
    }
  });
}

async function multi_product_delivery(order_num) {
  if (selected_items.length > 0) {
      items_to_remove = [];
      for (let cartItemId of selected_items) {
          if (!verified_items.includes(cartItemId)) {
              alert('يرجي التحقق من الأرقام الخاصة بالمنتجات')
              return;
          }
          
      }

      for (let cartItemId of selected_items) {
        let cartitem = $('#' + cartItemId);
        let item_id = cartitem.attr('data-item-id')
        items_to_remove.push(cartItemId)

        await single_product_delivery(item_id, order_num)
      }

      $.post('./delete_selected_cart_items', {'selected': items_to_remove}, () => {})
  } else {
      if (!verified_items.includes($(accepted_cart_item).attr('id'))) {
          alert('يرجي التحقق من رقم المنتج')
          return;
      }

      let item_id = $(accepted_cart_item).attr('data-item-id')
      
      await single_product_delivery(item_id, order_num)
      $.post('./delete_selected_cart_items', {'selected': [$(accepted_cart_item).attr('id')]}, () => {})
  }
}


// automatic search using page parameter

let selected_serial = new URLSearchParams(window.location.search).get('serial');
let is_unified = new URLSearchParams(window.location.search).get('unified');

if (selected_serial) {
  if (is_unified && is_unified == '1') {
      itemCode.value = selected_serial;
      newNextBtn_handler();
  } else {
      serialNumber.value = selected_serial;
      nextBtn_handler();
  }
}
