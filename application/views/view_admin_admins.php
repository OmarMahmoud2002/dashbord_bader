<title>المدرين</title>
<?php $this->view('view_header'); ?>
 <?php
$CI =& get_instance();
$CI->load->model('Model_admin');
?>

	<?php $this->view('view_admin_sidebar'); ?>

     <div class="app-content">
      <div class="app-content-header">

        <h1 class="app-content-headerText">المدرين</h1>
      </div>
      <div class="app-content-actions">
        <input id="Search" onkeyup="search()" class="search-bar" placeholder="بحث..." type="text">
                <button class="app-content-headerButton">اظافة مدير</button>

      </div>
      <div class="products-area-wrapper tableView">
        <div class="products-header">
          <div class="product-cell image">اسم المدير</div>
          <div class="product-cell id">اسم الدخول</div>
          <div class="product-cell id">البريد الالكتروني </div>
          <div class="product-cell added-date">تاريخ الاظافة</div>
          <div class="product-cell actions">اجراء</div>
        </div>
        	<?php
			$i=0;
			foreach ($admins as $row) {
				$i++;
				?>
        <div class="products-row ">
          <div class="product-cell image">
            <span><?php echo safe_data($row['user_fillname']); ?> </span>
          </div>
          <div class="product-cell id"><span class="cell-label">اسم الدخول</span><?php echo safe_data($row['user_name']); ?></div>
          <div class="product-cell id"><span class="cell-label">البريد الالكتروني </span><?php echo safe_data($row['user_email']); ?></div>
          <div class="product-cell added-date"><span class="cell-label">تاريخ الاظافة:</span><?php echo safe_data($row['user_create_date']); ?></div>
          <div class="product-cell actions">
            <button style="background-color: var(--action-color);" data-toggle="modal" data-target="#myModal<?php echo safe_data($i); ?>" class="edit-btn">تعديل</button>
            <?php if ($i > 1): ?>
            <button data-toggle="modal" data-target="#myModaldell<?php echo safe_data($i); ?>" class="del-btn">حذف</button>
          <?php endif ?>
          </div>
        </div>
         <!-- Edit Employee -->
      <div id="myModal<?php echo safe_data($i); ?>" class="popup ">
        <div class="popup-content">
          <div class="title">
            <h3>تعديل مدير: <span><?php echo safe_data($row['user_fillname']); ?> </span></h3> 
             <button type="button" class="close" data-dismiss="modal">&times;</button>
          </div>
          <?php echo form_open(base_url().MOD_VALUE.'admin/edit_employee',array('class' => '')); ?>
          <input name="user_id" type="hidden" value="<?php echo safe_data($row['user_id']); ?>">
            <div class="inpt">
              <label>اسم المدير</label>
              <input name="user_fillname" type="text" value="<?php echo safe_data($row['user_fillname']); ?>" placeholder="اسم المدير">
            </div>
            
            <div class="inpt">
              <label>البريد الالكتروني </label>
              <input name="user_email" type="text" value="<?php echo safe_data($row['user_email']); ?>" placeholder=" المعرف">
            </div>
            <div class="inpt">
              <label>اسم الدخول</label>
              <input name="user_name" type="text"  value="<?php echo safe_data($row['user_name']); ?>" placeholder="اسم الدخول">
            </div>
            <div class="inpt">
              <label>الرقم السري </label>
              <input name="user_password" type="text" value="" placeholder="الرقم السري ">
            </div>
           <button type="submit" class="btn btn-primary btn-primary" name="form1">تعديل</button>
            <?php echo form_close(); ?>
        </div>
      </div>
      <!-- Delete Employee -->
      <div id="myModaldell<?php echo safe_data($i); ?>" class="popup ">
        <div class="popup-content">
          <div class="title">
            <h3>حذف مدير: <span><?php echo safe_data($row['user_fillname']); ?> </span></h3> 
             <button type="button" class="close" data-dismiss="modal">&times;</button>
          </div>
          <?php echo form_open(base_url().MOD_VALUE.'admin/delete_employee',array('class' => '')); ?>
          <input name="user_id" type="hidden" value="<?php echo safe_data($row['user_id']); ?>">
          <div class="inpt">
              <label>هل ترغب فى حذف </label>
              <input disabled type="text" value="<?php echo safe_data($row['user_fillname']); ?>" placeholder="اسم المدير">
            </div>
           <button style="background-color: #f44336;" type="submit" class="btn btn-danger btn-danger" name="form2">حذف</button>
            <?php echo form_close(); ?>
        </div>
      </div>
        <?php
		}
		?>
      </div>
      <!-- Add Employee -->
      <div class="popup popup-add-employee">
        <div class="add_task-content popup-content">
          <div class="title">
            <h3>اضافة مدير جديدة</h3> 
            <div class="close"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512"><path d="M342.6 150.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L192 210.7 86.6 105.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L146.7 256 41.4 361.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0L192 301.3 297.4 406.6c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L237.3 256 342.6 150.6z"/></svg></div>
          </div>
          <?php echo form_open(base_url().MOD_VALUE.'admin/registration',array('class' => '')); ?>
          <input name="user_type" type="hidden" value="admin">
            <div class="inpt">
              <label>اسم المدير</label>
              <input name="user_fillname" type="text" placeholder="اسم المدير">
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
     
      <!-- Add -->
      <div class="popup popup-add-ohda">
        <div class="add_task-content popup-content">
          <div class="title">
            <h3>اضافة عهدة</h3> 
            <div class="close"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512"><path d="M342.6 150.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L192 210.7 86.6 105.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L146.7 256 41.4 361.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0L192 301.3 297.4 406.6c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L237.3 256 342.6 150.6z"/></svg></div>
          </div>
          <form>
            <div class="inpt">
              <label>العهدة</label>
              <input type="text" placeholder="">
            </div>
            <div class="inpt">
              <label>الوصف</label>
              <textarea></textarea>
            </div>
            <button>اظافة</button>
          </form>
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



<?php $this->view('view_footer'); ?>
