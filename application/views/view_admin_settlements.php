<title>الفروقات</title>
<?php


 $this->view('view_header'); ?>


<style>
   
    .positive {
        background-color: green ; 
        color: white;  
    }

 .negative {
        background-color: red; 
        color: white; 
    }
th,td{
    text-align: center;
}

</style>


<?php
$CI =& get_instance();
$CI->load->model('Model_admin');
$CI->load->model('Excel_import_model');
?>

	<?php $this->view('view_admin_sidebar'); ?>
<?php
//print_r($excel);
?>
    <div class="app-content admin-ui-page admin-ui-settlements">
    <div class="app-content-header">
      <h1 class="app-content-headerText">الفروقات</h1>
    </div>
    <div class="app-content-actions">
      <input id="Search" onkeyup="search()" class="search-bar" placeholder="اسم الموظف" type="text">
      <input type="text" class="search-bar" name="dates">
      <div class="app-content-actions-wrapper">
        <button class="action-button list active" title="List View">
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-list"><line x1="8" y1="6" x2="21" y2="6"></line><line x1="8" y1="12" x2="21" y2="12"></line><line x1="8" y1="18" x2="21" y2="18"></line><line x1="3" y1="6" x2="3.01" y2="6"></line><line x1="3" y1="12" x2="3.01" y2="12"></line><line x1="3" y1="18" x2="3.01" y2="18"></line></svg>
        </button>
        <button class="action-button grid" title="Grid View">
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-grid"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>
        </button>
      </div>
    </div>
    <div id="settlementsTable" class="products-area-wrapper tableView">
      <div class="products-header">
        <div class="product-cell name">الاسم</div>
        <div class="product-cell date">تاريخ</div>
        <div class="product-cell total">الاجمالي</div>
        <div class="product-cell diff">الفارق</div>
        <div class="product-cell actions"></div>
      </div>
      <?php

      $users_data = [];
      foreach ($employees as $employee) {
        $users_data[$employee['user_id']] = $employee;
      }

      
      $excel_data = $this->Excel_import_model->selectall();
      foreach ($excel_data as $row) {
        $user_id = $row['insert_excel_uid'];

        
        if (array_key_exists($user_id, $users_data)) {
          

          $userlock = $this->Model_admin->getlock_by_userid($user_id, $row['insert_excel_date'], $row['insert_excel_date']);
          $settlement = $this->Model_admin->get_settlement($user_id, $row['insert_excel_date'], $row['insert_excel_date']);
          if ($userlock !== NULL && !empty($userlock)) {
            $network_amount = $userlock['user_lock_span'];
            $cash_amount = $userlock['user_lock_cash'];
          } else {
            $network_amount = 0;
            $cash_amount = 0;
          }
          
          $user_settlement_amount = 0;
          if ($userlock !== NULL && !empty($userlock)) {
              foreach ($settlement as $settlement_row) {
                  $user_settlement_amount += (float)($settlement_row['settlement_amount']);
              }
              $user_settlement_amount = round($user_settlement_amount, 2);
          }

          $twasel_sale = (float)($row['insert_excel_twasel']);
          $electronic_sale = (float)($row['insert_excel_electronic']);
          $jowy_sale = (float)($row['insert_excel_jowy']);
          $quickplus_sale = (float)($row['insert_excel_quickplus']);
          
          $total_amount = $twasel_sale + $electronic_sale + $jowy_sale + $quickplus_sale;
          $difference = round(($cash_amount + $network_amount) - $total_amount + $user_settlement_amount, 2);

          if ($difference < 0) {

          ?>
            <div class="products-row" data-id="<?php echo $user_id; ?>">
              <div class="product-cell name">
                <a href="<?php echo base_url().MOD_VALUE.'index.php/admin/employee/'. $user_id; ?>"><?php echo $users_data[$user_id]['user_fillname']; ?></a>
              </div>
              <div class="product-cell date">
                <span><?php echo date_format(date_create($row['insert_excel_date']), 'j/n/Y');?></span>
              </div>
              <div class="product-cell total">
                <span><?php echo number_format($total_amount, 2); ?></span>
              </div>
              <div class="product-cell diff"><span class="minus" style = 'direction: ltr'><?php echo number_format($difference, 2); ?></span></div>
              <div class="product-cell actions">
                <button class="btn add-tswya" onclick="tswya(this, <?php echo $total_amount; ?>, <?php echo ($cash_amount + $network_amount); ?>, '<?php echo date_format(date_create($row['insert_excel_date']), 'Y-m-d'); ?>')" data-total="<?php echo number_format($total_amount, 2); ?>">تسوية</button>
              </div>
            </div>
          <?php
          }
        }
      }

      ?>

    </div>
    <div class="admin-table-pagination" data-admin-table-pagination data-target="#settlementsTable" data-search="#Search" data-page-size="20"></div>
    
    <!-- Tswya -->
    <div class="popup popup-Tswya">
      <div class="add_task-content popup-content">
        <div class="title">
          <h3>تسويه</h3> 
          <div class="close"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512"><path d="M342.6 150.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L192 210.7 86.6 105.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L146.7 256 41.4 361.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0L192 301.3 297.4 406.6c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L237.3 256 342.6 150.6z"/></svg></div>
        </div>
        <?php echo form_open(base_url().MOD_VALUE.'index.php/admin/add_tswya',array('class' => '')); ?>
            <input type="hidden" name="user_id" class="hr1">
            <input type = 'hidden' name = 'date'>
            <div class="inpt">
            <label>سبب التسوية</label>
            <input type="text" name="reason" placeholder="أدخل السبب هنا" class="hr1">
          </div>
          <div class="inpt">
            <label>اجمالي المبلغ</label>
            <input type="text" readonly min="1" name="totalAmount" id = 'totalAmount' class="hr1 readonly" style = 'direction: ltr;'>
          </div>
          <div class="inpt">
            <label>المدفوع</label>
            <input type="text" readonly value = "" name="userPaidAmount" class="hr2 readonly" id = 'userPaidAmount' style = 'direction: ltr'>
          </div>
          <div class="inpt">
            <label>المتبقي</label>
            <input type="text" readonly value = "" name="remaining" class="hr2 readonly" id = 'remaining' style = 'direction: ltr'>
          </div>
          <div class="inpt">
            <label>المدفوع الآن</label>
            <input type="text" value = "" name="insertedAmount" class="hr2" id = 'insertedAmount' onkeyup="calculateChanges()" style = 'direction: ltr'>
          </div>
          <div class="inpt">
            <label>الفروقات</label>
            <input type = "text" value = "" readonly name = "diffirence" id = 'diffirence' class="hr2" style = 'direction: ltr;'>
          </div>
          <button type="submit" class="btn btn-primary" style = 'width: fit-content' name="tswyaform">تأكيد التسوية</button>
          <?php echo form_close(); ?>
      </div>
    </div>
  </div>
        </div>
      </div>
    </div>

      <script>
        $(function() {
            $('#date_range').daterangepicker({
               
                locale: {
                    format: 'YYYY-MM-DD'
                }
            });
        });
    </script>
		
