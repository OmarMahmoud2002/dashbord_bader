queries = new URLSearchParams(location.href.split('?')[1])
if (queries.get('dates') == undefined) {
    var dates = [new Date(), new Date()];
} else {
    var dates = queries.get('dates').split(' - ')
}


$('input[name="dates"]').daterangepicker({
    "autoApply": true,
    "alwaysShowCalendars": true,
    "startDate": new Date(dates[0]),
    "endDate": new Date(dates[1]),
  }, function(start, end, label) {
  console.log('New date range selected: ' + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD') + ' (predefined range: ' + label + ')');
  }
);

function product_delivery_return(id) {
    let main_url = location.origin
    $.post(main_url + '/admin/products/product_delivery_return', {'id': id}, (data) => {
        console.log(data)
        location.reload()
    })
}