<?php
$CI =& get_instance();
$segment_2 = 0;
$segment_2 = $CI->uri->segment('2');
$segment_3 = 0;
$segment_3 = $CI->uri->segment('3');
$forms_query = $CI->input->get('form', TRUE);
$active_form = ($forms_query === 'settlement') ? 'settlement' : 'replacement';
?>
<script>
  var adminUiPath = window.location.pathname;
  document.body.classList.add('admin-ui-shell');

  document.addEventListener('DOMContentLoaded', function () {
    var content = document.querySelector('.app-content');
    if (!content) return;

    content.classList.add('admin-ui-page');

    var path = adminUiPath;
    if (path.indexOf('/products/low') !== -1) content.classList.add('admin-ui-low');
    if (path.indexOf('/products/search') !== -1) content.classList.add('admin-ui-product-search');
    if (path.indexOf('/shipments') !== -1) content.classList.add('admin-ui-shipments');
    if (path.indexOf('/shelves') !== -1) content.classList.add('admin-ui-shelves');
    if (path.indexOf('/lock-track') !== -1) content.classList.add('admin-ui-lock-track');
    if (path.indexOf('/employees') !== -1 || path.indexOf('/employee/') !== -1) content.classList.add('admin-ui-people');
    if (path.indexOf('/employees_timetable') !== -1) content.classList.add('admin-ui-timetable');
    if (path.indexOf('/employees_sales') !== -1) content.classList.add('admin-ui-employee-sales');
    if (path.indexOf('/admins') !== -1) content.classList.add('admin-ui-admins');
    if (path.indexOf('/products/add') !== -1 || path.indexOf('/products/edit') !== -1 || path.indexOf('/upload') !== -1) {
      content.classList.add('admin-ui-product-form');
    }

    document.querySelectorAll('.admin-ui-page .products-area-wrapper .actions button, .admin-ui-page .products-area-wrapper .actions a').forEach(function (action) {
      var label = action.textContent.replace(/\s+/g, ' ').trim();

      if (!label) {
        if (action.classList.contains('del-btn') || action.closest('.del-btn')) label = 'حذف';
        else if (action.classList.contains('edit-btn') || action.classList.contains('valid-btn')) label = 'تعديل';
        else if (action.classList.contains('show-btn') || action.closest('.show-btn')) label = 'عرض';
        else if (action.classList.contains('add-tswya')) label = 'تسوية';
        else label = 'إجراء';
      }

      action.setAttribute('title', label);
      action.setAttribute('aria-label', label);
    });
  });
