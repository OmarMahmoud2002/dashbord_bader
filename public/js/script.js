// Popup
$(".popup .popup-content .title .close").click(()=>{
  $(".popup").fadeOut(200);
})

window.adminUiConfirm = function(options) {
  const settings = Object.assign({
    title: "تأكيد الإجراء",
    message: "هل تريد المتابعة؟",
    confirmText: "تأكيد",
    cancelText: "إلغاء",
    icon: "bi-question-circle"
  }, options || {});
  const escapeHtml = (value) => String(value).replace(/[&<>"']/g, (char) => ({
    "&": "&amp;",
    "<": "&lt;",
    ">": "&gt;",
    '"': "&quot;",
    "'": "&#039;"
  }[char]));

  $(".admin-confirm-popup").remove();

  return new Promise((resolve) => {
    const popup = $(`
      <div class="popup admin-confirm-popup" role="dialog" aria-modal="true">
        <div class="popup-content admin-confirm-content">
          <div class="title">
            <h3>${escapeHtml(settings.title)}</h3>
            <div class="close" role="button" tabindex="0" aria-label="إغلاق">
              <i class="bi bi-x-lg"></i>
            </div>
          </div>
          <div class="admin-confirm-body">
            <span class="admin-confirm-icon"><i class="bi ${escapeHtml(settings.icon)}"></i></span>
            <p>${escapeHtml(settings.message)}</p>
          </div>
          <div class="admin-confirm-actions">
            <button type="button" class="admin-confirm-yes">${escapeHtml(settings.confirmText)}</button>
            <button type="button" class="admin-confirm-no">${escapeHtml(settings.cancelText)}</button>
          </div>
        </div>
      </div>
    `);

    const close = () => {
      popup.fadeOut(160, function() {
        popup.remove();
      });
    };

    popup.find(".admin-confirm-yes").on("click", function() {
      close();
      resolve(true);
    });

    popup.find(".admin-confirm-no, .close").on("click keydown", function(e) {
      if (e.type === "keydown" && e.key !== "Enter" && e.key !== " ") {
        return;
      }

      close();
      resolve(false);
    });

    popup.on("click", function(e) {
      if (e.target === popup[0]) {
        close();
        resolve(false);
      }
    });

    $("body").append(popup);
    popup.hide().fadeIn(160);
    popup.find(".admin-confirm-yes").trigger("focus");
  });
};

if (window.toastr) {
  toastr.options = Object.assign({}, toastr.options, {
    closeButton: true,
    progressBar: true,
    newestOnTop: true,
    preventDuplicates: true,
    positionClass: "toast-top-left",
    rtl: true,
    timeOut: 4500,
    extendedTimeOut: 1600
  });
}

function initAdminSidebarTooltips() {
  const sidebarLinks = document.querySelectorAll("body.admin-ui-shell .sidebar-list-item a");
  if (!sidebarLinks.length) {
    return;
  }

  let tooltip = document.querySelector(".admin-sidebar-tooltip");
  if (!tooltip) {
    tooltip = document.createElement("div");
    tooltip.className = "admin-sidebar-tooltip";
    document.body.appendChild(tooltip);
  }

  const hideTooltip = () => {
    tooltip.style.display = "none";
  };

  const showTooltip = (link) => {
    if (!document.body.classList.contains("sidebar-collapsed")) {
      hideTooltip();
      return;
    }

    const label = link.dataset.sidebarLabel || link.textContent.replace(/\s+/g, " ").trim();
    if (!label) {
      return;
    }

    tooltip.textContent = label;
    tooltip.style.display = "block";

    const rect = link.getBoundingClientRect();
    const tooltipRect = tooltip.getBoundingClientRect();
    const top = rect.top + (rect.height / 2) - (tooltipRect.height / 2);
    const left = rect.left - tooltipRect.width - 12;

    tooltip.style.top = `${Math.max(10, top)}px`;
    tooltip.style.left = `${Math.max(10, left)}px`;
  };

  sidebarLinks.forEach((link) => {
    const label = link.textContent.replace(/\s+/g, " ").trim();
    if (label) {
      link.dataset.sidebarLabel = label;
      link.setAttribute("aria-label", label);
    }

    link.addEventListener("mouseenter", () => showTooltip(link));
    link.addEventListener("focus", () => showTooltip(link));
    link.addEventListener("mouseleave", hideTooltip);
    link.addEventListener("blur", hideTooltip);
  });

  document.addEventListener("scroll", hideTooltip, true);
  window.addEventListener("resize", hideTooltip);
}

