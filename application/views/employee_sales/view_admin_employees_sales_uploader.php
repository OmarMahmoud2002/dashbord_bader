<title>مبيعات الموظفين</title>
<?php $this->view('view_header'); ?>
<?php $this->view('view_admin_sidebar'); ?>

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

<div class = 'app-content'>
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    رفع ملف بيانات الموظفين (xlsx, xls)
                </div>
                <div class="card-body">
                    <?php if ($this->session->flashdata('upload_error')): ?>
                        <div class="alert alert-danger"><?= $this->session->flashdata('upload_error') ?></div>
                    <?php endif; ?>
                    <?php if ($this->session->flashdata('upload_success')): ?>
                        <div class="alert alert-success"><?= $this->session->flashdata('upload_success') ?></div>
                    <?php endif; ?>

                    <?= form_open_multipart(site_url('/admin/handle_employees_sales_upload'), ['class' => 'mt-3']) ?>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="individuals_date" class="form-label">الأفراد حتى تاريخ:</label>
                                <input type="date" name="individuals_date" id="individuals_date" class="form-control" 
                                    value="<?= htmlspecialchars($this->session->userdata('individuals_date') ? $this->session->userdata('individuals_date') : '') ?>">
                            </div>
                            <div class="col-md-6">
                                <label for="business_date" class="form-label">الأعمال حتى تاريخ:</label>
                                <input type="date" name="business_date" id="business_date" class="form-control"
                                    value="<?= htmlspecialchars($this->session->userdata('business_date') ? $this->session->userdata('business_date') : '') ?>">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="userfile" class="form-label">اختر ملف xlsx, xls:</label>
                            <input type="file" name="userfile" id="userfile" class="form-control" required 
                                accept=".xlsx, .xls, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel">
                            <div class="form-text">يجب أن يكون الملف بصيغة xlsx, xls ومشابهًا في الهيكل للملف المثال.</div>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">رفع الملف</button>
                    <?= form_close() ?>

                    <?php if (!empty($file_uploaded_name)): ?>
                        <hr>
                        <p class="text-center">الملف المرفوع حالياً: <strong><?= htmlspecialchars($file_uploaded_name) ?></strong></p>
                        <?php 
                        $individualsDate = $this->session->userdata('individuals_date');
                        $businessDate = $this->session->userdata('business_date');
                        if ($individualsDate || $businessDate): 
                        ?>
                            <div class="alert alert-info text-center py-2">
                                <?php if ($individualsDate): ?>
                                    <strong>الأفراد حتى تاريخ:</strong> <?= htmlspecialchars($individualsDate) ?>
                                    <?php if ($businessDate): ?> | <?php endif; ?>
                                <?php endif; ?>
                                <?php if ($businessDate): ?>
                                    <strong>الأعمال حتى تاريخ:</strong> <?= htmlspecialchars($businessDate) ?>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                        <div class="text-center">
                            <a href="<?= site_url('employee/search') ?>" class="btn btn-info">الانتقال إلى صفحة البحث</a>
                        </div>
                    <?php endif; ?>
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