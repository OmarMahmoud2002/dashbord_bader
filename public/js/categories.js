$(".categoryButton").click(()=>{
  $(".popup-add-category").fadeIn(200)
})
$(".brandButton").click(()=>{
  $(".popup-add-brand").fadeIn(200)
})

$('.deleteBrand').click((e) => {
    let brand = $(e.target).parent().attr('data-brand')
    $.get('./delete_brand?brand=' + brand, () => {
        window.location = window.location.href
    })
})

$('.del-btn').click((event) => {
    var itemID = $(event.target).parent().parent().attr('id');
    $.get('./delete_category?id=' + itemID, () => {
        window.location = window.location.href
    })
})