<title>العمليات</title>
<?php $this->load->view('view_header'); ?>
<?php $this->load->view('view_admin_sidebar');?>

<div class="app-content">
    <style>
        /* متغيرات الألوان */
        :root {
            --bg-color: #f9f9f9;
            --main-color: #00abf0;
            --secondary-color: #112e42;
            --highlight-color: #ffcc00;
            --highlight-hover: #ffbb00;
            --text-color: #333;
            --border-color: #ddd;
        }

        .container {
            max-width: 90%;
            margin: auto;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .top-container {
            border: 1px solid var(--main-color);
            padding: 10px;
            margin: 40px 0px;
            border-radius: 10px;
        }
        .container h4 {
            width: 180px;
            margin: 0 0 15px;
            padding: 10px 20px;
            transform: translateY(-35px);
            color: #fff;
            border-radius: 20px;
            background-color: var(--main-color);
            text-align: center;
        }

        .top-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 40px;
            height: 20px;


        }

        .box-content {
            display: flex;
            flex-direction: column;
        }
        label{
            padding: 3px;
        }

        header {
            margin-bottom: 20px;
        }

        header h3 {
            margin: 0;
            color: var(--main-color);
        }

        label {
            font-size: 14px;
            color: blueviolet;
        }

        .iteme{
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ddd;
            color: #333;
            outline: none;
            min-width: 300px;
            transition: 0.3s;
            font-weight: normal;
        }

        /* تحسين إدخال البيانات */
        .input-section {
            margin-top: 10px;
            display: flex;
            align-items: center;
            gap: 10px;
            justify-content: right;
        }

        .input-section input {
            padding: 10px;
            border-radius: 5px;
            border: 2px solid var(--main-color);
            outline: none;
            width: 300px;
            transition: 0.3s;
        }

        .input-section button {
            padding: 10px 20px;
            border: none;
            background-color: var(--main-color);
            color: white;
            border-radius: 5px;
            cursor: pointer;
            transition: 0.3s;
        }

        .input-section button:hover {
            background-color: blueviolet;
        }

        /* تحسين تصميم الجدول */
        .transaction-details {
            border: 2px solid var(--main-color);
            border-radius: 8px;
            padding: 15px;
            margin-top: 50px;
            background-color: #f0f8ff;
        }

        .transaction-details h4 {


            width: fit-content;
            margin: 0 0 15px;
            padding: 10px 20px;
            transform: translateY(-35px);
            color: #fff;
            border-radius: 20px;
            background-color: var(--main-color);
            text-align: center;
        }

        .table-container {
            max-height: 400px;
            align-items: center;
            border: 1px solid blueviolet;
            border-radius: 5px;
            text-align: center;
            overflow-y: auto;
        }

        table {
            margin: 0 auto;
            width: 100%;
            border-collapse: collapse;
            min-width: 800px;
            font-size: 14px;
            color: var(--text-color);
        }

        thead {
            position: sticky;
            top: 0;
            background-color: blueviolet;
        }

        thead th {
            color: #fff;
            font-weight: bold;
            padding: 12px 8px;
            border: 1px solid blueviolet;
            text-align: center;
        }

        tbody tr:hover {
            background-color: var(--main-color);
            color: white;
            transition: 0.3s;
        }

        td {
            padding: 12px 8px;
            border: 1px solid blueviolet;
            white-space: nowrap;
        }

        /* تحسين شريط التمرير */
        .table-container::-webkit-scrollbar {
            width: 12px;

        }

        .table-container::-webkit-scrollbar-track {
            background: #f9f9f9;
            border-radius: 10px;
        }

        .table-container::-webkit-scrollbar-thumb {
            background: linear-gradient(180deg, var(--highlight-color), #ffeb99);
            border-radius: 10px;
            border: 2px solid #fff;
        }

        .table-container::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(180deg, var(--highlight-hover), #ffdd77);
        }
    </style>
    <div class="container">
      <header>
         <h3>تفاصيل المنتج</h3>
         <form class="input-section">
            <label for="serial-number">أدخل الرقم التسلسلي:</label>
            <input type="text" id="serial-number" name = 'serial' placeholder="أدخل الرقم التسلسلي">
            <button>بحث</button>
        </form>
      </header>
      <div class="top-container">
        <h4>تفاصيل البند</h4>
        <div class="top-header">
            <div class="box-content">
                <label> رقم الصنف</label>
                <label class="iteme"><?=$info['code']?></label>
            </div>
            <div class="box-content">
                <label> اسم المنتج</label>
                <label class="iteme"><?=$info['name']?></label>
            </div>
            <div class="box-content">
                <label>رقم الطلب</label>
                <label class="iteme"></label>
            </div>
        </div>
      </div>
      <section class="transaction-details">
         <h4>تفاصيل العملية <?=strtoupper($serial)?></h4>
         <div class="table-container">
            <table>
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