<title>الرف</title>
<?php $this->load->view('view_header'); ?>
<?php $this->load->view('view_admin_sidebar');?>

<div class = 'app-content'>
    <div class = 'app-content-header'>
        <h1 class = 'app-content-headerText'>الرف</h1>
    </div>
    <div class = 'app-content-actions'>
        <input id = 'Search' onkeyup = 'search()' class = 'search-bar' placeholder = 'بحث...' type = 'text'>
        <div>
            <button class = 'app-content-headerButton orderShelf'>اعادة ترتيب الرف</button>
            <button class = 'app-content-headerButton'>اضافة الرف</button>
        </div>
        
    </div>
    <div class = 'products-area-wrapper tableView'>
        <div class="products-header">
            <div class="product-cell num">رقم الرف</div>
            <div class="product-cell q">العدد</div>
            <div class="product-cell actions"></div>
        </div>
        <?php foreach ($shelves as $shelf) {?>
            <div class = 'products-row' id = '<?=$shelf->id?>'>
                <div class = 'product-cell num'><span class = 'cell-label'>رقم الرف</span> <?=$shelf->shelf_number?></div>
                <div class = 'product-cell q'>
                    <span class = 'cell-label'>العدد</span>
                    <?php
                    echo $this->Store->get_items_count_in_shelf($shelf->id);
                    ?>
                </div>
                <div class="product-cell actions">
                    <button class="show-btn"><a href = './shelves/show?id=<?=$shelf->id?>'>عرض المنتجات</a></button>
                    <button class="del-btn">حذف</button>
                </div>
            </div>
        <?php } ?>
    </div>
    <div class="popup popup-add-shelf" style="display: none;">
        <div class="add_task-content popup-content">
            <div class="title">
                <h3>اضافة رف جديدة</h3> 
                <div class="close"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512"><path d="M342.6 150.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L192 210.7 86.6 105.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L146.7 256 41.4 361.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0L192 301.3 297.4 406.6c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L237.3 256 342.6 150.6z"></path></svg></div>
            </div>
            <form method = 'post'>
                <div class="inpt">
                    <label>رقم الرف</label>
                    <input name = 'shelf_num' type="text" placeholder="رقم الرف">
                </div>
                <button>اضافة</button>
                <input type = 'hidden' name = 'action' value = 'add'>
            </form>
        </div>
    </div>
    <div class="popup popup-movein-serials" style="display: none;">
        <div class="popup-shelves-content popup-content">
          <div class="title">
            <h3>اعادة ترتيب الرف</h3> 
            <div class="close"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512"><path d="M342.6 150.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L192 210.7 86.6 105.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L146.7 256 41.4 361.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0L192 301.3 297.4 406.6c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L237.3 256 342.6 150.6z"></path></svg></div>
          </div>
          <form method = 'post'>
            <label id = 'formTitleShelf'>الرف</label>
            <div class = 'inpt'>
                <select name = 'shelf_id' id = 'shelves'>
                    <?php 
                    foreach ($shelves as $shelf) {
                        $shelfnumber = $shelf->shelf_number;
                        $shelfid = $shelf->id;
                        echo "<option value = '$shelfid'>$shelfnumber</option>";
                    }
                    ?>
                </select>
                <script>
                    $(document).ready(() => {
                        $('#shelves').select2()
                    })
                </script>
            </div>
            <label id = 'formTitle'>أختر السيريالات</label>
            <div class = 'formHeader'>
                <div class = 'inpt'>
                    <div style = 'display: flex;gap: 20px'>
                        <input style = 'flex: 5' type = 'text' id = 'SerialInputAdder' autocomplete="off">
                        <input style = 'flex: 1' type = 'text' id = 'PosterInputAdder' autocomplete="off">
                    </div>
                </div>
                <button id = 'serialAdder' type="button">إضافة سيريال</button>
            </div>
            <div id = 'serials'>
            </div>

            <input type = 'hidden' name = 'action' value="rearrange_shelf">
            <h2><span class = 'serials_number'>0</span></h2>
            <button style = 'width: 100%' type = 'button' onclick="send_change_request()">حفظ</button>
          </form>
        </div>
    </div>
</div>
</div>

<style>

.formHeader {
    display: flex;
    flex-direction: column;
    margin-bottom: 20px;
}

.formHeader button {
    margin: 0px !important;
    width: 100% !important;
}

.formHeader .inpt input {
    font-size: 20px !important;
}

#serials {
    display: flex;
    flex-direction: column-reverse;
    gap: 5px;
    overflow: auto;
    height: fit-content;
    max-height: 150px;
}

.serial {
    display: flex;
    gap: 10px;
    justify-content: space-between;
    align-items: center;
    font-size: 20px;
    width: fit-content;
    background-color: rgb(255, 255, 255);
    border-radius: 5px;
    padding: 0px;
    height: 40px;
    overflow: hidden;
    color: black;
    border: 1px solid lightblue;
    flex-shrink: 0;
}

.serial span {
    line-height: normal;
    padding: 10px;
    font-weight: normal;
    font-family: Arial, Helvetica, sans-serif;
    border: none;
    outline: none;
}

.serial i {
    font-size: 20px;
    padding: 10px;
    cursor: pointer;
    color: red;
}

.serial i:hover {
    color: rgb(107, 35, 35);
}

</style>
<style>
  .select2-container--open {
      z-index: 99999; /* A very high value */
  }
  
  .select2 {
      width: 100% !important;
      font-size: 20px;
  }

  .select2-selection {
      padding: 5px;
      height: auto !important;
  }
</style>


<script src="https://code.jquery.com/jquery-3.6.0.js" crossorigin="anonymous"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

<script src="<?php echo base_url(); ?>public/js/script.js?a=<?php $date=date_create();echo date_timestamp_get($date);?>"></script>
<script src="<?php echo base_url(); ?>public/js/shelf.js?a=<?php $date=date_create();echo date_timestamp_get($date);?>"></script>

<?php $this->load->view('view_footer'); ?>