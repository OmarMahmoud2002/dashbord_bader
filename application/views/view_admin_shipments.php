<title>الشحنات</title>
<?php $this->load->view('view_header'); ?>
<?php $this->load->view('view_admin_sidebar');?>

<div class="app-content">
  <div class="app-content-header">

    <h1 class="app-content-headerText">الشحنات</h1>
    <div>
      <button class="app-content-headerButton" onclick = 'show_popup()'>اضافة شحنة</button>
    </div>
  </div>
  <div class="app-content-actions">
    <input id = 'SearchPack' onkeyup = 'search_by_pack_number()' class="search-bar" placeholder="بحث" type="text">
  </div>
  <div class="products-area-wrapper tableView">
    <div class="products-header">
      <div class="product-cell ship_num">رقم الشحنة</div>
      <div class="product-cell nums">عدد الأكياس</div>
      <div class="product-cell date">تاريخ الشحنة</div>
      <div class="product-cell actions"></div>
    </div>
    <?php foreach ($shipments as $shipment) { ?>
        <div class="products-row" id = '<?=$shipment->id?>' data-packs = '<?=$shipment->packs?>'>
          <div class="product-cell ship_num"><span class="cell-label">رقم الشحنة</span><?=$shipment->shipment_number?></div>
          <div class="product-cell nums"><span class="cell-label">عدد الأكياس</span><?=$shipment->packs_number?></div>
          <div class="product-cell date"><span class="cell-label">تاريخ الشحنة</span><?=date_format(date_create($shipment->date_created), 'd/m/Y h:i:s A')?></div>
          <div class="product-cell actions">
            <button class="show-btn">رؤية الأكياس</button>
            <button class='del-btn'>حذف الشحنة</button>
          </div>
        </div>
    <?php } ?>
  </div>
  <!-- Add Shipment -->
  <div class="popup popup-add-shipment">
    <div class="add_task-content popup-content">
      <div class="title">
        <h3>اضافة شحنة جديدة</h3> 
        <div class="close"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512"><path d="M342.6 150.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L192 210.7 86.6 105.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L146.7 256 41.4 361.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0L192 301.3 297.4 406.6c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L237.3 256 342.6 150.6z"></path></svg></div>
      </div>
      <form method = 'post'>
        <div class="inpt">
          <label>رقم الشحنة</label>
          <input name = 'shipment_number' type="text" placeholder="رقم الشحنة">
        </div>
        <div class="inpt">
          <label>عدد الأكياس</label>
          <input name = 'packs_number' id = 'packsnum' type="text" placeholder="عدد الأكياس">
        </div>
        <label>رقم الكيس</label>
        <div class = 'packs'>

        </div>
        <input type = 'hidden' name = 'action' value = 'add_shipment'>
        <button>اضافة</button>
      </form>
    </div>
  </div>
    <!-- View Shipment Packs-->
  <div class="popup popup-view-shipment">
    <div class="add_task-content popup-content">
      <div class="title">
        <h3>رؤية الأكياس الخاصة بالشحنة</h3> 
        <div class="close"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512"><path d="M342.6 150.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L192 210.7 86.6 105.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L146.7 256 41.4 361.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0L192 301.3 297.4 406.6c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L237.3 256 342.6 150.6z"></path></svg></div>
      </div>
      <form>
        <div class = 'packs_view'>

        </div>
      </form>
      
    </div>
  </div>
</div>
</div>

<style>
  .pack_container {
    padding: 5px;
    border-radius: 13px;
    border: 1px solid grey;
    display: flex;
    justify-content: center;
    align-items: center;
    font-size: 23px;
    margin-bottom: 20px;
  }
  
  .pack_container * {
    height: fit-content;
  }
</style>


<!-- JS -->
<script src="https://code.jquery.com/jquery-3.6.0.js" crossorigin="anonymous"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

<script src="<?php echo base_url(); ?>public/js/script.js?a=<?php $date=date_create();echo date_timestamp_get($date);?>"></script>
<script src="<?php echo base_url(); ?>public/js/shipments.js?a=<?php $date=date_create();echo date_timestamp_get($date);?>"></script>

<?php $this->load->view('view_footer'); ?>