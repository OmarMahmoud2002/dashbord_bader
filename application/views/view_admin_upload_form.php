<?php $this->view('view_header'); ?>
  <style>
        .admin-ui-upload-form .app-content-actions {
            margin-bottom: 20px;
        }

        .stockd {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
            background-color: #f9f9f9;
        }

        .stockd label {
            display: block;
            margin-bottom: 10px;
            font-weight: bold;
        }

        .stockd input[type="text"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }

        .add_up-co {
            margin-top: 10px;
            padding: 10px;
            border: 1px dashed #ccc;
            border-radius: 4px;
            background-color: #f1f1f1;
            text-align: center;
        }

        .add_up-co input[type="file"] {
            display: block;
            margin: 0 auto 10px;
        }

        .add_up-co p {
            margin: 0;
            color: #333;
        }

        .stockd button {
            display: block;
            width: 100%;
            padding: 10px;
            border: none;
            border-radius: 4px;
            background-color: #28a745;
            color: white;
            font-size: 16px;
            cursor: pointer;
            margin-top: 10px;
        }

        .stockd button:hover {
            background-color: #218838;
        }

        .stockd a {
            display: block;
            margin-top: 10px;
            text-align: center;
            color: #007bff;
            text-decoration: none;
        }

        .stockd a:hover {
            text-decoration: underline;
        }

        /* Add New Feature */
        .upload-card-title {
            margin: 0 0 14px;
            color: #2d3748;
            font-size: 18px;
            font-weight: 900;
            text-align: center;
        }

        .upload-form-actions {
            justify-content: center;
            gap: 18px;
            flex-wrap: wrap;
        }
        /* End */
    </style>
 <?php
$CI =& get_instance();
$CI->load->model('Excel_import_model');
?>

	<?php $this->view('view_admin_sidebar'); ?>

     <div class="app-content admin-ui-page admin-ui-upload-form">
      <div class="app-content-header">

        <h1 class="app-content-headerText">مبيعات تواصل  </h1>
        
      </div>
      <div class="app-content-actions upload-form-actions">
        <?php echo form_open_multipart(base_url().MOD_VALUE.'excel_import/import',array('class' => 'form-horizontal')); ?>

      <div class="stockd">
            <!-- Add New Feature -->
            <h2 class="upload-card-title">مبيعات تواصل</h2>
            <!-- End -->
            <input class="form-control datepicker" autocomplete="off" type="text" placeholder="التاريخ" name="import_date" readonly>
        <div style="margin-top:10px;height:100px" class="add_up-co">
          <input id="file" name="file" type="file" accept=".xlsx, .xls" class="file-input" required>
          <p>قم بإسقاط الملفات هنا للتحميل أو انقر لاختيار الملفات للتحميل</p>
        </div>
        <button type="submit" name="import">رفع</button>
         <a style="display:none" download href="<?php echo base_url().MOD_VALUE.'public/file.xlsx'; ?>"> تحميل نموذج 
      </a>
      </div>
      	<?php echo form_close(); ?>
        <!-- Add New Feature -->
        <?php echo form_open_multipart(base_url().MOD_VALUE.'excel_import/import_erp',array('class' => 'form-horizontal')); ?>
      <div class="stockd">
            <h2 class="upload-card-title">مبيعات ERP</h2>
            <!-- Add New Feature -->
            <input class="form-control datepicker" autocomplete="off" type="text" placeholder="التاريخ" name="import_date" readonly>
            <!-- End -->
        <div style="margin-top:10px;height:100px" class="add_up-co">
          <input id="erp_file" name="file" type="file" accept=".xlsx, .xls" class="file-input" required>
          <p>قم بإسقاط ملف ERP هنا أو انقر لاختيار الملف</p>
        </div>
        <button type="submit" name="import_erp">رفع ERP</button>
      </div>
        <?php echo form_close(); ?>
        <!-- End -->
      </div>

      <br>
<style>
    .form-horizontal {
    display: flex;
    align-items: center;
    gap: 10px;
    flex-wrap: wrap;
}

.form-horizontal label {
    margin: 0;
}

.form-horizontal .form-control {
    flex: 1;
    max-width: 200px; /* Adjust as needed */
}

.form-horizontal button {
    flex-shrink: 0;
}

.horizontal-container-control {
    display: flex;
    justify-content: space-between;
    margin-bottom: 10px;
}

.page-control {
    display: flex;
    gap: 10px;
}

/* Add New Feature */
.admin-ui-upload-form .upload-form-actions {
    display: flex;
    flex-direction: row;
    align-items: stretch;
    justify-content: center;
    gap: 18px;
}

.admin-ui-upload-form .upload-form-actions > .form-horizontal {
    display: block;
    flex: 1 1 420px;
    max-width: 560px;
}

.admin-ui-upload-form .upload-form-actions .stockd {
    height: 100%;
    max-width: none;
    margin: 0;
}

.admin-ui-upload-form .upload-form-actions .stockd .form-control {
    max-width: none;
}

@media (max-width: 1100px) {
    .admin-ui-upload-form .upload-form-actions > .form-horizontal {
        flex-basis: 100%;
        max-width: 640px;
    }
}
/* End */

