<title>الموظف <?php echo safe_data($employee['user_fillname']); ?></title>
<?php $this->view('view_header'); ?>
 <?php
$CI =& get_instance();
$CI->load->model('Model_admin');
//$segment = $CI->uri->segment('3');

?>

	<?php $this->view('view_admin_sidebar'); ?>

  
  <div class="app-content">
    <?php 
    if($employee){
      ?>  
    <div class="app-content-header">
        <h1 class="app-content-headerText">الموظف <?php echo safe_data($employee['user_fillname']); ?></h1>
      </div>
      <div class="prod_info">
        <ul class="list-unstyled links_stng">
          <li class="active" data-sec=".t1_section">اليوزرات</li>
          <li data-sec=".t2_section">العهد</li>
          <li data-sec=".t3_section">العجوزات</li>
          <li data-sec=".t4_section">الأجهزة المباعة</li>
          <li data-sec=".t5_section">البيانات</li>
        </ul>
        <div class="all_sections">
          <div class="sec t1_section">
          <?php echo form_open(base_url().MOD_VALUE.'index.php/admin/edit_emp_users',array('class' => '')); ?>
          <input type="hidden" name="user_id" value="<?php echo safe_data($employee['user_id']); ?>" >    
          <div class="inpt">
                <label>يوزر تواصل</label>
                <input type="text" name="user_twasol" value="<?php echo safe_data($employee['user_twasol']); ?>" placeholder="أدخل هنا">
              </div>
              <div class="inpt">
                <label>يوزر الشحن</label>
                <input type="text" name="user_shahn" value="<?php echo safe_data($employee['user_shahn']); ?>" placeholder="أدخل هنا">
              </div>
              <div class="inpt">
                <label>يوزر الجوي</label>
                <input type="text" name="user_jawwi" value="<?php echo safe_data($employee['user_jawwi']); ?>" placeholder="أدخل هنا">
              </div>
              <div class="inpt">
                <label>يوزر كوبيك بلس</label>
                <input type="text" name="user_cubic_plus" value="<?php echo safe_data($employee['user_cubic_plus']); ?>" placeholder="أدخل هنا">
              </div>
              <button type="submit" name="empusersform" class="btn">حفظ</button>
              <?php echo form_close(); ?>
          </div>
          <div class="sec t2_section">
            <button class="b1" data-class="aj">الأجهزة</button>
            <button class="b2" data-class="cha">الشرائح</button>
            <button class="add-ohda">اضافة عهد</button>
            <div class="tt2 aj">
              <h3>الأجهزة</h3>
              <?php
                $i=0;
                foreach (array_reverse($custody_devices) as $custody_device) {
                  $i++;
                  $item = $this->Store->get_store_item(['item_code' => $custody_device['item_code']]);
                  $custody_device['barcode'] = $custody_device['barcode'] != null ? $custody_device['barcode'] : 'باركود غير محدد';
                  ?>
                  
                  <div class="d" id = '<?=$custody_device['id']?>'>
                    <span title="<?= safe_data($item['item_description']) ?>">
                        <?php 
                        echo shortenString(safe_data($item['item_description']), 50);;
                        ?>
                    </span>

                    
                    <span><?php echo safe_data($item['serial_control'] == 'yes' ? $custody_device['serial_number'] : $custody_device['barcode']); ?></span>
                    <span><?php echo safe_data(date_format(date_create($custody_device['date_created']), 'd-m-Y h:i:s A')); ?></span>
                    <button class = 'del-btn'>إرجاع</button>
                  </div>
              <?php } ?>
              
            </div>
            <div class="tt2 cha">
              <h3>الشرائح</h3>
              <?php
              $i=0;
              foreach ($custody_cards as $custody_card) {
                $i++;
                $item = $this->Store->get_store_item(['item_code' => $custody_card['item_code']]);
                $custody_card['barcode'] = $custody_card['barcode'] != null ? $custody_card['barcode'] : 'باركود غير محدد';
              ?>
              <div class="d" id = '<?=$custody_card['id']?>'>
                <span title="<?= safe_data($item['item_description']) ?>">
                    <?php 
                    echo shortenString(safe_data($item['item_description']), 50);;
                    ?>
                </span>

                
                <span><?php echo safe_data($item['serial_control'] == 'yes' ? $custody_card['serial_number'] : $custody_card['barcode']); ?></span>
                <span><?php echo safe_data(date_format(date_create($custody_card['date_created']), 'd-m-Y h:i:s A')); ?></span>
                <button class = 'del-btn'>إرجاع</button>
              </div>
              <?php } ?>   
          
            </div>
          </div>
          <div class="sec t3_section">
            <div class="app-content-actions">
              <input type="text" class="search-bar" name="dates">
            </div>
            <div class="products-area-wrapper tableView">
              <div class="products-header">
                <div class="product-cell date">التاريخ</div>
                <div class="product-cell total">الاجمالي</div>
                <div class="product-cell diff">الفارق</div>
                <div class="product-cell actions"></div>
              </div>
              <div class="products-row">
                <div class="product-cell date">
                  <span>04/10/2024</span>
                </div>
                <div class="product-cell total">
                  <span>2562</span>
                </div>
                <div class="product-cell diff"><span class="minus">-560</span></div>
                <div class="product-cell actions">
                  <button class="btn add-tswya" onclick="tswya(this)" data-total="-560">تسوية</button>
                </div>
              </div>
              <div class="products-row">
                <div class="product-cell date">
                  <span>04/10/2024</span>
                </div>
                <div class="product-cell total">
                  <span>520</span>
                </div>
                <div class="product-cell diff"><span class="plus">+520</span></div>
                <div class="product-cell actions">
                  <button class="btn add-tswya" onclick="tswya(this)" data-total="520">تسوية</button>
                </div>
              </div>
            </div>
          </div>
          <div class="sec t4_section">
            <div class="d">
              <span>جهاز 1</span>
              <span>54q6s654s654f6dsf6</span>
            </div>
            <div class="d">
              <span>جهاز 1</span>
              <span>54q6s654s654f6dsf6</span>
            </div>
            <div class="d">
              <span>جهاز 1</span>
              <span>54q6s654s654f6dsf6</span>
            </div>
            <div class="d">
              <span>جهاز 1</span>
              <span>54q6s654s654f6dsf6</span>
            </div>
            <div class="d">
              <span>جهاز 1</span>
              <span>54q6s654s654f6dsf6</span>
            </div>
          </div>
          <div class="sec t5_section">
            <?php echo form_open(base_url().MOD_VALUE.'index.php/admin/edit_emp_info',array('class' => '')); ?>
            <input type="hidden" name="user_id" value="<?php echo safe_data($employee['user_id']); ?>" >    
              <div class="inpt">
                <label>اسم الموظف</label>
                <input type="text" name="user_fillname" value="<?php echo safe_data($employee['user_fillname']); ?>">
              </div>
              <div class="inpt">
                <label>البريد الالكتروني</label>
                <input type="text" name="user_email" value="<?php echo safe_data($employee['user_email']); ?>" />
              </div>
              <div class="inpt">
                <label>اسم الدخول</label>
                <input type="text" name="user_name" value="<?php echo safe_data($employee['user_name']); ?>">
              </div>
              <div class="inpt">
                <label>الرقم التوظيفي</label>
                <input type="text" name="job_number" value="<?php echo safe_data($employee['job_number']); ?>">
              </div>
              <button class="btn" name="empinfoform">تعديل</button>
            </form>
          </div>
        </div>
      </div>

      <?php
      }
      ?>
    </div>
    <!-- Add Ohda -->
      <div class="popup popup-add-ohda">
        <div class="add_task-content popup-content">
          <div class="title">
            <h3>اضافة عهدة</h3> 
            <div class="close"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512"><path d="M342.6 150.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L192 210.7 86.6 105.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L146.7 256 41.4 361.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0L192 301.3 297.4 406.6c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L237.3 256 342.6 150.6z"/></svg></div>
          </div>
          <?php echo form_open(base_url().MOD_VALUE.'index.php/admin/add_user_custody',array('class' => '')); ?>
          <input type="hidden" name="user_id" value="<?php echo safe_data($employee['user_id']); ?>" >    
          <div class="inpt">
              <label>نوع العهدة</label>
              <select name="custody_type" style="width:100%;padding:6px">
                <option value="">اختر النوع</option>
                <option value="1">جهاز</option>
                <option value="2">شريحة</option>
              </select>
            </div>
            
            <div class = 'formGroup'>
                <div class = 'inpt'>
                    <label>أختر السيريالات</label>
                    <input type = 'text' id = 'SerialInputAdder' autocomplete="off">
                </div>
                <button id = 'serialAdder' type="button">إضافة سيريال</button>
            </div>
            <div id = 'serials'>
            </div>

            <h2><span class = 'serials_number'>0</span></h2>
            
            <button type="submit" name="addcustodyform" class="btn">اضافة</button>
            <?php echo form_close(); ?>
        </div>
      </div>
    <!-- Tswya -->
    <div class="popup popup-Tswya">
      <div class="add_task-content popup-content">
        <div class="title">
          <h3>تسويه</h3> 
          <div class="close"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512"><path d="M342.6 150.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L192 210.7 86.6 105.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L146.7 256 41.4 361.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0L192 301.3 297.4 406.6c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L237.3 256 342.6 150.6z"/></svg></div>
        </div>
        <form>
          <div class="inpt">
            <label>المبلغ</label>
            <input type="text" placeholder="أدخل التسويه هنا" class="hr1">
          </div>
          <div class="inpt">
            <label>الاجمالي</label>
            <input type="text" value="540" readonly class="hr2">
          </div>
          <button>تاكيد التسويه</button>
        </form>
      </div>
    </div>

  </div>
  