</script>
         <!-- HTML -->
  <nav>
    <div class="profile">
      <div class="top_profile">
        <img src="https://myduties.net/storage/users/avatars/6442ccd236b7a.jpg" />
        
      </div>
      <ul class="down_profile list-unstyled">
        <li>
          <a href="#">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><!--!Font Awesome Free 6.5.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path d="M224 256A128 128 0 1 0 224 0a128 128 0 1 0 0 256zm-45.7 48C79.8 304 0 383.8 0 482.3C0 498.7 13.3 512 29.7 512H418.3c16.4 0 29.7-13.3 29.7-29.7C448 383.8 368.2 304 269.7 304H178.3z"/></svg>
            ملف التعريف
          </a>
        </li>
        <li>
          <a href="<?php echo base_url().MOD_VALUE; ?>user/logout">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--!Font Awesome Free 6.5.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path d="M377.9 105.9L500.7 228.7c7.2 7.2 11.3 17.1 11.3 27.3s-4.1 20.1-11.3 27.3L377.9 406.1c-6.4 6.4-15 9.9-24 9.9c-18.7 0-33.9-15.2-33.9-33.9l0-62.1-128 0c-17.7 0-32-14.3-32-32l0-64c0-17.7 14.3-32 32-32l128 0 0-62.1c0-18.7 15.2-33.9 33.9-33.9c9 0 17.6 3.6 24 9.9zM160 96L96 96c-17.7 0-32 14.3-32 32l0 256c0 17.7 14.3 32 32 32l64 0c17.7 0 32 14.3 32 32s-14.3 32-32 32l-64 0c-53 0-96-43-96-96L0 128C0 75 43 32 96 32l64 0c17.7 0 32 14.3 32 32s-14.3 32-32 32z"/></svg>
            تسجيل الخروج
          </a>
        </li>
      </ul>
    </div>
    <div class="notification">
      <div class="top_noti">
        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 448 512"><!--!Font Awesome Free 6.5.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path d="M224 0c-17.7 0-32 14.3-32 32V51.2C119 66 64 130.6 64 208v25.4c0 45.4-15.5 89.5-43.8 124.9L5.3 377c-5.8 7.2-6.9 17.1-2.9 25.4S14.8 416 24 416H424c9.2 0 17.6-5.3 21.6-13.6s2.9-18.2-2.9-25.4l-14.9-18.6C399.5 322.9 384 278.8 384 233.4V208c0-77.4-55-142-128-156.8V32c0-17.7-14.3-32-32-32zm0 96c61.9 0 112 50.1 112 112v25.4c0 47.9 13.9 94.6 39.7 134.6H72.3C98.1 328 112 281.3 112 233.4V208c0-61.9 50.1-112 112-112zm64 352H224 160c0 17 6.7 33.3 18.7 45.3s28.3 18.7 45.3 18.7s33.3-6.7 45.3-18.7s18.7-28.3 18.7-45.3z"></path></svg>
        <?php if ($notifications_count > 0) { ?>
            <p class="new n notificationcount"><?=$notifications_count?></p>
        <?php } ?>
      </div>
      <div class="down_notif">
        <ul class="list-unstyled">
            <?php foreach($notifications as $notification) { ?>
                <li class='<?php  echo $notification->status?> noti' id = '<?=$notification->id?>'>
                    <a href = '<?=$notification->link?>'>
                    <h4>
                        <?=$notification->description?>
                    </h4>
                    <p class="n"><?=date_format(date_create($notification->date_created), 'Y/m/d h:i:s A')?></p>
                    </a>
                    <div class="options">
                    <button class = 'del-notification'><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><!--!Font Awesome Free 6.5.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path d="M135.2 17.7L128 32H32C14.3 32 0 46.3 0 64S14.3 96 32 96H416c17.7 0 32-14.3 32-32s-14.3-32-32-32H320l-7.2-14.3C307.4 6.8 296.3 0 284.2 0H163.8c-12.1 0-23.2 6.8-28.6 17.7zM416 128H32L53.2 467c1.6 25.3 22.6 45 47.9 45H346.9c25.3 0 46.3-19.7 47.9-45L416 128z"/></svg></button>
                    <button><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--!Font Awesome Free 6.5.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path d="M255.4 48.2c.2-.1 .4-.2 .6-.2s.4 .1 .6 .2L460.6 194c2.1 1.5 3.4 3.9 3.4 6.5v13.6L291.5 355.7c-20.7 17-50.4 17-71.1 0L48 214.1V200.5c0-2.6 1.2-5 3.4-6.5L255.4 48.2zM48 276.2L190 392.8c38.4 31.5 93.7 31.5 132 0L464 276.2V456c0 4.4-3.6 8-8 8H56c-4.4 0-8-3.6-8-8V276.2zM256 0c-10.2 0-20.2 3.2-28.5 9.1L23.5 154.9C8.7 165.4 0 182.4 0 200.5V456c0 30.9 25.1 56 56 56H456c30.9 0 56-25.1 56-56V200.5c0-18.1-8.7-35.1-23.4-45.6L284.5 9.1C276.2 3.2 266.2 0 256 0z"/></svg></button>
                    </div>
                </li>
            <?php } ?>
        </ul>
      </div>
    </div>
    <a href="<?php echo base_url().MOD_VALUE.'admin/index'; ?>" class="logo">
      <img src="https://upload.wikimedia.org/wikipedia/commons/8/85/Logo-Test.png" />
    </a>
    <div class="toggle_menu">
      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><!--!Font Awesome Free 6.5.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path d="M0 96C0 78.3 14.3 64 32 64H416c17.7 0 32 14.3 32 32s-14.3 32-32 32H32C14.3 128 0 113.7 0 96zM0 256c0-17.7 14.3-32 32-32H416c17.7 0 32 14.3 32 32s-14.3 32-32 32H32c-17.7 0-32-14.3-32-32zM448 416c0 17.7-14.3 32-32 32H32c-17.7 0-32-14.3-32-32s14.3-32 32-32H416c17.7 0 32 14.3 32 32z"/></svg>
    </div>
  </nav>    
    <div class="app-container">
    <div class="sidebar">
      <ul class="sidebar-list">
        <li class="sidebar-list-item <?php if($segment_2 == 'index') {echo 'active';} ?>">
          <a href="<?php echo base_url().MOD_VALUE.'admin/index'; ?>">
            <i class="bi bi-house-door"></i>
            <span>الصفحة الرئيسية</span>
          </a>
        </li>
        <li class="sidebar-list-item <?php if($segment_2 == 'products' && $segment_3 == 'search') {echo 'active';} ?>">
          <a href="<?php echo base_url().MOD_VALUE.'admin/products/search'; ?>">
            <i class="bi bi-search"></i>
            <span>البحث عن منتج</span>
          </a>
        </li>
        <li class="sidebar-list-item <?php if($segment_2 == 'dashboard') {echo 'active';} ?>">
          <a href="<?php echo base_url().MOD_VALUE.'admin/dashboard'; ?>">
            <i class="bi bi-box-seam"></i>
            <span>المنتجات</span>
          </a>
        </li>
        <li class="sidebar-list-item <?php if($segment_3 == 'low' && $segment_2 == 'products') {echo 'active';} ?>">
          <a href="<?php echo base_url().MOD_VALUE.'admin/products/low'; ?>">
            <i class="bi bi-exclamation-triangle"></i>
            <span>المنتجات منخفضة الكمية</span>
          </a>
        </li>
        <li class="sidebar-list-item <?php if($segment_3 == 'operations' && $segment_2 == 'products') {echo 'active';} ?>">
          <a href="<?php echo base_url().MOD_VALUE.'admin/products/operations'; ?>">
            <i class="bi bi-clock-history"></i>
            <span>العمليات</span>
          </a>
        </li>
        <li class="sidebar-list-item <?php if($segment_2 == 'shelves') {echo 'active';} ?>">
          <a href="<?php echo base_url().MOD_VALUE.'admin/shelves'; ?>">
            <i class="bi bi-archive"></i>
            <span>الرف</span>
          </a>
        </li>
        <li class="sidebar-list-item <?php if($segment_2 == 'employees') {echo 'active';} ?>">
          <a href="<?php echo base_url().MOD_VALUE.'admin/employees'; ?>">
            <i class="bi bi-people"></i>
            <span>الموظفين</span>
          </a>
        </li>
        <li class="sidebar-list-item <?php if($segment_2 == 'employees_sales_page') {echo 'active';} ?>">
          <a href="<?php echo base_url().MOD_VALUE.'admin/employees_sales_page'; ?>">
            <i class="bi bi-cash-coin"></i>
            <span>مبيعات الموظفين</span>
          </a>
        </li>
        <li class="sidebar-list-item <?php if($segment_2 == 'employees_timetable') {echo 'active';} ?>">
          <a href="<?php echo base_url().MOD_VALUE.'admin/employees_timetable'; ?>">
            <i class="bi bi-calendar-week"></i>
            <span>جداول الموظفين</span>
          </a>
        </li>
        <li class="sidebar-list-item <?php if($segment_2 == 'admins') {echo 'active';} ?>">
          <a href="<?php echo base_url().MOD_VALUE.'admin/admins'; ?>">
            <i class="bi bi-shield-lock"></i>
            <span>المدرين</span>
          </a>
        </li>
        <li class="sidebar-list-item <?php if($segment_2 == 'requests') {echo 'active';} ?>">
          <a href="<?php echo base_url().MOD_VALUE.'admin/requests'; ?>">
            <i class="bi bi-clipboard-check"></i>
            <span>الطلبات</span>
          </a>
        </li>
        <li class="sidebar-list-item <?php if($segment_2 == 'sales') {echo 'active';} ?>">
          <a href="<?php echo base_url().MOD_VALUE.'admin/sales'; ?>">
            <i class="bi bi-receipt"></i>
            <span>المبيعات</span>
          </a>
        </li>
        <li class="sidebar-list-item <?php if($segment_2 == 'settlements') {echo 'active';} ?>">
          <a href="<?php echo base_url().MOD_VALUE.'admin/settlements'; ?>">
            <i class="bi bi-arrow-left-right"></i>
            <span>الفروقات</span>
          </a>
        </li>
        <li class="sidebar-list-item <?php if($segment_2 == 'shipments') {echo 'active';} ?>">
          <a href="<?php echo base_url().MOD_VALUE.'admin/shipments'; ?>">
            <i class="bi bi-truck"></i>
            <span>الشحنات</span>
          </a>
        </li>
        <li class="sidebar-list-item <?php if($segment_2 == 'lock-admin') {echo 'active';} ?>">
          <a href="<?php echo base_url().MOD_VALUE.'admin/lock-admin'; ?>">
            <i class="bi bi-lock"></i>
            <span>التقفيلة</span>
          </a>
        </li>
       <li class="sidebar-list-item <?php if($segment_2 == 'lock-track') {echo 'active';} ?>">
          <a href="<?php echo base_url().MOD_VALUE.'admin/lock-track'; ?>">
            <i class="bi bi-patch-check"></i>
            <span>تأكيد التقفيلة</span>
          </a>
        </li>
        <li class="sidebar-list-item">
          <a href="<?php echo base_url().MOD_VALUE.'admin/stocktaking'; ?>">
            <i class="bi bi-clipboard-data"></i>
            <span>جرد المخزون</span>
          </a>
        </li>
        <li class="sidebar-list-item has-submenu <?php if($segment_2 == 'forms' || $segment_2 == 'forms-settings') {echo 'active submenu-open';} ?>">
          <a href="<?php echo base_url().MOD_VALUE.'admin/forms?form=settlement'; ?>" class="sidebar-submenu-toggle">
            <i class="bi bi-ui-checks"></i>
            <span>نماذج</span>
          </a>
          <ul class="sidebar-submenu">
            <li>
              <a href="<?php echo base_url().MOD_VALUE.'admin/forms?form=settlement'; ?>" class="<?php if($segment_2 == 'forms' && $active_form == 'settlement') {echo 'active';} ?>">
                <i class="bi bi-file-earmark-text"></i>
                <span>نموذج تسوية الغرامة</span>
              </a>
            </li>
            <li>
              <a href="<?php echo base_url().MOD_VALUE.'admin/forms?form=replacement'; ?>" class="<?php if($segment_2 == 'forms' && $active_form == 'replacement') {echo 'active';} ?>">
                <i class="bi bi-phone"></i>
                <span>استبدال الجهاز</span>
              </a>
            </li>
            <li>
              <a href="<?php echo base_url().MOD_VALUE.'admin/forms-settings'; ?>" class="<?php if($segment_2 == 'forms-settings') {echo 'active';} ?>">
                <i class="bi bi-sliders"></i>
                <span>إعدادات النماذج</span>
              </a>
            </li>
          </ul>
        </li>
        <li class="sidebar-list-item <?php if($segment_2 == 'settings') {echo 'active';} ?>">
          <a href="<?php echo base_url().MOD_VALUE.'admin/settings'; ?>">
            <i class="bi bi-gear"></i>
            <span>الاعدادات</span>
          </a>
        </li>
      </ul>
    </div>
   
