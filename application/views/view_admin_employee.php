<title>الموظف <?php echo safe_data($employee['user_fillname']); ?></title>
<?php $this->view('view_header'); ?>
 <?php
$CI =& get_instance();
$CI->load->model('Model_admin');
//$segment = $CI->uri->segment('3');

?>

	<?php $this->view('view_admin_sidebar'); ?>

  
  <div class="app-content admin-ui-page admin-ui-people">
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
            <!-- Add New Feature -->
            <?php
              $sold_devices = isset($sold_devices) && is_array($sold_devices) ? $sold_devices : [];
              $sold_devices_total = 0;
              foreach ($sold_devices as $sold_device) {
                // Add New Feature
                $sold_devices_total += (float) $sold_device['insert_excel_sales_amount'];
                // End
              }
            ?>
            <div class="employee-sold-devices">
              <div class="employee-sold-devices__summary">
                <div>
                  <span>الأجهزة المباعة</span>
                  <strong><?php echo number_format(count($sold_devices)); ?></strong>
                </div>
                <div>
                  <!-- Add New Feature -->
                  <span>إجمالي مبلغ المبيعات</span>
                  <!-- End -->
                  <strong><?php echo number_format($sold_devices_total, 2); ?></strong>
                </div>
              </div>

              <?php if (count($sold_devices) > 0): ?>
                <!-- Add New Feature -->
                <div class="employee-sold-devices__filters">
                  <div class="employee-sold-devices__search">
                    <i class="bi bi-search"></i>
                    <input type="search" id="soldDevicesOrderSearch" placeholder="بحث برقم الطلب" autocomplete="off">
                  </div>
                  <div class="employee-sold-devices__search employee-sold-devices__date-filter">
                    <i class="bi bi-calendar3"></i>
                    <input type="date" id="soldDevicesDateSearch" aria-label="بحث بالتاريخ">
                  </div>
                </div>
                <!-- End -->
                <div class="products-area-wrapper tableView employee-sold-devices__table">
                  <div class="products-header">
                    <!-- Add New Feature -->
                    <div class="product-cell sold-date">التاريخ</div>
                    <!-- End -->
                    <div class="product-cell sold-order">رقم الطلب</div>
                    <!-- Add New Feature -->
                    <div class="product-cell sold-type">النوع</div>
                    <!-- End -->
                    <div class="product-cell sold-description">الوصف</div>
                    <div class="product-cell sold-serial">PRODUCT SERIAL NUMBER</div>
                    <!-- Add New Feature -->
                    <div class="product-cell sold-payment">مبلغ المبيعات</div>
                    <!-- End -->
                  </div>
                  <?php foreach ($sold_devices as $sold_device): ?>
                    <?php
                      $display_order_number = !empty($sold_device['insert_excel_new_ordern']) ? $sold_device['insert_excel_new_ordern'] : $sold_device['insert_excel_ordern'];
                      // Add New Feature
                      $sales_type = !empty($sold_device['insert_excel_sales_type']) ? $sold_device['insert_excel_sales_type'] : '-';
                      // End
                      $description = !empty($sold_device['insert_excel_description']) ? $sold_device['insert_excel_description'] : '-';
                      $serial_number = !empty($sold_device['insert_excel_product_serial_number']) ? $sold_device['insert_excel_product_serial_number'] : '-';
                      // Add New Feature
                      $sold_date_timestamp = !empty($sold_device['insert_excel_date']) ? strtotime($sold_device['insert_excel_date']) : false;
                      $sold_date = $sold_date_timestamp ? date('Y-m-d', $sold_date_timestamp) : '-';
                      // End
                    ?>
                    <!-- Add New Feature -->
                    <div class="products-row" data-order-number="<?php echo htmlspecialchars((string) $display_order_number, ENT_QUOTES, 'UTF-8'); ?>" data-sold-date="<?php echo htmlspecialchars((string) $sold_date, ENT_QUOTES, 'UTF-8'); ?>">
                    <!-- End -->
                      <!-- Add New Feature -->
                      <div class="product-cell sold-date">
                        <span class="cell-label">التاريخ</span>
                        <span dir="ltr"><?php echo htmlspecialchars((string) $sold_date, ENT_QUOTES, 'UTF-8'); ?></span>
                      </div>
                      <!-- End -->
                      <div class="product-cell sold-order">
                        <span class="cell-label">رقم الطلب</span>
                        <span><?php echo htmlspecialchars((string) $display_order_number, ENT_QUOTES, 'UTF-8'); ?></span>
                      </div>
                      <!-- Add New Feature -->
                      <div class="product-cell sold-type">
                        <span class="cell-label">النوع</span>
                        <span><?php echo htmlspecialchars((string) $sales_type, ENT_QUOTES, 'UTF-8'); ?></span>
                      </div>
                      <!-- End -->
                      <div class="product-cell sold-description">
                        <span class="cell-label">الوصف</span>
                        <span><?php echo htmlspecialchars((string) $description, ENT_QUOTES, 'UTF-8'); ?></span>
                      </div>
                      <div class="product-cell sold-serial">
                        <span class="cell-label">PRODUCT SERIAL NUMBER</span>
                        <span dir="ltr"><?php echo htmlspecialchars((string) $serial_number, ENT_QUOTES, 'UTF-8'); ?></span>
                      </div>
                      <div class="product-cell sold-payment">
                        <!-- Add New Feature -->
                        <span class="cell-label">مبلغ المبيعات</span>
                        <span dir="ltr"><?php echo number_format((float) $sold_device['insert_excel_sales_amount'], 2); ?></span>
                        <!-- End -->
                      </div>
                    </div>
                  <?php endforeach; ?>
                </div>
                <!-- Add New Feature -->
                <div class="employee-sold-devices__no-results" id="soldDevicesNoResults">
                  لا توجد نتائج مطابقة للفلاتر المحددة
                </div>
                <div class="employee-sold-devices__pagination" id="soldDevicesPagination">
                  <button type="button" id="soldDevicesPrevPage">السابق</button>
                  <span id="soldDevicesPageInfo"></span>
                  <button type="button" id="soldDevicesNextPage">التالي</button>
                </div>
                <!-- End -->
              <?php else: ?>
                <div class="empty-admin-state employee-sold-devices__empty">
                  <i class="bi bi-box-seam"></i>
                  <p>لا توجد أجهزة مباعة لهذا الموظف حتى الآن</p>
                </div>
              <?php endif; ?>
            </div>
            <!-- End -->
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

    /* Add New Feature */
    .employee-sold-devices {
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    .employee-sold-devices__summary {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 12px;
    }

    .employee-sold-devices__summary > div {
        border: 1px solid rgba(226, 232, 240, 0.9);
        border-radius: 14px;
        background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
        box-shadow: 0 10px 25px rgba(15, 23, 42, 0.07);
        padding: 16px;
    }

    .employee-sold-devices__summary span {
        display: block;
        margin-bottom: 8px;
        color: #718096;
        font-size: 13px;
        font-weight: 700;
    }

    .employee-sold-devices__summary strong {
        display: block;
        color: #2d3748;
        font-size: 22px;
        line-height: 1.2;
        direction: ltr;
        text-align: right;
    }

    .employee-sold-devices__filters {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        align-items: center;
    }

    .employee-sold-devices__search {
        position: relative;
        max-width: 360px;
        flex: 1 1 240px;
    }

    .employee-sold-devices__search i {
        position: absolute;
        top: 50%;
        right: 14px;
        transform: translateY(-50%);
        color: #667eea;
        font-size: 15px;
        pointer-events: none;
    }

    .employee-sold-devices__search input {
        width: 100%;
        height: 44px;
        border: 1px solid rgba(226, 232, 240, 0.95);
        border-radius: 12px;
        background: #fff;
        color: #2d3748;
        box-shadow: 0 8px 20px rgba(15, 23, 42, 0.06);
        padding: 0 42px 0 14px;
        font-size: 14px;
        font-weight: 700;
        outline: none;
        transition: border-color 0.2s ease, box-shadow 0.2s ease;
    }

    .employee-sold-devices__search input:focus {
        border-color: rgba(102, 126, 234, 0.55);
        box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.12);
    }

    .employee-sold-devices__date-filter {
        max-width: 220px;
    }

    .employee-sold-devices__table {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    .employee-sold-devices__table .products-header,
    .employee-sold-devices__table .products-row {
        display: grid;
        /* Add New Feature */
        grid-template-columns: minmax(112px, 0.65fr) minmax(140px, 0.8fr) minmax(130px, 0.75fr) minmax(220px, 1.5fr) minmax(190px, 1fr) minmax(130px, 0.7fr);
        /* End */
        align-items: center;
    }

    .employee-sold-devices__table .product-cell {
        min-width: 0;
        word-break: break-word;
    }

    .employee-sold-devices__table .sold-description span:last-child {
        line-height: 1.7;
    }

    .employee-sold-devices__table .sold-serial span:last-child {
        color: #4a5568;
        font-family: Arial, Helvetica, sans-serif;
        font-size: 13px;
    }

    /* Add New Feature */
    .employee-sold-devices__table .sold-type span:last-child {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 999px;
        background: rgba(102, 126, 234, 0.12);
        color: #4c51bf;
        font-size: 12px;
        font-weight: 900;
        padding: 7px 10px;
    }
    /* End */

    .employee-sold-devices__table .sold-payment span:last-child {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 92px;
        border-radius: 999px;
        background: rgba(67, 233, 123, 0.15);
        color: #065f46;
        font-weight: 900;
        padding: 7px 10px;
    }

    .employee-sold-devices__empty {
        margin: 0;
    }

    .employee-sold-devices__no-results {
        display: none;
        border: 1px dashed rgba(102, 126, 234, 0.32);
        border-radius: 12px;
        background: #f8fafc;
        color: #718096;
        font-weight: 800;
        padding: 14px;
        text-align: center;
    }

    .employee-sold-devices__pagination {
        display: none;
        align-items: center;
        justify-content: center;
        gap: 12px;
        flex-wrap: wrap;
    }

    .employee-sold-devices__pagination button {
        min-width: 92px;
        border: 0;
        border-radius: 10px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: #fff;
        font-weight: 800;
        padding: 9px 14px;
        cursor: pointer;
        transition: opacity 0.2s ease, transform 0.2s ease;
    }

    .employee-sold-devices__pagination button:hover:not(:disabled) {
        transform: translateY(-1px);
    }

    .employee-sold-devices__pagination button:disabled {
        cursor: not-allowed;
        opacity: 0.45;
    }

    .employee-sold-devices__pagination span {
        color: #4a5568;
        font-size: 13px;
        font-weight: 800;
    }

    @media (max-width: 768px) {
        .employee-sold-devices__summary {
            grid-template-columns: 1fr;
        }

        .employee-sold-devices__filters {
            flex-direction: column;
            align-items: stretch;
        }

        .employee-sold-devices__search {
            max-width: 100%;
            flex-basis: auto;
        }

        .employee-sold-devices__date-filter {
            max-width: 100%;
        }

        .employee-sold-devices__table .products-header {
            display: none;
        }

        .employee-sold-devices__table .products-row {
            display: grid;
            grid-template-columns: 1fr;
            gap: 10px;
            padding: 14px;
        }

        .employee-sold-devices__table .product-cell {
            display: flex;
            justify-content: space-between;
            gap: 14px;
            width: 100%;
            text-align: right;
        }

        .employee-sold-devices__table .product-cell .cell-label {
            display: inline-flex;
            color: #718096;
            font-weight: 800;
            min-width: 118px;
        }
    }
    /* End */

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
// Add New Feature
const soldDevicesOrderSearch = document.getElementById("soldDevicesOrderSearch");
const soldDevicesDateSearch = document.getElementById("soldDevicesDateSearch");
const soldDevicesRows = Array.from(document.querySelectorAll(".employee-sold-devices__table .products-row"));
const soldDevicesNoResults = document.getElementById("soldDevicesNoResults");
const soldDevicesPagination = document.getElementById("soldDevicesPagination");
const soldDevicesPrevPage = document.getElementById("soldDevicesPrevPage");
const soldDevicesNextPage = document.getElementById("soldDevicesNextPage");
const soldDevicesPageInfo = document.getElementById("soldDevicesPageInfo");
const soldDevicesPageSize = 20;
let soldDevicesCurrentPage = 1;

function getFilteredSoldDeviceRows() {
  const orderQuery = soldDevicesOrderSearch ? soldDevicesOrderSearch.value.trim().toLowerCase() : "";
  const dateQuery = soldDevicesDateSearch ? soldDevicesDateSearch.value : "";

  return soldDevicesRows.filter(function (row) {
    const orderNumber = (row.dataset.orderNumber || "").toLowerCase();
    const soldDate = row.dataset.soldDate || "";
    return orderNumber.includes(orderQuery) && (!dateQuery || soldDate === dateQuery);
  });
}

function renderSoldDevicesPage() {
  if (!soldDevicesRows.length) {
    return;
  }

  const filteredRows = getFilteredSoldDeviceRows();
  const totalPages = Math.max(1, Math.ceil(filteredRows.length / soldDevicesPageSize));
  soldDevicesCurrentPage = Math.min(soldDevicesCurrentPage, totalPages);
  const startIndex = (soldDevicesCurrentPage - 1) * soldDevicesPageSize;
  const endIndex = startIndex + soldDevicesPageSize;
  const currentPageRows = filteredRows.slice(startIndex, endIndex);
  const currentPageRowsSet = new Set(currentPageRows);

  soldDevicesRows.forEach(function (row) {
    row.style.display = currentPageRowsSet.has(row) ? "" : "none";
  });

  if (soldDevicesNoResults) {
    soldDevicesNoResults.style.display = filteredRows.length === 0 ? "block" : "none";
  }

  if (soldDevicesPagination) {
    soldDevicesPagination.style.display = filteredRows.length > soldDevicesPageSize ? "flex" : "none";
  }

  if (soldDevicesPrevPage) {
    soldDevicesPrevPage.disabled = soldDevicesCurrentPage <= 1;
  }

  if (soldDevicesNextPage) {
    soldDevicesNextPage.disabled = soldDevicesCurrentPage >= totalPages;
  }

  if (soldDevicesPageInfo) {
    // Add New Feature
    soldDevicesPageInfo.textContent = "صفحة " + soldDevicesCurrentPage + " من " + totalPages + " - " + filteredRows.length + " عملية بيع";
    // End
  }
}

function resetSoldDevicesPage() {
  soldDevicesCurrentPage = 1;
  renderSoldDevicesPage();
}

if (soldDevicesOrderSearch) {
  soldDevicesOrderSearch.addEventListener("input", resetSoldDevicesPage);
}

if (soldDevicesDateSearch) {
  soldDevicesDateSearch.addEventListener("change", resetSoldDevicesPage);
}

if (soldDevicesPrevPage) {
  soldDevicesPrevPage.addEventListener("click", function () {
    soldDevicesCurrentPage--;
    renderSoldDevicesPage();
  });
}

if (soldDevicesNextPage) {
  soldDevicesNextPage.addEventListener("click", function () {
    soldDevicesCurrentPage++;
    renderSoldDevicesPage();
  });
}

renderSoldDevicesPage();
// End
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
