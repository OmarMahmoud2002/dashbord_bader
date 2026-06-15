<title>الرف</title>
<?php $this->load->view('view_header'); ?>
<?php $this->load->view('view_admin_sidebar');?>

<?php
$shelf_reorder_error = $this->session->flashdata('shelf_reorder_error');
?>

<div class = 'app-content admin-ui-page admin-ui-shelves'>
    <div class = 'app-content-header'>
        <h1 class = 'app-content-headerText'>الرف</h1>
    </div>
    <div class = 'app-content-actions'>
        <input id = 'Search' onkeyup = 'search()' class = 'search-bar' placeholder = 'بحث...' type = 'text'>
        <div>
            <button class = 'app-content-headerButton orderShelf'>اعادة ترتيب الرف</button>
            <button class = 'app-content-headerButton'>اضافة الرف</button>
        </div>
        
    </div>
    <div class = 'products-area-wrapper tableView'>
        <div class="products-header">
            <div class="product-cell num">رقم الرف</div>
            <div class="product-cell q">العدد</div>
            <div class="product-cell actions"></div>
        </div>
        <?php if ($shelves) { ?>
        <?php foreach ($shelves as $shelf) {?>
            <div class = 'products-row' id = '<?=$shelf->id?>'>
                <div class = 'product-cell num'><span class = 'cell-label'>رقم الرف</span> <?=$shelf->shelf_number?></div>
                <div class = 'product-cell q'>
                    <span class = 'cell-label'>العدد</span>
                    <?php
                    echo $this->Store->get_items_count_in_shelf($shelf->id);
                    ?>
                </div>
                <div class="product-cell actions">
                    <button class="show-btn"><a href = './shelves/show?id=<?=$shelf->id?>'>عرض المنتجات</a></button>
                    <button class="del-btn">حذف</button>
                </div>
            </div>
        <?php } ?>
        <?php } else { ?>
            <div class="empty-admin-state">
                <i class="bi bi-inbox"></i>
                <div>لا توجد رفوف</div>
            </div>
        <?php } ?>
    </div>
    <div class="popup popup-delete-shelf" style="display: none;">
        <div class="popup-content">
            <div class="title">
                <h3>حذف الرف</h3>
                <div class="close"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512"><path d="M342.6 150.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L192 210.7 86.6 105.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L146.7 256 41.4 361.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0L192 301.3 297.4 406.6c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L237.3 256 342.6 150.6z"></path></svg></div>
            </div>
            <form>
                <div class="inpt">
                    <label>هل تريد حذف هذا الرف؟</label>
                    <input id="deleteShelfNumber" type="text" disabled>
                </div>
                <a id="confirmDeleteShelf" class="danger-action" href="#">حذف</a>
            </form>
        </div>
    </div>
    <div class="popup popup-add-shelf" style="display: none;">
        <div class="add_task-content popup-content">
            <div class="title">
                <h3>اضافة رف جديدة</h3> 
                <div class="close"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512"><path d="M342.6 150.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L192 210.7 86.6 105.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L146.7 256 41.4 361.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0L192 301.3 297.4 406.6c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L237.3 256 342.6 150.6z"></path></svg></div>
            </div>
            <form method = 'post'>
                <div class="inpt">
                    <label>رقم الرف</label>
                    <input name = 'shelf_num' type="text" placeholder="رقم الرف">
                </div>
                <button>اضافة</button>
                <input type = 'hidden' name = 'action' value = 'add'>
            </form>
        </div>
    </div>
    <div class="popup popup-movein-serials" style="<?= $shelf_reorder_error ? 'display: block;' : 'display: none;' ?>">
        <div class="popup-shelves-content popup-content">
          <div class="title">
            <h3>اعادة ترتيب الرف</h3> 
            <div class="close"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512"><path d="M342.6 150.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L192 210.7 86.6 105.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L146.7 256 41.4 361.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0L192 301.3 297.4 406.6c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L237.3 256 342.6 150.6z"></path></svg></div>
          </div>
          <form method = 'post'>
            <label id = 'formTitleShelf'>الرف</label>
            <div class = 'inpt'>
                <select name = 'shelf_id' id = 'shelves'>
                    <?php 
                    foreach ($shelves as $shelf) {
                        $shelfnumber = $shelf->shelf_number;
                        $shelfid = $shelf->id;
                        echo "<option value = '$shelfid'>$shelfnumber</option>";
                    }
                    ?>
                </select>
                <script>
                    $(document).ready(() => {
                        $('#shelves').select2()
                    })
                </script>
            </div>
            <div class = 'formHeader'>
                <div class = 'inpt'>
                    <div class = 'serial-entry-fields'>
                        <div class = 'serial-entry-field serial-entry-field-main'>
                            <label for = 'SerialInputAdder'>رقم السيريالات</label>
                            <input type = 'text' id = 'SerialInputAdder' autocomplete="off" placeholder="رقم السيريالات">
                        </div>
                        <div class = 'serial-entry-field serial-entry-field-poster'>
                            <label for = 'PosterInputAdder'>رقم الملصق</label>
                            <input type = 'text' id = 'PosterInputAdder' autocomplete="off" placeholder="رقم الملصق">
                        </div>
                    </div>
                </div>
                <div class = 'serial-entry-actions'>
                    <button id = 'serialAdder' type="button">
                        <i class="bi bi-plus-circle"></i>
                        <span>إضافة سيريال</span>
                    </button>
                    <button id = 'shelfSerialScanStart' class = 'scan-serial-button' type="button">
                        <i class="bi bi-qr-code-scan"></i>
                        <span>Scan</span>
                    </button>
                </div>
                <div id = 'shelfSerialScannerPanel' class = 'shelf-serial-scanner-panel' hidden>
                    <div class = 'shelf-serial-scanner-toolbar'>
                        <span>
                            <i class="bi bi-camera-video"></i>
                            مسح السيريال
                        </span>
                        <div class = 'shelf-serial-scanner-actions'>
                            <button id = 'shelfSerialScanQrMode' type = 'button'>
                                <i class="bi bi-qr-code-scan"></i>
                                <span>Scan QR Code</span>
                            </button>
                            <button id = 'shelfSerialScanStop' type = 'button'>
                                <i class="bi bi-x-lg"></i>
                                <span>إغلاق الكاميرا</span>
                            </button>
                        </div>
                    </div>
                    <div id = 'shelfBarcodeTestModes' class = 'shelf-barcode-test-modes' aria-label = 'Barcode test mode'>
                        <button id = 'shelfBarcodeModeFull' type = 'button' data-mode = 'full'>Full</button>
                        <button id = 'shelfBarcodeModeWide' type = 'button' data-mode = 'wide'>Wide</button>
                    </div>
                    <div id = 'shelfSerialScannerReader' class = 'shelf-serial-scanner-reader'></div>
                    <div id = 'shelfSerialScannerStatus' class = 'shelf-serial-scanner-status' role = 'status' aria-live = 'polite'></div>
                </div>
                <div class = 'serials-total-card' aria-live="polite">
                    <span>المجموع</span>
                    <strong class = 'serials_number'>0</strong>
                </div>
            </div>
            <div id = 'serials'>
            </div>
            <?php if ($shelf_reorder_error) { ?>
                <div class="shelf-reorder-message" role="alert">
                    <i class="bi bi-exclamation-octagon"></i>
                    <div><?= nl2br(secure_data(str_replace(array('<br><br>', '<br>'), "\n", $shelf_reorder_error))) ?></div>
                </div>
            <?php } ?>

            <input type = 'hidden' name = 'action' value="rearrange_shelf">
            <button style = 'width: 100%' type = 'button' onclick="send_change_request()">حفظ</button>
          </form>
        </div>
    </div>
