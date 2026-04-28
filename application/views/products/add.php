
<title>نظام رفع ملفات المخزون</title>
<?php $this->load->view('view_header'); ?>
<?php $this->load->view('view_admin_sidebar');?>

<link href="<?= base_url('/public/css/add.css') ?>" rel="stylesheet">

<div class = 'app-content'>
    <!-- Header -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-gradient-primary shadow-lg">
        <div class="container">
            <a class="navbar-brand fw-bold fs-3" href="<?= base_url('admin/dashboard') ?>">
                <i class="bi bi-cloud-upload-fill me-2"></i>
                نظام إدارة المخزون
            </a>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-lg-8 col-xl-6">
                
                <!-- Status Messages -->
                <?php if ($uploadStatus): ?>
                <div class="alert alert-<?= $uploadStatus === 'success' ? 'success' : 'danger' ?> alert-dismissible fade show shadow-sm" role="alert">
                    <i class="bi bi-<?= $uploadStatus === 'success' ? 'check-circle-fill' : 'exclamation-triangle-fill' ?> me-2"></i>
                    <?= htmlspecialchars($uploadMessage) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php endif; ?>

                <!-- Upload Card -->
                <div class="card shadow-lg border-0 upload-card">
                    <div class="card-header bg-gradient-primary text-white text-center py-4">
                        <h2 class="card-title mb-0">
                            <i class="bi bi-file-earmark-excel-fill fs-1 d-block mb-2"></i>
                            رفع ملف Excel للمخزون
                        </h2>
                        <p class="mb-0 opacity-75">قم برفع ملف Excel لتحديث بيانات المخزون الحالي</p>
                    </div>
                    
                    <div class="card-body p-5">
                        <form id="uploadForm" method="POST" enctype="multipart/form-data">
                            
                            <!-- File Drop Zone -->
                            <div class="upload-zone mb-4" id="uploadZone">
                                <div class="upload-zone-content">
                                    <i class="bi bi-cloud-upload upload-icon"></i>
                                    <h4 class="upload-title">اسحب الملف هنا أو انقر للاختيار</h4>
                                    <p class="upload-subtitle text-muted">
                                        يدعم ملفات Excel (.xlsx, .xls) حتى 50 ميجابايت
                                    </p>
                                    <input type="file" name="excel_file" id="excelFile" accept=".xlsx,.xls" required hidden>
                                </div>
                            </div>

                            <!-- File Info -->
                            <div id="fileInfo" class="file-info d-none mb-4">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-file-earmark-excel-fill text-success fs-3 me-3"></i>
                                    <div class="flex-grow-1">
                                        <div class="fw-bold" id="fileName"></div>
                                        <div class="text-muted small" id="fileSize"></div>
                                    </div>
                                    <button type="button" class="btn btn-outline-danger btn-sm" id="removeFile">
                                        <i class="bi bi-x-lg"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- Progress Bar -->
                            <div id="progressContainer" class="d-none mb-4">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="fw-bold">جاري الرفع...</span>
                                    <span id="progressPercent">0%</span>
                                </div>
                                <div class="progress progress-animated">
                                    <div class="progress-bar bg-gradient-success" id="progressBar" role="progressbar" style="width: 0%"></div>
                                </div>
                                <div class="text-center mt-2">
                                    <small class="text-muted" id="progressStatus">جاري تحضير الملف...</small>
                                </div>
                            </div>

                            <!-- Upload Button -->
                            <div class="d-grid">
                                <button type="submit" class="btn btn-gradient-primary btn-lg" id="uploadBtn" disabled>
                                    <i class="bi bi-cloud-upload-fill me-2"></i>
                                    <span id="btnText">اختر ملف أولاً</span>
                                </button>
                            </div>

                        </form>
                    </div>
                </div>

                <!-- Instructions Card -->
                <div class="card mt-4 border-0 shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title text-primary">
                            <i class="bi bi-info-circle-fill me-2"></i>
                            تعليمات الاستخدام
                        </h5>
                        <ul class="list-unstyled mb-0">
                            <li class="mb-2">
                                <i class="bi bi-check-circle-fill text-success me-2"></i>
                                تأكد من أن الملف بصيغة Excel (.xlsx أو .xls)
                            </li>
                            <li class="mb-2">
                                <i class="bi bi-check-circle-fill text-success me-2"></i>
                                سيتم حذف البيانات القديمة واستبدالها بالجديدة
                            </li>
                            <li class="mb-2">
                                <i class="bi bi-check-circle-fill text-success me-2"></i>
                                سيتم تجاهل الصفوف من 1 إلى 9 والعناصر بدون رقم تسلسلي
                            </li>
                            <li>
                                <i class="bi bi-check-circle-fill text-success me-2"></i>
                                الحد الأقصى لحجم الملف: 50 ميجابايت
                            </li>
                        </ul>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-white text-center py-4 mt-5">
        <div class="container">
            <p class="mb-0">© 2024 نظام إدارة المخزون - جميع الحقوق محفوظة</p>
        </div>
    </footer>
</div>
</div>

<script src = "<?= base_url('public/js/add.js') ?>"></script>