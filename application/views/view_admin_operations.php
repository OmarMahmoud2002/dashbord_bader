<title>العمليات</title>
<?php $this->load->view('view_header'); ?>
<?php $this->load->view('view_admin_sidebar');?>

<div class="app-content admin-ui-page admin-ui-product-operations">
    <div class="operations-shell">
        <header class="operations-hero">
            <div class="operations-title">
                <span class="operations-title-icon" aria-hidden="true">
                    <i class="bi bi-clock-history"></i>
                </span>
                <div>
                    <h1>تفاصيل المنتج</h1>
                    <p>ابحث بالرقم التسلسلي .</p>
                </div>
            </div>
            <form class="input-section operations-search-form" method="get">
                <label for="serial-number">أدخل الرقم التسلسلي</label>
                <div class="operations-search-control">
                    <i class="bi bi-upc-scan" aria-hidden="true"></i>
                    <input type="text" id="serial-number" name="serial" value="<?=htmlspecialchars($serial ? $serial : '')?>" placeholder="أدخل الرقم التسلسلي">
                    <button type="submit">
                        <i class="bi bi-search"></i>
                        <span>بحث</span>
                    </button>
                </div>
            </form>
        </header>

        <section class="top-container operations-panel">
            <div class="operations-section-heading">
                <i class="bi bi-box-seam" aria-hidden="true"></i>
                <h2>تفاصيل البند</h2>
            </div>
            <div class="top-header operations-info-grid">
                <div class="box-content operations-info-item">
                    <label>رقم الصنف</label>
                    <span class="iteme"><?=$info['code']?></span>
                </div>
                <div class="box-content operations-info-item">
                    <label>اسم المنتج</label>
                    <span class="iteme"><?=$info['name']?></span>
                </div>
                <div class="box-content operations-info-item">
                    <label>الرقم التسلسلي</label>
                    <span class="iteme"><?=strtoupper($serial ? $serial : '')?></span>
                </div>
            </div>
        </section>

        <section class="transaction-details operations-panel">
            <div class="operations-section-heading">
                <i class="bi bi-list-check" aria-hidden="true"></i>
                <h2>تفاصيل العملية <?=strtoupper($serial ? $serial : '')?></h2>
            </div>
            <div class="table-container operations-table-wrap">
                <table class="operations-table">
                    <thead>
                        <tr>
                            <th>التاريخ</th>
                            <th>طلب المبيعات</th>
                            <th>نوع العملية</th>
                            <th>تم الإنشاء بواسطة</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($operations as $operation) { ?>
                            <tr>
                                <td><?=date_format(date_create($operation->date_created), 'd-m-Y h:i:s A')?></td>
                                <td><?=$operation->sales_order?></td>
                                <td><?=$operation->operation?></td>
                                <td><?=$this->Model_admin->get_user_by_id($operation->made_by)['user_fillname']?></td>
                            </tr>
                        <?php } ?>
                        <?php if (empty($operations)) { ?>
                            <tr class="operations-empty-row">
                                <td colspan="4">
                                    <i class="bi bi-inbox"></i>
                                    <span>لا توجد عمليات لعرضها حالياً</span>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.js" crossorigin="anonymous"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

<script src="<?php echo base_url(); ?>public/js/script.js?a=<?php $date=date_create();echo date_timestamp_get($date);?>"></script>

<?php $this->load->view('view_footer'); ?>