<style>

    .formGroup {
        display: flex;
        flex-direction: column;
        margin-bottom: 20px;
    }

    .formGroup button {
        margin: 0px !important;
        width: 100% !important;
    }

    .formGroup .inpt input {
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
  
 <!-- JS -->
  <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
  <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

  <script src="<?php echo base_url(); ?>public/js/script.js?a=<?php $date=date_create();echo date_timestamp_get($date);?>"></script>
<script src="<?php echo base_url(); ?>public/js/employees.js?a=<?php $date=date_create();echo date_timestamp_get($date);?>"></script>


<script>

function search() {
  var input = document.getElementById("Search");
  var filter = input.value.toLowerCase();
  var nodes = document.getElementsByClassName('products-row');

  for (i = 0; i < nodes.length; i++) {
    if (nodes[i].innerText.toLowerCase().includes(filter)) {
      nodes[i].style.display = "flex";
    } else {
      nodes[i].style.display = "none";
    }
  }
}
// Switch links
$(".links_stng li").click(function () {
      $(this).addClass("active").siblings().removeClass("active");
      $(".all_sections > div").hide();
      $($(this).data("sec")).fadeIn();
    });
    // Switching
    $(".t2_section button.b1").click(function () {
      $(".aj").fadeIn();
      $(".cha").hide();
    });
    $(".t2_section button.b2").click(function () {
      $(".cha").fadeIn();
      $(".aj").hide();
    });
    $(".add-ohda").click(function(){
      $(".popup-add-ohda").fadeIn();
    })
    function tswya(e){
      $(".popup-Tswya").fadeIn();
      document.querySelector(".popup-Tswya .hr2").value=e.dataset.total;
    }
</script>
<?php $this->view('view_footer'); ?>
