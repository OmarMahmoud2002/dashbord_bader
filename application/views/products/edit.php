<?php $this->load->view('view_header'); ?>
<?php $this->load->view('view_admin_sidebar'); ?>

<link rel='stylesheet' href='<?= base_url('/public/css/edit_product.css') ?>'>

<div class = 'app-content admin-ui-page admin-ui-product-form'>
    <div class="hero-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <div class="d-flex align-items-center mb-3">
                        <div class="product-icon me-3">
                            <i class="bi bi-box-seam"></i>
                        </div>
                        <div>
                            <h1 class="hero-title mb-1">تفاصيل المنتج</h1>

                            <p class="hero-subtitle mb-0"><?= htmlspecialchars($product['item_description']) ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 text-end">
                    <a href="<?= base_url('admin/dashboard') ?>" class="position-relative btn btn-outline-light btn-lg">
                        <i class="bi bi-arrow-right me-2"></i>
                        العودة للمخزون
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="container my-5">
        <!-- Product Summary Cards -->
        <div class="row mb-5">
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="summary-card card-primary">
                    <div class="card-icon">
                        <i class="bi bi-tag"></i>
                    </div>
                    <div class="card-content">
                        <h6>كود المنتج</h6>
                        <p><?= htmlspecialchars($product['item_code']) ?></p>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="summary-card card-success">
                    <div class="card-icon">
                        <i class="bi bi-grid-3x3-gap"></i>
                    </div>
                    <div class="card-content">
                        <h6>الفئة</h6>
                        <p><?= htmlspecialchars($product['item_category']) ?></p>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="summary-card card-info">
                    <div class="card-icon">
                        <i class="bi bi-list-ol"></i>
                    </div>
                    <div class="card-content">
                        <h6>عدد السيريالات</h6>
                        <p><?= count($serials) ?></p>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="summary-card card-warning">
                    <div class="card-icon">
                        <i class="bi bi-shield-check"></i>
                    </div>
                    <div class="card-content">
                        <h6>نوع التحكم</h6>
                        <p>
                            <?php if ($hasSerial && $hasNoSerial): ?>
                                مختلط
                            <?php elseif ($hasSerial): ?>
                                سيريال
                            <?php elseif ($hasNoSerial): ?>
                                باركود
                            <?php else: ?>
                                غير محدد
                            <?php endif; ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabs Section -->
        <div class="modern-tabs-container">
            <ul class="nav nav-pills modern-tabs justify-content-center mb-4" id="productTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="tab-basic" data-bs-toggle="pill" data-bs-target="#basic" type="button" role="tab">
                        <i class="bi bi-info-circle me-2"></i>
                        بيانات المنتج
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="tab-shelf" data-bs-toggle="pill" data-bs-target="#shelf" type="button" role="tab">
                        <i class="bi bi-gear me-2"></i>
                        الرف
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="tab-extra" data-bs-toggle="pill" data-bs-target="#extra" type="button" role="tab">
                        <i class="bi bi-gear me-2"></i>
                        بيانات إضافية
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="tab-serials" data-bs-toggle="pill" data-bs-target="#serials" type="button" role="tab">
                        <i class="bi bi-list-ul me-2"></i>
                        السيريالات
                    </button>
                </li>
            </ul>

            <div class="tab-content modern-tab-content" id="productTabsContent">
                <!-- التاب الأول: بيانات المنتج -->
                <div class="tab-pane fade show active" id="basic" role="tabpanel">
                    <div class="content-card">
                        <div class="card-header card-primary">
                            <h5>
                                <i class="bi bi-box me-2"></i>
                                معلومات المنتج الأساسية

                                <div class="card-icon mx-2" style="width: 40px; height:40px;font-size: 1.1rem;">
                                    <span><?php echo $totalQuantity; ?></span>
                                </div>
                            </h5>
                        </div>
                        <div class="card-body">
                            <form action="<?= base_url('admin/products/update_basic_product_data')?>" method="post" class="m-2">
                                <?php
                                $description = $product['item_description'];
                                $shortName = explode(',', $description)[0];
                                ?>
                                <div class="info-row">
                                    <div class="info-label">
                                        <i class="bi bi-tag-fill"></i>
                                        اسم المنتج
                                    </div>
                                    <div class="info-value w-100 <?= $text_danger ?>"><?= htmlspecialchars($shortName) ?></div>
                                </div>
                                <div class="info-row">
                                    <div class="info-label">
                                        <i class="bi bi-hash"></i>
                                        الايتم كود
                                    </div>
                                    <div class="info-value w-100">
                                        <input type="text" class="form-control" value="<?= htmlspecialchars($product['item_code']) ?>" disabled>
                                    </div>
                                </div>
                                <div class="info-row">
                                    <div class="info-label">
                                        <i class="bi bi-file-text"></i>
                                        وصف المنتج
                                    </div>
                                    <div class="info-value w-100">
                                        <input type="hidden" name="item_id" value="<?= $product['id'] ?>">
                                        <input type="hidden" name="item_code" value="<?= $product['item_code'] ?>">
                                        <textarea class="bg-primary bg-opacity-10 form-control" name="item_description" value="<?= htmlspecialchars($product['item_description']) ?>"><?= htmlspecialchars($product['item_description']) ?></textarea>
                                    </div>
                                </div>
                                
                                <div class="info-row">
                                    <div class="info-label">
                                        <i class="bi bi-collection"></i>
                                        الفئة
                                    </div>
                                    <div class="info-value w-100">
                                        <input type="text" class="bg-primary bg-opacity-10 form-control" name="item_category" value="<?= htmlspecialchars($product['item_category']) ?>">
                                    </div>
                                </div>
                                

                                <div class="info-row">
                                    <div class="info-label">
                                        <i class="bi bi-upc-scan"></i>
                                        رقم الباركود
                                    </div>
                                    <div class="info-value w-100">
                                        <input type="text" class="bg-primary bg-opacity-10 form-control" name="barcode" value="<?= htmlspecialchars($product['barcode']) ?>">
                                    </div>
                                </div>

                                <div class="info-row">
                                    <div class="text-center">
                                        <button type="submit" class="btn btn-info text-light fs-5 fw-bold">حفظ</button>
                                    </div>
                                </div>
                                
                                
                            </form>
                        </div>
                    </div>
                </div>

                <!-- التاب الثاني: بيانات الرف -->
                <div class="tab-pane fade show" id="shelf" role="tabpanel">
                    <form action="" class="col-md-6 m-2">
                        <div class="input-group mb-3">
                            <input type="text" class="form-control" value="R200">
                            <button class="btn btn-info text-light fs-4 fw-bold">+</button>
                        </div>
                        <div class="input-group mb-3">
                            <input type="text" class="form-control" value="R25">
                            <button class="btn btn-info text-light fs-4 fw-bold">+</button>
                        </div>
                        <div class="input-group mb-3">
                            <input type="text" class="form-control" value="R16">
                            <button class="btn btn-info text-light fs-4 fw-bold">+</button>
                        </div>
                        <div class="input-group mb-3">
                            <input type="text" class="form-control" value="R17">
                            <button class="btn btn-info text-light fs-4 fw-bold">+</button>
                        </div>
                        <div class="input-group mb-3">
                            <input type="text" class="form-control" value="20">
                            <button class="btn btn-info text-light fs-4 fw-bold">+</button>
                        </div>
                        <div class="input-group mb-3">
                            <input type="text" class="form-control" value="C910">
                            <button class="btn btn-info text-light fs-4 fw-bold">+</button>
                        </div>
                        <div class="text-center">
                            <button type="button" class="btn btn-info text-light fs-4 fw-bold">حفظ</button>
                        </div>
                        
                    </form>
                </div>

                <!-- التاب الثالث: بيانات إضافية -->
                <div class="tab-pane fade" id="extra" role="tabpanel">
                    <form action="<?= base_url('admin/products/update_extra_product_data') ?>" enctype="multipart/form-data" class="col-md-6 m-2" method="post">
                        <div class="mb-3">
                            <input type="hidden" name="item_id" value="<?= $product['id'] ?>">
                            <input type="hidden" name="item_code" value="<?= $product['item_code'] ?>">
                            <label class="form-label fw-bold">اضافة الصور</label>

                            <input type="file" class="form-control" name="product_images[]" id="product_images" multiple accept="image/*">

                            <!-- عرض الصور المختارة -->
                            <div id="preview_images" class="mt-2 d-flex flex-wrap gap-2"></div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">السعر</label>
                            <input type="number" class="form-control" name="price" value="<?= $product['price'] != null ? $product['price'] : '' ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">رقم التنبيه بأنخفاض الكمية</label>
                            <input type="number" class="form-control" name="low_quantity" value="<?= $product['low_quantity'] != null ? $product['low_quantity'] : '' ?>">
                        </div>

                        <div class="mb-3">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="serial_control" id="inlineRadio1" value="yes" <?= $hasSerial ? 'checked' : ''; ?> >
                                <label class="form-check-label" for="inlineRadio1">متعدد السيريالات</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="serial_control" id="inlineRadio2" value="no" <?= $hasNoSerial ? 'checked' : ''; ?> >
                                <label class="form-check-label" for="inlineRadio2">باركود موحد</label>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" id="inlineCheckbox1" name='CharSEnabled' <?= $product['char_s_enabled'] == 1 ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="inlineCheckbox1">ازالة حرف S</label>
                            </div>
                        </div>
                        
                        <div class="text-center">
                            <button type="submit" class="btn btn-info text-light fs-5 fw-bold">حفظ</button>
                        </div>
                        
                    </form>
                </div>

                <!-- التاب الرابع: السيريالات -->
                <div class="tab-pane fade" id="serials" role="tabpanel">
                    <div class="content-card">
                        <div class="card-header">
                            <h5><i class="bi bi-list-ul me-2"></i>قائمة السيريالات</h5>
                            <?php if ($serials): ?>
                                <span class="serial-count"><?= count($serials) ?> سيريال</span>
                            <?php endif; ?>
                        </div>
                        <div class="card-body">
                            <?php if ($serials): ?>
                                <div class="serials-grid">
                                    <?php foreach ($serials as $index => $s): ?>
                                        <div class="serial-item">
                                            <div class="serial-number"><?= $index + 1 ?></div>
                                            <div class="serial-value"><?= htmlspecialchars($s) ?></div>
                                            <div class="serial-icon">
                                                <i class="bi bi-check-circle"></i>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <div class="empty-state">
                                    <div class="empty-icon">
                                        <i class="bi bi-inbox"></i>
                                    </div>
                                    <h6>لا توجد سيريالات</h6>
                                    <p class="text-muted">لا توجد سيريالات مسجلة لهذا المنتج</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('product_images').addEventListener('change', function (event) {
            let preview = document.getElementById('preview_images');
            preview.innerHTML = ''; // مسح أي صور قديمة

            Array.from(event.target.files).forEach(file => {
                if (file.type.startsWith('image/')) {
                    let reader = new FileReader();
                    reader.onload = function (e) {
                        let img = document.createElement('img');
                        img.src = e.target.result;
                        img.classList.add('img-thumbnail');
                        img.style.width = '100px';
                        img.style.height = '100px';
                        preview.appendChild(img);
                    }
                    reader.readAsDataURL(file);
                }
            });
        });
    </script>
</div>
</div>


<!-- JS -->
<script src="https://code.jquery.com/jquery-3.6.0.js" crossorigin="anonymous"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

<script src="<?php echo base_url(); ?>public/js/script.js"></script>
<script src="<?php echo base_url(); ?>public/js/edit.js?a=<?php $date=date_create();echo date_timestamp_get($date);?>"></script>
<?php $this->load->view('view_footer'); ?>
