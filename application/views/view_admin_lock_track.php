<title>تأكيد التقفيلة</title>
<?php $this->view('view_header'); ?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />

 <?php
$CI =& get_instance();
$CI->load->model('Model_admin');
$CI2 =& get_instance();
$CI2->load->model('Excel_import_model');

?>

	<?php $this->view('view_admin_sidebar'); ?>

     <div class="app-content admin-ui-lock-track">
      <div class="app-content-header">

        <h1 class="app-content-headerText">تأكيد التقفيلة</h1>
        
         <!-- Button trigger modal -->
<button type="button" class="app-content-headerButton lock-add-button" data-bs-toggle="modal" data-bs-target="#exampleModal">
  اضافة تقفيلة
</button>
      </div>
   
      <div class="app-content-actions">
        <input id="Search" onkeyup="search()" class="search-bar" placeholder="بحث ..." type="text">
        <div class="app-content-actions-wrapper">
          <button class="action-button list active" title="List View">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-list"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/></svg>
          </button>
          <button class="action-button grid" title="Grid View">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-grid"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
          </button>
        </div>
      </div>
      <div class="page-container">
        <div class="content-wrap">
    <div class="tabs">
        <div class="tab" id="tab1">الدفعات النقدية</div>
        <div class="tab" id="tab2">المبيعات</div>
    </div>

    <div id="content1" class="tab-contents">
        <!-- محتوى الدفعات النقدية -->
        <div class="products-area-wrapper tableView">
            <div class="products-header">
                <div class="product-cell name">التاريخ</div>
                <div class="product-cell name">الموظف</div>
                <div class="product-cell nums">المبلغ كاش</div>
                <div class="product-cell serial">المبلغ بطاقة</div>
                <div class="product-cell type">الاجمالي</div>
                <div class="product-cell actions"></div>
            </div>
            <?php
            $i=0;
            foreach ($locktrack as $row) {
                $id = $row['user_lock_userid'];
                $c_user_name = $CI->Model_admin->get_user_by_id($row['user_lock_userid']);
                $user_name = $c_user_name['user_fillname'];
                $i++;
                ?>
            <div class="products-row">
                <div class="product-cell name"><span class="cell-label">التاريخ</span><?php echo safe_data($row['user_lock_time']); ?> </div>
                <div class="product-cell name"><span class="cell-label">الموظف</span><?php echo safe_data($user_name); ?> </div>
                <div class="product-cell nums"><span class="cell-label">المبلغ كاش</span><?php echo safe_data($row['user_lock_cash']); ?></div>
                <div class="product-cell serial"><span class="cell-label">المبلغ بطاقة</span><?php echo safe_data($row['user_lock_span']); ?></div>
                <div class="product-cell type"><span class="cell-label">الاجمالي</span><?php echo safe_data($row['user_lock_cash'] + ($row['user_lock_span'])); ?></div>
                <div class="product-cell actions">
                    <button style="background-color: var(--action-color);" data-bs-toggle="modal" data-bs-target="#myModal<?php echo safe_data($i); ?>" class="valid-btn">مراجعة</button>
                    <button data-bs-toggle="modal" data-bs-target="#myModaldell<?php echo safe_data($i); ?>" class="del-btn">رفض</button>
                </div>
            </div>

            <!-- Valid -->
            <div class="modal fade" id="myModal<?php echo safe_data($i); ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <div>
          <h5 class="modal-title" id="myModalLabel">مراجعة التقفيلة: <span><?php echo safe_data($user_name); ?></span></h5>
        </div>
        <div>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
      </div>
      <div class="modal-body">
        <?php echo form_open(base_url().MOD_VALUE.'admin/edit_lock', array('class' => '')); ?>
        <input name="user_lock_id" type="hidden" value="<?php echo safe_data($row['user_lock_id']); ?>">
        <div class="form-group mb-3">
          <label>المبلغ كاش</label>
          <input type="text" name="user_lock_cash" value="<?php echo safe_data($row['user_lock_cash']); ?>" class="form-control">
        </div>
        <div class="form-group mb-3">
          <label>المبلغ بطاقة</label>
          <input type="text" name="user_lock_span" value="<?php echo safe_data($row['user_lock_span']); ?>" class="form-control">
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
        <button type="submit" class="btn btn-primary" name="form">تأكيد</button>
        <?php echo form_close(); ?>
      </div>
    </div>
  </div>
</div>


            <!-- رفض -->
            <div class="modal fade" id="myModaldell<?php echo safe_data($i); ?>" tabindex="-1" role="dialog" aria-labelledby="myModaldellLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="myModaldellLabel">حذف التقفيلة بتاريخ: <span><?php echo safe_data($row['user_lock_time']); ?></span></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <?php echo form_open(base_url().MOD_VALUE.'admin/delete_lock', array('class' => '')); ?>
        <input name="user_lock_id" type="hidden" value="<?php echo safe_data($row['user_lock_id']); ?>">
        <div class="form-group">
          <label>هل ترغب فى حذف التقفيلة للموظف</label>
          <input disabled type="text" value="<?php echo safe_data($user_name); ?>" class="form-control" placeholder="اسم الموظف">
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
        <button type="submit" class="btn btn-danger" name="form2">حذف</button>
        <?php echo form_close(); ?>
      </div>
    </div>
  </div>
</div>

            <?php
            }
            ?>
        </div>
    </div>

    <div id="content2" class="tab-contents">
        <div class="products-area-wrapper tableView">
            <div class="products-header">
                <div class="product-cell image"># </div>
                <div class="product-cell image">التاريخ </div>
                <div class="product-cell image">اسم الموظف</div>
                <div class="product-cell id">شحن إلكتروني </div>
                <div class="product-cell added-date">مبيعات جوي</div>
                <div class="product-cell added-date">مبيعات كويك بلس</div>
                <div class="product-cell actions">اجراء</div>
            </div>
            <?php
            $i2=0;            
            foreach ($excel as $row) {
                $c_user_name = $CI2->Model_admin->get_user_by_id($row['insert_excel_uid']);
                $user_name = $c_user_name['user_fillname'];
                $i2++;
            ?>
            <div class="products-row">
                <div class="product-cell image">
                    <span class="cell-label">#</span> <?php echo safe_data($i2); ?>
                </div>
                <div class="product-cell image">
                    <span class="cell-label">التاريخ</span><?php echo safe_data($row['insert_excel_date']); ?>
                </div>
                <div class="product-cell image">
                    <span class="cell-label">الموظف</span><?php echo safe_data($user_name); ?>
                </div>
                <div class="product-cell id"><span class="cell-label">شحن إلكتروني</span><?php echo safe_data($row['insert_excel_electronic']); ?></div>
                <div class="product-cell added-date"><span class="cell-label">مبيعات جوي:</span><?php echo safe_data($row['insert_excel_jowy']); ?></div>
                <div class="product-cell added-date"><span class="cell-label">مبيعات كويك بلس:</span><?php echo safe_data($row['insert_excel_quickplus']); ?></div>
                <div class="product-cell actions">
                    <button data-bs-toggle="modal" data-bs-target="#myModaledit<?php echo safe_data($i2); ?>" class="edit-btn">تعديل</button>
                    <button data-bs-toggle="modal" data-bs-target="#myModaldelete<?php echo safe_data($i2); ?>" class="del-btn">حذف</button>
                </div>
            </div>
            <!-- Edit Employee -->
            <div class="modal fade" id="myModaledit<?php echo safe_data($i2); ?>" tabindex="-1" role="dialog" aria-labelledby="myModalEditLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="myModalEditLabel">تعديل تقفيلة الموظف: <span><?php echo safe_data($user_name); ?></span></h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <?php echo form_open(base_url().MOD_VALUE.'admin/edit_lock_track', array('class' => '')); ?>
                            <input name="insert_excel_id" type="hidden" value="<?php echo safe_data($row['insert_excel_id']); ?>">
                            <div class="form-group">
                                <label>شحن إلكتروني</label>
                                <input type="text" name="insert_excel_electronic" value="<?php echo safe_data($row['insert_excel_electronic']); ?>" class="form-control">
                            </div>
                            <div class="form-group">
                                <label>مبيعات جوي</label>
                                <input type="text" name="insert_excel_jowy" value="<?php echo safe_data($row['insert_excel_jowy']); ?>" class="form-control">
                            </div>
                            <div class="form-group">
                                <label>مبيعات كويك بلس</label>
                                <input type="text" name="insert_excel_quickplus" value="<?php echo safe_data($row['insert_excel_quickplus']); ?>" class="form-control">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
                            <button type="submit" class="btn btn-primary" name="form1">تعديل</button>
                            <?php echo form_close(); ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Delete Employee -->
            <div class="modal fade" id="myModaldelete<?php echo safe_data($i2); ?>" tabindex="-1" role="dialog" aria-labelledby="myModaldellEditLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="myModaldellEditLabel">حذف تقفيلة الموظف</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <?php echo form_open(base_url().MOD_VALUE.'admin/delete_excellock', array('class' => '')); ?>
                            <input name="insert_excel_id" type="hidden" value="<?php echo safe_data($row['insert_excel_id']); ?>">
                            <div class="form-group">
                                <label>هل ترغب فى حذف</label>
                                <input disabled type="text" value="<?php echo safe_data($user_name); ?>" class="form-control" placeholder="اسم الموظف">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
                            <button type="submit" class="btn btn-danger" name="form2">حذف</button>
                            <?php echo form_close(); ?>
                        </div>
                    </div>
                </div>
            </div>

            <?php
            }
            ?>
        </div>
    </div>
        </div>
    </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var tabs = document.querySelectorAll('.tab');
            var contents = document.querySelectorAll('.tab-contents');

            tabs.forEach(function (tab) {
                tab.addEventListener('click', function () {
                    var target = tab.id.replace('tab', 'content');
                    
                    tabs.forEach(function (t) { t.classList.remove('active'); });
                    contents.forEach(function (c) { c.classList.remove('active'); });
                    
                    tab.classList.add('active');
                    document.getElementById(target).classList.add('active');
                });
            });

            // Activate the first tab by default
            tabs[0].click();
        });
    </script>



		<!-- Modal -->
