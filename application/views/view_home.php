<?php $this->view('view_header'); ?>
<style>
    
  .input-group {
    width: 100%; /* تأكد من أن الحاوية تأخذ عرض 100% */
    margin-bottom: 15px; /* لإضافة مسافة بين الحقول */
  }

  .input-group label {
    display: block; /* لعرض التسمية في سطر منفصل */
    margin-bottom: 5px; /* مسافة بين التسمية والمدخل */
  }

  .input-group input {
    width: 100%; /* جعل المدخلات تأخذ عرض 100% */
    padding: 10px; /* إضافة حشوة للمدخلات */
    box-sizing: border-box; /* للتأكد من أن الحشوة تؤخذ في الاعتبار */
  }
</style>
<?php
$CI =& get_instance();
$CI->load->model('Model_user');
?>

<img class= 'background' src="<?=base_url()?>public/img/backgrounds/background.png">
<style>
    .background {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
        z-index: -1;
    }
</style>

<!-- HTML -->
  <div class="login">
    <div class="login-widget">
      <h2>تسجيل الدخول</h2>
      <?php echo form_open(base_url().MOD_VALUE.'user/login',array('class' => '')); ?>
        <div  class="input-group">
          <label htmlFor="username">اسم المستحدم</label>
          <input  name="user_name"
            type="text"
            placeholder="اسم المستحدم"
            id="user_name"
          />
        </div>
        <div class="input-group">
          <label htmlFor="password">كلمة المرور</label>
          <input
            type="password"
            placeholder="كلمة المرور"
            id="user_password"
            name="user_password"
          />
        </div>
     <button type="submit" class="btn btn-primary btn-success" name="form_login"> دخول</button>
     <?php echo form_close(); ?>
    </div>
  </div>


<?php $this->view('view_footer'); ?>
