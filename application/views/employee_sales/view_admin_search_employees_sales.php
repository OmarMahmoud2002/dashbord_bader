<title>مبيعات الموظفين</title>
<?php $this->view('view_header'); ?>
<?php $this->view('view_admin_sidebar'); ?>

<?php
// employee_search_page.php
$file_uploaded_name = isset($file_uploaded_name) ? $file_uploaded_name : ($this->Variables->getdata('uploaded_file_name') ? $this->Variables->getdata('uploaded_file_name') : 'غير محدد');
$search_error = isset($search_error) ? $search_error : ($this->session->flashdata('search_error') ? $this->session->flashdata('search_error') : null);
// $search_term_value يتم تمريره من المتحكم لإعادة ملء الحقل

$search_term_value = isset($search_term_value) ? $search_term_value : '';
?>

<?php
$individualsDate = $this->session->userdata('individuals_date');
$businessDate = $this->session->userdata('business_date');
$hasUploadedFile = isset($file_uploaded_name) && $file_uploaded_name !== 'غير محدد';
?>

<div class="app-content admin-ui-page admin-ui-employee-sales employee-sales-search-page">
    <div class="employee-sales-shell">
        <section class="employee-sales-card">
            <div class="employee-sales-heading">
                <div class="employee-sales-title">
                    <h1>البحث عن موظف</h1>
                    <p>قم بإدخال البيانات المطلوبة للوصول إلى السجلات المحدّثة</p>
                </div>
                <span class="employee-sales-icon" aria-hidden="true">
                    <i class="bi bi-person-vcard"></i>
                </span>
            </div>

            <?php if (isset($search_error) && $search_error): ?>
                <div class="employee-sales-alert employee-sales-alert-danger" role="alert">
                    <i class="bi bi-exclamation-triangle"></i>
                    <span><?php echo htmlspecialchars($search_error); ?></span>
                </div>
            <?php endif; ?>
            <?php if ($this->session->flashdata('upload_success')): ?>
                <div class="employee-sales-alert employee-sales-alert-success">
                    <i class="bi bi-check-circle"></i>
                    <span><?php echo htmlspecialchars($this->session->flashdata('upload_success')); ?></span>
                </div>
            <?php endif; ?>
            <?php if ($this->session->flashdata('upload_error') && !isset($search_error)): ?>
                <div class="employee-sales-alert employee-sales-alert-warning">
                    <i class="bi bi-info-circle"></i>
                    <span><?php echo htmlspecialchars($this->session->flashdata('upload_error')); ?></span>
                </div>
            <?php endif; ?>

            <div class="employee-sales-body">
                <div class="employee-sales-meta">
                    <div class="employee-sales-dates">
                        <h2>نطاق التاريخ</h2>
                        <div class="employee-sales-date-grid">
                            <div class="employee-sales-date-chip">
                                <i class="bi bi-calendar2-week"></i>
                                <span><?= htmlspecialchars($individualsDate ? $individualsDate : 'غير محدد') ?></span>
                            </div>
                            <div class="employee-sales-date-chip">
                                <i class="bi bi-calendar2-week"></i>
                                <span><?= htmlspecialchars($businessDate ? $businessDate : 'غير محدد') ?></span>
                            </div>
                        </div>
                    </div>

                    <div class="employee-sales-status">
                        <h2>حالة البحث</h2>
                        <div class="employee-sales-status-list" aria-label="حالة البحث">
                            <span class="active">الكل</span>
                            <span>نشط</span>
                            <span>مؤرشف</span>
                        </div>
                    </div>
                </div>

                <aside class="employee-sales-files">
                    <div class="employee-sales-files-head">
                        <h2>المستندات الملحقة</h2>
                        <span>XLS, XLSX</span>
                    </div>

                    <div class="employee-sales-file-item">
                        <span class="employee-sales-file-action" aria-hidden="true">
                            <i class="bi bi-file-earmark-spreadsheet-fill"></i>
                        </span>
                        <div>
                            <strong><?= htmlspecialchars($hasUploadedFile ? $file_uploaded_name : 'لا يوجد ملف مرفوع') ?></strong>
                            <p><?= htmlspecialchars($individualsDate ? 'آخر تحديث: ' . $individualsDate : 'ارفع ملفًا جديدًا لبدء البحث') ?></p>
                        </div>
                    </div>

                    <a id="uploadFile" href="<?= site_url('admin/employees_sales_upload') ?>" class="employee-sales-upload-link">
                        <i class="bi bi-file-earmark-arrow-up"></i>
                        <span>رفع ملف جديد</span>
                    </a>
                </aside>
            </div>

            <form action="<?= site_url('admin/process_employee_sales_search') ?>" method="post" class="employee-sales-search-form">
                <?php echo form_hidden($this->security->get_csrf_token_name(), $this->security->get_csrf_hash()); ?>
                <label for="search_term" class="visually-hidden">أدخل رقم الهوية، الاسم، أو الرقم الوظيفي</label>
                <div class="employee-sales-search-control">
                    <i class="bi bi-person-badge" aria-hidden="true"></i>
                    <input type="text" class="form-control"
                        id="search_term" name="search_term"
                        value="<?php echo htmlspecialchars(isset($search_term_value) ? $search_term_value : ''); ?>"
                        placeholder="أدخل رقم الهوية أو اسم الموظف..." required>
                    <button class="employee-sales-search-button" type="submit" aria-label="بحث">
                        <i class="bi bi-search"></i>
                    </button>
                </div>
            </form>
        </section>
    </div>
</div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.js" crossorigin="anonymous"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

<script src="<?php echo base_url(); ?>public/js/script.js?a=<?php $date=date_create();echo date_timestamp_get($date);?>"></script>
<script src="<?php echo base_url(); ?>public/js/sales.js?a=<?php $date=date_create();echo date_timestamp_get($date);?>"></script>
<?php $this->load->view('view_footer'); ?>
