<title>البحث عن منتج</title>
<?php $this->load->view('view_header'); ?>
<?php $this->load->view('view_admin_sidebar')?>

<style>
    .serialAdd, .newserialAdd {
        margin-right: 15px;
        margin-bottom: 3px;
        color: black;
        padding: 5px;
        border-radius: 5px;
        cursor: pointer;
    }
    .serialAdd:hover, .newserialAdd:hover {
        background-color: rgba(230, 230, 230, 0.5);
    }

    .remCustody {
        padding: 5px;
        border-radius: 5px;
        background-color: rgb(116, 192, 228);
        color: white;
        font-weight: bold;
        font-size: 20px;
        cursor: pointer;
    }

</style>

<div class = 'app-content admin-ui-page admin-ui-product-search'>
    <div class = 'app-content-header'>
        <h1 class = 'app-content-headerText'>البحث عن منتج</h1>
    </div>
    <div class="prod_form">
        <form class = 'formTag product-search-card'>
          <div class="search-card-intro">
            <span class="search-card-icon" aria-hidden="true"><i class="bi bi-upc-scan"></i></span>
            <h1>مطابقة رقم التحقق</h1>
            <p>يرجى إدخال الرقم التسلسلي أو رقم الصنف للتحقق من عملية المطابقة.</p>
          </div>
          <div class = 'top_part' >
            <button type = 'button' id = 'startOverBtn'>البدء من جديد</button>
            <?php $count = count($cart); ?>
            <div class="cart-menu">
              <div class="cart" title="سلة المنتجات" aria-label="سلة المنتجات">
                <i class="bi bi-basket2" aria-hidden="true"></i>
                <?php
                if ($count > 0) {
                  echo '<p>' . $count . '</p>';
                }
                ?>
              </div>
              <div class="cart-popup" >
                <?php if ($count > 0) { ?>
                  <?php foreach ($cart as $cart_item) { ?>
                      <div class = 'cart-item' id = '<?=$cart_item->id?>' data-item-id = '<?=$cart_item->item_id?>' data-serial_control = '<?=$cart_item->serial_control?>'>
                    <?php
                    $item = $this->Store->get_store_item(['id' => $cart_item->item_id]);
                    $shelf_id = $this->Store->get_item_shelf($item['item_code'], $item['serial_number'], $item['barcode']);

                    if ($shelf_id) {
                      $shelfObj = $this->Shelves->get_one_shelf_by_id($shelf_id);
                      $shelf = $shelfObj->shelf_number;
                    } else {
                      $shelf = 'عام';
                    }
                    ?>

                    <input type="checkbox" onchange="toggleItemSelection(this)">
                    <a href="/public/img/products/default_product_image.png" class="img" data-fancybox="cart-gallery" data-caption="Product 1">
                      <img src="/public/img/products/default_product_image.png" />
                    </a>
                    <div class="item-details">
                    <div class="l">
                        <p class = 'itemname'>
                          <span>اسم:</span>
                          <?php 
                          $exploded_name = explode(' ', $item['item_description']);
                          $chosen_words = array_splice($exploded_name, 0, 3);
                          $item_name = implode(' ', $chosen_words);
                          ?>
                          <span title = '<?=$item['item_description']?>'><?=$item_name?></span>
                        </p>
                        <p class = 'item_number' data-number = '<?=$item['serial_control'] == 'yes' ? $item['serial_number'] : $item['barcode']?>'>
                          <span>الرقم:</span>
                          <span><?=$item['serial_control'] == 'yes' ? $item['serial_number'] : $item['barcode']?></span>
                        </p>
                    </div>
                    <div class="l">
                        <p class = 'itemshelf'>
                          <span>الرف:</span>
                          <span><?=$shelf?></span>
                        </p>
                        <div class="cart-verify">
                            <div class="image-container">
                                <svg class="toggle-input-image" clip-rule="evenodd" fill-rule="evenodd" stroke-linejoin="round"
                                    stroke-miterlimit="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path
                                    d="m22 5c0-.478-.379-1-1-1h-18c-.62 0-1 .519-1 1v14c0 .621.52 1 1 1h18c.478 0 1-.379 1-1zm-18.5.5h17v13h-17zm5.5 2c0-.276-.224-.5-.5-.5s-.5.224-.5.5v9c0 .276.224.5.5.5s.5-.224.5-.5zm8 0c0-.276-.224-.5-.5-.5s-.5.224-.5.5v9c0 .276.224.5.5.5s.5-.224.5-.5zm2 0c0-.276-.224-.5-.5-.5s-.5.224-.5.5v9c0 .276.224.5.5.5s.5-.224.5-.5zm-13.25-.5c-.414 0-.75.336-.75.75v8.5c0 .414.336.75.75.75s.75-.336.75-.75v-8.5c0-.414-.336-.75-.75-.75zm5.5 0c-.414 0-.75.336-.75.75v8.5c0 .414.336.75.75.75s.75-.336.75-.75v-8.5c0-.414-.336-.75-.75-.75zm3-.013c-.414 0-.75.336-.75.75v8.5c0 .414.336.75.75.75s.75-.336.75-.75v-8.5c0-.414-.336-.75-.75-.75z"
                                    fill-rule="nonzero" />
                                </svg>
                                <input type="text" class="toggle-input-field" placeholder="ادخل رقم السريال">
                                <span class="verification-result correct">✔</span>
                                <span class="verification-result incorrect">✖</span>
                            </div>
                        </div>
                    </div>
                    </div>
                    <div class="options-item">
                        <button type = 'button' class = 'acc-btn'>✔</button>
                        <button type = 'button' class = 'rem-btn'>✖</button>
                    </div>
                      </div>

                  <?php } ?>
                  <div class="select-all-container">
                      <input type="checkbox" id="selectAllCheckbox" onchange="toggleAllSelections(this)">
                      <label for="selectAllCheckbox">تحديد الكل</label>
                  </div>
                <?php } else { ?>
                  <div class="cart-empty-state">لا توجد منتجات</div>
                <?php } ?>
              </div>
            </div>
          </div>
          <ul class="list">
            <li class="active" data-elm=".conts">البحث رقم السريال</li>
            <li data-elm=".conts2">البحث رقم الصنف</li>
          </ul>
          <div class="ff conts">
            <div class="inpts">
              <div class="inpt" id="serialNumber">
                <div style = 'display: flex; align-items: center; gap: 10px;'>
                    <input type="text" id="serialNumberInput" placeholder="أدخل رقم السريال">
                    <i class = "fa fa-plus serialAdd hide" onclick = "register_item_to_cart()"></i>
                </div>
                
                <p id="serialNumberError" class="hide">الحد الأدنى للبحث 6 أرقام</p>
                <p id = "serialNumberHistory" class  = 'hide'><a>اضغط هنا للمزيد من التفاصيل</a></p>
              </div>
              <div class="select_type hide">
                <label>حدد ماتريد البحث عنه</label>
              </div>
              <div class="inpt hide" id="verification">
                <input type="text" id="verificationInput" placeholder="تحقق من رقم السريال"><br>
              </div>
            </div>
            <div class="checkResult">
            </div>
            <div class="btns">
              <button type="button" id="nextBtn">التالي</button>
              <button type="button" id="checkBtn" class="hide">تحقق</button>
              <button type="button" id="deliveryBtn" class="hide">تسليم الجهاز</button>
            </div>
          </div>
          <div class="ff conts2 hide">
            <div class="inpts">
              <div class="inpt" id="itemCode">
                <div style = 'display: flex; align-items: center; gap: 10px;'>
                    <input type="text" id="itemCodeInput" placeholder="ادخل الصنف"><br>
                    <i class = "fa fa-plus newserialAdd hide" onclick = "register_item_to_cart()"></i>
                </div>
                
                <p id="itemCodeError" class="hide">الحد الأدنى للبحث 6 </p>
              </div>
              <div class="inpt hide" id="newVerification">
                <input type="text" id="newVerificationInput" placeholder="تحقق من رقم الباركود"><br>
              </div>
            </div>
            <div class="newCheckResult"></div>
            <div class="btns">
              <button type="button" id="newNextBtn">التالي</button>
              <button type="button" id="newCheckBtn" class="hide">تحقق</button>
              <button type="button" id="newDeliveryBtn" class="hide">تسليم الجهاز</button>
            </div>
          </div>
        </form>
        <div class="prod_infos hide">
          <div class="product-details-card">
            <button type="button" class="product-details-close" aria-label="إغلاق">
              <i class="bi bi-x-lg" aria-hidden="true"></i>
            </button>
            <div class="product-details-head">
            <span class="product-details-icon" aria-hidden="true"><i class="bi bi-box-seam"></i></span>
            <div>
              <h1>تفاصيل المنتج</h1>
              <p>بيانات المنتج المرتبطة بنتيجة البحث الحالية</p>
            </div>
          </div>
            <div class="cnts">
            <table class="prod_info">
              <tbody><tr>

                <td>اسم المنتج</td>
                <td id = 'product_name'></td>
              </tr>
              <tr>
                <td>رقم الرف</td>
                <td id = 'item_shelf'></td>
              </tr>
              <tr>
                <td>الايتم كود</td>
                <td id = 'item_code'></td>
              </tr>
              <tr>
                <td>المخزون</td>
                <td id = 'subinventory_code'></td>
              </tr>
              <tr>
                <td>المتوفر</td>
                <td id = 'total_quantity'></td>
              </tr>
              <tr>
                <td>رقم الباركود</td>
                <td id = 'item_barcode'></td>
              </tr>
              <tr>
                <td>رقم الملصق</td>
                <td id = 'poster_number'></td>
              </tr>
            </tbody></table>
            <a id = 'product_image' class="product-image-preview" href="" data-fancybox="product-gallery" data-caption>
              <img src="" alt="صورة المنتج">
            </a>
            </div>
          </div>
        </div>
    </div>
