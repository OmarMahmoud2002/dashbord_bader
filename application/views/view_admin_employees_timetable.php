<title>جدول الدوام الأسبوعي</title>
<?php $this->load->view('view_header'); ?>
<?php $this->load->view('view_admin_sidebar');?>

<link href="<?=base_url('public/css/employees_timetable.css')?>?a=<?php $date=date_create();echo date_timestamp_get($date);?>" rel="stylesheet">

<style>
  body {
    height: fit-content;
    overflow: auto;
    
  }

  .app-container {
    height: fit-content;
    overflow: auto;
    max-height: fit-content;
  }
</style>

<!-- Add New Feature -->
<div class="app-content admin-ui-page admin-ui-timetable employees-timetable-page" style="height: fit-content;">
<!-- End -->
  <h2 class="text-center mb-4">جدول الدوام الأسبوعي</h2>

  <!-- زر إضافة موظف واختيار الشهر -->
  <!-- Add New Feature -->
  <div class="d-flex gap-3 mb-4 timetable-toolbar">
  <!-- End -->
    <div class="d-flex align-items-center gap-2">
      <select id="yearSelect" class="form-select" style="width: 100px;">
        <option value="2024">2024</option>
        <option value="2025">2025</option>
        <option value="2026">2026</option>
      </select>
      <select id="monthSelect" class="form-select" style="width: 120px;">
        <option value="0">يناير</option>
        <option value="1">فبراير</option>
        <option value="2">مارس</option>
        <option value="3">أبريل</option>
        <option value="4">مايو</option>
        <option value="5">يونيو</option>
        <option value="6">يوليو</option>
        <option value="7">أغسطس</option>
        <option value="8">سبتمبر</option>
        <option value="9">أكتوبر</option>
        <option value="10">نوفمبر</option>
        <option value="11">ديسمبر</option>
      </select>
    </div>
  </div>

  <!-- اختيار الأسبوع -->
  <!-- Add New Feature -->
  <div class="mb-3 timetable-week-filter">
  <!-- End -->
    <div class="btn-group" role="group">
      <button class="btn btn-outline-primary week-btn" data-week="0">الأسبوع الأول</button>
      <button class="btn btn-outline-primary week-btn" data-week="1">الأسبوع الثاني</button>
      <button class="btn btn-outline-primary week-btn" data-week="2">الأسبوع الثالث</button>
      <button class="btn btn-outline-primary week-btn" data-week="3">الأسبوع الرابع</button>
      <button class="btn btn-outline-primary week-btn" data-week="4">الأسبوع الخامس</button>
      <button class="btn btn-outline-primary week-btn" data-week="all">عرض الكل</button>
    </div>
  </div>

  <!-- جدول الدوام -->
  <!-- Add New Feature -->
  <div class="table-responsive timetable-table-shell" aria-label="جدول الدوام الأسبوعي">
    <div id="scheduleTableContainer"></div>
  </div>
  <!-- End -->

  <!-- جدول المشرفين -->
  <!-- Add New Feature -->
  <div>
    <div class="table-responsive timetable-table-shell" aria-label="جدول المشرفين الأسبوعي">
      <div id="supervisorsTableContainer">
        <!-- سيتم إضافة جدول المشرفين هنا عبر JavaScript -->
      </div>
    </div>
  </div>
  <!-- End -->

  <!-- Modal إضافة موظف -->
  <div class="modal fade" id="addEmployeeModal" tabindex="-1" aria-labelledby="addEmployeeModalLabel"
    aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="addEmployeeModalLabel">إضافة موظف جديد</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label for="employeeName" class="form-label">اسم الموظف</label>
            <input type="text" class="form-control" id="employeeName" placeholder="أدخل اسم الموظف">
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
          <button type="button" class="btn btn-primary" onclick="saveNewEmployee()">حفظ</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Dropdown menu -->
  <div id="dropdownMenu" class="dropdown-menu shadow-sm" style="display: none;">
    <button class="dropdown-item bg-am" onclick="selectOption('am')">AM</button>
    <button class="dropdown-item bg-pm" onclick="selectOption('pm')">PM</button>
    <button class="dropdown-item bg-leave" onclick="selectOption('leave')">أجازه</button>
    <button class="dropdown-item bg-note" onclick="selectOption('note')">ملاحظه</button>
  </div>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
  <script src="<?=base_url('public/js/employees_timetable.js')?>"></script>
  <script src="<?=base_url('public/js/script.js')?>"></script>
</div>
</div>