<!-- Modal Structure -->

<div class="modal fade lock-add-modal" id="exampleModal"  aria-labelledby="exampleModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
  <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
    <div class="modal-content add-task-content">
      <div class="modal-header d-flex justify-content-between">
        <div>
          <h5 class="modal-title" id="exampleModalLabel">اضافة تقفيلة</h5>
        </div>
        <div>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="إغلاق"></button>
        </div>
      </div>
      <div class="modal-body">
        <?php echo form_open(base_url().MOD_VALUE.'user/sendlock', array('class' => '')); ?>

        <div class="cash-calculator">
          <div class="lock-modal-fields">
          <div class="mb-3">
            <label for="datepicker">التاريخ</label>
            <input type="date" class="form-control" id="user_lock_time" name = 'user_lock_time' placeholder="dd-mm-yyyy" required>

          </div>
          <div class="mb-3">
            <label for="user_id">الموظف</label>
            <select class="form-select" name="user_id" style="width: 100%;" id="user_id" required>
              <option disabled selected>اختر</option>
              <?php
              $arr = $CI->Model_admin->getemployees();
              foreach ($arr as $row1) {
                ?>
          <option value="<?php echo safe_data($row1['user_id']); ?>"><?php echo safe_data($row1['user_fillname']); ?></option>
                <?php
              }
              ?>
            </select>
          </div>
          </div>
        
        <!-- التبويبات -->
        <ul class="nav nav-tabs mb-3" id="myTab" role="tablist">
          <li class="nav-item">
            <a class="nav-link active" id="payment-tab" data-bs-toggle="tab" href="#payment" role="tab" aria-controls="payment" aria-selected="true">الدفعات النقدية</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" id="charging-tab" data-bs-toggle="tab" href="#charging" role="tab" aria-controls="charging" aria-selected="false">المبيعات </a>
          </li>
        </ul>
        <div class="tab-content" id="myTabContent">
          <!-- التبويب الأول: تفاصيل الدفع -->
          <div class="tab-pane fade show active" id="payment" role="tabpanel" aria-labelledby="payment-tab">
            <div class = 'mb-3 position-relative'>
              <div class="mb-3">
                <label for="user_lock_cash" class = 'form-label'>
                  المبلغ كاش (نقدي)
                  <button type="button" class="btn btn-sm btn-outline-secondary p-1 ms-2"
                    id="openCalculatorPopup">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                      class="bi bi-calculator" viewBox="0 0 16 16">
                      <path
                        d="M12 1a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1zM4 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2z" />
                      <path
                        d="M4 2.5a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-.5.5h-7a.5.5 0 0 1-.5-.5zm0 4a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5zm0 3a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5zm0 3a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5zm3-6a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5zm0 3a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5zm0 3a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5zm3-6a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5zm0 3a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v4a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5z" />
                    </svg>
                  </button>
                </label>
                <input type="number" class="form-control number" name="user_lock_cash" id="user_lock_cash" placeholder="أدخل المبلغ" step = '0.01' oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');">
              </div>
              <div class="mb-3">
                <label for="user_lock_span" class = 'form-label d-flex align-items-center'>
                  <span>المبلغ بطاقة</span>
                  <button type="button" class="btn btn-sm btn-outline-secondary p-1 ms-2 m-2"
                    data-bs-toggle="modal" data-bs-target="#amountModal">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                      class="bi bi-credit-card" viewBox="0 0 16 16">
                      <path
                        d="M0 4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2zm2-1a1 1 0 0 0-1 1v1h14V4a1 1 0 0 0-1-1zm13 4H1v5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1z" />
                      <path d="M2 10a1 1 0 0 1 1-1h1a1 1 0 0 1 1 1v1a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1z" />
                    </svg>
                  </button>
                </label>
                <input type = "number" class="form-control number" name="user_lock_span" id="user_lock_span" placeholder="أدخل المبلغ" step = '0.01' oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');">
              </div>
              <div class="mb-3">
                <label for="totalAmountField" class = 'form-label'>الاجمالي</label>
                <input type="text" class="form-control" id="totalAmountField" readonly>
              </div>
            </div>
            
          </div>
          
          <!-- التبويب الثاني: تفاصيل الشحن -->
          <div class="tab-pane fade" id="charging" role="tabpanel" aria-labelledby="charging-tab">
            <div class="form-group mb-3">
              <label for="user_lock_electronic" class = 'form-label'>شحن إلكتروني</label>
              <input type="text" class="form-control number" name="user_lock_electronic" id="user_lock_electronic" placeholder="أدخل المبلغ" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');">
            </div>
            <div class="form-group mb-3">
              <label for="user_lock_jowy" class = 'form-label'>مبيعات جوى</label>
              <input type="text" class="form-control number" name="user_lock_jowy" id="user_lock_jowy" placeholder="أدخل المبلغ" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');">
            </div>
            <div class="form-group mb-3">
              <label for="user_lock_quick_plus" class = 'form-label'>مبيعات كويك بلس</label>
              <input type="text" class="form-control number" name="user_lock_quick_plus" id="user_lock_quick_plus" placeholder="أدخل المبلغ" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');">
            </div>
          </div>
        </div>
        
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-primary" name="form_lock">إضافة</button>
      </div>
      <?php echo form_close(); ?>
    </div>
  </div>
