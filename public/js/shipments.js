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

function show_deliver_popup() {
    const now = new Date();
    const today = `${now.getFullYear()}-${String(now.getMonth() + 1).padStart(2, '0')}-${String(now.getDate()).padStart(2, '0')}`;
    $('.popup-deliver-shipment input[name="delivery_date"]').val(today);
    $('.popup-deliver-shipment').css('display', 'block');
    $('.popup-deliver-shipment input[name="shipment_identifier"]').trigger('focus');
}

function build_pack_view(pack) {
    return `
    <div class = 'pack_container'>
        <b>${pack}</b>
    </div>
    `
}

function show_view_shipments(packs) {
    if (!Array.isArray(packs)) {
        packs = [];
    }

    $('.packs_view').html('')
    for (let pack of packs) {
        document.querySelector('.packs_view').innerHTML += build_pack_view(pack)
    }
    $('.popup-view-shipment').css('display', 'block');
}

$('.show-btn').click((event) => {
    let packs = [];
    try {
        packs = JSON.parse($(event.target).closest('.products-row').attr('data-packs') || '[]');
    } catch (e) {
        packs = [];
    }
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
        let packs = nodes[i].getAttribute('data-packs') || ''
        if (packs.includes(inpt_text) || nodes[i].innerText.toUpperCase().includes(inpt_text)) {
            nodes[i].style.display = "";
        } else {
            nodes[i].style.display = "none";
        }
    }
}

function normalize_sort_value(value, type) {
    value = (value || '').toString().trim();

    if (type === 'number') {
        const numberValue = Number(value);
        return value === '' || Number.isNaN(numberValue) ? null : numberValue;
    }

    if (type === 'date') {
        const dateValue = new Date(value.replace(' ', 'T')).getTime();
        return value === '' || Number.isNaN(dateValue) ? null : dateValue;
    }

    return value.toLowerCase();
}

function sort_shipments_by(header) {
    const table = document.querySelector('#shipmentsTable');
    const key = header.getAttribute('data-sort-key');
    const type = header.getAttribute('data-sort-type') || 'text';
    const order = header.getAttribute('data-sort-order') === 'asc' ? 'desc' : 'asc';
    const rows = Array.from(table.querySelectorAll('.products-row'));
    const direction = order === 'asc' ? 1 : -1;

    rows.sort((a, b) => {
        const aCell = a.querySelector(`.product-cell.${key}`);
        const bCell = b.querySelector(`.product-cell.${key}`);
        const aValue = normalize_sort_value(aCell ? aCell.getAttribute('data-sort-value') : '', type);
        const bValue = normalize_sort_value(bCell ? bCell.getAttribute('data-sort-value') : '', type);

        if (aValue === null && bValue === null) {
            return 0;
        }

        if (aValue === null) {
            return 1;
        }

        if (bValue === null) {
            return -1;
        }

        if (aValue < bValue) {
            return -1 * direction;
        }

        if (aValue > bValue) {
            return 1 * direction;
        }

        return 0;
    });

    rows.forEach((row) => table.appendChild(row));

    document.querySelectorAll('#shipmentsTable .sortable-cell').forEach((cell) => {
        cell.classList.remove('is-sorted-asc', 'is-sorted-desc');
        cell.removeAttribute('data-sort-order');
        const icon = cell.querySelector('i');
        if (icon) {
            icon.className = 'bi bi-arrow-down-up';
        }
    });

    header.setAttribute('data-sort-order', order);
    header.classList.add(order === 'asc' ? 'is-sorted-asc' : 'is-sorted-desc');

    const icon = header.querySelector('i');
    if (icon) {
        icon.className = order === 'asc' ? 'bi bi-caret-up-fill' : 'bi bi-caret-down-fill';
    }

    if (window.AdminTablePagination) {
        window.AdminTablePagination.refresh(table);
    }
}

$('#shipmentsTable .sortable-cell').click((event) => {
    sort_shipments_by(event.currentTarget);
});

$('.del-btn').click((event) => {
    let id = $(event.target).closest('.products-row').attr('id')
    $.post('./delete_shipment', {'id': id}, () => {
        window.location = window.location.href
    })
})