</div>
</div>

<style>

.formHeader {
    display: flex;
    flex-direction: column;
    margin-bottom: 20px;
}

.popup-movein-serials .popup-shelves-content {
    width: min(96vw, 1100px);
    max-height: min(92vh, 820px);
    overflow-y: auto;
}

.popup-movein-serials form {
    display: flex;
    flex-direction: column;
}

.formHeader button {
    margin: 0px !important;
    width: 100% !important;
}

.serial-entry-actions {
    display: grid;
    grid-template-columns: minmax(0, 1fr) auto;
    gap: 10px;
    width: 100%;
}

.serial-entry-actions button {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    min-height: 52px;
}

.serial-entry-actions .scan-serial-button {
    width: auto !important;
    min-width: 118px;
    padding-inline: 18px;
    background: #25364d;
    box-shadow: 0 10px 22px rgba(37, 54, 77, 0.18);
}

.serial-entry-actions .scan-serial-button:disabled {
    cursor: not-allowed;
    opacity: 0.84;
}

.serial-entry-actions .scan-serial-button[data-state="running"] {
    background: #157347;
}

.shelf-serial-scanner-panel {
    margin-top: 14px;
    padding: 12px;
    border: 1px solid #dbe5f4;
    border-radius: 12px;
    background: #f8fbff;
    box-shadow: 0 10px 24px rgba(102, 126, 234, 0.12);
}

.shelf-serial-scanner-toolbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    margin-bottom: 10px;
    color: #25364d;
    font-size: 16px;
    font-weight: 800;
}

