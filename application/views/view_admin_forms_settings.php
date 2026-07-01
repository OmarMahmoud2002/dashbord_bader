<title>إعدادات النماذج</title>
<?php $this->load->view('view_header'); ?>
<?php $this->load->view('view_admin_sidebar'); ?>

<style>
  .admin-ui-forms-settings .forms-settings-shell {
    width: min(1120px, 100%);
    margin: 0 auto;
    padding: 24px;
    border: 1px solid var(--admin-ui-border);
    border-radius: 16px;
    background: var(--admin-ui-surface);
    box-shadow: var(--admin-ui-shadow);
  }

  .admin-ui-forms-settings .forms-settings-grid {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 16px;
    margin-bottom: 22px;
  }

  .admin-ui-forms-settings .forms-settings-field {
    min-width: 0;
  }

  .admin-ui-forms-settings .forms-settings-field label {
    display: block;
    margin-bottom: 8px;
    color: var(--admin-ui-text);
    font-size: 14px;
    font-weight: 900;
  }

  .admin-ui-forms-settings .forms-settings-field input {
    width: 100%;
    min-height: 52px;
    padding: 10px 14px;
    border: 2px solid rgba(102, 126, 234, 0.24);
    border-radius: 13px;
    background: linear-gradient(180deg, #fff 0%, #f8fafc 100%);
    color: var(--admin-ui-text);
    font-size: 15px;
    font-weight: 800;
    outline: 0;
    box-shadow: 0 8px 20px rgba(15, 23, 42, 0.06);
  }

  .admin-ui-forms-settings .forms-settings-field input:focus {
    border-color: var(--admin-ui-primary);
    background: #fff;
    box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.14), 0 12px 28px rgba(15, 23, 42, 0.08);
  }

  .admin-ui-forms-settings .forms-settings-actions {
    display: flex;
    justify-content: flex-start;
  }

  .admin-ui-forms-settings .forms-settings-save {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    min-height: 46px;
    padding: 10px 22px;
    border: 0;
    border-radius: 12px;
    background: var(--admin-ui-gradient);
    color: #fff;
    font-size: 15px;
    font-weight: 900;
    box-shadow: 0 12px 24px rgba(102, 126, 234, 0.24);
  }

  .admin-ui-forms-settings .forms-settings-save:hover {
    filter: brightness(1.02);
    transform: translateY(-1px);
  }

  @media (max-width: 900px) {
    .admin-ui-forms-settings .forms-settings-grid {
      grid-template-columns: 1fr;
    }
  }
</style>

<div class="app-content admin-ui-page admin-ui-forms-settings">
  <div class="app-content-header">
    <h1 class="app-content-headerText">إعدادات النماذج</h1>
  </div>

  <form method="post" class="forms-settings-shell">
    <div class="forms-settings-grid">
      <div class="forms-settings-field">
        <label for="manager_name">اسم مدير المعرض والتوقيع</label>
        <input id="manager_name" type="text" name="manager_name" value="<?php echo safe_data($forms_settings['manager_name']); ?>">
      </div>

      <div class="forms-settings-field">
        <label for="manager_employee_id">الرقم الوظيفي</label>
        <input id="manager_employee_id" type="text" name="manager_employee_id" value="<?php echo safe_data($forms_settings['manager_employee_id']); ?>">
      </div>

      <div class="forms-settings-field">
        <label for="stamp">الختم</label>
        <input id="stamp" type="text" name="stamp" value="<?php echo safe_data($forms_settings['stamp']); ?>">
      </div>

      <div class="forms-settings-field">
        <label for="store_name">اسم المعرض</label>
        <input id="store_name" type="text" name="store_name" value="<?php echo safe_data($forms_settings['store_name']); ?>">
      </div>

      <!-- START settlement form defaults additions -->
      <div class="forms-settings-field">
        <label for="settlement_service_package">باقة الخدمة - نموذج تسوية الغرامة</label>
        <input id="settlement_service_package" type="text" name="settlement_service_package" value="<?php echo safe_data($forms_settings['settlement_service_package']); ?>">
      </div>

      <div class="forms-settings-field">
        <label for="settlement_contract_duration">مدة العقد - نموذج تسوية الغرامة</label>
        <input id="settlement_contract_duration" type="text" name="settlement_contract_duration" value="<?php echo safe_data($forms_settings['settlement_contract_duration']); ?>">
      </div>
      <!-- END settlement form defaults additions -->
    </div>

    <div class="forms-settings-actions">
      <button type="submit" class="forms-settings-save">
        <i class="bi bi-check2-circle"></i>
        <span>حفظ</span>
      </button>
    </div>
  </form>
</div>
</div>

<script src="<?php echo base_url(); ?>public/js/script.js?a=<?php $date=date_create();echo date_timestamp_get($date);?>"></script>
<?php $this->load->view('view_footer'); ?>
