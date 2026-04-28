
<?php $this->load->view('view_header'); ?>
<?php $this->load->view('view_admin_sidebar'); ?>

<div class = 'app-content' >
    <div class = 'app-content-header'>
        <h1 class = 'app-content-headerText'>منتجات رف <span><?=$shelf->shelf_number?></span></h1>
        
    </div>
    <div class = 'app-content-actions'>
        <input id = 'Search' onkeyup = 'search()' class = 'search-bar' placeholder = 'بحث...' type = 'text'>
    </div>
    <div class = 'products-area-wrapper tableView'>
        <div class="products-header">
            <div class="product-cell num">اسم المنتج</div>
            <div class="product-cell q">السيريال أو باركود</div>
            <div class="product-cell quantity">الكمية</div>
            <div class="product-cell actions"></div>
        </div>
        <?php foreach ($items as $item) { ?>
            <?php 
            if ($this->Custodys->check_for_serial($item['serial_number'])) {
                continue; // تخطي هذا المنتج إذا كان في الحراسة
            } else if ($this->Requests->get_requests_length(['serial_number' => $item['serial_number']]) > 0) {
                continue; // تخطي هذا المنتج إذا كان في طلبات الانتظار
            }
            
            ?>
            <?php $item_details = $this->Store->get_store_item(['item_code' => $item['item_code'], 'serial_number' => $item['serial_number'], 'barcode' => $item['barcode']]); ?>
            <div class = 'products-row' id = '<?=$item_details['id']?>'>
                <div class = 'product-cell num'><span class = 'cell-label'>اسم المنتج</span> <?=$item_details['item_description']?></div>
                <div class = 'product-cell q'><span class = 'cell-label'>نوع التحكم</span><?=$item_details['serial_control'] == 'yes' ? $item['serial_number'] : $item['barcode']?></div>
                <div class = 'product-cell note'><span class = 'cell-label'>الكمية</span><?=$item['quantity_total']?></div>
                <div class="product-cell actions">
                    <button class = 'del-btn'>ازالة من الرف</button>
                </div>
            </div>
        <?php } ?>
    </div>
    <div class="popup popup-remove-serial" style="display: none;">
        <div class="popup-shelves-content popup-content">
          <div class="title">
            <h3>ازالة السيريال من الرف</h3> 
            <div class="close"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512"><path d="M342.6 150.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L192 210.7 86.6 105.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L146.7 256 41.4 361.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0L192 301.3 297.4 406.6c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L237.3 256 342.6 150.6z"></path></svg></div>
          </div>
          <form method = 'post'>
            <label id = 'formTitle'>هل تريد إزالته من الرف ؟</label>
            <input type = 'hidden' name = 'action' value="delete">
            <input type = 'hidden' name = 'itemid' id = 'itemid' value = "">
            <button>إزالة من الرف</button>
          </form>
        </div>
    </div>
</div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.js" crossorigin="anonymous"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

<script src="<?php echo base_url(); ?>public/js/script.js?a=<?php $date=date_create();echo date_timestamp_get($date);?>"></script>
<script src="<?php echo base_url(); ?>public/js/shelf_products.js?a=<?php $date=date_create();echo date_timestamp_get($date);?>"></script>


<?php $this->load->view('view_footer'); ?>