.shelf-serial-scanner-toolbar span,
.shelf-serial-scanner-toolbar button {
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

.shelf-serial-scanner-actions {
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

.shelf-serial-scanner-toolbar button {
    width: auto !important;
    min-height: 38px;
    padding: 8px 12px;
    border: 0;
    border-radius: 9px;
    background: #eef3ff;
    color: #25364d;
    font-size: 14px;
    font-weight: 800;
    box-shadow: none;
}

.shelf-barcode-test-modes {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    margin-bottom: 10px;
}

.shelf-serial-scanner-panel[data-mode="qr"] .shelf-barcode-test-modes {
    display: none;
}

.shelf-barcode-test-modes button {
    width: auto !important;
    min-height: 34px;
    padding: 7px 12px;
    border: 1px solid #dbe5f4;
    border-radius: 8px;
    background: #fff;
    color: #25364d;
    font-size: 13px;
    font-weight: 800;
    box-shadow: none;
}

.shelf-barcode-test-modes button[data-active="true"] {
    border-color: #667eea;
    background: #eef3ff;
    color: #4b55c7;
}

.shelf-serial-scanner-reader {
    position: relative;
    width: 100%;
    min-height: 220px;
    aspect-ratio: 5 / 1.45;
    overflow: hidden;
    border: 1px dashed #bdc9e6;
    border-radius: 10px;
    background: #fff;
}

.shelf-serial-scanner-panel[data-mode="barcode"] .shelf-serial-scanner-reader::after {
    content: "";
    position: absolute;
    inset: 18% 3%;
    border-top: 2px solid rgba(255, 255, 255, 0.88);
    border-bottom: 2px solid rgba(255, 255, 255, 0.88);
    box-shadow: 0 0 0 999px rgba(0, 0, 0, 0.12);
    pointer-events: none;
}

.shelf-serial-scanner-panel[data-mode="barcode"][data-barcode-test-mode="full"] .shelf-serial-scanner-reader::after {
    display: none;
}

.shelf-serial-scanner-panel[data-mode="qr"] .shelf-serial-scanner-reader {
    width: min(100%, 560px);
    min-height: 420px;
    margin-inline: auto;
    aspect-ratio: 1 / 1;
}

.shelf-serial-scanner-reader video,
.shelf-serial-scanner-reader canvas {
    width: 100% !important;
    height: 100% !important;
    max-width: 100% !important;
    object-fit: cover;
    border-radius: 10px;
}

.shelf-serial-scanner-reader canvas.drawingBuffer {
    position: absolute;
    inset: 0;
    pointer-events: none;
}

.shelf-serial-barcode-video {
    width: 100%;
    height: 100%;
    min-height: inherit;
    object-fit: cover;
    background: #000;
}

.shelf-serial-scanner-status {
    min-height: 22px;
    margin-top: 8px;
    color: #667085;
    font-size: 14px;
    font-weight: 700;
}

.shelf-serial-scanner-status[data-type="loading"],
.shelf-serial-scanner-status[data-type="ready"] {
    color: #25364d;
}

.shelf-serial-scanner-status[data-type="success"] {
    color: #157347;
}

.shelf-serial-scanner-status[data-type="warning"] {
    color: #a15c07;
}

.shelf-serial-scanner-status[data-type="error"] {
    color: #b42318;
}

.serials-total-card {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    margin-top: 14px;
    padding: 14px 18px;
    width: 100%;
    border: 1px solid #dbe5f4;
    border-radius: 12px;
    background: #f8fbff;
    color: #25364d;
    box-shadow: 0 10px 24px rgba(102, 126, 234, 0.12);
}

.serials-total-card span {
    font-size: 18px;
    font-weight: 700;
}

.serials-total-card strong {
    min-width: 48px;
    min-height: 42px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 10px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: #fff;
    font-size: 24px;
    font-weight: 800;
    line-height: 1;
}

.formHeader .inpt input {
    font-size: 20px !important;
}

#serials {
    display: flex;
    flex-direction: column-reverse;
    gap: 8px;
    overflow: auto;
    height: fit-content;
    max-height: 180px;
    padding-inline: 2px;
}

.serial {
    display: flex;
    gap: 0;
    justify-content: space-between;
    align-items: center;
    font-size: 20px;
    width: 100%;
    min-height: 48px;
    background-color: rgb(255, 255, 255);
    border-radius: 10px;
    padding: 0px;
    overflow: hidden;
    color: black;
    border: 1px solid #dbe5f4;
    flex-shrink: 0;
    box-shadow: 0 8px 18px rgba(102, 126, 234, 0.08);
}

.serial-check-pending {
    border-color: #dbe5f4;
}

.serial-check-valid {
    border-color: #9ad5b1;
    background: #fbfffd;
}

.serial-check-invalid,
.serial-check-error {
    border-color: #f1b8b3;
    background: #fff8f7;
}

.serial-status-badge {
    width: 44px;
    align-self: stretch;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    flex: 0 0 44px;
    border-inline-start: 1px solid #dbe5f4;
    font-style: normal;
    font-size: 22px;
    font-weight: 900;
    color: #667085;
    background: #f8fbff;
}

.serial-status-badge[data-state="valid"] {
    color: #157347;
    background: #ecfdf3;
    border-inline-start-color: #b7e4c7;
}

.serial-status-badge[data-state="invalid"],
.serial-status-badge[data-state="error"] {
    color: #b42318;
    background: #fff1f0;
    border-inline-start-color: #f1b8b3;
}

.serial-status-badge[data-state="pending"] {
    color: #4b55c7;
}

.serial-poster-input {
    width: min(42%, 220px);
    min-width: 132px;
    height: 100%;
    padding: 8px 10px;
    border: 0;
    border-inline-start: 1px solid #dbe5f4;
    outline: none;
    color: #25364d;
    font-size: 16px;
    font-weight: 700;
    background: #f8fbff;
}

.serial-poster-input:focus {
    background: #fff;
    box-shadow: inset 0 0 0 2px rgba(102, 126, 234, 0.22);
}

.serial-poster-input:disabled {
    opacity: 0.72;
}

.serial span {
    flex: 1 1 auto;
    min-width: 0;
    line-height: normal;
    padding: 10px;
    font-weight: 700;
    font-family: Arial, Helvetica, sans-serif;
    border: none;
    outline: none;
    overflow-wrap: anywhere;
    color: #25364d;
}

.serial i {
    font-size: 20px;
    width: 44px;
    align-self: stretch;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    color: #b42318;
    border-inline-start: 1px solid #f1c9c5;
    background: #fff5f4;
}

.serial i:hover {
    color: rgb(107, 35, 35);
}

@media (max-width: 640px) {
    .serial-entry-actions {
        grid-template-columns: 1fr;
    }

    .serial-entry-actions .scan-serial-button {
        width: 100% !important;
    }

    .shelf-serial-scanner-toolbar {
        align-items: stretch;
        flex-direction: column;
    }

    .shelf-serial-scanner-actions {
        flex-direction: column;
        align-items: stretch;
    }

    .shelf-serial-scanner-toolbar button {
        width: 100% !important;
        justify-content: center;
    }

    .shelf-barcode-test-modes {
        width: 100%;
    }

    .shelf-barcode-test-modes button {
        flex: 1 1 0;
    }

    .shelf-serial-scanner-reader {
        min-height: 230px;
        aspect-ratio: 2.6 / 1;
    }

    .shelf-serial-scanner-panel[data-mode="qr"] .shelf-serial-scanner-reader {
        width: 100%;
        min-height: 320px;
        aspect-ratio: 1 / 1;
    }

    #serials {
        max-height: 220px;
    }

    .serial {
        align-items: stretch;
        flex-wrap: nowrap;
        min-height: 44px;
    }

    .serial span {
        flex: 1 1 auto;
        flex-basis: auto;
        min-width: 0;
        min-height: 44px;
        display: flex;
        align-items: center;
        padding: 8px;
        font-size: 13px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .serial-poster-input {
        order: initial;
        flex: 0 0 clamp(76px, 29%, 112px);
        width: auto;
        min-width: 0;
        min-height: 44px;
        height: auto;
        padding: 8px 6px;
        border-inline-start: 1px solid #dbe5f4;
        border-top: 0;
        font-size: 13px;
        text-align: center;
    }

    .serial-status-badge {
        width: 38px;
        flex-basis: 38px;
        min-height: 44px;
        font-size: 18px;
    }

    .serial i {
        width: 38px;
        flex: 0 0 38px;
        min-height: 44px;
        font-size: 16px;
    }
}

</style>
<style>
  .select2-container--open {
      z-index: 99999; /* A very high value */
  }
  
  .select2 {
      width: 100% !important;
      font-size: 20px;
  }

  .select2-selection {
      padding: 5px;
      height: auto !important;
  }
</style>


<script src="https://code.jquery.com/jquery-3.6.0.js" crossorigin="anonymous"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js" type="text/javascript"></script>

<script>
    window.shelfSerialCheckUrl = "<?= base_url() . MOD_VALUE . 'admin/shelves/check_serial' ?>";
</script>
<script src="<?php echo base_url(); ?>public/js/script.js?a=<?php $date=date_create();echo date_timestamp_get($date);?>"></script>
<script src="<?php echo base_url(); ?>public/js/shelf.js?a=<?php $date=date_create();echo date_timestamp_get($date);?>"></script>

<?php $this->load->view('view_footer'); ?>
