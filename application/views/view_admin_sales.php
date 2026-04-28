<title>المبيعات</title>
<?php $this->view('view_header'); ?>
<?php $this->view('view_admin_sidebar'); ?>

<div class = 'app-content'>
    <div class="app-content-header">
      <h1 class="app-content-headerText">المبيعات</h1>
    </div>
    <div class="app-content-actions">
      <input class="search-bar" id = 'Search' onkeyup = 'search()' placeholder="بحث..." type="text">
      <form>
        <input type="text" class = 'search-bar' name = 'dates'>
      </form>
    </div>
    <div class="products-area-wrapper tableView">
      <div class="products-header">
        <div class="product-cell category">الصنف</div>
        <div class="product-cell emp">الموظف</div>
        <div class="product-cell prc">السعر</div>
        <div class="product-cell date">تاريخ البيع</div>
        <div class="product-cell actions"></div>
      </div>
      <?php foreach ($sales as $sale) { ?>
        <?php 
        $custodies = $this->Custodys->get_custodys(['id' => $sale->custody_id]);

        if ($custodies) {
            $custody = $custodies[0];
        }

        if ($custody) {
            $item = $this->Store->get_store_item(['item_code' => $custody['item_code'], 'serial_number' => $custody['serial_number'], 'barcode' => $custody['barcode']]);
        }

        $product_details = $this->Store->get_store_item(['item_code' => $custody['item_code']]);

        $employee = $this->Model_admin->get_user_by_id($sale->user_id);

        ?>
        <div class="products-row" id = '<?=$sale->id?>'>
            <div class = 'product-cell category'>
                <span class = 'cell-label'>الصنف</span>
                <?php if ($item) { ?>
                    <p><?=$item['item_category']?></p>
                <?php } else { ?>
                    <p>غير موجود</p>
                <?php }?>
                
                
            </div>
            <div class="product-cell emp">
                <span class = 'cell-label'>الموظف</span>
                <p><?=$employee['user_fillname']?></p>
            </div>
            <div class="product-cell prc">
                <span class = 'cell-label'>السعر</span>
                <?php if ($product_details) {?>
                    <p><?=$product_details['price']?></p>
                <?php } else { ?>
                    <p>غير موجود</p>
                <?php } ?>
            </div>
            <div class="product-cell date">
                <span class="cell-label">تاريخ البيع</span>
                <p><?=date_format(date_create($sale->date_created), 'd/m/Y h:i:s A')?></p>
            </div>
            <div class="product-cell actions"></div>
        </div>
      <?php } ?>
    </div>
</div>
</div>


<script src="https://code.jquery.com/jquery-3.6.0.js" crossorigin="anonymous"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

<script src="<?php echo base_url(); ?>public/js/script.js?a=<?php $date=date_create();echo date_timestamp_get($date);?>"></script>
<script src="<?php echo base_url(); ?>public/js/sales.js?a=<?php $date=date_create();echo date_timestamp_get($date);?>"></script>
<?php $this->load->view('view_footer'); ?>