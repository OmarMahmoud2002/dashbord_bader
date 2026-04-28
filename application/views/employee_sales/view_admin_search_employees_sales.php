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

<style>
    body {
        font-family: 'Cairo', sans-serif;
        background-color: #f4f6f9;
    }
    .container-fluid { padding-left: 0; padding-right: 0; }
    .content-wrapper { padding: 20px; display: flex; justify-content: center; align-items: center; min-height: 80vh; }
        .card {
        border-radius: .75rem;
        box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.1);
        width: 100%;
        max-width: 700px; /* تحديد عرض أقصى للبطاقة */
    }
    .card-header {
        background-color: #007bff;
        color: white;
        font-weight: bold;
        border-top-left-radius: .75rem;
        border-top-right-radius: .75rem;
        padding: 1rem 1.25rem;
    }

    .card-title { margin-bottom: 0; }
</style>
<style>
    body {
        font-family: 'Cairo', sans-serif;
        background-color: #f8f9fa;
    }
    .navbar-brand {
        font-weight: bold;
    }
    .container {
        margin-top: 20px;
        margin-bottom: 20px;
    }
    .card {
        border-radius: 0.75rem;
        box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.1);
    }
    .card-header {
        background-color: #007bff; /* Primary color */
        color: white;
        font-weight: bold;
        border-top-left-radius: 0.75rem;
        border-top-right-radius: 0.75rem;
    }
    .btn-primary {
        background-color: #007bff;
        border-color: #007bff;
    }
    .btn-primary:hover {
        background-color: #0056b3;
        border-color: #0056b3;
    }
    .btn-success {
            background-color: #28a745;
            border-color: #28a745;
    }
    .btn-success:hover {
            background-color: #1e7e34;
            border-color: #1e7e34;
    }
    .table th {
        background-color: #e9ecef;
    }
    .employee-details-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 1.5rem;
    }
    .detail-item {
        background-color: #fff;
        padding: 1rem;
        border-radius: 0.5rem;
        border: 1px solid #dee2e6;
    }
    .detail-item strong {
        display: block;
        margin-bottom: 0.5rem;
        color: #007bff;
    }
</style>


<div class="app-content">
    <div class = "content-wrapper">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8 col-md-10">
                    <div class="card search-card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">البحث عن موظف</h5>
                            <a id = "uploadFile" href="<?= site_url('admin/employees_sales_upload') ?>" class="btn btn-primary btn-sm" style="background-color: #007bff; color: white; border-radius: 8px; padding: 6px 20px; font-weight: 600;">
                                رفع ملف جديد
                            </a>
                        </div>
                        <div class="card-body">
                            <?php if (isset($file_uploaded_name) && $file_uploaded_name !== 'غير محدد'): ?>
                                <p class="text-muted small">الملف المرفوع حاليًا: <strong><?= htmlspecialchars($file_uploaded_name) ?></strong></p>
                                <?php 
                                $individualsDate = $this->session->userdata('individuals_date');
                                $businessDate = $this->session->userdata('business_date');
                                if ($individualsDate || $businessDate): 
                                ?>
                                    <div class="alert alert-info py-2">
                                        <?php if ($individualsDate): ?>
                                            <strong>الأفراد حتى تاريخ:</strong> <?= htmlspecialchars($individualsDate) ?>
                                            <?php if ($businessDate): ?> | <?php endif; ?>
                                        <?php endif; ?>
                                        <?php if ($businessDate): ?>
                                            <strong>الأعمال حتى تاريخ:</strong> <?= htmlspecialchars($businessDate) ?>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                            <?php endif; ?>

                            <?php if (isset($search_error) && $search_error): ?>
                                <div class="alert alert-danger" role="alert">
                                    <?php echo htmlspecialchars($search_error); ?>
                                </div>
                            <?php endif; ?>
                            <?php if ($this->session->flashdata('upload_success')): ?>
                                <div class="alert alert-success"><?php echo $this->session->flashdata('upload_success'); ?></div>
                            <?php endif; ?>
                            <?php if ($this->session->flashdata('upload_error') && !isset($search_error)): // عرض خطأ الرفع فقط إذا لم يكن هناك خطأ بحث ?>
                                <div class="alert alert-warning"><?php echo $this->session->flashdata('upload_error'); ?></div>
                            <?php endif; ?>


                            <form action="<?= site_url('admin/process_employee_sales_search') ?>" method="post">
                                <?php echo form_hidden($this->security->get_csrf_token_name(), $this->security->get_csrf_hash()); ?>
                                <div class="mb-3">
                                    <label for="search_term" class="form-label fw-bold">أدخل رقم الهوية، الاسم، أو الرقم الوظيفي:</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control form-control-lg" 
                                            id="search_term" name="search_term" 
                                            value="<?php echo htmlspecialchars(isset($search_term_value) ? $search_term_value : ''); ?>" 
                                            placeholder="مثال: 280..." required>
                                        <button class="btn btn-primary btn-lg" type="submit">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-search" viewBox="0 0 16 16"><path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001q.044.06.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1 1 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0"/></svg>
                                            بحث
                                        </button>
                                    </div>
                                    <div class="form-text mt-2">
                                        يمكنك البحث باستخدام أحد المعايير المذكورة.
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
</div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.js" crossorigin="anonymous"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

<script src="<?php echo base_url(); ?>public/js/script.js?a=<?php $date=date_create();echo date_timestamp_get($date);?>"></script>
<script src="<?php echo base_url(); ?>public/js/sales.js?a=<?php $date=date_create();echo date_timestamp_get($date);?>"></script>
<?php $this->load->view('view_footer'); ?>