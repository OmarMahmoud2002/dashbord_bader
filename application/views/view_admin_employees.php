<title>الموظفين</title>
<?php $this->view('view_header'); ?>
 <?php
$CI =& get_instance();
$CI->load->model('Model_admin');
?>

	<?php $this->view('view_admin_sidebar'); ?>

     <div class="app-content admin-ui-page admin-ui-people">
      <div class="app-content-header">

        <h1 class="app-content-headerText">الموظفين</h1>
      </div>
      <div class="app-content-actions">
        <input id="Search" onkeyup="search()" class="search-bar" placeholder="بحث..." type="text">
                <button class="app-content-headerButton">اضافة موظف</button>

      </div>
      <div class="products-area-wrapper tableView">
        <div class="products-header">
          <div class="product-cell image">اسم الموظف</div>
          <div class="product-cell added-date">تاريخ الاظافة</div>
          <div class="product-cell actions">اجراء</div>
        </div>
        	<?php
      if ($employees) {
			$i=0;
			foreach ($employees as $row) {
				$i++;
				?>
        <div class="products-row ">
          <div class="product-cell image">
            <!--<span><?php echo safe_data($row['user_fillname']); ?> </span>-->
            <span><a href="<?php echo base_url().MOD_VALUE.'index.php/admin/employee/'.$row['user_id']; ?>"><?php echo $row['user_fillname']; ?></a></span>
          </div>
          <div class="product-cell added-date"><span class="cell-label">تاريخ الاظافة:</span><?php echo safe_data($row['user_create_date']); ?></div>
          <div class="product-cell actions">
            <button data-toggle="modal" data-target="#myModaldell<?php echo safe_data($i); ?>" class="del-btn">حذف</button>
          </div>
        </div>
        
      <!-- Delete Employee -->
      <div id="myModaldell<?php echo safe_data($i); ?>" class="popup ">
        <div class="popup-content">
          <div class="title">
            <h3>حذف موظف: <span><?php echo safe_data($row['user_fillname']); ?> </span></h3> 
             <button type="button" class="close" data-dismiss="modal">&times;</button>
          </div>
          <?php echo form_open(base_url().MOD_VALUE.'admin/delete_employee',array('class' => '')); ?>
          <input name="user_id" type="hidden" value="<?php echo safe_data($row['user_id']); ?>">
          <div class="inpt">
              <label>هل ترغب فى حذف </label>
              <input disabled type="text" value="<?php echo safe_data($row['user_fillname']); ?>" placeholder="اسم الموظف">
            </div>
           <button style="background-color: #f44336;" type="submit" class="btn btn-danger btn-danger" name="form2">حذف</button>
            <?php echo form_close(); ?>
        </div>
      </div>
        <?php
							}
      } else {
							?>
        <div class="empty-admin-state">
          <i class="bi bi-people"></i>
          <div>لا يوجد موظفين</div>
        </div>
      <?php } ?>
      </div>
      <!-- Add Employee -->
      <div class="popup popup-add-employee">
        <div class="add_task-content popup-content">
          <div class="title">
            <h3>اضافة موظف جديدة</h3> 
            <div class="close"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512"><path d="M342.6 150.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L192 210.7 86.6 105.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L146.7 256 41.4 361.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0L192 301.3 297.4 406.6c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L237.3 256 342.6 150.6z"/></svg></div>
          </div>
          <?php echo form_open(base_url().MOD_VALUE.'admin/registration',array('class' => '')); ?>
          <input name="user_type" type="hidden" value="user">
            <div class="inpt">
              <label>اسم الموظف</label>
              <input name="user_fillname" type="text" placeholder="اسم الموظف">
            </div>
            <div class="inpt">
              <label>المعرف</label>
              <input name="user_employee_Id" type="text" placeholder="المعرف">
            </div>
            <div class="inpt">
              <label>البريد الالكتروني </label>
              <input name="user_email" type="text" placeholder="البريد الالكتروني ">
            </div>
            <div class="inpt">
              <label>اسم الدخول</label>
              <input name="user_name" type="text" placeholder="اسم الدخول">
            </div>
            <div class="inpt">
              <label>الرقم السري </label>
              <input name="user_password" type="text" placeholder="الرقم السري ">
            </div>
           <button type="submit" class="btn btn-primary btn-primary" name="form_registration">إضافة</button>
            <?php echo form_close(); ?>
        </div>
      </div>
     
    </div>
  </div>
  
  
 <!-- JS -->

  <script src="https://code.jquery.com/jquery-3.6.0.js" crossorigin="anonymous"></script>
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
</script>
<?php $this->view('view_footer'); ?>
