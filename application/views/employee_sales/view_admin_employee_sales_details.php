<title>مبيعات الموظف</title>
<?php $this->load->view('view_header'); ?>
<?php $this->load->view('view_admin_sidebar'); ?>



<style>
    body {
        overflow-x: hidden;
    }

    .container {
        max-width: 100%;
        overflow-x: hidden;
        padding-left: 90px;
        padding-right: 90px;
    }

    @media (max-width: 768px) {
        .container {
            padding-left: 15px;
            padding-right: 15px;
        }
    }

    .employee-card {
        background: linear-gradient(135deg, #f5f7fa 0%, #e4e7eb 100%);
        border-radius: 15px;
        box-shadow: 0 8px 16px rgba(0,0,0,0.1);
        margin-bottom: 30px;
        padding: 25px;
        transition: transform 0.3s ease;
    }
    
    .employee-card:hover {
        transform: translateY(-5px);
    }

    .info-label {
        color: #2c3e50;
        font-weight: 600;
        margin-bottom: 5px;
    }

    .info-value {
        color: #34495e;
        font-size: 1.1em;
        padding: 8px;
        background: rgba(255,255,255,0.7);
        border-radius: 8px;
        margin-bottom: 10px;
    }

    .table-container {
        background: white;
        border-radius: 15px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        margin-bottom: 30px;
        overflow: visible;
        display: flex;
        flex-direction: column;
    }

    .table-title {
        background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
        color: white;
        padding: 12px 20px;
        font-size: 1.1em;
        font-weight: 600;
        text-align: center;
        margin-bottom: 0;
        border-radius: 15px 15px 0 0;
    }

    .custom-table {
        margin-bottom: 0;
        font-size: 0.9rem;
        width: 100% !important;
        table-layout: fixed;
    }

    .custom-table thead th {
        background: #f8f9fa;
        color: #2c3e50;
        font-weight: bold;
        text-align: center;
        border-bottom: 2px solid #e9ecef;
        padding: 8px 4px;
        white-space: normal;
        font-size: 16px;
        word-wrap: break-word;
    }

    /* تحديد عرض عمود البيانات */
    .custom-table thead th:first-child {
        width: 28%;
    }

    .custom-table thead th:not(:first-child) {
        width: auto;
    }

    .custom-table tbody tr:hover {
        background-color: #f8f9fa;
    }

    .custom-table td {
        vertical-align: middle;
        text-align: center;
        padding: 6px 4px;
        white-space: normal;
        font-size: 0.8rem;
        word-wrap: break-word;
        overflow-wrap: break-word;
        background-color: white;
    }

    /* تنسيق الأرقام */
    .custom-table td:not(:first-child) {
        font-family: 'Cairo', 'Courier New', monospace;
        font-weight: 600;
        font-size: 15px;
    }

    .table-responsive {
        overflow: visible;
        border-radius: 0 0 15px 15px;
    }

    /* جعل الجدول يأخذ كامل العرض */
    .table-container .table {
        width: 100% !important;
    }

    .table-wrapper {
        display: flex;
        justify-content: center;
        width: 100%;
        overflow-x: auto;
    }

    .tables-side-by-side {
        display: flex;
        gap: 20px;
        justify-content: space-between;
        align-items: stretch;
        width: 100%;
        margin-bottom: 30px;
    }

    .tables-side-by-side .table-container:first-of-type {
        flex: 0 0 62%;
    }

    .tables-side-by-side .table-container:last-of-type {
        flex: 0 0 calc(38% - 20px);
    }

    .tables-side-by-side .table-container {
        display: flex;
        flex-direction: column;
        min-width: 0;
    }

    .tables-side-by-side .table-responsive {
        flex: 1;
        overflow: visible;
    }

    .tables-side-by-side .table {
        height: 100%;
    }

    /* جعل الخلايا تمتد لملء المساحة */
    .tables-side-by-side tbody {
        height: 100%;
    }

    /* Responsive للشاشات الصغيرة */
    @media (max-width: 1200px) {
        .tables-side-by-side {
            flex-direction: column;
            gap: 30px;
        }

        .tables-side-by-side .table-container:first-of-type,
        .tables-side-by-side .table-container:last-of-type {
            flex: 1 1 100%;
            width: 100%;
        }

        .custom-table {
            font-size: 0.75rem;
        }

        .custom-table thead th {
            font-size: 0.7rem;
            padding: 6px 2px;
        }

        /* توسيع عمود البيانات في الشاشات الصغيرة */
        .custom-table thead th:first-child {
            width: 30%;
        }

        .custom-table td {
            font-size: 0.7rem;
            padding: 4px 2px;
        }

        .custom-table td:not(:first-child) {
            font-size: 0.7rem;
        }

        .product-name {
            font-size: 0.7rem;
            padding: 4px 2px;
        }

        .percentage-badge {
            font-size: 0.7rem;
            padding: 3px 6px;
        }
    }

    @media (max-width: 768px) {
        .search-form {
            padding: 15px;
        }

        .search-form .row {
            flex-direction: column;
        }

        .search-form .col-md-8,
        .search-form .col-md-4,
        .search-form .col-md-9,
        .search-form .col-md-3 {
            width: 100%;
            margin-bottom: 10px;
        }

        #toggleEmployeeInfo {
            font-size: 0.85rem;
            padding: 6px 12px;
        }

        .employee-card .row {
            flex-direction: column;
        }

        .employee-card .col-md-4 {
            width: 100%;
            margin-bottom: 15px;
        }

        .info-value {
            font-size: 0.95em;
        }

        .custom-table {
            font-size: 0.65rem;
        }

        .custom-table thead th {
            font-size: 0.6rem;
            padding: 4px 1px;
            font-weight: 700;
        }

        /* توسيع عمود البيانات أكثر في الشاشات الصغيرة جداً */
        .custom-table thead th:first-child {
            width: 28%;
        }

        .custom-table td {
            font-size: 0.7rem;
            padding: 3px 1px;
        }

        .custom-table td:not(:first-child) {
            font-size: 0.68rem;
            font-weight: 400;
        }

        .product-name {
            font-size: 0.65rem;
            padding: 3px 1px;
        }

        .percentage-badge {
            font-size: 0.65rem;
            padding: 3px 5px;
        }

        .table-title {
            font-size: 0.9em;
            padding: 10px 15px;
        }
    }

    @media (max-width: 480px) {
        .custom-table {
            font-size: 0.55rem;
        }

        .custom-table thead th {
            font-size: 0.7rem;
            padding: 4px 1px;
            font-weight: 700;
        }

        /* تصغير عمود البيانات في شاشات الموبايل */
        .custom-table thead th:first-child {
            width: 25%;
        }

        .custom-table td {
            font-size: 0.65rem;
            padding: 2px 0.5px;
        }

        .custom-table td:not(:first-child) {
            font-size: 0.65rem;
            font-weight: 400;
        }

        .product-name {
            font-size: 0.55rem;
            padding: 2px 1px;
        }

        .percentage-badge {
            font-size: 0.55rem;
            padding: 2px 4px;
            font-size: 14px;
        }

        .table-title {
            font-size: 0.8em;
            padding: 8px 10px;
        }
    }

    /* إضافة حدود خفيفة للخلايا */
    .custom-table td,
    .custom-table th {
        border: 1px solid #e9ecef;
    }

    .custom-table tbody tr:nth-child(even) {
        background-color: #fafbfc;
    }

    .custom-table tbody tr:hover {
        background-color: #f1f3f5 !important;
    }

    /* تنعيم زوايا آخر صف في الجدول */
    .custom-table tbody tr:last-child td {
        border-bottom: 1px solid #e9ecef;
    }

    .custom-table tbody tr:last-child td:first-child {
        border-bottom-right-radius: 15px;
    }

    .custom-table tbody tr:last-child td:last-child {
        border-bottom-left-radius: 15px;
    }

    /* إخفاء الحدود للصف الأخير في الأسفل */
    .table-responsive {
        border-bottom-left-radius: 15px;
        border-bottom-right-radius: 15px;
        overflow: hidden;
    }

    /* تنسيق خانة البيانات السابقة */
    .alert-info {
        border-radius: 12px;
        border: 1px solid #b8daff;
    }

    #previousDataDate {
        border-radius: 8px;
        border: 2px solid #17a2b8;
        transition: all 0.3s ease;
    }

    #previousDataDate:focus {
        border-color: #0056b3;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
    }

    #searchPreviousDataBtn {
        padding: 0.375rem 0.75rem;
        font-size: 0.875rem;
        border-radius: 6px;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 38px;
        height: 38px;
    }

    #searchPreviousDataBtn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 123, 255, 0.3);
    }

    #searchPreviousDataBtn:active {
        transform: translateY(0);
    }

    @media (max-width: 768px) {
        .alert-info .row {
            flex-direction: column;
        }

        .alert-info .col-md-4 {
            margin-top: 15px;
        }

        .alert-info .d-flex {
            justify-content: start !important;
            flex-wrap: wrap;
        }

        #previousDataDate {
            max-width: 100% !important;
            flex: 1;
            min-width: 150px;
        }

        #searchPreviousDataBtn {
            margin-top: 8px;
            width: 100%;
        }
    }

    .percentage-badge {
        padding: 6px 12px;
        border-radius: 20px;
        font-weight: 600;
    }

    .product-name {
        font-weight: 700;
        color: #1a237e;
        padding: 7px 3px;
        /* border-radius: 6px; */
        /* background: #f8f9fa; */
        display: inline-block;
        text-align: center;
        /* border: 1px solid #e3e6f0; */
        box-shadow: 0 2px 2px rgba(0,0,0,0.05);
        transition: all 0.3s ease;
        font-size: 16px;
        white-space: normal;
        word-wrap: break-word;
        max-width: 100%;
    }

    .product-name:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 4px rgba(0,0,0,0.1);
        /* background: #fff; */
    }

    .percentage-badge {
        padding: 4px 6px;
        border-radius: 20px;
        font-weight: 400;
        font-size: 14px;
    }

    .search-form {
        background: white;
        padding: 20px;
        border-radius: 15px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        margin-bottom: 30px;
    }

    .search-form .btn-primary {
        transition: all 0.3s ease;
    }

    .search-form .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,123,255,0.3);
    }

    @media (max-width: 768px) {
        .search-form {
            padding: 15px;
        }

        .search-form .row {
            flex-direction: column;
        }

        .search-form .col-md-8,
        .search-form .col-md-4,
        .search-form .col-md-9,
        .search-form .col-md-3 {
            width: 100%;
            margin-bottom: 10px;
        }

        #toggleEmployeeInfo {
            font-size: 0.85rem;
            padding: 6px 12px;
        }

        .employee-card .row {
            flex-direction: column;
        }

        .employee-card .col-md-4 {
            width: 100%;
            margin-bottom: 15px;
        }

        .info-value {
            font-size: 0.95em;
        }
    }

    .btn-download {
        background: linear-gradient(135deg, #27ae60 0%, #2ecc71 100%);
        border: none;
        padding: 10px 25px;
        border-radius: 25px;
        color: white;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .btn-download:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(46,204,113,0.3);
        color: white;
    }

    .stats-summary {
        display: flex;
        justify-content: space-around;
        margin-bottom: 30px;
    }

    .stat-item {
        text-align: center;
        padding: 15px;
        background: white;
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0,0,0,0.05);
        flex: 1;
        margin: 0 10px;
    }

    .stat-value {
        font-size: 1.8em;
        font-weight: 700;
        color: #2c3e50;
    }

    .stat-label {
        color: #7f8c8d;
        margin-top: 5px;
    }

    #toggleEmployeeInfo {
        transition: all 0.3s ease;
        box-shadow: 0 2px 8px rgba(0,0,0,0.15);
        padding: 8px 12px;
        font-weight: 600;
        border-radius: 8px;
        font-size: 0.9rem;
    }

    #toggleEmployeeInfo:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.2);
    }

    #toggleEmployeeInfo svg {
        margin-left: 5px;
        transition: transform 0.3s ease;
    }

    .employee-card {
        transition: all 0.4s ease;
    }

    .calculation-options-container {
        background: white;
        padding: 20px 80px 20px 55px;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        margin-bottom: 20px;
    }

    .calculation-options-container .col-md-12 {
        text-align: center;
    }

    /* تنسيق الخلايا القابلة للضغط */
    .clickable-cell {
        cursor: pointer;
        position: relative;
        transition: all 0.2s ease;
    }

    .clickable-cell:hover {
        background-color: #e3f2fd !important;
        transform: scale(1.02);
    }

    .clickable-cell::after {
        content: '👁';
        position: absolute;
        left: 5px;
        top: 50%;
        transform: translateY(-50%);
        opacity: 0;
        font-size: 12px;
        transition: opacity 0.2s ease;
    }

    .clickable-cell:hover::after {
        opacity: 0.6;
    }

    /* تنسيق الـ Modal */
    .subordinates-modal .modal-dialog {
        max-width: 95%;
        margin: 1.75rem auto;
    }

    .subordinates-modal .modal-content {
        border-radius: 15px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.2);
        border: none;
    }

    .subordinates-modal .modal-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 15px 15px 0 0;
        padding: 20px 30px;
        border-bottom: none;
    }

    .subordinates-modal .modal-title {
        font-weight: 700;
        font-size: 1.3rem;
    }

    .subordinates-modal .btn-close {
        filter: brightness(0) invert(1);
        opacity: 0.8;
    }

    .subordinates-modal .btn-close:hover {
        opacity: 1;
    }

    .subordinates-modal .modal-body {
        padding: 30px;
        background: #f8f9fa;
    }

    .subordinates-modal .table-container {
        background: white;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    }

    .subordinates-modal .custom-table thead th {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        font-weight: 600;
        padding: 15px 10px;
        border: none;
        white-space: normal;
        word-wrap: break-word;
    }

    /* تكبير عرض عمود الاسم */
    .subordinates-modal .custom-table thead th:first-child,
    .subordinates-modal .custom-table tbody td:first-child,
    .subordinates-modal .custom-table tfoot td:first-child {
        width: 25%;
        min-width: 150px;
    }

    /* تصغير حجم خط الاسم وجعله يلتف */
    .subordinates-modal .custom-table tbody td:first-child {
        font-size: 0.85rem;
        white-space: normal;
        word-wrap: break-word;
        overflow-wrap: break-word;
        line-height: 1.3;
        text-align: right;
        padding: 8px 10px;
    }

    /* جعل الأرقام تلتف داخل الخلايا */
    .subordinates-modal .custom-table tbody td,
    .subordinates-modal .custom-table tfoot td {
        white-space: normal;
        word-wrap: break-word;
        overflow-wrap: break-word;
        max-width: 100px;
    }

    .subordinates-modal .custom-table tbody td:not(:first-child),
    .subordinates-modal .custom-table tfoot td:not(:first-child) {
        padding: 8px 5px;
    }

    .subordinates-modal .custom-table tbody tr {
        transition: all 0.2s ease;
    }

    .subordinates-modal .custom-table tbody tr:hover {
        background-color: #f0f4ff !important;
        transform: translateX(-3px);
    }

    .subordinates-modal .custom-table tfoot tr {
        background: linear-gradient(135deg, #f5f7fa 0%, #e4e7eb 100%);
        font-weight: 700;
        font-size: 1.05em;
    }

    .subordinates-modal .custom-table tfoot td {
        padding: 15px 10px;
        border-top: 3px solid #667eea;
    }

    /* Loading spinner */
    .loading-spinner {
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 60px;
    }

    .spinner-border {
        width: 3rem;
        height: 3rem;
        border-width: 0.3em;
    }

    /* Pagination */
    .subordinates-pagination {
        margin-top: 20px;
        display: flex;
        justify-content: center;
        gap: 10px;
    }

    .subordinates-pagination .page-btn {
        padding: 8px 15px;
        border: 2px solid #667eea;
        background: white;
        color: #667eea;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.3s ease;
        font-weight: 600;
    }

    .subordinates-pagination .page-btn:hover {
        background: #667eea;
        color: white;
        transform: translateY(-2px);
    }

    .subordinates-pagination .page-btn.active {
        background: #667eea;
        color: white;
    }

    .subordinates-pagination .page-btn:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    .subordinates-pagination .page-info {
        padding: 8px 15px;
        color: #667eea;
        font-weight: 600;
    }

    .calculation-options-container .form-select {
        border: 2px solid #e9ecef;
        border-radius: 8px;
        padding: 8px 15px;
        transition: all 0.3s ease;
    }

    .calculation-options-container .form-select:focus {
        border-color: #007bff;
        box-shadow: 0 0 0 0.2rem rgba(0,123,255,0.25);
    }

    /* ============================================
       Responsive Design للشاشات الصغيرة والموبايل
       ============================================ */

    /* للشاشات الصغيرة (Tablets) */
    @media (max-width: 768px) {
        .subordinates-modal .modal-dialog {
            max-width: 98%;
            margin: 0.5rem auto;
        }

        .subordinates-modal .modal-header {
            padding: 15px 20px;
        }

        .subordinates-modal .modal-title {
            font-size: 1rem;
        }

        .subordinates-modal .modal-body {
            padding: 15px;
        }

        .subordinates-modal .custom-table {
            font-size: 0.85rem;
        }

        .subordinates-modal .custom-table thead th {
            padding: 10px 5px;
            font-size: 0.8rem;
        }

        .subordinates-modal .custom-table tbody td,
        .subordinates-modal .custom-table tfoot td {
            padding: 8px 5px;
            font-size: 0.8rem;
        }

        /* تكبير عرض عمود الاسم في Tablets */
        .subordinates-modal .custom-table thead th:first-child,
        .subordinates-modal .custom-table tbody td:first-child,
        .subordinates-modal .custom-table tfoot td:first-child {
            width: 30%;
            min-width: 120px;
        }

        /* تصغير حجم خط الاسم */
        .subordinates-modal .custom-table tbody td:first-child {
            font-size: 0.75rem;
            white-space: normal;
            word-wrap: break-word;
            overflow-wrap: break-word;
            line-height: 1.2;
            padding: 6px 8px;
        }

        /* إخفاء عمود المنصب */
        .subordinates-modal .position-column {
            display: none;
        }

        /* تصغير النسبة وإزالة الخلفية الملونة */
        .subordinates-modal .percentage-badge {
            background: none !important;
            color: #333 !important;
            padding: 0 !important;
            font-size: 0.75rem;
            font-weight: 600;
        }

        /* جعل الأرقام تلتف في Tablets */
        .subordinates-modal .custom-table tbody td:not(:first-child):not(.position-column),
        .subordinates-modal .custom-table tfoot td:not(:first-child):not(.position-column) {
            white-space: normal;
            word-break: break-word;
        }

        .subordinates-pagination .page-btn {
            padding: 6px 10px;
            font-size: 0.85rem;
        }

        .subordinates-pagination .page-info {
            padding: 6px 10px;
            font-size: 0.85rem;
        }
    }

    /* للموبايل (الشاشات الصغيرة جداً) */
    @media (max-width: 576px) {
        .subordinates-modal .modal-dialog {
            max-width: 100%;
            margin: 0;
            height: 100vh;
        }

        .subordinates-modal .modal-content {
            border-radius: 0;
            height: 100vh;
        }

        .subordinates-modal .modal-header {
            padding: 12px 15px;
            border-radius: 0;
        }

        .subordinates-modal .modal-title {
            font-size: 0.9rem;
        }

        .subordinates-modal .modal-body {
            padding: 10px;
            overflow-x: auto;
        }

        .subordinates-modal .custom-table {
            font-size: 0.75rem;
            min-width: 100%;
        }

        .subordinates-modal .custom-table thead th {
            padding: 8px 3px;
            font-size: 0.7rem;
            white-space: normal;
        }

        .subordinates-modal .custom-table tbody td,
        .subordinates-modal .custom-table tfoot td {
            padding: 6px 3px;
            font-size: 0.7rem;
        }

        /* تكبير عرض عمود الاسم في الموبايل */
        .subordinates-modal .custom-table thead th:first-child,
        .subordinates-modal .custom-table tbody td:first-child,
        .subordinates-modal .custom-table tfoot td:first-child {
            width: 35%;
            min-width: 100px;
        }

        /* تصغير حجم خط الاسم وجعله يلتف */
        .subordinates-modal .custom-table tbody td:first-child {
            font-size: 0.65rem;
            white-space: normal;
            word-wrap: break-word;
            overflow-wrap: break-word;
            line-height: 1.2;
            padding: 5px 6px;
            text-align: right;
        }

        /* باقي الأعمدة */
        .subordinates-modal .custom-table tbody td:not(:first-child) {
            white-space: nowrap;
        }

        /* إخفاء عمود المنصب */
        .subordinates-modal .position-column {
            display: none;
        }

        /* تصغير النسبة وإزالة الخلفية الملونة */
        .subordinates-modal .percentage-badge {
            background: none !important;
            color: #333 !important;
            padding: 0 !important;
            font-size: 0.65rem;
            font-weight: 600;
        }

        /* جعل الأرقام تلتف في الموبايل */
        .subordinates-modal .custom-table tbody td:not(:first-child):not(.position-column),
        .subordinates-modal .custom-table tfoot td:not(:first-child):not(.position-column) {
            white-space: normal;
            word-break: break-word;
            overflow-wrap: break-word;
        }

        .subordinates-modal .custom-table tbody tr:hover {
            transform: none;
        }

        .subordinates-pagination {
            margin-top: 10px;
            gap: 5px;
        }

        .subordinates-pagination .page-btn {
            padding: 5px 8px;
            font-size: 0.75rem;
        }

        .subordinates-pagination .page-info {
            padding: 5px 8px;
            font-size: 0.75rem;
        }

        .loading-spinner {
            padding: 40px;
        }

        .spinner-border {
            width: 2rem;
            height: 2rem;
        }
    }

    .calculation-options-container .form-select:hover {
        border-color: #007bff;
    }

    @media (max-width: 768px) {
        .calculation-options-container .row {
            flex-direction: column;
        }

        .calculation-options-container .col-md-6,
        .calculation-options-container .col-md-12 {
            width: 100%;
            margin-bottom: 15px;
        }

        .calculation-options-container .d-flex {
            flex-direction: column;
        }

        .calculation-options-container .form-select {
            max-width: 100% !important;
            margin-top: 10px;
        }

        .calculation-options-container .col-md-12 {
            text-align: center;
        }
    }
</style>

<div class = 'app-content'>
    <div class="container-fluid py-4">
        <!-- عرض الملف المرفوع والتواريخ -->
        <?php
        $file_uploaded_name = $this->Variables->getdata('uploaded_file_name');
        $individualsDate = $this->session->userdata('individuals_date');
        $businessDate = $this->session->userdata('business_date');
        if ($file_uploaded_name):
        ?>
            <div class="alert alert-info">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <strong>الملف المرفوع حاليًا:</strong> <?= htmlspecialchars($file_uploaded_name) ?>
                        <?php if ($individualsDate || $businessDate): ?>
                            <br>
                            <?php if ($individualsDate): ?>
                                <strong>الأفراد حتى تاريخ:</strong> <?= htmlspecialchars($individualsDate) ?>
                                <?php if ($businessDate): ?> | <?php endif; ?>
                            <?php endif; ?>
                            <?php if ($businessDate): ?>
                                <strong>الأعمال حتى تاريخ:</strong> <?= htmlspecialchars($businessDate) ?>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                    <form class="col-md-4" method="post" action="<?= base_url('admin/process_employee_sales_search') ?>">
                        <div class="d-flex align-items-center justify-content-end gap-2">
                            <label for="previousDataDate" class="mb-0" style="white-space: nowrap;">
                                <strong>البيانات السابقة بتاريخ:</strong>
                            </label>
                            <input type="date"
                                id="previousDataDate"
                                class="form-control form-control-sm"
                                style="max-width: 150px;"
                                placeholder="اختر التاريخ"
                                name="individuals_date">
                            <button type="submit"
                                    id="searchPreviousDataBtn"
                                    class="btn btn-sm btn-primary"
                                    title="بحث عن البيانات السابقة"
                                    style="white-space: nowrap;">
                                <i class="fas fa-search"></i>
                            </button>
                            <input type="hidden" name="search_term" value="<?= $search_term_value ? $search_term_value : '' ?>">
                        </div>
                    </form>
                </div>
            </div>
        <?php endif; ?>

        <!-- نموذج البحث -->
        <div class="search-form">
            <form action="<?= base_url('admin/process_employee_sales_search') ?>" method="post" class="row g-3 align-items-center">
                <div class="col-md-9">
                    <input type="text" name="search_term" class="form-control form-control-lg" 
                            placeholder="ابحث باستخدام الاسم، رقم الهوية، أو الرقم الوظيفي" 
                            style="border-radius: 8px;"
                            value="<?= htmlspecialchars($search_term_value ? $search_term_value : '') ?>">
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary btn-lg w-100" style="border-radius: 8px;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-search me-1" viewBox="0 0 16 16">
                            <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001q.044.06.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1 1 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0"/>
                        </svg>
                        بحث
                    </button>
                </div>
            </form>
        </div>

        <?php if (isset($search_error)): ?>
            <div class="alert alert-danger" role="alert">
                <?= htmlspecialchars($search_error) ?>
            </div>
        <?php endif; ?>

        <?php if (isset($employee_data) && !empty($employee_data)): ?>
            <!-- زر Toggle لإخفاء/إظهار معلومات الموظف -->
            <div class="d-flex justify-content-start mb-3">
                <button class="btn btn-outline-primary" type="button" id="toggleEmployeeInfo" title="إظهار/إخفاء معلومات الموظف">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eye-slash" viewBox="0 0 16 16">
                        <path d="m10.79 12.912-1.614-1.615a3.5 3.5 0 0 1-4.474-4.474l-2.06-2.06C.938 6.278 0 8 0 8s3 5.5 8 5.5a7 7 0 0 0 2.79-.588M5.21 3.088A7 7 0 0 1 8 2.5c5 0 8 5.5 8 5.5s-.939 1.721-2.641 3.238l-2.062-2.062a3.5 3.5 0 0 0-4.474-4.474z"/>
                        <path d="M5.525 7.646a2.5 2.5 0 0 0 2.829 2.829zm4.95.708-2.829-2.83a2.5 2.5 0 0 1 2.829 2.829zm3.171 6-12-12 .708-.708 12 12z"/>
                    </svg>
                    <span id="toggleText"></span>
                </button>
            </div>

            <!-- معلومات الموظف -->
            <div class="employee-card" id="employeeInfoCard" style="display: none; opacity: 0;">
                <div class="row">
                    <div class="col-md-4">
                        <div class="info-label">الاسم</div>
                        <div class="info-value"><?= htmlspecialchars($employee_data['employee_info']['name']) ?></div>
                        
                        <div class="info-label">رقم الهوية</div>
                        <div class="info-value"><?= htmlspecialchars($employee_data['employee_info']['national_id']) ?></div>
                        
                        <div class="info-label">الرقم الوظيفي</div>
                        <div class="info-value"><?= htmlspecialchars($employee_data['employee_info']['emp_id']) ?></div>
                    </div>
                    <div class="col-md-4">
                        <div class="info-label">المنصب</div>
                        <div class="info-value"><?= htmlspecialchars($employee_data['employee_info']['position']) ?></div>
                        
                        <div class="info-label">المنطقة</div>
                        <div class="info-value"><?= htmlspecialchars($employee_data['employee_info']['region']) ?></div>
                        
                        <div class="info-label">الفرع</div>
                        <div class="info-value"><?= htmlspecialchars($employee_data['employee_info']['branch_name']) ?></div>
                    </div>
                    <div class="col-md-4">
                        <div class="info-label">كود الفرع</div>
                        <div class="info-value"><?= htmlspecialchars($employee_data['employee_info']['branch_code']) ?></div>
                        
                        <div class="info-label">ملاحظات</div>
                        <div class="info-value"><?= htmlspecialchars($employee_data['employee_info']['note']) ?></div>
                        
                        <div class="info-label">نسبة الخصم</div>
                        <div class="info-value"><?= htmlspecialchars($employee_data['employee_info']['discount_percentage']) ?></div>
                    </div>
                </div>
            </div>

            <!-- الجدول الأول -->
            <?php if (!empty($employee_data['table1']) || !empty($employee_data['table2'])): ?>
                <!-- خيارات طريقة احتساب المبيعات واسم الموظف -->
                <div class="calculation-options-container">
                    <div class="row align-items-center mb-3">
                        <?php
                        // التحقق من المنصب
                        $position = $employee_data['employee_info']['position'] ? $employee_data['employee_info']['position'] : '';
                        $showCalculationMethod = (trim($position) === 'Branch Manager' || trim($position) === 'Branch Supervisor' || trim($position) === 'Area Manager');
                        ?>

                        <div class="col-md-4 text-start">
                            <h5 class="mb-0" style="font-weight: 700; color: #007bff; font-size: 25px;">
                                <?= htmlspecialchars($employee_data['employee_info']['name']) ?>
                            </h5>
                        </div>

                        <?php if ($showCalculationMethod) { ?>
                            <div class = "col-md-2 form-check" style = "font-size: 25px;">
                                <input class = "form-check-input" type = "checkbox" onchange = "togglePosition()">
                                <label>المنصب</label>
                            </div>
                        <?php } ?>

                        <?php 
                        
                        if ($employee_data['employee_info']['position'] == 'Branch Supervisor') {
                            $option1_text = 'مبيعات الموظفين';
                            $option2_text = 'مبيعاتي مع الموظفين';
                            $option3_text = 'مبيعاتي';
                        } else if ($employee_data['employee_info']['position'] == 'Branch Manager') {
                            $option1_text = 'مبيعات الموظفين';
                            $option2_text = 'مبيعات المشرفين مع الموظفين';
                            $option3_text = 'مبيعاتي';
                        } else if ($employee_data['employee_info']['position'] == 'Area Manager') {
                            $option1_text = 'مبيعات الموظفين';
                            $option2_text = 'مبيعات المشرفين و الموظفين و المدراء ';
                            $option3_text = 'مبيعاتي';
                        }

                        ?>

                        <?php if ($showCalculationMethod): ?>
                            <div class="col-md-6">
                                <form action = '<?= base_url('admin/process_employee_sales_search') ?>' method = 'post'>
                                    <input type = 'hidden' name = 'search_term' value = '<?= htmlspecialchars($employee_data['employee_info']['emp_id'] ? $employee_data['employee_info']['emp_id'] : '') ?>'>
                                    <div class="d-flex align-items-center justify-content-end">
                                        <label for="calculation_method" class="form-label mb-0 ms-3" style="font-weight: 600; color: #2c3e50; font-size:20px;">طريقة احتساب المبيعات:</label>
                                        
                                        <select id="calculation_method" class="form-select" style="max-width: 300px;" onchange = 'this.form.submit()' name = "calculation_method">
                                            <option value="1" <?php echo $employee_data['calculation_method'] == '1' ? 'selected' : ''; ?>><?= $option1_text ?></option>
                                            <option value="2" <?php echo $employee_data['calculation_method'] == '2' ? 'selected' : ''; ?>><?= $option2_text ?></option>
                                            <option value="3" <?php echo $employee_data['calculation_method'] == '3' ? 'selected' : ''; ?>><?= $option3_text ?></option>
                                        </select>
                                    </div>
                                </form>
                                
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- الجدولين بجانب بعض -->
                <div class="tables-side-by-side">
                    <?php if (!empty($employee_data['table1'])): ?>
                        <div class="table-container">
                            <h3 class="table-title">جدول المبيعات مع المبيعات الرقمية</h3>
                            <div class="table-responsive">
                                <table class="table custom-table table-sm">
                                    <thead>
                                        <tr>
                                            <th>البيانات</th>
                                            <th>TGT</th>
                                            <th>Branch Sales</th>
                                            <th>Digital Sales</th>
                                            <th>Total</th>
                                            <th>المتبقي</th>
                                            <th>نسبة الإنجاز</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    // التحقق من المنصب لتفعيل الخاصية
                                    $position = $employee_data['employee_info']['position'] ? $employee_data['employee_info']['position'] : '';
                                    $isManager = in_array(trim($position), ['Area Manager', 'Branch Manager', 'Branch Supervisor']);
                                    $empId = $employee_data['employee_info']['emp_id'] ? $employee_data['employee_info']['emp_id'] : '';


                                    foreach ($employee_data['table1'] as $fieldKey => $item):
                                    ?>
                                        <tr>
                                            <td class="<?= $isManager ? 'clickable-cell' : '' ?>"
                                                <?php if ($isManager): ?>
                                                data-field-key="<?= htmlspecialchars($fieldKey) ?>"
                                                data-field-label="<?= htmlspecialchars($item['label']) ?>"
                                                data-table-type="table1"
                                                data-emp-id="<?= htmlspecialchars($empId) ?>"
                                                data-position="<?= htmlspecialchars($position) ?>"
                                                <?php endif; ?>
                                            >
                                                <span class="product-name"><?= htmlspecialchars($item['label']) ?></span>
                                            </td>
                                            <td><?= number_format($item['tgt']) ?></td>
                                            <td><?= number_format($item['branch_sales']) ?></td>
                                            <td><?= number_format($item['digital_sales']) ?></td>
                                            <td><strong><?= number_format($item['total']) ?></strong></td>
                                            <td><?= number_format($item['remaining']) ?></td>
                                            <td>
                                                <?php
                                                $percentage = (float)str_replace('%', '', $item['percentage']);
                                                $colorClass = 'bg-danger text-white';
                                                if ($percentage >= 90) {
                                                    $colorClass = 'bg-success text-white';
                                                } elseif ($percentage >= 70) {
                                                    $colorClass = 'bg-info text-white';
                                                } elseif ($percentage >= 50) {
                                                    $colorClass = 'bg-warning text-dark';
                                                }
                                                ?>
                                                <span class="percentage-badge <?= $colorClass ?>"><?= $item['percentage'] ?></span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- الجدول الثاني -->
                    <?php if (!empty($employee_data['table2'])): ?>
                        <div class="table-container">
                            <h3 class="table-title">جدول المبيعات بدون مبيعات رقمية</h3>
                            <div class="table-responsive">
                                <table class="table custom-table table-sm">
                                    <thead>
                                        <tr>
                                            <th>البيانات</th>
                                            <th>TGT</th>
                                            <th>Sales</th>
                                            <th>المتبقي</th>
                                            <th>نسبة الإنجاز</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        $position = $employee_data['employee_info']['position'] ? $employee_data['employee_info']['position'] : '';
                                        $isManager = in_array(trim($position), ['Area Manager', 'Branch Manager', 'Branch Supervisor']);
                                        $empId = $employee_data['employee_info']['emp_id'] ? $employee_data['employee_info']['emp_id'] : '';
                                        ?>
                                        <?php foreach ($employee_data['table2'] as $fieldKey => $item): ?>
                                            <tr>
                                                <td class="<?= $isManager ? 'clickable-cell' : '' ?>"
                                                    <?php if ($isManager): ?>
                                                    data-field-key="<?= htmlspecialchars($fieldKey) ?>"
                                                    data-field-label="<?= htmlspecialchars($item['label']) ?>"
                                                    data-table-type="table2"
                                                    data-emp-id="<?= htmlspecialchars($empId) ?>"
                                                    data-position="<?= htmlspecialchars($position) ?>"
                                                    <?php endif; ?>
                                                >
                                                    <span class="product-name"><?= htmlspecialchars($item['label']) ?></span>
                                                </td>
                                                <td><?= number_format($item['tgt']) ?></td>
                                                <td><?= number_format($item['sales']) ?></td>
                                                <td><?= number_format($item['remaining']) ?></td>
                                                <td>
                                                    <?php
                                                    $percentage = (float)str_replace('%', '', $item['percentage']);
                                                    $colorClass = 'bg-danger text-white';
                                                    if ($percentage >= 90) {
                                                        $colorClass = 'bg-success text-white';
                                                    } elseif ($percentage >= 70) {
                                                        $colorClass = 'bg-info text-white';
                                                    } elseif ($percentage >= 50) {
                                                        $colorClass = 'bg-warning text-dark';
                                                    }
                                                    ?>
                                                    <span class="percentage-badge <?= $colorClass ?>"><?= $item['percentage'] ?></span>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <!-- زر التحميل -->
            <div class="text-center mt-4">
                <a href="<?= base_url('admin/download_employee_sales_data') ?>" class="btn btn-download">
                    <i class="fas fa-download me-2"></i> تحميل البيانات
                </a>
            </div>
        <?php endif; ?>
    </div>

    <!-- Modal للموظفين التابعين -->
    <div class="modal fade subordinates-modal" id="subordinatesModal" tabindex="-1" aria-labelledby="subordinatesModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="subordinatesModalLabel">
                        <i class="fas fa-users me-2"></i>
                        <span id="modalFieldName"></span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="subordinatesTableContainer">
                        <!-- سيتم تحميل الجدول هنا ديناميكياً -->
                        <div class="loading-spinner">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">جاري التحميل...</span>
                            </div>
                        </div>
                    </div>
                    <div id="subordinatesPagination" class="subordinates-pagination" style="display: none;">
                        <!-- سيتم إضافة أزرار الـ Pagination هنا -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const toggleBtn = document.getElementById('toggleEmployeeInfo');
    const employeeCard = document.getElementById('employeeInfoCard');
    const toggleText = document.getElementById('toggleText');
    const icon = toggleBtn.querySelector('svg');
    let isVisible = false; // البداية: مخفي
    let positionToggled = false;


    if (toggleBtn && employeeCard) {
        toggleBtn.addEventListener('click', function() {
            isVisible = !isVisible;
            
            if (isVisible) {
                employeeCard.style.display = 'block';
                setTimeout(() => {
                    employeeCard.style.opacity = '1';
                    employeeCard.style.transform = 'scale(1)';
                }, 10);
                toggleText.textContent = '';
                toggleBtn.classList.remove('btn-outline-primary');
                toggleBtn.classList.add('btn-primary');
                icon.innerHTML = '<path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8M1.173 8a13 13 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5s3.879 1.168 5.168 2.457A13 13 0 0 1 14.828 8q-.086.13-.195.288c-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5s-3.879-1.168-5.168-2.457A13 13 0 0 1 1.172 8z"/><path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5M4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0"/>';
            } else {
                employeeCard.style.opacity = '0';
                employeeCard.style.transform = 'scale(0.95)';
                setTimeout(() => {
                    employeeCard.style.display = 'none';
                }, 300);
                toggleText.textContent = '';
                toggleBtn.classList.remove('btn-primary');
                toggleBtn.classList.add('btn-outline-primary');
                icon.innerHTML = '<path d="m10.79 12.912-1.614-1.615a3.5 3.5 0 0 1-4.474-4.474l-2.06-2.06C.938 6.278 0 8 0 8s3 5.5 8 5.5a7 7 0 0 0 2.79-.588M5.21 3.088A7 7 0 0 1 8 2.5c5 0 8 5.5 8 5.5s-.939 1.721-2.641 3.238l-2.062-2.062a3.5 3.5 0 0 0-4.474-4.474z"/><path d="M5.525 7.646a2.5 2.5 0 0 0 2.829 2.829zm4.95.708-2.829-2.83a2.5 2.5 0 0 1 2.829 2.829zm3.171 6-12-12 .708-.708 12 12z"/>';
            }
        });
        
        // إضافة الأنماط الأساسية
        employeeCard.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
    }

    // ============================================
    // Subordinates Modal Functionality
    // ============================================

    const subordinatesModal = new bootstrap.Modal(document.getElementById('subordinatesModal'));
    let currentSubordinatesData = [];
    let currentPage = 1;
    const itemsPerPage = 20;

    // Event listener للخلايا القابلة للضغط
    document.querySelectorAll('.clickable-cell').forEach(cell => {
        cell.addEventListener('click', function() {
            const fieldKey = this.dataset.fieldKey;
            const fieldLabel = this.dataset.fieldLabel;
            const tableType = this.dataset.tableType;
            const empId = this.dataset.empId;
            const position = this.dataset.position;

            // عرض الـ Modal مع Loading
            document.getElementById('modalFieldName').textContent = fieldLabel;
            document.getElementById('subordinatesTableContainer').innerHTML = `
                <div class="loading-spinner">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">جاري التحميل...</span>
                    </div>
                </div>
            `;
            document.getElementById('subordinatesPagination').style.display = 'none';
            subordinatesModal.show();

            // إرسال AJAX request
            fetch('<?= base_url('admin/getSubordinatesData') ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: new URLSearchParams({
                    emp_id: empId,
                    field_key: fieldKey,
                    table_type: tableType,
                    option: <?php echo $employee_data['calculation_method']; ?>
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    currentSubordinatesData = data.subordinates;
                    currentPage = 1;
                    renderSubordinatesTable(data, tableType);
                } else {
                    document.getElementById('subordinatesTableContainer').innerHTML = `
                        <div class="alert alert-danger text-center">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            ${data.message || 'حدث خطأ أثناء جلب البيانات'}
                        </div>
                    `;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('subordinatesTableContainer').innerHTML = `
                    <div class="alert alert-danger text-center">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        حدث خطأ أثناء الاتصال بالخادم
                    </div>
                `;
            });
        });
    });

    function renderSubordinatesTable(data, tableType) {
        const container = document.getElementById('subordinatesTableContainer');
        const paginationContainer = document.getElementById('subordinatesPagination');

        if (!data.subordinates || data.subordinates.length === 0) {
            container.innerHTML = `
                <div class="alert alert-info text-center">
                    <i class="fas fa-info-circle me-2"></i>
                    لا يوجد موظفين تابعين لهذا المدير
                </div>
            `;
            paginationContainer.style.display = 'none';
            return;
        }

        // حساب الـ Pagination
        const totalPages = Math.ceil(data.subordinates.length / itemsPerPage);
        const startIndex = (currentPage - 1) * itemsPerPage;
        const endIndex = startIndex + itemsPerPage;
        const currentPageData = data.subordinates.slice(startIndex, endIndex);

        // بناء الجدول
        let tableHTML = '<div class="table-container"><div class="table-responsive"><table class="table custom-table table-sm">';

        // Headers
        tableHTML += '<thead><tr>';
        tableHTML += '<th>الاسم</th>';
        if (positionToggled) {
            tableHTML += '<th class="position-column">المنصب</th>';
        }
        
        tableHTML += '<th>TGT</th>';

        if (tableType === 'table1') {
            tableHTML += '<th>Branch Sales</th>';
            tableHTML += '<th>Digital Sales</th>';
            tableHTML += '<th>Total</th>';
        } else {
            tableHTML += '<th>Sales</th>';
        }

        tableHTML += '<th>المتبقي</th>';
        tableHTML += '<th>نسبة الإنجاز</th>';
        tableHTML += '</tr></thead>';

        // Body
        tableHTML += '<tbody>';
        currentPageData.forEach(sub => {
            const percentage = parseFloat(sub.percentage.replace('%', ''));
            let colorClass = 'bg-danger text-white';
            if (percentage >= 90) {
                colorClass = 'bg-success text-white';
            } else if (percentage >= 70) {
                colorClass = 'bg-info text-white';
            } else if (percentage >= 50) {
                colorClass = 'bg-warning text-dark';
            }

            tableHTML += '<tr>';
            tableHTML += `<td><strong>${sub.name}</strong></td>`;
            if (positionToggled) {
                tableHTML += `<td class="position-column"><span class="badge bg-secondary">${sub.position}</span></td>`;
            }
            
            tableHTML += `<td>${formatNumber(sub.tgt)}</td>`;

            if (tableType === 'table1') {
                tableHTML += `<td>${formatNumber(sub.branch_sales)}</td>`;
                tableHTML += `<td>${formatNumber(sub.digital_sales)}</td>`;
                tableHTML += `<td><strong>${formatNumber(sub.total)}</strong></td>`;
            } else {
                tableHTML += `<td>${formatNumber(sub.sales)}</td>`;
            }

            tableHTML += `<td>${formatNumber(sub.remaining)}</td>`;
            tableHTML += `<td><span class="percentage-badge ${colorClass}">${sub.percentage}</span></td>`;
            tableHTML += '</tr>';
        });
        tableHTML += '</tbody>';

        // Footer (المجموع الإجمالي)
        if (data.totals) {
            const totalPercentage = parseFloat(data.totals.percentage.replace('%', ''));
            let totalColorClass = 'bg-danger text-white';
            if (totalPercentage >= 90) {
                totalColorClass = 'bg-success text-white';
            } else if (totalPercentage >= 70) {
                totalColorClass = 'bg-info text-white';
            } else if (totalPercentage >= 50) {
                totalColorClass = 'bg-warning text-dark';
            }

            tableHTML += '<tfoot><tr>';
            tableHTML += '<td><strong>المجموع الإجمالي</strong></td>';
            if (positionToggled) {
                tableHTML += '<td class="position-column"><strong>-</strong></td>';
            }

            tableHTML += `<td><strong>${formatNumber(data.totals.tgt)}</strong></td>`;

            if (tableType === 'table1') {
                tableHTML += `<td><strong>${formatNumber(data.totals.branch_sales)}</strong></td>`;
                tableHTML += `<td><strong>${formatNumber(data.totals.digital_sales)}</strong></td>`;
                tableHTML += `<td><strong>${formatNumber(data.totals.total)}</strong></td>`;
            } else {
                tableHTML += `<td><strong>${formatNumber(data.totals.sales)}</strong></td>`;
            }

            tableHTML += `<td><strong>${formatNumber(data.totals.remaining)}</strong></td>`;
            tableHTML += `<td><span class="percentage-badge ${totalColorClass}">${data.totals.percentage}</span></td>`;
            tableHTML += '</tr></tfoot>';
        }

        tableHTML += '</table></div></div>';
        container.innerHTML = tableHTML;

        // عرض الـ Pagination إذا كان هناك أكثر من صفحة
        if (totalPages > 1) {
            renderPagination(totalPages, data, tableType);
        } else {
            paginationContainer.style.display = 'none';
        }
    }

    function renderPagination(totalPages, data, tableType) {
        const paginationContainer = document.getElementById('subordinatesPagination');
        paginationContainer.style.display = 'flex';

        let paginationHTML = '';

        // زر السابق
        paginationHTML += `<button class="page-btn" ${currentPage === 1 ? 'disabled' : ''} onclick="changePage(${currentPage - 1}, ${totalPages}, '${tableType}')">
            <i class="fas fa-chevron-right"></i> السابق
        </button>`;

        // معلومات الصفحة
        paginationHTML += `<span class="page-info">صفحة ${currentPage} من ${totalPages}</span>`;

        // زر التالي
        paginationHTML += `<button class="page-btn" ${currentPage === totalPages ? 'disabled' : ''} onclick="changePage(${currentPage + 1}, ${totalPages}, '${tableType}')">
            التالي <i class="fas fa-chevron-left"></i>
        </button>`;

        paginationContainer.innerHTML = paginationHTML;

        // حفظ البيانات للاستخدام في changePage
        window.currentModalData = data;
        window.currentTableType = tableType;
    }

    window.changePage = function(page, totalPages, tableType) {
        if (page < 1 || page > totalPages) return;
        currentPage = page;
        renderSubordinatesTable(window.currentModalData, tableType);
    };

    function formatNumber(num) {
        return new Intl.NumberFormat('en-US').format(num);
    }

    window.togglePosition = function () {
        if (positionToggled) {
            positionToggled = false;
        } else {
            positionToggled = true;
        }
    }
});
</script>


<script src="https://code.jquery.com/jquery-3.6.0.js" crossorigin="anonymous"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

<script src="<?php echo base_url(); ?>public/js/requests.js?a=<?php $date=date_create();echo date_timestamp_get($date);?>"></script>
<script src="<?php echo base_url(); ?>public/js/script.js?a=<?php $date=date_create();echo date_timestamp_get($date);?>"></script>
<?php $this->load->view('view_footer'); ?>