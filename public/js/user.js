let checkBtn = document.getElementById("checkBtn");
let nextBtn = document.getElementById("nextBtn");
let select_type = document.querySelector(".select_type");
let serialNumber = document.getElementById("serialNumberInput");
let prod_infos = document.querySelector(".prod_info");
let radios = document.getElementsByName('productType');
let radios_enabled = false

function set_data(data) {
    document.querySelector('#ProductName').innerText = data['name'];
    document.querySelector('#ProductSerial').innerText = data['serial'];
    $('#hiddenSerial').val(data['serial']);
}

function request_prod_info(serial) {
    $.get('./get_product_by_serial?serial=' + serial, function(data) {
        data = JSON.parse(data);
        set_data(data);
    });
}

function request_prod_info_without_code(serial) {
    $.get('./get_product_by_serial?serial=' + serial, function(data) {
        data = JSON.parse(data);
        set_data(data);
    });
}

nextBtn.onclick = () => {
    // check for duplicates part of a serial
    let serial = serialNumber.value;
    if (serial.length < 4) {
        document.querySelector('#serialNumberError').innerText = 'السيريال غير موجود';
        document.querySelector('#serialNumberError').classList.remove('hide');
    } else if (4 <= serial.length <= 6) {
        enable_radios();
    } else if (serial.length > 6) {
        document.querySelector('#serialNumberError').classList.add('hide');
        checkBtn.classList.remove("hide");
        nextBtn.classList.add("hide");
        serialNumber.disabled = true;
        request_prod_info_without_code(serial);
    }
}

function build_radios(serial) {
    select_type.innerHTML = '';
    $.get('./get_serial_categories?serial=' + serial, function(data) {
        if (data === '[]') {
            document.querySelector('#serialNumberError').innerText = 'السيريال غير موجود';
            document.querySelector('#serialNumberError').classList.remove('hide');
            serialNumber.disabled = false;
            return;
        }

        document.querySelector('#serialNumberError').classList.add('hide');
        checkBtn.classList.remove("hide");
        nextBtn.classList.add("hide");
        serialNumber.disabled = true;

        data = JSON.parse(data);
        for (category of data) {
            radio = `
<div class="bt">
    <input type="radio" name="productType" value="${category['code']}">
    <button type="button">${category['name']}</button>
</div>
            `
            select_type.innerHTML += radio;
        }
        radios = document.getElementsByName('productType');
        radios.forEach(radioInput => {
            radioInput.addEventListener('click', function() {
                request_prod_info(serialNumber.value, radioInput.value);
                prod_infos.classList.remove("hide");
                disableRadios();
            });
        });
        select_type.classList.remove("hide");
    })
}

function enable_radios() {
    radios_enabled = true;
    build_radios(serialNumber.value);
}

function checkRadio() {
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
  const radios = document.querySelectorAll('input[type="radio"][name="productType"]');
  radios.forEach(function(radio) {
      radio.disabled = true;
  });
}
checkBtn.onclick = () => {
    document.querySelector(".prod_info").classList.remove("hide");
    checkBtn.style.display="none";
    document.querySelector(".check").style.display="flex";
}

