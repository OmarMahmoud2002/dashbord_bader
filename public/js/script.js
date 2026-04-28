// Popup
$(".popup .popup-content .title .close").click(()=>{
  $(".popup").fadeOut(200);
})

// Menu
$(".toggle_menu").click(function(){
  $(".sidebar").toggleClass("active")
})

// Notifications
$('.del-notification').click((event) => {
    var noti_id = $(event.target).closest('.noti').attr('id');
    $.post('/notifications/delete-notification', {noti_id: noti_id}, (data) => {
        if (data == 'deleted') {
            $(event.target).closest('.noti').remove();
            let new_count = parseInt($('.notificationcount').text()) - 1;
            if (new_count == 0) {
                $('.notificationcount').remove()
            } else {
                $('.notificationcount').text(new_count);
            }
        }
    })
})
//
if($(window).width()<=997){
  $(".products-area-wrapper").removeClass("tableView").addClass("gridView");
}


// Notification sound
// window.onload = function() {
//   if($("nav .notification .top_noti .n").hasClass("new")){
//     var audioElement = document.createElement('audio');
//     audioElement.src = '../assets/notif-sound.mp3';
//     audioElement.addEventListener('canplaythrough', function() {
//         console.log('Audio is working and can be played through!');
//         audioElement.play();
//     }, { once: true });

//     audioElement.addEventListener('error', function() {
//         // console.error('Error loading audio. Please check the file and try again.');
//     }, { once: true });

//     audioElement.load();
//   }
// };

/*-------- Navbar ------*/
// Notification
$("nav  .notification .top_noti").click(function (e) {
  e.stopPropagation();
  $("nav  .notification .down_notif").fadeToggle(300);
  $("nav  .profile .down_profile").hide();
  $("nav  .notification .top_noti").toggleClass("active");
});
// Profile
$("nav  .profile .top_profile").click(function (e) {
  e.stopPropagation();
  $("nav  .profile .down_profile").fadeToggle(300);
  $("nav  .notification .top_noti").removeClass("active");
  $("nav  .notification .down_notif").hide();
});
$(
  "nav  .notification .down_notif,nav  .profile .down_profile"
).click(function (e) {
  e.stopPropagation();
});

$("body").click(function () {
  $(
    "nav  .profile .down_profile, nav  .notification .down_notif"
  ).hide();
  $("nav  .notification .top_noti").removeClass("active");
});


// Table responsive
function checkWidth() {
  const elm = document.querySelector('.products-area-wrapper');
  if (window.innerWidth <= 650) {
    elm.classList.add('gridView');
    elm.classList.remove('tableView');
  } else {
    elm.classList.add('tableView');
    elm.classList.remove('gridView');
  }
}

window.addEventListener('resize', checkWidth);