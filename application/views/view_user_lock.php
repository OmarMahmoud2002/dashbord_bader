<title>التقفيلة</title>
<?php $this->view('view_header'); ?>
 

	<?php $this->view('view_user_sidebar'); ?>

    <div class="app-content">
      <div class="app-content-header">
      </div>
      <div class="lock-content">
          <?php echo form_open(base_url().MOD_VALUE.'user/sendlock',array('class' => '')); ?>
        <div class="lock-c">
          <div class="cash-calculator">
            <label></label>
            <table>
                <thead>
                    <tr>
                        <th colspan="3">التقفيلة</th>
                    </tr>
                    <tr>
                        <th>المبلغ كاش (نقدي)</th>
                        <th>المبلغ بطاقة (سبان)</th>
                    </tr>
                </thead>
                <tbody>
                  <tr>
                      <td><input placeholder="أدخل المبلغ" type="text" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" class="number" name="user_lock_cash" id="user_lock_cash"   min="0"></td>
                      <td><input placeholder="أدخل المبلغ" type="text" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');"  class="number" name="user_lock_span" id="user_lock_span"  min="0"></td>
                  </tr>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="1">الاجمالي</td>
                        <td colspan="1"><input type="text"  id="total" readonly></td>
                    </tr>
                </tfoot>
            </table>
          </div>
        </div>
         <button type="submit" class="btn btn-primary btn-info" name="form_lock"> تأكيد</button>
     <?php echo form_close(); ?>
      </div>
    </div>
  </div>
		
 <!-- JS -->

  <script src="https://code.jquery.com/jquery-3.6.0.js" crossorigin="anonymous"></script>
  <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
  <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

  <script src="<?php echo base_url(); ?>public/js/script.js?a=<?php $date=date_create();echo date_timestamp_get($date);?>"></script>
  <script src="<?php echo base_url(); ?>public/js/home.js?a=<?php $date=date_create();echo date_timestamp_get($date);?>"></script>
<script src="<?php echo base_url(); ?>public/js/user.js?a=<?php $date=date_create();echo date_timestamp_get($date);?>"></script>
<?php $this->view('view_footer'); ?>
 <script>
jQuery(function($) {
        $(document).on('change', '#user_lock_cash, #user_lock_span', function () {
            var user_lock_cash =   document.getElementById("user_lock_cash").value 
            var user_lock_span =   document.getElementById("user_lock_span").value 
            var number = parseFloat(user_lock_cash) + parseFloat(user_lock_span);
            $('#total').val(number);
        });
    });

 </script>