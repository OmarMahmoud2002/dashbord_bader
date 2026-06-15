<?php $this->view('view_header'); ?>
<?php
$CI =& get_instance();
$CI->load->model('Model_user');
?>

<main class="login-page" dir="rtl">
  <img class="login-bg" src="<?=base_url()?>public/img/backgrounds/store-gate-login-bg.png" alt="">
  <div class="login-veil"></div>
  <span class="login-orb login-orb-one"></span>
  <span class="login-orb login-orb-two"></span>

  <section class="login">
    <div class="login-widget">
      <div class="login-card-sheen"></div>
      <div class="login-brand">
        <img src="<?=base_url()?>logo.png" alt="Store Gate">
      </div>
      <h1>مرحبًا بك مجددًا</h1>
      <p>سجل الدخول إلى حسابك</p>

      <?php echo form_open(base_url().MOD_VALUE.'user/login',array('class' => 'login-form')); ?>
        <div class="login-field">
          <label for="user_name">اسم المستخدم أو البريد الإلكتروني</label>
          <span class="field-icon"><i class="fa-regular fa-user"></i></span>
          <input
            name="user_name"
            type="text"
            placeholder="اسم المستخدم أو البريد الإلكتروني"
            id="user_name"
            autocomplete="username"
          />
        </div>
        <div class="login-field">
          <label for="user_password">كلمة المرور</label>
          <span class="field-icon"><i class="fa-solid fa-lock"></i></span>
          <input
            type="password"
            placeholder="كلمة المرور"
            id="user_password"
            name="user_password"
            autocomplete="current-password"
          />
          <button class="password-toggle" type="button" aria-label="إظهار كلمة المرور" data-password-toggle>
            <i class="fa-regular fa-eye-slash"></i>
          </button>
        </div>
        <label class="remember-control">
          <input type="checkbox" name="remember_me" value="1" checked>
          <span class="remember-check" aria-hidden="true"><i class="fa-solid fa-check"></i></span>
          <span>تذكرني</span>
        </label>
        <button type="submit" class="login-submit" name="form_login">
          <span>تسجيل الدخول</span>
          <i class="fa-solid fa-arrow-right-long"></i>
        </button>
      <?php echo form_close(); ?>
    </div>
  </section>
</main>

<script>
  (function () {
    var toggle = document.querySelector('[data-password-toggle]');
    var password = document.getElementById('user_password');

    if (!toggle || !password) {
      return;
    }

    toggle.addEventListener('click', function () {
      var isHidden = password.type === 'password';
      password.type = isHidden ? 'text' : 'password';
      toggle.setAttribute('aria-label', isHidden ? 'إخفاء كلمة المرور' : 'إظهار كلمة المرور');
      toggle.innerHTML = isHidden ? '<i class="fa-regular fa-eye"></i>' : '<i class="fa-regular fa-eye-slash"></i>';
    });
  })();
</script>


<?php $this->view('view_footer'); ?>