</style>
    <div class = 'horizontal-container-control'>
        <?php echo form_open_multipart(base_url().MOD_VALUE.'excel_import/delete_by_date',array('class' => 'form-horizontal')); ?>
        <label for="date">اختر التاريخ:</label>
        <input class="form-control datepicker" type="text" id="date" name="date" readonly required>
        <button class="btn btn-danger" type="submit">حذف</button>
        <?php echo form_close(); ?>
        <div class = 'page-control'>
            <a href = '<?=base_url() . "admin/upload_form?page=" . $next ?>'><button class = 'btn'>التالي</button></a>
            <?php if ($page > 0) { ?>
                <a href = '<?=base_url() . "admin/upload_form?page=" . $previous ?>'><button class = 'btn'>السابق</button></a>
            <?php } ?>
        </div>
    </div>
    

      
      <div class="products-area-wrapper tableView">
        <div class="products-header">
            <div class="product-cell image"># </div>
          <div class="product-cell image">التاريخ </div>
          <div class="product-cell image">اسم الموظف</div>
          <div class="product-cell image">رقم الطلب </div>
          <div class="product-cell id">مبيعات تواصل</div>
          <div class="product-cell actions">اجراء</div>
        </div>
        	<?php
			$i=0;
			foreach ($excel as $row) {
                  $c_user_name = $CI->Model_admin->get_user_by_id($row['insert_excel_uid']);
        		$user_name=  $c_user_name['user_fillname'];
                // Add New Feature
                $display_order_number = !empty($row['insert_excel_new_ordern']) ? $row['insert_excel_new_ordern'] : $row['insert_excel_ordern'];
                // End
				$i++;
				?>
        <div class="products-row ">
           
            
             <div class="product-cell image">
            <span class="cell-label">#</span> <?php echo safe_data($i); ?>
          </div>
          
            <div class="product-cell image">
            <span class="cell-label">التاريخ</span><?php echo safe_data($row['insert_excel_date']); ?>
          </div>
          <div class="product-cell image">
            <span class="cell-label">الموظف</span><?php echo safe_data($user_name); ?>
          </div>
          <div class="product-cell image">
            <span class="cell-label">رقم الطلب</span><?php echo safe_data($display_order_number); ?>
          </div>
          <div class="product-cell id"><span class="cell-label">مبيعات تواصل</span><?php echo safe_data($row['insert_excel_twasel']); ?></div>
          <div class="product-cell actions">
            <button data-toggle="modal" data-target="#myModal<?php echo safe_data($i); ?>" class="edit-btn">تعديل</button>
            <button data-toggle="modal" data-target="#myModaldell<?php echo safe_data($i); ?>" class="del-btn">حذف</button>
          </div>
        </div>
         <!-- Edit Employee -->
      <div id="myModal<?php echo safe_data($i); ?>" class="popup ">
        <div class="popup-content">
          <div class="title">
            <h3>تعديل تقفيلة الموظف: <span> <?php echo safe_data($user_name); ?> </span></h3> 
             <button type="button" class="close" data-dismiss="modal">&times;</button>
          </div>
          <?php echo form_open(base_url().MOD_VALUE.'admin/edit_excellock',array('class' => '')); ?>
          <input name="insert_excel_id" type="hidden" value="<?php echo safe_data($row['insert_excel_id']); ?>">
            
            <div class="inpt">
              <label>مبيعات تواصل</label>
              <input name="insert_excel_twasel" type="text" value="<?php echo safe_data($row['insert_excel_twasel']); ?>" placeholder="مبيعات تواصل">
            </div>
            
           
           <button type="submit" class="btn btn-primary btn-primary" name="form1">تعديل</button>
            <?php echo form_close(); ?>
        </div>
      </div>
      <!-- Delete Employee -->
      <div id="myModaldell<?php echo safe_data($i); ?>" class="popup ">
        <div class="popup-content">
          <div class="title">
            <h3>حذف تقفيلة الموظف: <span></span></h3> 
             <button type="button" class="close" data-dismiss="modal">&times;</button>
          </div>
          <?php echo form_open(base_url().MOD_VALUE.'admin/delete_excellock',array('class' => '')); ?>
          <input name="insert_excel_id" type="hidden" value="<?php echo safe_data($row['insert_excel_id']); ?>">
          <div class="inpt">
              <label>هل ترغب فى حذف </label>
              <input disabled type="text" value="<?php echo safe_data($user_name); ?>" placeholder="اسم الموظف">
            </div>
           <button style="background-color: #f44336;" type="submit" class="btn btn-danger btn-danger" name="form2">حذف</button>
            <?php echo form_close(); ?>
        </div>
      </div>
       
     
       <?php
		}
		?>
      </div>
  
 <!-- JS -->

  <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>

  <script src="<?php echo base_url(); ?>public/js/script.js?a=<?php $date=date_create();echo date_timestamp_get($date);?>"></script>
<script src="<?php echo base_url(); ?>public/js/employees.js?a=<?php $date=date_create();echo date_timestamp_get($date);?>"></script>


<?php $this->view('view_footer'); ?>
