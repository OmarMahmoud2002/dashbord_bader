function build_input() {
    return `
    <div class = 'inpt'>
        <input type = 'text' name = 'packs[]'>
    </div>
    `
}

$('#packsnum').on('keyup', () => {
    let packs_number = parseInt($('#packsnum').val())
    console.log($('#packsnum').val());


    document.querySelector('.packs').innerHTML = '';
    for (let i = 0; i < packs_number; i++) {
        document.querySelector('.packs').innerHTML += build_input()
    }
})

function show_popup() {
    $('.popup-add-shipment').css('display', 'block');
}

function build_pack_view(pack) {
    return `
    <div class = 'pack_container'>
        <b>${pack}</b>
    </div>
    `
}

function show_view_shipments(packs) {
    $('.packs_view').html('')
    for (let pack of packs) {
        document.querySelector('.packs_view').innerHTML += build_pack_view(pack)
    }
    $('.popup-view-shipment').css('display', 'block');
}

$('.show-btn').click((event) => {
    packs = JSON.parse($(event.target).closest('.products-row').attr('data-packs'));
    show_view_shipments(packs);
})

function search_by_pack_number() {
    let inpt_element = document.querySelector('#SearchPack')
    if (window.AdminTablePagination && window.AdminTablePagination.updateFromInput(inpt_element)) {
        return;
    }

    let inpt_text = inpt_element.value.toUpperCase();
    
    var nodes = document.getElementsByClassName('products-row');

    for (i = 0; i < nodes.length; i++) {
        packs = nodes[i].getAttribute('data-packs')
        if (packs.includes(inpt_text) || nodes[i].innerText.toUpperCase().includes(inpt_text)) {
            nodes[i].style.display = "";
        } else {
            nodes[i].style.display = "none";
        }
    }
}

$('.del-btn').click(() => {
    let id = $(event.target).closest('.products-row').attr('id')
    $.post('./delete_shipment', {'id': id}, () => {
        window.location = window.location.href
    })
})
