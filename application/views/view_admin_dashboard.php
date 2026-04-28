<?php $this->view('view_header'); ?>
<title>الرئيسية</title>
<?php $this->view('view_admin_sidebar'); ?>

<link rel="stylesheet" href = "/public/css/dashboard.css" />

<div class = 'app-content'>
    <header class="modern-header py-4">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <div class="d-flex align-items-center">
                        <div class="header-icon me-3">
                            <i class="bi bi-boxes"></i>
                        </div>
                        <div>
                            <h1 class="header-title">نظام إدارة المخزون</h1>
                            <p class="header-subtitle mb-0">عرض وإدارة المنتجات والمخزون</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 text-end">
                    <a href="<?= base_url('admin/products/add') ?>" class="btn-modern-primary">
                        <i class="bi bi-plus-circle-fill me-2"></i>
                        إضافة منتجات
                    </a>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="main-content">
        <div class="container">

            <!-- Statistics Cards -->
            <div class="row mb-4">
                <div class="col-md-6 col-lg-3">
                    <div class="stats-card fade-in" data-bs-toggle="tooltip" data-bs-placement="top" title="يشمل جميع الأصناف المسجلة في النظام، حتى المكررة">
                        <div class="stats-icon">
                            <i class="bi bi-box-seam-fill"></i>
                        </div>
                        <div class="stats-content">
                            <h3><?= number_format(isset($totalRows) ? $totalRows : 0) ?></h3>
                            <p>إجمالي السجلات</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="stats-card fade-in" data-bs-toggle="tooltip" data-bs-placement="top" title="عدد الأصناف المختلفة، يتم حساب كل منتج فريد مرة واحدة فقط">
                        <div class="stats-icon">
                            <i class="bi bi-box-seam-fill"></i>
                        </div>
                        <div class="stats-content">
                            <h3><?= number_format(isset($stats['total_products']) ? $stats['total_products'] : 0) ?></h3>
                            <p>إجمالي المنتجات</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="stats-card fade-in" data-bs-toggle="tooltip" data-bs-placement="top" title="عدد فئات الأصناف الموجودة في المخزون">
                        <div class="stats-icon">
                            <i class="bi bi-tags-fill"></i>
                        </div>
                        <div class="stats-content">
                            <h3><?= number_format(isset($stats['total_categories']) ? $stats['total_categories'] : 0) ?></h3>
                            <p>عدد الفئات</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="stats-card fade-in" data-bs-toggle="tooltip" data-bs-placement="top" title="إجمالي عدد القطع المتوفرة حالياً في المخزون">
                        <div class="stats-icon">
                            <i class="bi bi-graph-up-arrow"></i>
                        </div>
                        <div class="stats-content">
                            <h3><?= number_format(isset($stats['total_quantity']) ? $stats['total_quantity'] : 0) ?></h3>
                            <p>إجمالي الكمية</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Search Section -->
            <section class="search-section fade-in">
                <div class="search-header">
                    <h4>
                        <i class="bi bi-search me-2"></i>
                        البحث والتصفية
                    </h4>
                </div>

                <form method="GET" class="search-form">
                    <div class="row g-3">
                        <div class="col-md-5">
                            <div class="form-floating">
                                <input type="text" name="name" class="form-control" id="searchName"
                                    placeholder="بحث باسم المنتج او كود المنتج" value="<?= htmlspecialchars($searchName) ?>">
                                <label for="searchName">
                                    <i class="bi bi-box me-2"></i>
                                    بحث باسم المنتج او كود المنتج
                                </label>
                            </div>
                        </div>
                        <div class="col-md-5">
                            <div class="form-floating">
                                <input type="text" name="category" class="form-control" id="searchCategory"
                                    placeholder="بحث بالفئة" value="<?= htmlspecialchars($searchCategory) ?>">
                                <label for="searchCategory">
                                    <i class="bi bi-tag me-2"></i>
                                    بحث بالفئة
                                </label>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="d-flex gap-2 h-100">
                                <button type="submit" class="btn btn-search flex-fill">
                                    <i class="bi bi-search"></i>
                                </button>
                                <a href="<?= basename(__FILE__) ?>" class="btn btn-reset flex-fill">
                                    <i class="bi bi-arrow-clockwise"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </section>

            <!-- Products Section -->
            <section class="products-section fade-in">
                <div class="products-header">
                    <h4>
                        <i class="bi bi-list-ul me-2"></i>
                        قائمة المنتجات
                    </h4>
                    <div class="products-count">
                        <?= number_format($totalItems) ?> منتج
                    </div>
                </div>

                <div class="table-responsive">
                    <?php if ($items): ?>
                        <table class="table table-striped">

                            <thead class="table-header table-dark">
                                <tr>
                                    <th class="text-center">#</th>
                                    <!-- اسم المنتج -->
                                    <th class="<?= $sortColumn == 'item_description' ? 'sorted-column' : '' ?>">
                                        <a class="sortable-link rounded <?= ($sortColumn == 'item_description')
                                                                            ? (strtolower($sortOrder) == 'asc' ? 'text-info' : 'text-danger')
                                                                            : '' ?>"
                                            href="?sort=item_description&order=<?= ($sortColumn == 'item_description' && $sortOrder == 'ASC') ? 'DESC' : 'ASC' ?>">
                                            <span>اسم المنتج</span>
                                            <?php if ($sortColumn == 'item_description'): ?>
                                                <i class="bi bi-caret-<?= strtolower($sortOrder) == 'asc' ? 'up' : 'down' ?>-fill sort-arrow"></i>
                                            <?php else: ?>
                                                <i class="bi bi-arrow-down-up inactive-arrow"></i>
                                            <?php endif; ?>
                                        </a>
                                    </th>

                                    <!-- كود المنتج -->
                                    <th class="<?= $sortColumn == 'item_code' ? 'sorted-column' : '' ?>">
                                        <a class="sortable-link <?= ($sortColumn == 'item_code')
                                                                    ? (strtolower($sortOrder) == 'asc' ? 'text-info' : 'text-danger')
                                                                    : '' ?>"
                                            href="?sort=item_code&order=<?= ($sortColumn == 'item_code' && $sortOrder == 'ASC') ? 'DESC' : 'ASC' ?>">
                                            <span>كود المنتج</span>
                                            <?php if ($sortColumn == 'item_code'): ?>
                                                <i class="bi bi-caret-<?= strtolower($sortOrder) == 'asc' ? 'up' : 'down' ?>-fill sort-arrow"></i>
                                            <?php else: ?>
                                                <i class="bi bi-arrow-down-up inactive-arrow"></i>
                                            <?php endif; ?>
                                        </a>
                                    </th>

                                    <!-- الفئة -->
                                    <th class="<?= $sortColumn == 'item_category' ? 'sorted-column' : '' ?>">
                                        <a class="sortable-link <?= ($sortColumn == 'item_category')
                                                                    ? (strtolower($sortOrder) == 'asc' ? 'text-info' : 'text-danger')
                                                                    : '' ?>"
                                            href="?sort=item_category&order=<?= ($sortColumn == 'item_category' && $sortOrder == 'ASC') ? 'DESC' : 'ASC' ?>">
                                            <span>الفئة</span>
                                            <?php if ($sortColumn == 'item_category'): ?>
                                                <i class="bi bi-caret-<?= strtolower($sortOrder) == 'asc' ? 'up' : 'down' ?>-fill sort-arrow"></i>
                                            <?php else: ?>
                                                <i class="bi bi-arrow-down-up inactive-arrow"></i>
                                            <?php endif; ?>
                                        </a>
                                    </th>

                                    <!-- العدد الكلي -->
                                    <th class="<?= $sortColumn == 'total_quantity' ? 'sorted-column' : '' ?>">
                                        <a class="sortable-link <?= ($sortColumn == 'total_quantity')
                                                                    ? (strtolower($sortOrder) == 'asc' ? 'text-info' : 'text-danger')
                                                                    : '' ?>"
                                            href="?sort=total_quantity&order=<?= ($sortColumn == 'total_quantity' && $sortOrder == 'ASC') ? 'DESC' : 'ASC' ?>">
                                            <span>الكمية</span>
                                            <?php if ($sortColumn == 'total_quantity'): ?>
                                                <i class="bi bi-caret-<?= strtolower($sortOrder) == 'asc' ? 'up' : 'down' ?>-fill sort-arrow"></i>
                                            <?php else: ?>
                                                <i class="bi bi-arrow-down-up inactive-arrow"></i>
                                            <?php endif; ?>
                                        </a>
                                    </th>

                                </tr>
                            </thead>

                            <tbody>
                                <?php
                                $index = ($page - 1) * $itemsPerPage + 1;
                                foreach ($items as $row):
                                    $item_description = $row['item_description'];
                                ?>
                                    <tr>
                                        <td>
                                            <span class="row-number"><?= $index++ ?></span>
                                        </td>
                                        <td>
                                            <?php
                                            $text_danger = ((int)$row['total_quantity'] === 0) ? 'text-danger' : '';
                                            $quantity_badge = ((int)$row['total_quantity'] === 0) ? 'quantity-badge-danger' : 'quantity-badge';

                                            $shortName = explode(',', $item_description)[0];
                                            ?>
                                            <a href="<?= base_url('admin/products/edit')?>?item_code=<?= urlencode($row['item_code']) ?>" class="text-start w-100 product-link <?= $text_danger ?>">
                                                <i class="bi bi-box me-2"></i>
                                                <?= htmlspecialchars($shortName) ?>
                                            </a>
                                        </td>

                                        <td>
                                            <span class="w-100 category-badge text-dark ">
                                                <i class="bi bi-upc-scan me-1"></i>
                                                <?= htmlspecialchars($row['item_code']) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="w-100 category-badge text-dark">
                                                <i class="bi bi-tag-fill me-1"></i>
                                                <?= htmlspecialchars($row['item_category']) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="w-100 text-dark <?= $quantity_badge ?>">
                                                <?= number_format((int)$row['total_quantity']) ?>
                                            </span>
                                        </td>

                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <div class="empty-state">
                            <div class="empty-icon">
                                <i class="bi bi-inbox"></i>
                            </div>
                            <h5>لا توجد منتجات</h5>
                            <p>لم يتم العثور على أي منتجات تطابق معايير البحث</p>
                            <a href="<?= base_url('admin/dashboard') ?>" class="btn btn-search">
                                <i class="bi bi-arrow-clockwise me-2"></i>
                                عرض جميع المنتجات
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </section>

            <!-- Pagination -->
            <?php if ($totalPages > 1): ?>
            <section class="pagination-section mt-4">
                <div class="pagination-info">
                    عرض <?= ($page - 1) * $itemsPerPage + 1 ?> - <?= min($page * $itemsPerPage, $totalItems) ?>
                    من أصل <?= number_format($totalItems) ?> منتج
                </div>

                <nav>
                    <ul class="pagination modern-pagination">
                        <!-- Previous -->
                        <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                            <a class="page-link" 
                            href="?page=<?= $page - 1 ?>&sort=<?= urlencode($sortColumn) ?>&order=<?= urlencode($sortOrder) ?>&name=<?= urlencode($searchName) ?>&category=<?= urlencode($searchCategory) ?>">
                                <i class="bi bi-chevron-right"></i> السابق
                            </a>
                        </li>

                        <!-- Page Numbers -->
                        <?php
                        $startPage = max(1, $page - 2);
                        $endPage   = min($totalPages, $page + 2);

                        for ($i = $startPage; $i <= $endPage; $i++):
                        ?>
                            <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                                <a class="page-link" 
                                href="?page=<?= $i ?>&sort=<?= urlencode($sortColumn) ?>&order=<?= urlencode($sortOrder) ?>&name=<?= urlencode($searchName) ?>&category=<?= urlencode($searchCategory) ?>">
                                    <?= $i ?>
                                </a>
                            </li>
                        <?php endfor; ?>

                        <!-- Next -->
                        <li class="page-item <?= ($page >= $totalPages) ? 'disabled' : '' ?>">
                            <a class="page-link" 
                            href="?page=<?= $page + 1 ?>&sort=<?= urlencode($sortColumn) ?>&order=<?= urlencode($sortOrder) ?>&name=<?= urlencode($searchName) ?>&category=<?= urlencode($searchCategory) ?>">
                                التالي <i class="bi bi-chevron-left"></i>
                            </a>
                        </li>
                    </ul>
                </nav>
            </section>
            <?php endif; ?>

        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-dark text-white text-center py-4 mt-5">
        <div class="container">
            <p class="mb-0">© 2024 نظام إدارة المخزون - جميع الحقوق محفوظة</p>
        </div>
    </footer>
</div>
</div>

<!-- JS -->
<script src="https://code.jquery.com/jquery-3.6.0.js" crossorigin="anonymous"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.forEach(function (tooltipTriggerEl) {
            new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
</script>

<script src="<?php echo base_url(); ?>public/js/script.js?a=<?php $date=date_create();echo date_timestamp_get($date);?>"></script>
<script src="<?php echo base_url(); ?>public/js/home.js?a=<?php $date=date_create();echo date_timestamp_get($date);?>"></script>

<?php $this->view('view_footer'); ?>