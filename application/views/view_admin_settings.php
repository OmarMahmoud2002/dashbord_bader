<title>الإعدادات</title>
<?php $this->load->view('view_header'); ?>
<?php $this->load->view('view_admin_sidebar');?>


<div class="app-content admin-ui-page admin-ui-settings">
    <div class="app-content-header">
    <h1 class="app-content-headerText">الاعدادات</h1>
    </div>
    <div class="prod_info">
    <ul class="list-unstyled links_stng">
        <li class="active" data-sec=".fist_section">عامة</li>
        <li data-sec=".second_section"> SMTP اعدادت</li>
        <li data-sec=".third_section">إعدادات النماذج</li>
    </ul>
    <div class="all_sections">
        <div class="sec fist_section">
        <form method = 'post'>
            <input type="hidden" name="settings_section" value="general">
            <div class="inpt">
            <label>اسم الموقع</label>
            <input type="text" value="<?php echo $settings['Website_name']?>" name = 'website_name'>
            </div>
            <div class="inpt">
            <label>البريد الالكتروني للموقع</label>
            <input type="text" value="<?php echo $settings['Website_email']?>" name = 'website_email'>
            </div>
            <div class="inpt">
            <label>النطاق</label>
            <input type="text" value="<?php echo $settings['Website_domain']?>" name = 'website_domain'>
            </div>
            <div class="inpt">
            <label>البريد الالكتروني للنسخ الاحتياطي</label>
            <input type="text" value="<?php echo $settings['Website_backup_email']?>" name = 'website_backup_email'>
            </div>
            <button class="save">حفظ</button>
        </form>
        </div>
        <div class="sec second_section">
        <form method="post">
            <input type="hidden" name="settings_section" value="smtp">
            <div class="inpt">
            <label>ترميز البريد</label>
            <input type="text" value="<?php echo $settings['SMTP_mail_encoding']?>" name = 'smtp_mail_encoding'>
            </div>
            <div class="inpt">
            <label>المنفذ</label>
            <input type="text" value="<?php echo $settings['SMTP_port']?>" name = 'smtp_port'>
            </div>
            <div class="inpt">
            <label>مستضيف</label>
            <input type="text" value="<?php echo $settings['SMTP_host']?>" name = 'smtp_host'>
            </div>
            <div class="inpt">
            <label>اسم المستخدم</label>
            <input type="text" value="<?php echo $settings['SMTP_username']?>" name = 'smtp_username'>
            </div>
            <div class="inpt">
            <label>الرقم السري</label>
            <input type="text" value="<?php echo $settings['SMTP_password']?>" name = 'smtp_password'>
            </div>
            <button class="save">حفظ</button>
        </form>
        </div>
        <div class="sec third_section">
        <form method="post" class="forms-settings-form">
            <input type="hidden" name="settings_section" value="forms">
            <div class="inpt">
            <label for="manager_name">اسم مدير المعرض والتوقيع</label>
            <input id="manager_name" type="text" value="<?php echo safe_data($forms_settings['manager_name']); ?>" name="manager_name">
            </div>
            <div class="inpt">
            <label for="manager_employee_id">الرقم الوظيفي</label>
            <input id="manager_employee_id" type="text" value="<?php echo safe_data($forms_settings['manager_employee_id']); ?>" name="manager_employee_id">
            </div>
            <div class="inpt">
            <label for="stamp">الختم</label>
            <input id="stamp" type="text" value="<?php echo safe_data($forms_settings['stamp']); ?>" name="stamp">
            </div>
            <div class="inpt">
            <label for="store_name">اسم المعرض</label>
            <input id="store_name" type="text" value="<?php echo safe_data($forms_settings['store_name']); ?>" name="store_name">
            </div>
            <button class="save">حفظ</button>
        </form>
        </div>
    </div>
    </div>
</div>
</div>


<!-- JS -->
<script src="https://code.jquery.com/jquery-3.6.0.js" crossorigin="anonymous"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

<script src="<?php echo base_url(); ?>public/js/script.js?a=<?php $date=date_create();echo date_timestamp_get($date);?>"></script>
<script src="<?php echo base_url(); ?>public/js/settings.js?a=<?php $date=date_create();echo date_timestamp_get($date);?>"></script>

<?php $this->load->view('view_footer'); ?>