</div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
  
  // حساب الإجمالي في التبويب الأول
  function calculateTotal() {
    var cash = parseFloat(document.getElementById('user_lock_cash').value) || 0;
    var span = parseFloat(document.getElementById('user_lock_span').value) || 0;
    var total = cash + span;
    document.getElementById('totalAmountField').value = total.toFixed(2);
  }

  // ربط وظيفة الحساب مع إدخال البيانات في التبويب الأول
  document.getElementById('user_lock_cash').addEventListener('input', calculateTotal);
  document.getElementById('user_lock_span').addEventListener('input', calculateTotal);
});
</script>

<!-- Modal حاسبة الكاش (popup صغير) -->
<div class="modal fade modal-sm " id="cashCalculatorModal" tabindex="-1" aria-labelledby="cashCalculatorLabel"
  aria-hidden="true">
  <!-- Modal حاسبة الكاش -->
  <div class="modal-dialog modal-md modal-dialog-scrollable" style="max-width: 300px;">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white d-flex justify-content-between">
        <div>
          <h5 class="modal-title" id="cashCalculatorLabel">حاسبة الكاش</h5>
        </div>
        <div>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="إغلاق"></button>
        </div>
      </div>
      <div class="modal-body p-0">
        <div style=" overflow-y: auto;">
          <table class="table table-bordered text-center mb-0">
            <thead class="table-secondary sticky-top">
              <tr>
                <th>العدد</th>
                <th>فئة الريال</th>
                <th>المجموع</th>
              </tr>
            </thead>
            <tbody id="cashTable">
            </tbody>
          </table>
        </div>
        <table class="table table-bordered text-center mb-0">
          <tfoot>
            <tr class="table-primary">
              <td colspan="2"><strong>الإجمالي</strong></td>
              <td><strong id="totalAmount">0.00</strong></td>
            </tr>
            <tr>
              <td colspan="3">
                <button class="btn btn-success w-100" onclick="submitCash()">اعتماد المبلغ</button>
              </td>
            </tr>
          </tfoot>
        </table>
      </div>
    </div>
  </div>