<!-- JS -->
  <<script src="https://code.jquery.com/jquery-3.6.0.js" crossorigin="anonymous"></script>
  <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
  <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
  <script  src="<?php echo base_url(); ?>public/js/script.js?a=<?php $date=date_create();echo date_timestamp_get($date);?>"></script>
  <script  src="<?php echo base_url(); ?>public/js/admin-table-pagination.js?a=<?php $date=date_create();echo date_timestamp_get($date);?>"></script>
  <script  src="<?php echo base_url(); ?>public/js/home.js?a=<?php $date=date_create();echo date_timestamp_get($date);?>"></script>

   
<?php $this->view('view_footer'); ?>

<script>
  $(function() {
      
    // إرسال النموذج تلقائيًا عند اختيار تاريخ
    $('#date_range').on('apply.daterangepicker', function(ev, picker) {
        // الحصول على التواريخ المختارة
        var startDate = picker.startDate.format('YYYY-MM-DD');
        var endDate = picker.endDate.format('YYYY-MM-DD');

        // تعيين قيم التواريخ في النموذج
        $('<input>').attr({
            type: 'hidden',
            name: 'date_start',
            value: startDate
        }).appendTo('#search-form');
        $('<input>').attr({
            type: 'hidden',
            name: 'date_end',
            value: endDate
        }).appendTo('#search-form');

        // إرسال النموذج
        $('#search-form').get(0).submit(); // استخدم .get(0) للحصول على العنصر DOM الفعلي
    });
});

function tswya(e, totalamount, paidAmount, date){
  $(".popup-Tswya").fadeIn();
  totalamount = Math.round(parseFloat(totalamount) * 100) / 100;
  paidAmount = Math.round(parseFloat(paidAmount) * 100) / 100;
  document.querySelector("#totalAmount").value = totalamount;
  document.querySelector("#userPaidAmount").value = paidAmount;
  document.querySelector("#remaining").value = Math.round((totalamount - paidAmount) * 100) / 100;
  calculateChanges();
  
  
  let user = $(e).closest('.products-row').data('id');
  $(".popup-Tswya").find('input[name=user_id]').val(user);
  document.querySelector("input[name=date]").value = date;
}

function calculateChanges() {
  let insertedAmount = $('#insertedAmount').val()
  let remaining = $('#remaining').val()
  if (insertedAmount == '') {
    insertedAmount = 0;
  }
  let diffirence = $('#diffirence');

  let value = Math.round((parseFloat(insertedAmount) - parseFloat(remaining)) * 100) / 100;
  if (value > 0) {
      var eleClass = 'positive';
  } else if (value < 0) {
      var eleClass = 'negative';
  } else {
      var eleClass = 'zero';
  }

  diffirence.val(value)
  diffirence.removeClass('negative');
  diffirence.removeClass('positive');
  diffirence.removeClass('zero');
  diffirence.addClass(eleClass);
}
</script>
<style>
    .popup-Tswya input[readonly],
    .popup-Tswya input[disabled],
    .popup-Tswya .readonly {
        cursor: not-allowed !important;
        caret-color: transparent;
        background-color: #f8fafc;
        color: #64748b;
    }

    .popup-Tswya input[readonly]:hover,
    .popup-Tswya input[readonly]:focus,
    .popup-Tswya input[disabled]:hover,
    .popup-Tswya input[disabled]:focus,
    .popup-Tswya .readonly:hover,
    .popup-Tswya .readonly:focus {
        cursor: not-allowed !important;
        border-color: #f43f5e !important;
        background-color: #ffe4e6 !important;
        color: #be123c !important;
        box-shadow: 0 0 0 0.2rem rgba(244, 63, 94, 0.14) !important;
        outline: none;
    }
</style>
