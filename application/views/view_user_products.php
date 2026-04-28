<title>سجل المنتجات المبعات</title>
<?php $this->view('view_header'); ?>
<?php $this->view('view_user_sidebar'); ?>
<?php
function formatDate($dateString) {
    $date = new DateTime($dateString);
    return $date->format('d-m-Y h:i:s A'); // dd-mm-yyyy hh:mm:ss AM/PM
}

?>

<link rel="stylesheet" href="<?=base_url()?>public/css/employee_products_table.css">

<div class="app-content">
    <div class="app-content-header">

    </div>
    <div class="container">
        <header class="app-header">
            <h1 class="page-title">
                <i class="fas fa-shopping-bag"></i> سجل المنتجات المباعة
            </h1>
        </header>
        
        <form class="search-filters card" method = 'get'>
            <div class="filter-header">
                <i class="fas fa-filter"></i> فلترة البحث
            </div>
            <div class="filter-row">
                <div class="filter-group">
                    <label for="from-date"><i class="far fa-calendar-alt"></i> من تاريخ:</label>
                    <input type="date" id="from-date" class="form-control" name = 'from_date' value = '<?php echo $fromdate ?>'>
                </div>
                
                <div class="filter-group">
                    <label for="to-date"><i class="far fa-calendar-alt"></i> إلى تاريخ:</label>
                    <input type="date" id="to-date" class="form-control" name = 'to_date' value = '<?php echo date('Y-m-d', strtotime($todate)); ?>'>
                </div>
                
                <div class="filter-actions">
                    <button id="search-btn" class="btn btn-search" type = 'submit'>
                        <i class="fas fa-search"></i> بحث
                    </button>
                    <button id="reset-btn" class="btn btn-reset" type = 'button'>
                        <i class="fas fa-undo"></i> إعادة تعيين
                    </button>
                </div>
            </div>
        </form>
        
        <div class="table-container card">
            <div class="table-responsive" style = "display: none;">
                <table id="products-table" class="data-table" >
                    <thead>
                        <tr>
                            <th>المنتج </th>
                            <th>
                                تاريخ البيع
                                <span class="sort-icon date-sort" data-sort="date">
                                    <i class="fas fa-sort"></i>
                                </span>
                            </th>
                            <th>إجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($user_products) == 0) {?>
                        <td colspan="3">
                            <div class="empty-state">
                                <i class="fas fa-box-open"></i>
                                <p>لا توجد منتجات مسجلة</p>
                            </div>
                        </td>
                        <?php } else { ?>
                        <?php foreach ($user_products as $product) {?>
                            <?php 
                            $item = $this->Store->get_store_item(['item_code' => $product['item_code'], 'serial_number' => $product['serial_number'], 'barcode' => $product['barcode']])
                            ?>
                            <tr class = 'product' id = '<?=$product['id']?>'>
                                <td>
                                    <div class="product-info">
                                        <span class="product-name"><?=$item['item_description']?></span>
                                        <span class="product-serial"><?=$product['serial_number']?></span>
                                    </div>
                                </td>
                                <td class = 'product-date' style = 'direction: ltr; text-align: right'><?=formatDate($product['date_created'])?></td>
                                <td>
                                    <div class="action-buttons">
                                        <button class="action-btn btn-confirm" data-id="<?=$product['id']?>">
                                            <i class="fas fa-check"></i> تأكيد
                                        </button>
                                        <button class="action-btn btn-return" data-id="<?=$product['id']?>">
                                            <i class="fas fa-undo"></i> إرجاع
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php }} ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
</div>
		
<!-- JS -->
<script src="https://code.jquery.com/jquery-3.6.0.js" crossorigin="anonymous"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

<script src="<?php echo base_url(); ?>public/js/script.js?a=<?php $date=date_create();echo date_timestamp_get($date);?>"></script>
<script src="<?php echo base_url(); ?>public/js/home.js?a=<?php $date=date_create();echo date_timestamp_get($date);?>"></script>
<script src="<?php echo base_url(); ?>public/js/employee_products_table.js?a=<?php $date=date_create();echo date_timestamp_get($date);?>"></script>
<?php $this->view('view_footer'); ?>