</div>



<!-- Modal Content -->
<div class="modal fade" id="amountModal" tabindex="-1" aria-labelledby="amountModalLabel" aria-hidden="true">
<div class="modal-dialog modal-m " style="max-width: 350px;">
  <div class="modal-content">
    <div class="modal-header d-flex justify-content-between">
      <div>
        <h5 class="modal-title" id="amountModalLabel">إضافة مبلغ</h5>
      </div>
      <div><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="إغلاق"></button></div>
    </div>
    <div class="modal-body">
      <!-- محتوى المودال -->
      <div class="container bg-white p-4 rounded shadow" style="max-width: 350px;">
        <label for="amountInput" class="form-label">إضافة مبلغ</label>
        <div class = 'd-flex' style = 'gap: 20px'>
          <input type = "text" id="amountInput" class="form-control mb-3" placeholder="أدخل مبلغًا">
          <input type = "text" id="repetitionInput" class="form-control mb-3 w-25" placeholder="X" style = 'text-align: center'>
        </div>
        
        <button class="btn btn-info w-100 mb-3 text-white" onclick="addAmount()">إضافة مبلغ</button>
        <div class="table-responsive" style="max-height: 320px; overflow-y: auto;">
          <table class="table table-bordered text-center">
            <thead class="table-light sticky-top bg-white"></thead>
            <tr>
              <th>#</th>
              <th>المبلغ</th>
              <th>حذف</th>
            </tr>
            </thead>
            <tbody id="amountList">
              <!-- سيتم تعبئة الجدول هنا ديناميكيًا -->
            </tbody>
          </table>
        </div>
        <table class="table table-bordered text-center mt-2">
          <tfoot class="table-light">
            <tr>
              <td colspan="2" class="text-right">عدد المبالغ:</td>
              <td id="amountCount" class="footer-value bg-info">0</td>
            </tr>
            <tr>
              <td colspan="2" class="text-right">إجمالي المبلغ:</td>
              <td id="totalAmount1" class="footer-value bg-info">0</td>
            </tr>
          </tfoot>
        </table>
        <button onclick="esc()" id="Approval" class="btn btn-info w-100 mt-3 text-white">اعتماد المبلغ</button>
      </div>
    </div>
  </div>
</div>
</div>

<?php $this->view('view_footer'); ?>

<script src="<?php echo base_url(); ?>public/js/lock_track.js?a=<?php $date=date_create();echo date_timestamp_get($date);?>"></script>
<script src="<?php echo base_url(); ?>public/js/script.js?a=<?php $date=date_create();echo date_timestamp_get($date);?>"></script>
<script src="<?php echo base_url(); ?>public/js/home.js?a=<?php $date=date_create();echo date_timestamp_get($date);?>"></script>


<script>

    
$(function() {
    $('input[name="user_lock_time"]').daterangepicker({
        singleDatePicker: true,
        showDropdowns: true,
        locale: {
          format: 'YYYY-MM-DD' // تنسيق التاريخ
        }
    });
});
</script>

<script>
jQuery(function($) {
        $(document).on('change', '#user_lock_cash, #user_lock_span', function () {
            var user_lock_cash =   document.getElementById("user_lock_cash").value 
            var user_lock_span =   document.getElementById("user_lock_span").value 
            var number = parseFloat(user_lock_cash) + parseFloat(user_lock_span);
            $('#total').val(number);
        });
    });

 </script>
