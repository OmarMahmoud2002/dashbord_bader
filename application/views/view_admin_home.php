<title>الصفحة الرئيسية</title>
<?php $this->load->view('view_header'); ?>
<?php $this->load->view('view_admin_sidebar'); ?>

<div class="app-content admin-ui-page admin-ui-home">
    <div class="app-content-header">
        <h1 class="app-content-headerText">الصفحة الرئيسية</h1>
    </div>

    <div class="products-area-wrapper tableView">
        <div class="empty-admin-state">
            <i class="bi bi-hourglass-split"></i>
            <div>جاري انشاء الصفحة</div>
        </div>
    </div>
</div>
</div>

<style>
    .admin-ui-page.admin-ui-home .app-content-headerText::before {
        content: "\F425";
    }

    .admin-ui-page.admin-ui-home .products-area-wrapper {
        min-height: 280px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
</style>

<script src="<?php echo base_url(); ?>public/js/script.js?a=<?php $date=date_create();echo date_timestamp_get($date);?>"></script>
<?php $this->load->view('view_footer'); ?>