</div>
<div class="delivery_popup hide">
    <div class="popup_content">
      <div class="close">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512"><!--!Font Awesome Free 6.5.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path d="M342.6 150.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L192 210.7 86.6 105.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L146.7 256 41.4 361.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0L192 301.3 297.4 406.6c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L237.3 256 342.6 150.6z"></path></svg>
      </div>
      <h2 id="delivery_popup_title">الرجاء اختيار نوع التسليم</h2>
      <div class="btns" data-title="تسليم الجهاز">
        <button class="delivery_btn" data-type=".popup_employee" data-title="تسليم لموظف">تسليم لموظف</button>
        <button class="delivery_btn" data-type=".popup_delivery" data-title="توصيل الجهاز">توصيل </button>
      </div>
      <div class="delivery_box popup_delivery hide">
        <div class = 'content'>
          <div class="inpt">
            <label>ادخل رقم الطلب:</label>
            <input type="text" id  = 'delivery_order_number'>
          </div>
          <button onclick = 'product_delivery_confirm(event)'>توصيل</button>
        </div>
        
        <div class="done hide">
          <div class="sa">
            <div class="sa-success">
              <div class="sa-success-tip"></div>
              <div class="sa-success-long"></div>
              <div class="sa-success-placeholder"></div>
              <div class="sa-success-fix"></div>
            </div>
          </div>
        </div>
      </div>
      
      <div class="delivery_box popup_employee hide">
        <div class="content">
          <div class="inpt">
            <label>اختر موظف:</label>
            <select id="selectElement">
              
              <option value = "">&nbsp;</option>
              <?php foreach ($employees as $employee) {?>
                <option value="<?=$employee['user_id']?>"><?=$employee['user_fillname']?></option>
              <?php } ?>
            </select>
            <script>

            
                $(document).ready(() => {
                    $('#selectElement').select2()
                })
            </script>
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
          </div>
          <div class="inpt">
            <label>ادخل رقم الطلب:</label>
            <input id="empoo" type="text">
          </div>
          <p id="popEmplError" class="hide">الرجاء ملئ موظف و رقم الطلب</p>
          <button id="popup_employee_btn">تسليم</button>
        </div>
        <div class="done hide">
          <div class="sa">
            <div class="sa-success">
              <div class="sa-success-tip"></div>
              <div class="sa-success-long"></div>
              <div class="sa-success-placeholder"></div>
              <div class="sa-success-fix"></div>
            </div>
          </div>
        </div>
      </div>
    </div>
</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.umd.js"></script>

<script src="<?php echo base_url(); ?>public/js/script.js?a=<?php $date=date_create();echo date_timestamp_get($date);?>"></script>
<script src="<?php echo base_url(); ?>public/js/search_product.js?a=<?php $date=date_create();echo date_timestamp_get($date);?>"></script>
<?php $this->load->view('view_footer')?>
