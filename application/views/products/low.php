<title>المنتجات منخفضة الكمية</title>
<?php $this->load->view('view_header'); ?>
<?php $this->load->view('view_admin_sidebar');?>

<div class="app-content">
  <div class="app-content-header">
    <h1 class="app-content-headerText">المنتجات منخفضة الكمية</h1>
  </div>
  <div class="app-content-actions">
    <input id = 'Search' onkeyup = 'search()' class="search-bar" placeholder="النوع" type="text">
  </div>
    
    <div class="products-area-wrapper tableView">
        <div class="products-header">
          <div class="product-cell image">المنتج</div>
          <div class="product-cell category">تصنيف المنتج</div>
          <div class="product-cell last_updated">أخر تحديث</div>
          <div class="product-cell actions"></div>
        </div>
        <?php foreach ($low_products as $product) { ?>
            <div class = 'products-row' id = '<?=$product['id']?>'>
                <div class = 'product-cell image'>
                    <img src = '<?=base_url("public/img/products/default_product_image.png")?>' alt = 'product'>
                    <a href = '<?=base_url()?>admin/products/edit?item_code=<?=$product['item_code']?>'><?=$product['item_description']?></a>
                </div>

                <div class = 'product-cell category'><span class="cell-label">تصنيف المنتج</span><?=$product['item_category']?></div>
                <div class = 'product-cell last_updated'><span class="cell-label">آخر تحديث</span><?=$product['updated_at']?></div>
                <div class="product-cell actions">
                </div>
            </div>
        <?php } ?>
    </div>
</div>
</div>


<!-- JS -->
<script src="https://code.jquery.com/jquery-3.6.0.js" crossorigin="anonymous"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

<script src="<?php echo base_url(); ?>public/js/script.js?a=<?php $date=date_create();echo date_timestamp_get($date);?>"></script>

<?php $this->load->view('view_footer'); ?>