function scrollAdminSidebarToTarget(target, smooth) {
  const sidebar = document.querySelector("body.admin-ui-shell .sidebar");
  if (!sidebar || !target) {
    return;
  }

  const sidebarRect = sidebar.getBoundingClientRect();
  const targetRect = target.getBoundingClientRect();
  const targetTop = sidebar.scrollTop + targetRect.top - sidebarRect.top - (sidebar.clientHeight / 2) + (targetRect.height / 2);
  const maxTop = Math.max(0, sidebar.scrollHeight - sidebar.clientHeight);

  sidebar.scrollTo({
    top: Math.min(Math.max(0, targetTop), maxTop),
    behavior: smooth ? "smooth" : "auto"
  });
}

function getActiveAdminSidebarTarget() {
  const activeSubmenuLink = document.querySelector("body.admin-ui-shell .sidebar-submenu a.active");
  if (activeSubmenuLink && activeSubmenuLink.offsetParent !== null) {
    return activeSubmenuLink;
  }

  return document.querySelector("body.admin-ui-shell .sidebar-list-item.active");
}

function initAdminSidebarAutoScroll() {
  const sidebar = document.querySelector("body.admin-ui-shell .sidebar");
  if (!sidebar) {
    return;
  }

  setTimeout(() => scrollAdminSidebarToTarget(getActiveAdminSidebarTarget(), false), 80);

  sidebar.querySelectorAll(".sidebar-list-item a").forEach((link) => {
    link.addEventListener("click", () => {
      const target = link.classList.contains("active") ? link : link.closest(".sidebar-list-item");
      setTimeout(() => scrollAdminSidebarToTarget(target, true), 60);
    });
  });
}

function initAdminSidebarSubmenus() {
  document.querySelectorAll("body.admin-ui-shell .sidebar-list-item.has-submenu").forEach((item) => {
    const toggle = item.querySelector(":scope > .sidebar-submenu-toggle");
    if (!toggle) {
      return;
    }

    const syncExpandedState = () => {
      toggle.setAttribute("aria-expanded", item.classList.contains("submenu-open") || item.classList.contains("active") ? "true" : "false");
    };

    toggle.setAttribute("aria-haspopup", "true");
    syncExpandedState();

    toggle.addEventListener("click", (event) => {
      if (document.body.classList.contains("sidebar-collapsed")) {
        return;
      }

      event.preventDefault();
      item.classList.toggle("submenu-open");
      syncExpandedState();
      scrollAdminSidebarToTarget(item, true);
    });
  });
}

function initAdminTableColumnHover() {
  document.querySelectorAll(".admin-ui-page .tableView").forEach((table) => {
    if (table.dataset.columnHoverReady === "1") {
      return;
    }

    const header = table.querySelector(".products-header");
    if (!header) {
      return;
    }

    table.dataset.columnHoverReady = "1";

    const clearColumnHover = () => {
      table.querySelectorAll(".product-cell.is-column-hovered").forEach((cell) => {
        cell.classList.remove("is-column-hovered");
      });
    };

    const setColumnHover = (index) => {
      clearColumnHover();

      Array.from(table.children).forEach((row) => {
        if (!row.classList.contains("products-header") && !row.classList.contains("products-row")) {
          return;
        }

        const cells = Array.from(row.children).filter((child) => child.classList.contains("product-cell"));
        if (cells[index]) {
          cells[index].classList.add("is-column-hovered");
        }
      });
    };

    header.querySelectorAll(".product-cell").forEach((cell, index) => {
      cell.setAttribute("tabindex", cell.getAttribute("tabindex") || "0");

      cell.addEventListener("pointerenter", () => {
        setColumnHover(index);
      });

      cell.addEventListener("focus", () => {
        setColumnHover(index);
      });
    });

    header.addEventListener("pointerleave", clearColumnHover);

    header.addEventListener("focusout", (event) => {
      if (!header.contains(event.relatedTarget)) {
        clearColumnHover();
      }
    });
  });
}

if (document.readyState === "loading") {
  document.addEventListener("DOMContentLoaded", function () {
    initAdminSidebarTooltips();
    initAdminSidebarSubmenus();
    initAdminSidebarAutoScroll();
    initAdminTableColumnHover();
  });
} else {
  initAdminSidebarTooltips();
  initAdminSidebarSubmenus();
  initAdminSidebarAutoScroll();
  initAdminTableColumnHover();
}

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
  if (!elm) {
    return;
  }

  if (window.innerWidth <= 650) {
    elm.classList.add('gridView');
    elm.classList.remove('tableView');
  } else {
    elm.classList.add('tableView');
    elm.classList.remove('gridView');
  }
}

window.addEventListener('resize', checkWidth);
