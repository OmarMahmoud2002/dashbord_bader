// Popup
$(".popup .popup-content .title .close").click(()=>{
  $(".popup").fadeOut(200);
})

// Menu
function isAdminSidebarMobile() {
  return window.matchMedia("(max-width: 1020px)").matches;
}

function syncAdminSidebarToggleLabel() {
  const collapsed = $("body").hasClass("sidebar-collapsed");
  $(".toggle_menu").attr({
    role: "button",
    tabindex: "0",
    title: collapsed ? "فتح القائمة" : "إغلاق القائمة",
    "aria-label": collapsed ? "فتح القائمة" : "إغلاق القائمة"
  });
}

function restoreAdminSidebarState() {
  if (isAdminSidebarMobile()) {
    $("body").removeClass("sidebar-collapsed");
    syncAdminSidebarToggleLabel();
    return;
  }

  if (window.localStorage && localStorage.getItem("adminSidebarCollapsed") === "1") {
    $("body").addClass("sidebar-collapsed");
  }

  $(".sidebar").removeClass("active");
  syncAdminSidebarToggleLabel();
}

restoreAdminSidebarState();

$(".toggle_menu").click(function(e){
  e.stopPropagation();

  if (isAdminSidebarMobile()) {
    $("body").removeClass("sidebar-collapsed");
    $(".sidebar").toggleClass("active");
  } else {
    $(".sidebar").removeClass("active");
    $("body").toggleClass("sidebar-collapsed");

    if (window.localStorage) {
      localStorage.setItem("adminSidebarCollapsed", $("body").hasClass("sidebar-collapsed") ? "1" : "0");
    }
  }

  syncAdminSidebarToggleLabel();
})

$(".toggle_menu").keydown(function(e){
  if (e.key === "Enter" || e.key === " ") {
    e.preventDefault();
    $(this).trigger("click");
  }
})

$(window).on("resize", restoreAdminSidebarState);

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
