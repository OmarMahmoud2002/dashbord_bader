<title>الطلبات</title>
<?php $this->load->view('view_header'); ?>
<?php $this->load->view('view_admin_sidebar'); ?>

<?php 

$product_status = [1 => 'تم التحقق', 0 => 'لم يتم التحقق'];

?>

<div class = 'app-content'>
    <div class="app-content-header">
      <h1 class="app-content-headerText">الطلبات</h1>
    </div>
    <div class="app-content-actions">
      <input class="search-bar" id = 'Search' onkeyup = 'search()' placeholder="بحث..." type="text">
      <form>
        <input type="text" class = 'search-bar' name = 'dates'>
      </form>
    </div>
    <div class="products-area-wrapper tableView">
      <div class="products-header">
        <div class="product-cell order_number">رقم الطلب</div>
        <div class="product-cell name">اسم الجهاز</div>
        <div class="product-cell">سيريال الجهاز</div>
        <div class="product-cell itemCode">رقم الصنف</div>
        <div class="product-cell date">تاريخ الطلب</div>
        <div class="product-cell actions"></div>
      </div>
      <?php foreach ($requests as $request) { ?>
        <?php $item = $this->Store->get_store_item(['item_code' => $request['item_code'], 'serial_number' => $request['serial_number'], 'barcode' => $request['barcode']]); ?>
        <div class="products-row" id = '<?=$request['id']?>'>
            <div class="product-cell order_number">

                <span class = 'cell-label'>رقم الطلب</span>
                <p><?=$request['order_number']?></p>
            </div>
            <div class="product-cell name">
                <span class = 'cell-label'>اسم الجهاز</span>
                <p><?=$item['item_description']?></p>
            </div>
            <div class="product-cell">
                <span class = 'cell-label'>سيريال الجهاز</span>
                <p><?=$item['serial_number']?></p>
            </div>
            <div class="product-cell itemCode">
                <span class = 'cell-label'>رقم الصنف</span>
                <p><?=$item['item_code']?></p>
            </div>
            <div class="product-cell date">
                <span class="cell-label">تاريخ الطلب</span>
                <p><?=date_format(date_create($request['date_created']), 'd/m/Y')?></p>
            </div>
            <div class="product-cell actions">
              <button class = 'show-btn' onclick = 'product_delivery_return(<?=$request["id"]?>)'>إرجاع</button>
            </div>
        </div>
      <?php } ?>
    </div>
</div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.js" crossorigin="anonymous"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

<script src="<?php echo base_url(); ?>public/js/requests.js?a=<?php $date=date_create();echo date_timestamp_get($date);?>"></script>
<script src="<?php echo base_url(); ?>public/js/script.js?a=<?php $date=date_create();echo date_timestamp_get($date);?>"></script>
<?php $this->load->view('view_footer'); ?>
