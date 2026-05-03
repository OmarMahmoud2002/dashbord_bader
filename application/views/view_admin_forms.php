<title>نماذج</title>
<?php $this->load->view('view_header'); ?>
<?php $this->load->view('view_admin_sidebar'); ?>

<style>
  .admin-forms-frame-shell {
    height: calc(100vh - 104px);
    min-height: 760px;
    overflow: hidden;
    border: 1px solid rgba(148, 163, 184, 0.22);
    border-radius: 16px;
    background: #fff;
    box-shadow: 0 18px 45px rgba(15, 23, 42, 0.08);
  }

  .admin-forms-frame {
    width: 100%;
    height: 100%;
    border: 0;
    background: #fff;
  }

  @media (max-width: 768px) {
    .admin-forms-frame-shell {
      height: calc(100vh - 88px);
      min-height: 680px;
      border-radius: 12px;
    }
  }
</style>

<div class="app-content admin-ui-forms">
  <div class="admin-forms-frame-shell">
    <iframe
      class="admin-forms-frame"
      src="<?php echo base_url('free_form/index.html'); ?>"
      title="نماذج"
    ></iframe>
  </div>
</div>
</div>

<script src="<?php echo base_url(); ?>public/js/script.js?a=<?php $date=date_create();echo date_timestamp_get($date);?>"></script>
<?php $this->load->view('view_footer'); ?>
