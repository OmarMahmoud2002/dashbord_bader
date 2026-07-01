<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Other extends MY_Controller {

	function __construct()
	{
		// Increase memory limit for large Excel file processing
		ini_set('memory_limit', '512M');
        parent::__construct();
        $this->load->model('Model_admin');
        $this->load->model('Excel_import_model');
        $this->load->model('Model_shelves', 'Shelves');
        $this->load->model('Model_products', 'Products');
        $this->load->model('Model_serials', 'Serials');
		$this->load->model('Model_barcodes', 'Barcodes');
        $this->load->model('Model_categories', 'Categories');
        $this->load->model('Model_brands', 'Brands');
        $this->load->model('Model_notifications', 'Notifications');
        $this->load->model('Model_requests', 'Requests');
        $this->load->model('Model_custody', 'Custodys');
		$this->load->model('Model_sales', 'Sales');
        $this->load->model('Model_shipments', 'Shipments');
        $this->load->model('Model_operations', 'Operations');
        $this->load->model('Model_cart', 'Cart');
		$this->load->model('Model_settings', 'Settings');
		$this->load->model('Model_employee_schedule', 'Schedule');
		$this->load->model('Model_variables', 'Variables');
		$this->load->model('Model_employees_sales', 'EmployeesSales');
		$this->load->model('Model_store_items', 'Store');
		$this->load->helper('cupload');
		$this->load->helper('employees_sales');
		$this->load->library('excel');
        notifications($this);
        date_default_timezone_set('Asia/Riyadh');
    }

	private function get_admins() {
		$ids = array();
		foreach ($this->Model_admin->getadmins() as $admin) {
			array_push($ids, strval($admin['user_id']));
		}
		return $ids;
	}

	public function index()
	{
		if(!in_array($this->session->userdata('user_id'), $this->get_admins())) {
			redirect(base_url());
		}

		$this->load->view('view_admin_home', $this->data);
	}

	public function permissions()
	{
		if(!in_array($this->session->userdata('user_id'), $this->get_admins())) {
			redirect(base_url());
		}

		$this->data['permission_groups'] = $this->get_admin_permissions_catalog();
		$this->data['demo_roles'] = [
			[
				'name' => 'مدير كامل',
				'description' => 'كل صفحات وأكشنز لوحة التحكم',
				'badge' => 'كامل',
				'permissions' => 'all'
			],
			[
				'name' => 'مسؤول مخزن',
				'description' => 'المنتجات والرفوف والبحث والتسليم',
				'badge' => 'تشغيلي',
				'permissions' => [
					'product_search.view', 'product_search.cart.add', 'product_search.cart.remove',
					'product_search.custody.create', 'product_search.delivery.create',
					'product_search.custody.return', 'product_search.delivery.return',
					'products.view', 'products.import', 'products.edit.view', 'products.update.basic',
					'products.update.extra', 'products.low.view', 'operations.view',
					'shelves.view', 'shelves.create', 'shelves.rearrange', 'shelves.delete',
					'shelves.items.view', 'shelves.items.remove'
				]
			],
			[
				'name' => 'مسؤول شحنات',
				'description' => 'إضافة وتسليم وحذف الشحنات ورؤية الأكياس',
				'badge' => 'شحن',
				'permissions' => [
					'shipments.view', 'shipments.create', 'shipments.deliver',
					'shipments.delete', 'shipments.packs.view'
				]
			],
			[
				'name' => 'مشاهد فقط',
				'description' => 'فتح الصفحات بدون تنفيذ تعديلات',
				'badge' => 'قراءة',
				'permissions' => [
					'home.view', 'product_search.view', 'products.view', 'products.low.view',
					'operations.view', 'shelves.view', 'shelves.items.view', 'employees.view',
					'employees_sales.view', 'employees_timetable.view', 'admins.view',
					'requests.view', 'sales.view', 'settlements.view', 'shipments.view',
					'lock_admin.view', 'lock_track.view', 'forms.view', 'settings.view'
				]
			]
		];

		$this->load->view('view_admin_permissions', $this->data);
	}

	private function get_admin_permissions_catalog()
	{
		return [
			[
				'section' => 'الصفحات الرئيسية والمخزون',
				'icon' => 'bi-box-seam',
				'pages' => [
					[
						'label' => 'الصفحة الرئيسية',
						'route' => 'admin/index',
						'permissions' => [
							['key' => 'home.view', 'label' => 'عرض الصفحة', 'type' => 'view'],
						],
					],
					[
						'label' => 'البحث عن منتج',
						'route' => 'admin/products/search',
						'permissions' => [
							['key' => 'product_search.view', 'label' => 'عرض الصفحة', 'type' => 'view'],
							['key' => 'product_search.cart.add', 'label' => 'إضافة للسلة', 'type' => 'create'],
							['key' => 'product_search.cart.remove', 'label' => 'حذف من السلة', 'type' => 'delete'],
							['key' => 'product_search.custody.create', 'label' => 'تسليم لموظف', 'type' => 'special'],
							['key' => 'product_search.delivery.create', 'label' => 'تسليم للتوصيل', 'type' => 'special'],
							['key' => 'product_search.custody.return', 'label' => 'إرجاع من عهدة', 'type' => 'special'],
							['key' => 'product_search.delivery.return', 'label' => 'إرجاع من التوصيل', 'type' => 'special'],
						],
					],
					[
						'label' => 'المنتجات',
						'route' => 'admin/dashboard',
						'permissions' => [
							['key' => 'products.view', 'label' => 'عرض المنتجات', 'type' => 'view'],
							['key' => 'products.import', 'label' => 'رفع ملف مخزون', 'type' => 'create'],
							['key' => 'products.edit.view', 'label' => 'فتح صفحة التعديل', 'type' => 'update'],
							['key' => 'products.update.basic', 'label' => 'تعديل البيانات الأساسية', 'type' => 'update'],
							['key' => 'products.update.extra', 'label' => 'تعديل بيانات إضافية', 'type' => 'update'],
						],
					],
					[
						'label' => 'منخفض الكمية',
						'route' => 'admin/products/low',
						'permissions' => [
							['key' => 'products.low.view', 'label' => 'عرض الصفحة', 'type' => 'view'],
						],
					],
					[
						'label' => 'العمليات',
						'route' => 'admin/products/operations',
						'permissions' => [
							['key' => 'operations.view', 'label' => 'عرض سجل العمليات', 'type' => 'view'],
						],
					],
				],
			],
			[
				'section' => 'الرفوف والموظفين',
				'icon' => 'bi-people',
				'pages' => [
					[
						'label' => 'الرفوف',
						'route' => 'admin/shelves',
						'permissions' => [
							['key' => 'shelves.view', 'label' => 'عرض الرفوف', 'type' => 'view'],
							['key' => 'shelves.create', 'label' => 'إضافة رف', 'type' => 'create'],
							['key' => 'shelves.rearrange', 'label' => 'ترتيب/نقل منتجات', 'type' => 'update'],
							['key' => 'shelves.delete', 'label' => 'حذف رف', 'type' => 'delete'],
							['key' => 'shelves.items.view', 'label' => 'عرض منتجات الرف', 'type' => 'special'],
							['key' => 'shelves.items.remove', 'label' => 'إزالة منتج من الرف', 'type' => 'special'],
						],
					],
					[
						'label' => 'الموظفين',
						'route' => 'admin/employees',
						'permissions' => [
							['key' => 'employees.view', 'label' => 'عرض الموظفين', 'type' => 'view'],
							['key' => 'employees.create', 'label' => 'إضافة موظف', 'type' => 'create'],
							['key' => 'employees.detail', 'label' => 'فتح تفاصيل موظف', 'type' => 'view'],
							['key' => 'employees.update', 'label' => 'تعديل بيانات موظف', 'type' => 'update'],
							['key' => 'employees.delete', 'label' => 'حذف موظف', 'type' => 'delete'],
							['key' => 'employees.custody.create', 'label' => 'إضافة عهدة', 'type' => 'special'],
							['key' => 'employees.settlement.create', 'label' => 'إضافة تسوية', 'type' => 'special'],
						],
					],
					[
						'label' => 'مبيعات الموظفين',
						'route' => 'admin/employees_sales_page',
						'permissions' => [
							['key' => 'employees_sales.view', 'label' => 'عرض الصفحة', 'type' => 'view'],
							['key' => 'employees_sales.upload', 'label' => 'رفع ملف مبيعات', 'type' => 'create'],
							['key' => 'employees_sales.search', 'label' => 'بحث في المبيعات', 'type' => 'special'],
							['key' => 'employees_sales.download', 'label' => 'تحميل بيانات موظف', 'type' => 'special'],
						],
					],
					[
						'label' => 'جداول الموظفين',
						'route' => 'admin/employees_timetable',
						'permissions' => [
							['key' => 'employees_timetable.view', 'label' => 'عرض الجداول', 'type' => 'view'],
							['key' => 'employees_timetable.save', 'label' => 'حفظ جدول موظف', 'type' => 'update'],
							['key' => 'employees_timetable.supervisors.view', 'label' => 'عرض جداول المشرفين', 'type' => 'special'],
						],
					],
				],
			],
			[
				'section' => 'الإدارة والحركة المالية',
				'icon' => 'bi-shield-check',
				'pages' => [
					[
						'label' => 'المديرين',
						'route' => 'admin/admins',
						'permissions' => [
							['key' => 'admins.view', 'label' => 'عرض المديرين', 'type' => 'view'],
							['key' => 'admins.create', 'label' => 'إضافة مدير', 'type' => 'create'],
							['key' => 'admins.update', 'label' => 'تعديل مدير', 'type' => 'update'],
							['key' => 'admins.delete', 'label' => 'حذف مدير', 'type' => 'delete'],
							['key' => 'admins.assign_role', 'label' => 'تعيين صلاحية', 'type' => 'special'],
						],
					],
					[
						'label' => 'الصلاحيات',
						'route' => 'admin/permissions',
						'permissions' => [
							['key' => 'roles.view', 'label' => 'عرض الصفحة', 'type' => 'view'],
							['key' => 'roles.create', 'label' => 'إنشاء صلاحية', 'type' => 'create'],
							['key' => 'roles.update', 'label' => 'تعديل صلاحية', 'type' => 'update'],
							['key' => 'roles.delete', 'label' => 'حذف صلاحية', 'type' => 'delete'],
						],
					],
					[
						'label' => 'الطلبات',
						'route' => 'admin/requests',
						'permissions' => [
							['key' => 'requests.view', 'label' => 'عرض الطلبات', 'type' => 'view'],
							['key' => 'requests.return', 'label' => 'إرجاع منتج للتخزين', 'type' => 'special'],
						],
					],
					[
						'label' => 'المبيعات',
						'route' => 'admin/sales',
						'permissions' => [
							['key' => 'sales.view', 'label' => 'عرض المبيعات', 'type' => 'view'],
						],
					],
					[
						'label' => 'الفروقات',
						'route' => 'admin/settlements',
						'permissions' => [
							['key' => 'settlements.view', 'label' => 'عرض الفروقات', 'type' => 'view'],
							['key' => 'settlements.create', 'label' => 'إضافة تسوية', 'type' => 'create'],
						],
					],
				],
			],
			[
				'section' => 'الشحنات والتقفيلة والنماذج',
				'icon' => 'bi-truck',
				'pages' => [
					[
						'label' => 'الشحنات',
						'route' => 'admin/shipments',
						'permissions' => [
							['key' => 'shipments.view', 'label' => 'عرض الشحنات', 'type' => 'view'],
							['key' => 'shipments.create', 'label' => 'إضافة شحنة', 'type' => 'create'],
							['key' => 'shipments.deliver', 'label' => 'تسليم شحنة', 'type' => 'update'],
							['key' => 'shipments.delete', 'label' => 'حذف شحنة', 'type' => 'delete'],
							['key' => 'shipments.packs.view', 'label' => 'رؤية الأكياس', 'type' => 'special'],
						],
					],
					[
						'label' => 'التقفيلة',
						'route' => 'admin/lock-admin',
						'permissions' => [
							['key' => 'lock_admin.view', 'label' => 'عرض التقفيلة', 'type' => 'view'],
						],
					],
					[
						'label' => 'تأكيد التقفيلة',
						'route' => 'admin/lock-track',
						'permissions' => [
							['key' => 'lock_track.view', 'label' => 'عرض التأكيد', 'type' => 'view'],
							['key' => 'lock_track.update', 'label' => 'تعديل سجل', 'type' => 'update'],
							['key' => 'lock_track.delete', 'label' => 'حذف سجل', 'type' => 'delete'],
						],
					],
					[
						'label' => 'النماذج',
						'route' => 'admin/forms',
						'permissions' => [
							['key' => 'forms.view', 'label' => 'عرض النماذج', 'type' => 'view'],
							['key' => 'forms.settlement.view', 'label' => 'نموذج تسوية الغرامة', 'type' => 'special'],
							['key' => 'forms.replacement.view', 'label' => 'نموذج استبدال الجهاز', 'type' => 'special'],
						],
					],
					[
						'label' => 'الإعدادات',
						'route' => 'admin/settings',
						'permissions' => [
							['key' => 'settings.view', 'label' => 'عرض الإعدادات', 'type' => 'view'],
							['key' => 'settings.update', 'label' => 'تعديل الإعدادات', 'type' => 'update'],
						],
					],
				],
			],
		];
	}

	function get_serials_categories() {
	    if(!in_array($this->session->userdata('user_id'), $this->get_admins())) {
		    redirect(base_url());
		}

        $serials = $this->Serials->get_serials_like(strtoupper($this->input->get('serial')), ['location' => 0]);
        $serials_categories = [];
        foreach ($serials as $serial) {
            array_push($serials_categories, [$serial->serial, $this->Categories->get_categories(['id' => $serial->categoryId])[0]]);
        }
        
        echo json_encode($serials_categories);
	}

    public function get_product_by_serial() {
        $categoryId = $this->input->get('categoryid');
		$serialnumber = strtoupper($this->input->get('serial'));

        if ($categoryId != null) {
            $serial = $this->Serials->get_serial_like_by_category($serialnumber, $categoryId)[0];
        } else {
			
            $serials = $this->Serials->get_serials(array('serial' => $serialnumber));
            if (count($serials) == 0) {
                echo json_encode(['status' => 'error', 'description' => 'السيريال غير موجود']);
                return;
            } else {
                $serial = $serials[0];
            }
        }
        $product = $this->Products->get_one_product_by_id($serial->productid);
		$product->quantity = count($this->Serials->get_serials(array('productid' => $product->id, 'location' => 0)));

        $data = [
            'status' => 'ok',
            'product' => $product,
            'shelf' => $serial->shelf_number,
			'serial' => $serial->serial,
			'poster_number' => $serial->poster_number,
        ];

		if ($serial->location == 1) {
			$custody = $this->Custodys->get_custodys(['custody_serial' => $serial->serial])[0];
			$data['status'] = 'custody_product';
			$data['description'] = 'السيريال مسجل بالفعل في عهدة موظف';
			$data['description'] .= " <span class = 'remCustody' onclick = 'remove_custody($custody->user_custody_id)'>إرجاع</span>";
		}

		if ($serial->location == 2) {
			$request = $this->Requests->get_requests(['serial' => $serial->serial])[0];
			$data['status'] = 'delivered_product';
			$data['description'] = 'السيريال مسجل لدى احد مناديب التوصيل';
			$data['description'] .= " <span class = 'remCustody' onclick = 'remove_request($request->id)'>إرجاع</span>";
		}

		if ($this->Cart->search_item(['identifierid' => $serial->serial]) == true) {
			$data['status'] = 'custody_product';
			$data['description'] = 'السيريال مسجل بالفعل في السلة';
		}

        echo json_encode($data);
    }

    function get_product_by_category() {
        $product = $this->Products->get_products(array('categoryCode' => $this->input->get('categoryCode'), 'unified' => 1));
        if (count($product) == 0) {
            echo json_encode(array('status' => 'error', 'description' => 'لا يوجد منتجات'));
        } else {
			$data = [
                'status' => 'ok', 
                'product' => $product[0],
                'poster_number' => 'لا يوجد'
            ];
            
			$barcode = $this->Barcodes->get_barcodes(['productid' => $data['product']->id])[0];
            $product[0]->quantity = $barcode->quantity - $barcode->registered_count;

			
			$data['shelf'] = implode(' - ', $this->Barcodes->get_barcode_shelves($barcode));
			$data['barcode'] = strtoupper($barcode->barcode);
            if ($product[0]->quantity == 0) {
                $data['status'] = 'wrong';
                $data['description'] = 'كل الكمية مسجلة في عهد موظفين';
                echo json_encode($data);
			} else if ($this->Cart->count_items(['identifierid' => $barcode->barcode]) == $barcode->quantity) {
				$data['status'] = 'wrong';
				$data['description'] = 'كل الكمية مسجلة في السلة';
				echo json_encode($data);
            } else {
                echo json_encode($data);
            }

        }
    }
	
	function add_product_as_custody() {
		/* Serial can be Serial or Barcode */
		$emp_id = $this->input->post('emp_id');
		$product_id = $this->input->post('product_id');
		$unified = $this->input->post('unified');
		$barcode = strtoupper($this->input->post('barcode'));
		$serial = strtoupper($this->input->post('serial'));
		$order_num = $this->input->post('order');
		
		$product = $this->Products->get_one_product_by_id($product_id);
		$note = '';
		
        if (!$unified) {
			if (count($this->Custodys->get_custodys(['custody_serial' => $serial])) > 0) {
				echo json_encode(['msg' => 'المنتج بالفعل في عهدة موظف', 'status' => 'wrong']);
				return;
			}
			if ($this->Requests->get_requests_length(['serial' => $serial]) > 0) {
				echo json_encode(['msg' => 'المنتج بالفعل تم تسليمه للتوصيل', 'status' => 'wrong']);
				return;
			}
            $this->Serials->update_serial(['location' => 1], $serial);
        } else {
            $this->Barcodes->update_barcode_registered_count($barcode);
		}

		$user_id = $this->session->userdata('user_id');
		$this->Products->check_low_product_and_update($user_id, $product_id, $barcode, $this->Serials, $this->Barcodes, $this->Notifications);
        
        $data = array(
			'custody_user' => $emp_id,
			'custody_serial' => $unified ? $barcode : $serial,
			'custody_type' => 1,
			'custody_product_unified' => $unified,
			'custody_note' => $note,
			'custody_date' => date('Y-m-d H:i:s'),
			'order_number' => $order_num
		);
		$this->Model_admin->insert_custody($data);
		$this->Sales->add_sale([
			'productid' => $product_id,
			'userid' => $emp_id,
			'identifier' => $unified ? $barcode : $serial,
            'user_custody_id' => $this->db->insert_id(),
			'date_created' => date('Y-m-d H:i:s')
		]);

        if (!$unified) {
            $this->Operations->add_operation(['serial' => $serial, 'operation' => 'تم تسجيل المنتج في عهدة ' . $this->Model_admin->get_user_by_id($emp_id)['user_fillname'], 'sales_order' => '0'], $this->session->userdata('user_id'));
        }
		
        
        echo json_encode(['msg' => 'المنتج تم اضافته في عهدة الموظف', 'status' => 'ok']);
        
	}

	function add_product_delivery() {
		$product_id = $this->input->post('product_id');
		$unified = $this->input->post('unified');
		$barcode = strtoupper($this->input->post('barcode'));
		$serial = strtoupper($this->input->post('serial'));
		$order_num = $this->input->post('order');
		
		$product = $this->Products->get_one_product_by_id($product_id);

		$data = [
			'order_number' => $order_num,
			'product_id' => $product->id,
			'product_name' => $product->name,
			'serial' => $unified ? $barcode : $serial,
			'categoryCode' => $product->categoryCode,
			'date_created' => date('Y-m-d H:i:s')
		];

		if (!$unified) {
			if (count($this->Custodys->get_custodys(['custody_serial' => $serial])) > 0) {
				echo json_encode(['msg' => 'المنتج بالفعل في عهدة موظف', 'status' => 'wrong']);
				return;
			}
			if ($this->Requests->get_requests_length(['serial' => $serial]) > 0) {
				echo json_encode(['msg' => 'المنتج بالفعل تم تسليمه للتوصيل', 'status' => 'wrong']);
				return;
			}

            $this->Serials->update_serial(['location' => 2], $serial);
        } else {
            $this->Barcodes->update_barcode_delivered_count($barcode);   
        }
		
		$user_id = $this->session->userdata('user_id');
		$this->Products->check_low_product_and_update($user_id, $product->id, $barcode, $this->Serials, $this->Barcodes, $this->Notifications);
		
		if (!$unified) {
            $this->Operations->add_operation(['serial' => $serial, 'operation' => 'تم تسليم المنتج للتوصيل', 'sales_order' => '0'], $this->session->userdata('user_id'));
        }

		$this->Requests->add_request($data);
		echo json_encode(['msg' => 'تم تسليم المنتج للتوصيل', 'status' => 'ok']);
	}

    function remove_custody() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $custody_id = $this->input->post('custody_id');
            $custodyObj = $this->Custodys->get_custodys(['user_custody_id' => $custody_id])[0];

            if ($custodyObj->custody_product_unified) {
                // barcode
                $barcode = $custodyObj->custody_serial;
                $barcodeObj = $this->Barcodes->get_barcodes(['barcode' => $barcode])[0];
                $this->Barcodes->update_barcode_registered_count($barcode, -1);
                $this->Custodys->delete_custody(['user_custody_id' => $custody_id]);
                $this->Sales->delete_sale(['identifier' => $barcode, 'productid' => $barcodeObj->productid, 'user_custody_id' => $custody_id]);
			
				$user_id = $this->session->userdata('user_id');
				$this->Products->check_low_product_and_update($user_id, $barcodeObj->productid, $barcode, $this->Serials, $this->Barcodes, $this->Notifications);

			} else {
                // serial
                $serial = $custodyObj->custody_serial;
                $this->Custodys->delete_custody(['user_custody_id' => $custody_id]);

                if (!$this->Serials->check_for_serial($serial)) {
                    $serialObj = $this->Serials->get_serials(['serial' => $serial])[0];
					$this->Serials->update_serial(['location' => 0], $serial);


					$user_id = $this->session->userdata('user_id');
					$this->Products->check_low_product_and_update($user_id, $serialObj->productid, null, $this->Serials, $this->Barcodes, $this->Notifications);

                    $this->Sales->delete_sale(['identifier' => $serial, 'user_custody_id' => $custody_id]);
                    $this->Operations->add_operation(['serial' => $serial, 'operation' => 'تم ارجاع المنتج من عهدة ' . $this->Model_admin->get_user_by_id($custodyObj->custody_user)['user_fillname'] . ' إلي المخزون', 'sales_order' => '0'], $this->session->userdata('user_id'));
                } else {
					$this->Sales->delete_sale(['identifier' => $serial, 'user_custody_id' => $custody_id]);
				}
            }
        }
    }
	
	function delete_product() {
	    if(!in_array($this->session->userdata('user_id'), $this->get_admins())) {
		    redirect(base_url());
		}

	    $id = $this->input->get('id');
	    $this->Products->delete_all_product_infos($id, $this->Serials, $this->Barcodes, $this->Custodys, $this->Requests, $this->Operations, $this);
	}
	

	function request_categories_by_brand() {
		if(!in_array($this->session->userdata('user_id'), $this->get_admins())) {
		    redirect(base_url());
		}

		$brand = $this->input->get('brand');
		if ($brand == '') {
			return false;
		}

		$result = $this->Categories->get_categories(['brand' => $brand]);
		echo json_encode($result);
	}
	

	function delete_shelf() {
	    if(!in_array($this->session->userdata('user_id'), $this->get_admins())) {
		    redirect(base_url());
		}

        $id = $this->input->get('id');
        $shelf = $this->Shelves->delete_shelf(array('id' => $id))[0];

        foreach ($this->Serials->get_serials(['shelf_number' => $shelf->shelf_number]) as $serialObj) {
            $this->Operations->add_operation(['serial' => $serialObj->serial, 'operation' => 'تم ازالته من الرف', 'sales_order' => '0'], $this->session->userdata('user_id'));
        }
        $this->Serials->update_serial_shelf('', $shelf->shelf_number);
		
        $barcodes = $this->Barcodes->get_barcodes(['shelf_info !=' => '']);
		foreach ($barcodes as $barcode) {
			$this->Barcodes->delete_barcode_shelf($barcode, $shelf->shelf_number);
		}
	}

	function requests() {
		if(!in_array($this->session->userdata('user_id'), $this->get_admins())) {
		    redirect(base_url());
		}

		if ($this->input->get('dates')) {
            $dates = explode(' - ', $this->input->get('dates'));
        } else {
            $dates = [null, null];
        }
        
		$datefrom = $dates[0];
		$dateto = $dates[1];

        $date_range = [
            date_format(date_create(), 'Y/m/d 00:00:00'),
            date_format(date_create(), 'Y/m/d 23:59:59'),
        ];

        if ($datefrom != null) {
            $date_range[0] = date_format(date_create($datefrom), 'Y/m/d 00:00:00');
        }

        if ($dateto != null) {
            $date_range[1] = date_format(date_create($dateto), 'Y/m/d 23:59:59');
        } 
    

        $this->data['requests'] = $this->Requests->get_requests_by_date($date_range[0], $date_range[1]);

		$this->load->view('view_admin_requests', $this->data);
	}

	function product_delivery_return() {
		if(!in_array($this->session->userdata('user_id'), $this->get_admins())) {
		    redirect(base_url());
		}

		$id = $this->input->post('id');

		$request = $this->Requests->get_requests(['id' => $id])[0];
		$product = $this->Products->get_one_product_by_id($request->product_id);
		$barcode = null;
		
		if ($product->unified) {
			$barcode = $request->serial;
			$this->Barcodes->update_barcode_delivered_count($barcode, -1);
		} else {
			$serial = $request->serial;
			$this->Serials->update_serial(['location' => 0], $serial);
		}

		$this->Requests->delete_request(['id' => $id]);

		$user_id = $this->session->userdata('user_id');
		$this->Products->check_low_product_and_update($user_id, $product->id, $barcode, $this->Serials, $this->Barcodes, $this->Notifications);

		if (!$product->unified) {
			$this->Operations->add_operation(['serial' => $request->serial, 'operation' => 'تم ارجاع المنتج إلي المخزون', 'sales_order' => '0'], $this->session->userdata('user_id'));
		}

		echo json_encode(['msg' => 'تم ارجاع المنتج إلي المخزون', 'status' => 'ok']);
	}

	function sales() {
		if(!in_array($this->session->userdata('user_id'), $this->get_admins())) {
		    redirect(base_url());
		}

        if ($this->input->get('dates')) {
            $dates = explode(' - ', $this->input->get('dates'));
        } else {
            $dates = [null, null];
        }
        
		$datefrom = $dates[0];
		$dateto = $dates[1];

        $date_range = [
            date_format(date_create(), 'Y/m/d 00:00:00'),
            date_format(date_create(), 'Y/m/d 23:59:59'),
        ];

        if ($datefrom != null) {
            $date_range[0] = date_format(date_create($datefrom), 'Y/m/d 00:00:00');
        }

        if ($dateto != null) {
            $date_range[1] = date_format(date_create($dateto), 'Y/m/d 23:59:59');
        } 
    
		$sales = $this->Sales->get_sales_by_date($date_range[0], $date_range[1]);
		$this->data['sales'] = $sales;

		$this->load->view('view_admin_sales', $this->data);
	}

    function shipments() {
        if(!in_array($this->session->userdata('user_id'), $this->get_admins())) {
		    redirect(base_url());
		}

        $this->Shipments->ensure_delivery_date_column();

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $actionRequested = $this->input->post('action');
            if ($actionRequested == 'add_shipment') {
                $packs = $this->input->post('packs');
                if (!is_array($packs)) {
                    $packs = [];
                }

                $rowData = [
                    'shipment_number' => strtoupper($this->input->post('shipment_number')),
                    'packs_number' => $this->input->post('packs_number'),
                    'packs' => strtoupper(json_encode($packs)),
                    'date_created' => date('Y-m-d H:i:s A'),
                ];

                $msg = $this->Shipments->add_shipment($rowData);
                if ($msg['status'] == 'wrong') {
                    $this->session->set_flashdata('error', $msg['description']);
                } else {
                    $this->session->set_flashdata('success', 'تمت إضافة الشحنة');
                }
            } else if ($actionRequested == 'deliver_shipment') {
                $msg = $this->Shipments->deliver_shipment(
                    $this->input->post('shipment_identifier'),
                    $this->input->post('delivery_date')
                );

                if ($msg['status'] == 'wrong') {
                    $this->session->set_flashdata('error', $msg['description']);
                } else {
                    $this->session->set_flashdata('success', $msg['description']);
                }
            }
        }
        
        $this->data['shipments'] = $this->Shipments->get_shipments_by_lastdate();
        $this->load->view('view_admin_shipments', $this->data);
    }

    function delete_shipment() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $id = $this->input->post('id');
            $this->Shipments->delete_shipment($id);
        }
    }

	function forms() {
		if(!in_array($this->session->userdata('user_id'), $this->get_admins())) {
		    redirect(base_url());
		}

		$this->data['forms_data_url'] = base_url().MOD_VALUE.'admin/forms-data';
		$this->load->view('view_admin_forms', $this->data);
	}

	function forms_settings() {
		if(!in_array($this->session->userdata('user_id'), $this->get_admins())) {
		    redirect(base_url());
		}

		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			$this->Variables->setdata('forms_manager_name', $this->encode_forms_value($this->input->post('manager_name', true)));
			$this->Variables->setdata('forms_manager_employee_id', $this->encode_forms_value($this->input->post('manager_employee_id', true)));
			$this->Variables->setdata('forms_stamp', $this->encode_forms_value($this->input->post('stamp', true)));
			$this->Variables->setdata('forms_store_name', $this->encode_forms_value($this->input->post('store_name', true)));
			// START settlement form defaults additions
			$this->Variables->setdata('forms_settlement_service_package', $this->encode_forms_value($this->input->post('settlement_service_package', true)));
			$this->Variables->setdata('forms_settlement_contract_duration', $this->encode_forms_value($this->input->post('settlement_contract_duration', true)));
			// END settlement form defaults additions

			$this->session->set_flashdata('success', 'تم حفظ إعدادات النماذج بنجاح');
			redirect(base_url().MOD_VALUE.'admin/settings');
		}

		redirect(base_url().MOD_VALUE.'admin/settings');
	}

	function forms_data() {
		if(!in_array($this->session->userdata('user_id'), $this->get_admins())) {
			$this->output->set_status_header(403);
			echo json_encode(['status' => 'error'], JSON_UNESCAPED_UNICODE);
			return;
		}

		$employees = [];
		foreach ($this->Model_admin->getemployees() as $employee) {
			$employee_number = '';
			if (isset($employee['job_number']) && $employee['job_number'] !== '') {
				$employee_number = $employee['job_number'];
			} else if (isset($employee['user_employee_Id'])) {
				$employee_number = $employee['user_employee_Id'];
			}

			$employees[] = [
				'id' => isset($employee['user_id']) ? $employee['user_id'] : '',
				'name' => isset($employee['user_fillname']) ? $employee['user_fillname'] : '',
				'employee_number' => $employee_number,
			];
		}

		$this->output
			->set_content_type('application/json')
			->set_output(json_encode([
				'status' => 'ok',
				'settings' => $this->get_forms_defaults(),
				'employees' => $employees,
			], JSON_UNESCAPED_UNICODE));
	}

	private function get_forms_defaults() {
		// START settlement form defaults additions
		$keys = ['forms_manager_name', 'forms_manager_employee_id', 'forms_stamp', 'forms_store_name', 'forms_settlement_service_package', 'forms_settlement_contract_duration'];
		// END settlement form defaults additions
		$values = $this->Variables->getmany($keys);

		return [
			'manager_name' => isset($values['forms_manager_name']) ? $this->decode_forms_value($values['forms_manager_name']) : '',
			'manager_employee_id' => isset($values['forms_manager_employee_id']) ? $this->decode_forms_value($values['forms_manager_employee_id']) : '',
			'stamp' => isset($values['forms_stamp']) ? $this->decode_forms_value($values['forms_stamp']) : '',
			'store_name' => isset($values['forms_store_name']) ? $this->decode_forms_value($values['forms_store_name']) : '',
			// START settlement form defaults additions
			'settlement_service_package' => isset($values['forms_settlement_service_package']) ? $this->decode_forms_value($values['forms_settlement_service_package']) : '',
			'settlement_contract_duration' => isset($values['forms_settlement_contract_duration']) ? $this->decode_forms_value($values['forms_settlement_contract_duration']) : '',
			// END settlement form defaults additions
		];
	}

	private function encode_forms_value($value) {
		$value = $value === null ? '' : $value;
		return 'b64:' . base64_encode($value);
	}

	private function decode_forms_value($value) {
		if ($value === null || $value === '') {
			return '';
		}

		if (strpos($value, 'b64:') === 0) {
			$decoded = base64_decode(substr($value, 4), true);
			return $decoded === false ? '' : $decoded;
		}

		if (preg_match('/^[?\s]+$/', $value)) {
			return '';
		}

		return $value;
	}


	function settings() {
		if(!in_array($this->session->userdata('user_id'), $this->get_admins())) {
		    redirect(base_url());
		}


		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			$settings_section = $this->input->post('settings_section');

			if ($settings_section === 'forms') {
				$this->Variables->setdata('forms_manager_name', $this->encode_forms_value($this->input->post('manager_name', true)));
				$this->Variables->setdata('forms_manager_employee_id', $this->encode_forms_value($this->input->post('manager_employee_id', true)));
				$this->Variables->setdata('forms_stamp', $this->encode_forms_value($this->input->post('stamp', true)));
				$this->Variables->setdata('forms_store_name', $this->encode_forms_value($this->input->post('store_name', true)));
				// START settlement form defaults additions
				$this->Variables->setdata('forms_settlement_service_package', $this->encode_forms_value($this->input->post('settlement_service_package', true)));
				$this->Variables->setdata('forms_settlement_contract_duration', $this->encode_forms_value($this->input->post('settlement_contract_duration', true)));
				// END settlement form defaults additions
				$this->session->set_flashdata('success', 'تم حفظ إعدادات النماذج بنجاح');
			} else if ($settings_section === 'smtp') {
				$data = [
					'SMTP_mail_encoding' => $this->input->post('smtp_mail_encoding'),
					'SMTP_port' => $this->input->post('smtp_port'),
					'SMTP_host' => $this->input->post('smtp_host'),
					'SMTP_username' => $this->input->post('smtp_username'),
					'SMTP_password' => $this->input->post('smtp_password')
				];
				$this->Settings->update_fields($data);
			} else {
				$data = [
					'Website_name' => $this->input->post('website_name'),
					'Website_email' => $this->input->post('website_email'),
					'Website_domain' => $this->input->post('website_domain'),
					'Website_backup_email' => $this->input->post('website_backup_email')
				];
				$this->Settings->update_fields($data);
			}
		}

		$this->data['settings'] = $this->Settings->get_fields();
		$this->data['forms_settings'] = $this->get_forms_defaults();

		$this->load->view('view_admin_settings', $this->data);
	}

  function lock_track()
	{
		if(!in_array($this->session->userdata('user_id'), $this->get_admins())) {
		    redirect(base_url());
		}

		$this->data['locktrack'] = $this->Model_admin->getlock();
		$this->data['excel'] = $this->Excel_import_model->select2();
		$this->load->view('view_admin_lock_track',$this->data);
	}

	  function employees()
	{
		if(!in_array($this->session->userdata('user_id'), $this->get_admins())) {
		    redirect(base_url());
		}
		$this->data['employees'] = $this->Model_admin->getemployees();
		$this->load->view('view_admin_employees',$this->data);
	}  

	function employees_sales_search() {
		if(!in_array($this->session->userdata('user_id'), $this->get_admins())) {
			redirect(base_url());
		}

		if (!$this->Variables->getdata('uploaded_file_path')) {
			$this->session->set_flashdata('error', 'يرجى رفع ملف Excel أولاً.');
		} else {
			// get individuals_date and business_date from database using filepath and then set them in session
			$uploadRecord = $this->EmployeesSales->get_upload_record_by_filepath($this->Variables->getdata('uploaded_file_path'));
			if ($uploadRecord) {
				$this->session->set_userdata('individuals_date', $uploadRecord->individuals_date);
				$this->session->set_userdata('business_date', $uploadRecord->business_date);
			}
			
		}

		// استرجاع مصطلح البحث لملء الحقل إذا كان البحث سابقًا غير ناجح
		$searchTerm = $this->session->flashdata('search_term_repopulate');
		if (!isset($searchTerm) || $searchTerm === null) {
			$searchTerm = $this->session->userdata('search_term_repopulate');
		}
		if(($searchTerm === null || !isset($searchTerm)) && $this->input->post('search_term')){ // إذا كان أول بحث
			$searchTerm = trim($this->input->post('search_term'));
		}

		$data = array(
			'employee_data' => $this->session->userdata('employee_data'), // للحفاظ على بيانات الموظف المعروض
			'search_error' => $this->session->flashdata('error'),
			'search_term_value' => $searchTerm, // لإعادة ملء حقل البحث
			'file_uploaded_name' => ($this->Variables->getdata('uploaded_file_name')) ? $this->Variables->getdata('uploaded_file_name') : 'الملف المرفوع'
		);

		$this->data = array_merge($this->data, $data);


		// إذا كانت هناك بيانات موظف، اعرض صفحة النتائج
		if ($data['employee_data']) {
            $this->load->view('employee_sales/view_admin_employee_sales_details', $this->data);
			return;
        }

        // إذا لم يكن هناك بيانات موظف، اعرض صفحة البحث العادية (نموذج فارغ)
        $this->load->view('employee_sales/view_admin_search_employees_sales', $this->data);
	}

	function employees_sales_page() {
		if(!in_array($this->session->userdata('user_id'), $this->get_admins())) {
			redirect(base_url());
		}
	
		// get individuals_date and business_date from database using filepath and then set them in session
		$uploadRecord = $this->EmployeesSales->get_upload_record_by_filepath($this->Variables->getdata('uploaded_file_path'));
		if ($uploadRecord) {
			$this->session->set_userdata('individuals_date', $uploadRecord->individuals_date);
			$this->session->set_userdata('business_date', $uploadRecord->business_date);
		}
			
		
		$this->load->view('employee_sales/view_admin_search_employees_sales', $this->data);
	}

	function employees_sales_upload() {
		if(!in_array($this->session->userdata('user_id'), $this->get_admins())) {
			redirect(base_url());
		}
		
		$this->load->view('employee_sales/view_admin_employees_sales_uploader', $this->data);
	}

	public function handle_employees_sales_upload()
    {
		$original_filename = $_FILES['userfile']['name'];
        $uploadStatus = do_upload($this, 'userfile', $original_filename, './public/employees_sales/', 'xls|xlsx', 10 * 1024 * 1024);

		if ($uploadStatus['status'] == 'wrong') {
			$this->session->set_flashdata('upload_error', 'خطأ في رفع الملف: ' . $uploadStatus['description']);
			redirect(base_url('admin/employees_sales_upload'));
		} else {
			 // حفظ التواريخ في الـ session
			$individualsDate = $this->input->post('individuals_date');
			$businessDate = $this->input->post('business_date');
			
			if ($individualsDate) {
				$this->session->set_userdata('individuals_date', $individualsDate);
			}
			if ($businessDate) {
				$this->session->set_userdata('business_date', $businessDate);
			}
			
			$filepath = './public/employees_sales/' . $uploadStatus['data']['file_name'];

			$this->session->set_flashdata('upload_success', 'تم رفع الملف بنجاح: ' . htmlspecialchars($original_filename));
			$this->Variables->setdata('uploaded_file_path', $filepath);
			$this->Variables->setdata('uploaded_file_name', $original_filename);
			$this->EmployeesSales->insert_upload_record($filepath, $original_filename, $individualsDate, $businessDate);

			$this->session->unset_userdata('employee_data');
			$this->session->unset_userdata('search_term');

			redirect(base_url('admin/employees_sales_search'));
		}

    }

	function getSubColumnLettersByGroupLabel($filePath, $groupLabel, $topRow = 1, $subRow = 2) {
		$objPHPExcel = PHPExcel_IOFactory::load($filePath);
		$sheet = $objPHPExcel->getActiveSheet();

		$highestColumn = $sheet->getHighestColumn();
		$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);

		// Step 1: Find the center column of the group label
		$centerCol = null;
		for ($col = 0; $col < $highestColumnIndex; $col++) {
			$topValue = trim($sheet->getCellByColumnAndRow($col, $topRow)->getValue());
			if ($topValue === $groupLabel) {
				$centerCol = $col;
				break;
			}
		}

		if ($centerCol === null) {
			return array(); // Group label not found
		}

		$matchedColumns = array();

		// Step 2: Expand left (up to 2 columns)
		for ($offset = 2; $offset >= 1; $offset--) {
			$col = $centerCol - $offset;
			if ($col < 0) {
				continue;
			}

			$topValue = trim($sheet->getCellByColumnAndRow($col, $topRow)->getValue());
			$subValue = trim($sheet->getCellByColumnAndRow($col, $subRow)->getValue());

			if ($topValue === '' && $subValue !== '') {
				$matchedColumns[] = PHPExcel_Cell::stringFromColumnIndex($col);
			}
		}

		// Step 3: Include center column if it has a sub-label
		$subCenter = trim($sheet->getCellByColumnAndRow($centerCol, $subRow)->getValue());
		if ($subCenter !== '') {
			$matchedColumns[] = PHPExcel_Cell::stringFromColumnIndex($centerCol);
		}

		// Step 4: Expand right (up to 2 columns)
		for ($offset = 1; $offset <= 2; $offset++) {
			$col = $centerCol + $offset;
			if ($col >= $highestColumnIndex) {
				break;
			}

			$topValue = trim($sheet->getCellByColumnAndRow($col, $topRow)->getValue());
			$subValue = trim($sheet->getCellByColumnAndRow($col, $subRow)->getValue());

			if ($topValue === '' && $subValue !== '') {
				$matchedColumns[] = PHPExcel_Cell::stringFromColumnIndex($col);
			}
		}

		return $matchedColumns;
	}

	function getGroupsBySubLabelTriggers($filePathOrSheet, $topRow = 1, $subRow = 2) {
		// Accept either a file path or a sheet object to avoid redundant file loads
		if (is_string($filePathOrSheet)) {
			$objPHPExcel = PHPExcel_IOFactory::load($filePathOrSheet);
			$sheet = $objPHPExcel->getActiveSheet();
		} else {
			$sheet = $filePathOrSheet;
		}

		$highestColumn = $sheet->getHighestColumn();
		$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);

		$groups = array();
		$currentGroupLabel = '';
		$currentGroup = array();


		for ($col = 0; $col < $highestColumnIndex; $col++) {
			$subLabel = trim($sheet->getCellByColumnAndRow($col, $subRow)->getValue());

			if ($currentGroupLabel == '') {
				$currentGroupLabel = trim($sheet->getCellByColumnAndRow($col, $topRow)->getValue());
			}
			

			// If we hit a new TGT, start a new group
			if ($subLabel === 'TGT') {
				if (!empty($currentGroup) && $currentGroupLabel !== '') {
					$groups[$currentGroupLabel] = $currentGroup;
				}
				$currentGroupLabel = trim($sheet->getCellByColumnAndRow($col, $topRow)->getValue());
				$currentGroup = array(); // start fresh
			}

			// If it's a desired label and not already in the group, add it
			if ($subLabel !== '') {
				$currentGroup[$subLabel] = PHPExcel_Cell::stringFromColumnIndex($col);
			}
			
		}

		// Add the final group if it has content
		if (!empty($currentGroup)) {
			$groups[$currentGroupLabel] = $currentGroup;
		}

		return $groups;
	}

	function generate_fields_map($data) {
		$fieldsMap = [];
		foreach ($data as $top => $subs) {
			$fieldsMap[formatColName($top)] = $top;	
		}
		return [$fieldsMap, array_flip($fieldsMap)];
	}

	function generateTables($data) {
		$fieldsMap = $this->generate_fields_map($data)[0];

		$table1 = [];
		$table2 = [];

		foreach ($data as $top => $subs) {
			$subs_length = count($subs);
			if ($subs_length == 5){
				$table1[$fieldsMap[formatColName($top)]] = [
					'label' => $top,
					'tgt' => $subs['TGT'],
					'branch_sales' => $subs['Branch sales'],
					'digital_sales' => $subs['Digital Sales'],
					'total' => $subs['Total'],
					'percentage' => $subs['%']
				];
			} else if ($subs_length == 3) {
				$table2[$fieldsMap[formatColName($top)]] = [
					'label' => $top,
					'tgt' => $subs['TGT'],
					'sales' => $subs['Sales'],
					'percentage' => $subs['%']
				];
			}
		}

		return [$table1, $table2];
	}

	function ValueOrNull($array, $key) {
		if (array_key_exists($key, $array)) {
			return $array[$key];
		} else {
			return ['TGT' => null, 'Digital Sales' => null, 'Sales' => null, 'Branch sales' => null, 'Total' => null, '%' => null];
		}
	}

	

	private function getUserSales($sheet, $itemDetails, $row, $tableType, &$sales) {
		
		$tgt = getNumericValue(getCellValue($sheet, $itemDetails['tgt'], $row));

		if ($tableType == 1) {
			$branchSales = getNumericValue(getCellValue($sheet, $itemDetails['branch_sales'], $row));
			$digitalSales = getNumericValue(getCellValue($sheet, $itemDetails['digital_sales'], $row));
			$branchSales = is_numeric($branchSales) ? (float)$branchSales : 0;
			$digitalSales = is_numeric($digitalSales) ? (float)$digitalSales : 0;

			$total = $branchSales + $digitalSales;
			
			$calculatedPercentage = 0;
			if ($tgt > 0) {
				$calculatedPercentage = round(($total / $tgt) * 100, 0);
			} elseif ($total > 0) {
				$calculatedPercentage = 100;
			}

			$percentage_available = isset($sales['percentage']);

			$sales['branch_sales'] = $branchSales + (isset($sales['branch_sales']) ? $sales['branch_sales'] : 0);
			$sales['digital_sales'] = $digitalSales + (isset($sales['digital_sales']) ? $sales['digital_sales'] : 0);
			$sales['total'] = $total + (isset($sales['total']) ? $sales['total'] : 0);
		
		} else if ($tableType == 2) {
			$salesV = getNumericValue(getCellValue($sheet, $itemDetails['sales'], $row));
			
			// الحصول على النسبة المئوية المحسوبة مباشرة من الخلية
			$percentageCell = $sheet->getCell($itemDetails['percentage'] . $row);
			$percentageValue = $percentageCell->isFormula() ? 
				$percentageCell->getCalculatedValue() : 
				$percentageCell->getValue();
			
			$calculatedPercentage = $tgt > 0 ? round(($salesV / $tgt) * 100, 0) : 0;
			
			// استخدام القيمة المحسوبة من Excel إذا كانت متوفرة
			if (is_numeric($percentageValue)) {
				$percentageValue = round($percentageValue * 100, 0);
			} else {
				$percentageValue = $calculatedPercentage;
			}

			
			if ($tgt == 0 && $salesV > 0) $percentageValue = 100;
			if ($tgt == 0 && $salesV == 0) $percentageValue = 0;

			$sales['sales'] = $salesV + (isset($sales['sales']) ? $sales['sales'] : 0);
		}
		

		$sales['tgt'] = $tgt + (isset($sales['tgt']) ? $sales['tgt'] : 0);

	}

	private function getSalesAgentSales($sheet, $itemDetails, $row, $emp_id, $emp_position, $tableType) {
		$sales = [];

		# there is only one option and it is his sales
		
		$this->getUserSales($sheet, $itemDetails, $row, $tableType, $sales);

		return $sales;

	}

	private function getBranchSupervisorSales($sheet, $itemDetails, $option, $emp_id, $emp_position, $tableType) {
		$sales = [];
		// get sales agents sales only
		$highestRow = $sheet->getHighestDataRow();
		$firstDataRow = 2;

		for ($row = $firstDataRow; $row <= $highestRow; $row++) {
			if ($option == 1 || $option == 2) {
				if (getCellValue($sheet, 'B', $row) == $emp_id) {
					$this->getUserSales($sheet, $itemDetails, $row, $tableType, $sales);	
				}
			}

			if ($option == 2 || $option == 3) {
				if (getCellValue($sheet, 'A', $row) == $emp_id) {
					$this->getUserSales($sheet, $itemDetails, $row, $tableType, $sales);
				}
			}
			
		}

		return $sales;
	}

	private function get_direct_down_position($sheet, $emp_id) {
		$highestRow = $sheet->getHighestDataRow();
		$firstDataRow = 2;

		$DirectDown = [];
		for ($row = $firstDataRow; $row <= $highestRow; $row++) {
			if (getCellValue($sheet, 'B', $row) == $emp_id) {
				$DirectDown[] = [
					'id' => getCellValue($sheet, 'A', $row),
					'row' => $row,
					'position' => getCellValue($sheet, 'E', $row)
				];
			}
		}

		return $DirectDown;
	}

	private function getBranchManagerSales($sheet, $itemDetails, $option, $emp_id, $emp_position, $tableType) {
		$sales = [];
		// get sales agents sales only
		$highestRow = $sheet->getHighestDataRow();
		$firstDataRow = 2;

		for ($row = $firstDataRow; $row <= $highestRow; $row++) {
			if ($option == 1 || $option == 2) {
				if (getCellValue($sheet, 'B', $row) == $emp_id) {
					if ($option == 2) {
						$this->getUserSales($sheet, $itemDetails, $row, $tableType, $sales);
					}

					$salesAgents = $this->get_direct_down_position($sheet, getCellValue($sheet, 'A', $row));
					
					foreach ($salesAgents as $salesAgent) {
						$this->getUserSales($sheet, $itemDetails, $salesAgent['row'], $tableType, $sales);	
					}
				}
			}

			if ($option == 3) {
				if (getCellValue($sheet, 'A', $row) == $emp_id) {
					$this->getUserSales($sheet, $itemDetails, $row, $tableType, $sales);
				}
			}
			
		}

		return $sales;
	}

	private function getAreaManagerSales($sheet, $itemDetails, $option, $emp_id, $emp_position, $tableType, $row) {
		$sales = [];

		if ($option == 3) {
			$this->getUserSales($sheet, $itemDetails, $row, $tableType, $sales);
			return $sales;
		}

		$directDownEmployees = $this->get_direct_down_position($sheet, getCellValue($sheet, 'A', $row));
		$secondLevelEmployees = [];
		$thirdLevelEmployees = [];
		

		foreach ($directDownEmployees as $directDownEmployee) {
			if ($option == 2) {
				$this->getUserSales($sheet, $itemDetails, $directDownEmployee['row'], $tableType, $sales);
			}

			$secondLevelEmployees = array_merge($secondLevelEmployees, $this->get_direct_down_position($sheet, $directDownEmployee['id']));
		}

		foreach ($secondLevelEmployees as $secondLevelEmployee) {
			if ($option == 2 || $secondLevelEmployee['position'] == 'Sales Agent') {
				$this->getUserSales($sheet, $itemDetails, $secondLevelEmployee['row'], $tableType, $sales);
			}

			$thirdLevelEmployees = array_merge($thirdLevelEmployees, $this->get_direct_down_position($sheet, $secondLevelEmployee['id']));
		}

		if ($option == 2 || $option == 1) {
			foreach ($thirdLevelEmployees as $thirdLevelEmployee) {
				$this->getUserSales($sheet, $itemDetails, $thirdLevelEmployee['row'], $tableType, $sales);
			}
		}
		

		return $sales;
	}

	

	private function getSalesByOption($sheet, $itemDetails, $option, $emp_id, $emp_position, $tableType, $row) {
		
		if ($emp_position == "Sales Agent") {
			return $this->getSalesAgentSales($sheet, $itemDetails, $row, $emp_id, $emp_position, $tableType);
		} else if ($emp_position == "Branch Supervisor") {
			return $this->getBranchSupervisorSales($sheet, $itemDetails, $option, $emp_id, $emp_position, $tableType);
		} else if ($emp_position == 'Branch Manager') {
			return $this->getBranchManagerSales($sheet, $itemDetails, $option, $emp_id, $emp_position, $tableType);
		} else if ($emp_position == 'Area Manager') {
			return $this->getAreaManagerSales($sheet, $itemDetails, $option, $emp_id, $emp_position, $tableType, $row);
		}
	}

	public function process_employee_sales_search()
    {
        $filePath = $this->Variables->getdata('uploaded_file_path');
        $searchTerm = trim((string) $this->input->post('search_term'));
		$calculation_method = $this->input->post('calculation_method') ? $this->input->post('calculation_method') : 1;
		$individualsDate = $this->input->post('individuals_date');
        
        // تخزين مصطلح البحث لإعادة ملء الحقل في حالة الخطأ أو عدم العثور
        $this->session->set_userdata('search_term_repopulate', $searchTerm);


        if (!$filePath || !file_exists($filePath)) {
            $this->session->set_flashdata('upload_error', 'ملف البيانات غير موجود. يرجى إعادة رفع الملف.');
			$this->Variables->remdata('uploaded_file_path');
			$this->Variables->remdata('uploaded_file_name');
            $this->session->unset_userdata(['employee_data']);
            redirect(base_url('admin/employees_sales_upload'));
        }

		if ($individualsDate) {
			$upload_record = $this->EmployeesSales->get_upload_record_by_date($individualsDate);
			if (!$upload_record) {
				$this->session->set_flashdata('search_error', 'لا يوجد سجل رفع مطابق لتاريخ الأفراد المحدد.');
				$this->session->unset_userdata('employee_data');
				redirect(base_url('admin/employees_sales_search'));
			}
			$filepath = $upload_record->filepath;
			$filename = $upload_record->filename;
			$this->Variables->setdata('uploaded_file_path', $filepath);
			$this->Variables->setdata('uploaded_file_name', $filename);
			$this->session->set_userdata('individuals_date', $individualsDate);
			$this->session->set_userdata('business_date', $upload_record->business_date);
			$filePath = $filepath;
		} else {
			$uploadRecord = $this->EmployeesSales->get_upload_record_by_filepath($filePath);
			$individualsDate = $uploadRecord->individuals_date;
			$this->Variables->setdata('uploaded_file_path', $filePath);
			$this->Variables->setdata('uploaded_file_name', $uploadRecord->filename);
			$this->session->set_userdata('individuals_date', $individualsDate);
			$this->session->set_userdata('business_date', $uploadRecord->business_date);
			$filePath = $filePath;
		}

		if (empty($searchTerm)) {
            $this->session->set_flashdata('search_error', 'يرجى إدخال نص للبحث.');
            $this->session->unset_userdata('employee_data');
            redirect(base_url('admin/employees_sales_search'));
        }

		$option = 1;

        try {
            $spreadsheet = PHPExcel_IOFactory::load($filePath);
            $sheet = $spreadsheet->getActiveSheet();
		
            
            // أعمدة البحث الأساسية
            $empIdCol = 'A';         // EMP ID
            $empNationalIdCol = 'C';  // EMP National ID
            $empNameCol = 'D';        // EMP Name
            $cbuUserCol = 'G';        // CBU User

            $found = false;
            $employeeData = null;
            
            $highestRow = $sheet->getHighestDataRow();
            $firstDataRow = 2; // نفترض أن البيانات تبدأ من الصف الثاني بعد العناوين

			$AllData = $this->getGroupsBySubLabelTriggers($sheet);
			$tables = $this->generateTables($AllData);


			$table1Items = $tables[0];
			$table2Items = $tables[1];


            for ($row = $firstDataRow; $row <= $highestRow; $row++) {
                $currentEmpId = trim((string)$sheet->getCell($empIdCol . $row)->getValue());
                $currentNationalId = trim((string)$sheet->getCell($empNationalIdCol . $row)->getValue());
                $currentName = trim((string)$sheet->getCell($empNameCol . $row)->getValue());
                $currentCbuUser = trim((string)$sheet->getCell($cbuUserCol . $row)->getValue());


                if (strcasecmp($currentNationalId, $searchTerm) == 0 ||
                    mb_stripos($currentName, $searchTerm) !== false ||
                    strcasecmp($currentCbuUser, $searchTerm) == 0 ||
                    strcasecmp($currentEmpId, $searchTerm) == 0) {
                    
                    $employeeData = [
						'calculation_method' => $calculation_method,
                        'employee_info' => [
                            'name' => $currentName,
                            'national_id' => $currentNationalId,
                            'cbu_user' => $currentCbuUser,
                            'emp_id' => (string) $sheet->getCell('A' . $row)->getValue(),
                            'supervisor_id' => (string) $sheet->getCell('B' . $row)->getValue(),
                            'position' => (string) $sheet->getCell('E' . $row)->getValue(),
                            'position_united' => (string) $sheet->getCell('F' . $row)->getValue(),
                            'ebu_user' => (string) $sheet->getCell('H' . $row)->getValue(),
                            'region' => (string) $sheet->getCell('I' . $row)->getValue(),
                            'area' => (string) $sheet->getCell('J' . $row)->getValue(),
                            'branch_name' => (string) $sheet->getCell('K' . $row)->getValue(),
                            'branch_code' => (string) $sheet->getCell('L' . $row)->getValue(),
                            'note' => (string) $sheet->getCell('M' . $row)->getValue(),
                            'vacation_days' => (string) $sheet->getCell('N' . $row)->getValue(),
                            'discount_percentage' => (string) $sheet->getCell('O' . $row)->getValue()
                        ],
                        'table1' => [],
                        'table2' => []
                    ];
                    

                    foreach ($table1Items as $key => $itemDetails) {
						if (in_array(null, array_values($itemDetails))){
							$employeeData['table1'][$key] = [
								'label' => $itemDetails['label'],
								'tgt' => 0,
								'branch_sales' => 0,
								'digital_sales' => 0,
								'total' => 0,
								'remaining' => 0,
								'percentage' => 0
							];
							continue;
						}
                        
						
						$sales = $this->getSalesByOption($sheet, $itemDetails, intval($calculation_method), $employeeData['employee_info']['emp_id'], $employeeData['employee_info']['position'], 1, $row);
						
						// calculate percentage
						$percentage = 0;
						if ($sales['tgt'] > 0) {
							$percentage = round(($sales['total'] * 100) / $sales['tgt'], 0);
						} elseif ($sales['total'] > 0) {
							$percentage = 100;
						}

						$employeeData['table1'][$key] = [
                            'label' => $itemDetails['label'],
                            'tgt' => $sales['tgt'],
                            'branch_sales' => $sales['branch_sales'],
                            'digital_sales' => $sales['digital_sales'],
                            'total' => $sales['total'],
                            'remaining' => $sales['tgt'] - $sales['total'],
                            'percentage' => $percentage . '%'
                        ];
                    }

                    

                    foreach ($table2Items as $key => $itemDetails) {

						if (in_array(null, array_values($itemDetails))){
							$employeeData['table2'][$key] = [
								'label' => $itemDetails['label'],
								'tgt' => 0,
								'sales' => 0,
								'remaining' => 0,
								'percentage' => 0
							];
							continue;
						}

						$sales = $this->getSalesByOption($sheet, $itemDetails, intval($calculation_method), $employeeData['employee_info']['emp_id'], $employeeData['employee_info']['position'], 2, $row);

						// calculate percentage
						$percentage = 0;
						if ($sales['tgt'] > 0) {
							$percentage = round(($sales['sales'] * 100) / $sales['tgt'], 0);
						} elseif ($sales['sales'] > 0) {
							$percentage = 100;
						}
						
						
						$employeeData['table2'][$key] = [
                            'label' => $itemDetails['label'],
                            'tgt' => $sales['tgt'],
                            'sales' => $sales['sales'],
                            'remaining' => $sales['tgt'] - $sales['sales'],
                            'percentage' => $percentage . '%'
                        ];
                    }

					
                    // تحويل نسبة الخصم
                    if (isset($employeeData['employee_info']['discount_percentage'])) {
                        $discount = str_replace('%', '', $employeeData['employee_info']['discount_percentage']);
                        $discount = floatval($discount) * 100;
                        $employeeData['employee_info']['discount_percentage'] = $discount . '%';
                    }

                    // ترتيب بيانات الجدول الأول تصاعدياً حسب النسبة المئوية
                    if (!empty($employeeData['table1'])) {
                        uasort($employeeData['table1'], function($a, $b) {
                            $percentA = (float)str_replace('%', '', $a['percentage']);
                            $percentB = (float)str_replace('%', '', $b['percentage']);
                            return $percentA - $percentB;
                        });
                    }

                    // ترتيب بيانات الجدول الثاني تصاعدياً حسب النسبة المئوية

					if (!empty($employeeData['table2'])) {
                        uasort($employeeData['table2'], function($a, $b) {
                            $percentA = (float)str_replace('%', '', $a['percentage']);
                            $percentB = (float)str_replace('%', '', $b['percentage']);
                            return $percentA - $percentB;
                        });
                    }

                    $found = true;
                    break;
                }
            }

            if ($found) {
                $this->session->set_userdata('employee_data', $employeeData);
                $this->session->unset_userdata('search_term_repopulate');
            } else {
                $this->session->set_flashdata('search_error', 'لم يتم العثور على موظف بالمعلومات المقدمة (رقم الموظف، رقم الهوية، الاسم، أو كود المستخدم): ' . htmlspecialchars($searchTerm));
                $this->session->unset_userdata('employee_data');
            }

        } catch (SpreadsheetException $e) {
            $this->session->setFlashdata('search_error', 'خطأ في قراءة ملف Excel: ' . htmlspecialchars($e->getMessage()));
            log_message('error', '[SpreadsheetException] ' . $e->getMessage() . ' Trace: ' . $e->getTraceAsString());
        } catch (\Exception $e) {
            $this->session->setFlashdata('search_error', 'حدث خطأ عام أثناء البحث: ' . htmlspecialchars($e->getMessage()));
            log_message('error', '[Exception] ' . $e->getMessage() . ' Trace: ' . $e->getTraceAsString());
        } finally {
            // Clean up spreadsheet object to free memory
            if (isset($spreadsheet)) {
                $spreadsheet->disconnectWorksheets();
                unset($spreadsheet);
            }
            // Force garbage collection
            gc_collect_cycles();
        }
        
        redirect(base_url('admin/employees_sales_search'));
    }

    

    

	public function getSubordinatesData()
    {
        // التحقق من أن الطلب هو AJAX
        if (!$this->input->is_ajax_request()) {
            echo json_encode([
                'success' => false,
                'message' => 'طلب غير صالح'
            ]);
            return;
        }

        $filePath = $this->Variables->getdata('uploaded_file_path');

        // الحصول على البيانات من الطلب
        $empId = $this->input->post('emp_id');
        $fieldKey = $this->input->post('field_key');
        $tableType = $this->input->post('table_type');
		$option = $this->input->post('option');

        if (!$empId || !$fieldKey || !$tableType) {
            echo json_encode([
                'success' => false,
                'message' => 'بيانات غير كاملة'
            ]);
            return;
        }

        if (!$filePath || !file_exists($filePath)) {
            echo json_encode([
                'success' => false,
                'message' => 'ملف البيانات غير موجود'
            ]);
            return;
        }

        try {
            $spreadsheet = PHPExcel_IOFactory::load($filePath);
            $sheet = $spreadsheet->getActiveSheet();

            // الحصول على معلومات المدير
            $managerInfo = findEmployeeByIdInSheet($sheet, $empId);

            if (!$managerInfo) {
                echo json_encode([
                    'success' => false,
                    'message' => 'لم يتم العثور على بيانات المدير'
                ]);
                return;
            }

			$AllData = $this->getGroupsBySubLabelTriggers($sheet);
			$tables = $this->generateTables($AllData);
			$fieldsMap = $this->generate_fields_map($AllData)[1];

            // الحصول على جميع الموظفين التابعين في الهيكل
            $subordinates = $this->findAllSubordinatesRecursive($sheet, $empId, $fieldKey, $tableType, $tables, $managerInfo['position'], $option);

            // ترتيب حسب نسبة الإنجاز (من الأقل للأعلى)
            usort($subordinates, function($a, $b) {
                $percentA = (float)str_replace('%', '', $a['percentage']);
                $percentB = (float)str_replace('%', '', $b['percentage']);
                return $percentA - $percentB;
            });

            // حساب المجموع الإجمالي
            $totals = calculateTotals($subordinates, $tableType);

            // الحصول على اسم الحقل بالعربي
            $fieldLabel = $fieldsMap[$fieldKey];

            echo json_encode([
                'success' => true,
                'manager' => [
                    'name' => $managerInfo['name'],
                    'position' => $managerInfo['position'],
                    'emp_id' => $empId
                ],
                'field_info' => [
                    'key' => $fieldKey,
                    'label' => $fieldLabel,
                    'table_type' => $tableType
                ],
                'subordinates' => $subordinates,
                'totals' => $totals
            ]);

        } catch (Exception $e) {
            log_message('error', 'Error in getSubordinatesData: ' . $e->getMessage());
            echo json_encode([
                'success' => false,
                'message' => 'حدث خطأ أثناء جلب البيانات: ' . $e->getMessage()
            ]);
        }
    }

	

    /**
     * البحث عن جميع الموظفين التابعين بشكل تكراري (recursive)
     */
    private function findAllSubordinatesRecursive($sheet, $managerId, $fieldKey, $tableType, $tables, $CurrentPosition, $option)
    {
        $allSubordinates = [];
        $highestRow = $sheet->getHighestDataRow();
        $firstDataRow = 2;

		$grades = ['Area Manager', 'Branch Manager', 'Branch Supervisor', 'Sales Agent'];
		
		$empData = findEmployeeByIdInSheet($sheet, $managerId);

		if (($option == 3) || ($option == 2 && $empData['position'] == $grades[2] && $CurrentPosition == $empData['position'])) {
			$fieldData = $this->extractFieldData($sheet, $empData['row'], $fieldKey, $tableType, $tables);

			$subordinateData = [
				'name' => $empData['name'],
				'position' => $empData['position'],
				'emp_id' => $managerId
			];

			// دمج بيانات الحقل
			$subordinateData = array_merge($subordinateData, $fieldData);

			$allSubordinates[] = $subordinateData;
		}

		if ($option == 3) {
			return $allSubordinates;
		}

        // البحث عن الموظفين المباشرين
        for ($row = $firstDataRow; $row <= $highestRow; $row++) {
            $supervisorId = trim((string)$sheet->getCell('B' . $row)->getValue());
			

            // إذا كان Supervisor ID يساوي Manager ID
            if ($supervisorId === $managerId) {
				
                $empId = trim((string)$sheet->getCell('A' . $row)->getValue());
                $name = trim((string)$sheet->getCell('D' . $row)->getValue());
                $position = trim((string)$sheet->getCell('E' . $row)->getValue());

                // استخراج بيانات الحقل المطلوب
				$fieldData = $this->extractFieldData($sheet, $row, $fieldKey, $tableType, $tables);

				
				if ($option == 2 && $position != $CurrentPosition) {
					if ($fieldData) {
						$subordinateData = [
							'name' => $name,
							'position' => $position,
							'emp_id' => $empId
						];


						// دمج بيانات الحقل
						$subordinateData = array_merge($subordinateData, $fieldData);

						$allSubordinates[] = $subordinateData;
					}
				}

				if ($option == 1 && $position == $grades[3]) {

					if ($fieldData) {
						$subordinateData = [
							'name' => $name,
							'position' => $position,
							'emp_id' => $empId
						];
						$allSubordinates[] = array_merge($subordinateData, $fieldData);
					}
				}
                

                // البحث عن موظفين تابعين لهذا الموظف (recursive)
                $subSubordinates = $this->findAllSubordinatesRecursive($sheet, $empId, $fieldKey, $tableType, $tables, $CurrentPosition, $option);
                $allSubordinates = array_merge($allSubordinates, $subSubordinates);
            }
        }

        return $allSubordinates;
    }

    /**
     * استخراج بيانات الحقل المطلوب من الصف
     */
    private function extractFieldData($sheet, $row, $fieldKey, $tableType, $tables)
    {
        if ($tableType === 'table1') {
            return $this->extractTable1FieldData($sheet, $row, $fieldKey, $tables[0]);
        } else {
            return $this->extractTable2FieldData($sheet, $row, $fieldKey, $tables[1]);
        }
    }

	private function extractTable1FieldData($sheet, $row, $fieldKey, $tableItems)
    {

        if (!isset($tableItems[$fieldKey])) {
            return null;
        }

        $itemDetails = $tableItems[$fieldKey];

        $tgt = getNumericValue(getCellValue($sheet, $itemDetails['tgt'], $row));
        $branchSales = getNumericValue(getCellValue($sheet, $itemDetails['branch_sales'], $row));
        $digitalSales = getNumericValue(getCellValue($sheet, $itemDetails['digital_sales'], $row));

        $total = $branchSales + $digitalSales;
        $remaining = $tgt - $total;

        $percentage = 0;
        if ($tgt > 0) {
            $percentage = round(($total / $tgt) * 100, 0);
        } elseif ($total > 0) {
            $percentage = 100;
        }

        return [
            'tgt' => $tgt,
            'branch_sales' => $branchSales,
            'digital_sales' => $digitalSales,
            'total' => $total,
            'remaining' => $remaining,
            'percentage' => $percentage . '%'
        ];
    }

    /**
     * استخراج بيانات من الجدول الثاني (بدون مبيعات رقمية)
     */
    private function extractTable2FieldData($sheet, $row, $fieldKey, $tableItems)
    {


        if (!isset($tableItems[$fieldKey])) {
            return null;
        }

        $itemDetails = $tableItems[$fieldKey];

        $tgt = getNumericValue(getCellValue($sheet, $itemDetails['tgt'], $row));
        $sales = getNumericValue(getCellValue($sheet, $itemDetails['sales'], $row));

        $remaining = $tgt - $sales;

        $percentage = 0;
        if ($tgt > 0) {
            $percentage = round(($sales / $tgt) * 100, 0);
        } elseif ($sales > 0) {
            $percentage = 100;
        }

        return [
            'tgt' => $tgt,
            'sales' => $sales,
            'remaining' => $remaining,
            'percentage' => $percentage . '%'
        ];
    }

	function employees_sales_details() {
		if(!in_array($this->session->userdata('user_id'), $this->get_admins())) {
		    redirect(base_url());
		}

		
		$this->load->view('employee_sales/view_admin_employee_sales_details', $this->data);
	}

	public function download_employee_sales_data()
	{
		$employeeData = $this->session->userdata('employee_data');

		if (!$employeeData || !isset($employeeData['employee_info'])) {
			$this->session->set_flashdata('search_error', 'لا توجد بيانات موظف لتحميلها. يرجى البحث أولاً.');
			redirect(base_url('admin/employees_sales_search'));
		}

		// Create new PHPExcel object
		$objPHPExcel = new PHPExcel();
		$sheet = $objPHPExcel->getActiveSheet();
		
		// Set RTL direction
		$sheet->setRightToLeft(true);

		// Format title
		$sheet->mergeCells('A1:O1');
		$sheet->setCellValue('A1', 'تقرير أداء الموظف');
		$sheet->getStyle('A1')->applyFromArray(array(
			'font' => array('bold' => true, 'size' => 16),
			'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
		));
		
		// Employee info section title
		$sheet->setCellValue('A3', '━━━━━━━━━━ معلومات الموظف ━━━━━━━━━━');
		$sheet->mergeCells('A3:O3');
		$sheet->getStyle('A3')->applyFromArray(array(
			'font' => array('bold' => true),
			'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
		));

		// Employee info headers
		$infoHeaders = array(
			'👤 الاسم', '🆔 رقم الهوية', '👨‍💼 رقم الموظف', '👥 المشرف', '💼 المنصب',
			'📋 المنصب الموحد', '🏢 مستخدم CBU', '🏭 مستخدم EBU', '🌍 المنطقة', '🏙️ المدينة',
			'🏬 اسم الفرع', '🔢 كود الفرع', '📝 ملاحظات', '📅 أيام الإجازة', '💰 نسبة الخصم'
		);
		$sheet->fromArray($infoHeaders, NULL, 'A4');
		
		// Employee data
		$infoData = array(
			isset($employeeData['employee_info']['name']) ? $employeeData['employee_info']['name'] : '',
			isset($employeeData['employee_info']['national_id']) ? $employeeData['employee_info']['national_id'] : '',
			isset($employeeData['employee_info']['emp_id']) ? $employeeData['employee_info']['emp_id'] : '',
			isset($employeeData['employee_info']['supervisor_id']) ? $employeeData['employee_info']['supervisor_id'] : '',
			isset($employeeData['employee_info']['position']) ? $employeeData['employee_info']['position'] : '',
			isset($employeeData['employee_info']['position_united']) ? $employeeData['employee_info']['position_united'] : '',
			isset($employeeData['employee_info']['cbu_user']) ? $employeeData['employee_info']['cbu_user'] : '',
			isset($employeeData['employee_info']['ebu_user']) ? $employeeData['employee_info']['ebu_user'] : '',
			isset($employeeData['employee_info']['region']) ? $employeeData['employee_info']['region'] : '',
			isset($employeeData['employee_info']['area']) ? $employeeData['employee_info']['area'] : '',
			isset($employeeData['employee_info']['branch_name']) ? $employeeData['employee_info']['branch_name'] : '',
			isset($employeeData['employee_info']['branch_code']) ? $employeeData['employee_info']['branch_code'] : '',
			isset($employeeData['employee_info']['note']) ? $employeeData['employee_info']['note'] : '',
			isset($employeeData['employee_info']['vacation_days']) ? $employeeData['employee_info']['vacation_days'] : '',
			isset($employeeData['employee_info']['discount_percentage']) ? $employeeData['employee_info']['discount_percentage'] : '0%'
		);
		$sheet->fromArray($infoData, NULL, 'A5');

		// Format employee info cells
		$sheet->getStyle('A4:O5')->applyFromArray(array(
			'borders' => array(
				'allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN)
			)
		));
		$sheet->getStyle('A4:O4')->applyFromArray(array(
			'fill' => array(
				'type' => PHPExcel_Style_Fill::FILL_SOLID,
				'color' => array('rgb' => 'E2EFDA')
			),
			'font' => array('bold' => true)
		));

		// Table 1 (Sales with digital sales)
		$currentRow = 7;
		if (isset($employeeData['table1']) && !empty($employeeData['table1'])) {
			// Table 1 title
			$sheet->mergeCells("A{$currentRow}:G{$currentRow}");
			$sheet->setCellValue("A{$currentRow}", '━━━━━━━━━━ جدول المبيعات مع المبيعات الرقمية ━━━━━━━━━━');
			$sheet->getStyle("A{$currentRow}")->applyFromArray(array(
				'font' => array('bold' => true),
				'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
			));
			
			$currentRow++;
			
			// Table 1 headers
			$table1Headers = array(
				'📊 المنتج/الخدمة', '🎯 الهدف', '🏬 مبيعات الفرع',
				'💻 المبيعات الرقمية', '📈 الإجمالي', '⭐ المتبقي', '📊 نسبة الإنجاز'
			);
			$sheet->fromArray($table1Headers, NULL, "A{$currentRow}");
			
			// Format headers
			$sheet->getStyle("A{$currentRow}:G{$currentRow}")->applyFromArray(array(
				'fill' => array(
					'type' => PHPExcel_Style_Fill::FILL_SOLID,
					'color' => array('rgb' => 'BDD7EE')
				),
				'font' => array('bold' => true),
				'borders' => array(
					'allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN)
				)
			));
			
			$currentRow++;
			
			// Sort table1 data by percentage
			$table1Data = $employeeData['table1'];
			uasort($table1Data, function($a, $b) {
				$percentA = (float)str_replace('%', '', $a['percentage']);
				$percentB = (float)str_replace('%', '', $b['percentage']);
				return $percentB - $percentA;
			});
			
			// Add table1 data
			foreach ($table1Data as $item) {
				$rowData = array(
					'【' . (isset($item['label']) ? $item['label'] : '') . '】',
					isset($item['tgt']) ? $item['tgt'] : 0,
					isset($item['branch_sales']) ? $item['branch_sales'] : 0,
					isset($item['digital_sales']) ? $item['digital_sales'] : 0,
					isset($item['total']) ? $item['total'] : 0,
					isset($item['remaining']) ? $item['remaining'] : 0,
					'▶ ' . (isset($item['percentage']) ? $item['percentage'] : '0%')
				);
				$sheet->fromArray($rowData, NULL, "A{$currentRow}");
				
				$sheet->getStyle("A{$currentRow}:G{$currentRow}")->applyFromArray(array(
					'borders' => array(
						'allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN)
					)
				));
				
				$sheet->getStyle("B{$currentRow}:F{$currentRow}")
					->getNumberFormat()
					->setFormatCode('#,##0');
				
				$currentRow++;
			}
			
			$currentRow += 2;
		}

		// Table 2 (Sales without digital)
		if (isset($employeeData['table2']) && !empty($employeeData['table2'])) {
			$sheet->mergeCells("A{$currentRow}:E{$currentRow}");
			$sheet->setCellValue("A{$currentRow}", '━━━━━━━━━━ جدول المبيعات بدون مبيعات رقمية ━━━━━━━━━━');
			$sheet->getStyle("A{$currentRow}")->applyFromArray(array(
				'font' => array('bold' => true),
				'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
			));
			
			$currentRow++;
			
			$table2Headers = array(
				'📊 المنتج/الخدمة', '🎯 الهدف', '💰 المبيعات',
				'⭐ المتبقي', '📊 نسبة الإنجاز'
			);
			$sheet->fromArray($table2Headers, NULL, "A{$currentRow}");
			
			$sheet->getStyle("A{$currentRow}:E{$currentRow}")->applyFromArray(array(
				'fill' => array(
					'type' => PHPExcel_Style_Fill::FILL_SOLID,
					'color' => array('rgb' => 'BDD7EE')
				),
				'font' => array('bold' => true),
				'borders' => array(
					'allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN)
				)
			));
			
			$currentRow++;
			
			// Sort table2 data
			$table2Data = $employeeData['table2'];
			uasort($table2Data, function($a, $b) {
				$percentA = (float)str_replace('%', '', $a['percentage']);
				$percentB = (float)str_replace('%', '', $b['percentage']);
				return $percentB - $percentA;
			});
			
			foreach ($table2Data as $item) {
				$rowData = array(
					'【' . (isset($item['label']) ? $item['label'] : '') . '】',
					isset($item['tgt']) ? $item['tgt'] : 0,
					isset($item['sales']) ? $item['sales'] : 0,
					isset($item['remaining']) ? $item['remaining'] : 0,
					'▶ ' . (isset($item['percentage']) ? $item['percentage'] : '0%')
				);
				$sheet->fromArray($rowData, NULL, "A{$currentRow}");
				
				$sheet->getStyle("A{$currentRow}:E{$currentRow}")->applyFromArray(array(
					'borders' => array(
						'allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN)
					)
				));
				
				$sheet->getStyle("B{$currentRow}:D{$currentRow}")
					->getNumberFormat()
					->setFormatCode('#,##0');
				
				$currentRow++;
			}
		}

		// Auto-size columns
		foreach (range('A', 'O') as $col) {
			$sheet->getColumnDimension($col)->setAutoSize(true);
		}

		// Add report footer
		$currentRow += 2;
		$sheet->mergeCells("A{$currentRow}:O{$currentRow}");
		$sheet->setCellValue("A{$currentRow}", '━━━━━━━━━━ نهاية التقرير ━━━━━━━━━━');
		$sheet->getStyle("A{$currentRow}")->applyFromArray(array(
			'font' => array('bold' => true),
			'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
		));

		// Set filename
		$nationalId = isset($employeeData['employee_info']['national_id']) ? 
			$employeeData['employee_info']['national_id'] : 'data';
		$fileName = "employee_data_" . preg_replace('/[^a-z0-9_]/i', '', $nationalId) . ".xlsx";

		// Set headers for download
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="' . $fileName . '"');
		header('Cache-Control: max-age=0');

		// Save and output file
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		$objWriter->save('php://output');
		exit;
	}


	function employees_timetable() {
		if(!in_array($this->session->userdata('user_id'), $this->get_admins())) {
		    redirect(base_url());
		}
		$this->data['employees'] = $this->Model_admin->getemployees();
		$this->load->view('view_admin_employees_timetable', $this->data);
	}

	function get_employees() {
		if(!in_array($this->session->userdata('user_id'), $this->get_admins())) {
		    redirect(base_url());
		}
		$employees = $this->Model_admin->getemployees();
		echo json_encode($employees);
	}

	function get_supervisors() {
		if(!in_array($this->session->userdata('user_id'), $this->get_admins())) {
		    redirect(base_url());
		}

		$supervisors = $this->Model_admin->getsupervisors();
		echo json_encode($supervisors);
	}

	function save_user_schedule() {
		if(!in_array($this->session->userdata('user_id'), $this->get_admins())) {
		    redirect(base_url());
		}

		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			$scheduleData = json_decode($this->input->post('scheduleData'), true);

			foreach ($scheduleData as $key => $info) {
				$user_id = explode('_', $key)[0];
				$date = explode('_', $key)[1];
				$type = $scheduleData[$key]['type'];

				$data = [
					'user_id' => $user_id,
					'date' => $date,
					'type' => $type,
					'description' => $scheduleData[$key]['status']
				];


				$this->Schedule->update_schedule($data);
			}

		}
	}

	function get_schedule() {
		if(!in_array($this->session->userdata('user_id'), $this->get_admins())) {
		    redirect(base_url());
		}
		
		
		$schedules = $this->Schedule->get_schedule();
		
		if (empty($schedules)) {
			echo '{}';
			return;
		}
		
		echo json_encode($schedules);
	}

	function get_supervisors_schedule() {
		if(!in_array($this->session->userdata('user_id'), $this->get_admins())) {
		    redirect(base_url());
		}
		
		$user_id = $this->input->get('user_id');
		$schedules = $this->Schedule->get_schedule(true);
		
		if (empty($schedules)) {
			echo '{}';
			return;
		}
		
		echo json_encode($schedules);
	}
		
	function employee()
	{
		if(!in_array($this->session->userdata('user_id'), $this->get_admins())) {
		    redirect(base_url());
		}
		
		$segment = $this->uri->segment('3');

		$employee = $this->Model_admin->get_user_by_id($segment);
		$custody1 = $this->Custodys->get_custodys(['user_id' => $segment, 'type' => 1]);
		$custody2 = $this->Custodys->get_custodys(['user_id' => $segment, 'type' => 2]);
		
		$this->data['employee'] = $employee;
		$this->data['custody_devices'] = $custody1;
		$this->data['custody_cards'] = $custody2;
		$this->load->view('view_admin_employee',$this->data);
	}

	function employees_has_sales()
	{
		if(!in_array($this->session->userdata('user_id'), $this->get_admins())) {
		    redirect(base_url());
		}
		$this->data['employees'] = $this->Model_admin->getemployees();
		$this->load->view('view_admin_employees',$this->data);
	}  
	

	
	 function admins()
	{
		if(!in_array($this->session->userdata('user_id'), $this->get_admins())) {
		    redirect(base_url());
		}
		$this->data['admins'] = $this->Model_admin->getadmins();
		$this->load->view('view_admin_admins',$this->data);
	}  
	
	public function registration()
	{
		$error = '';
		$success = '';

		
       $user_create_date = date('Y-m-d');
		if(isset($_POST['form_registration'])) 
		{
		

			$valid = 1;
            $user_employee_Id = $this->input->post('user_employee_Id', true);
			$user_name        = $this->input->post('user_name', true);
			$user_fillname    = $this->input->post('user_fillname', true);
			$user_email       = $this->input->post('user_email', true);
			$user_password    = $this->input->post('user_password', true);
		    $user_type    = $this->input->post('user_type', true);
		    
            if(empty($user_fillname)) {
            	$valid = 0;
			    $error .= "ادخل اسم الموظف".'<br>';
            }
            
            
            
			if(empty($user_name)) {
            	$valid = 0;
			    $error .= "ادخل اسم الدخول".'<br>';
            }

            if(empty($user_email)) {
            	$valid = 0;
			    $error .= "ادخل البريد الالكتروني".'<br>';
            }

            if( empty($user_password)) {
		    	$valid = 0;
		        $error .= "ادخل الرقم السري".'<br>';
		    }			
			
		    

		    $chk = $this->Model_admin->check_duplicate_username($user_name);
            if($chk) {
            	$valid = 0;
            	$error .= "اسم الدخول مستخدم".'<br>';
            }

		    if($valid == 1)
		    {

		    	$form_data = array(
		    	    'user_employee_Id'        => secure_data($user_employee_Id),
		    	    'user_fillname'        => secure_data($user_fillname),
					'user_name'        => secure_data($user_name),
					'user_email'       => secure_data($user_email),
					'user_password'    => secure_data(md5($user_password)),
					'user_type'       => secure_data($user_type),
					'user_create_date'  => secure_data($user_create_date)
	            );
	            $this->Model_admin->registration($form_data);


	            $success = "تم الحفظ بنجاح";
	            
        		$this->session->set_flashdata('success',$success);
        		redirect($this->agent->referrer());
		    }
		    else
		    {
		    	$this->session->set_flashdata('error',$error);
		    	redirect($this->agent->referrer());
		    }

		}
		else
		{
			redirect($this->agent->referrer());
		}
	}
	
	public function edit_employee()
	{
		$error = '';
		$success = '';

		
		if(isset($_POST['form1'])) 
		{
			

			$valid = 1;
			$user_id    = $this->input->post('user_id', true);
            $user_employee_Id = $this->input->post('user_employee_Id', true);
			$user_name        = $this->input->post('user_name', true);
			$user_fillname    = $this->input->post('user_fillname', true);
			$user_email       = $this->input->post('user_email', true);
			$user_password    = $this->input->post('user_password', true);
		    
            if(empty($user_fillname)) {
            	$valid = 0;
			    $error .= "ادخل اسم الموظف".'<br>';
            }
            
            
            
			if(empty($user_name)) {
            	$valid = 0;
			    $error .= "ادخل اسم الدخول".'<br>';
            }

            if(empty($user_email)) {
            	$valid = 0;
			    $error .= "ادخل البريد الالكتروني".'<br>';
            }

            if( empty($user_password)) {
		    	$valid = 0;
		        $error .= "ادخل الرقم السري".'<br>';
		    }
			
		    

		    $chk = $this->Model_admin->username_duplication_check_edit($user_name,$user_id);
            if($chk) {
            	$valid = 0;
            	$error .= "اسم الدخول مستخدم".'<br>';
            }

		    if($valid == 1)
		    {

		    	$form_data = array(
		    	    'user_employee_Id'     => secure_data($user_employee_Id),
		    	    'user_fillname'        => secure_data($user_fillname),
					'user_name'            => secure_data($user_name),
					'user_email'           => secure_data($user_email),
					'user_password'        => secure_data(md5($user_password))
	            );
	            $this->Model_admin->edit_employee($user_id,$form_data);


	            $success = "تم الحفظ بنجاح";
	            
        		$this->session->set_flashdata('success',$success);
        		redirect($this->agent->referrer());
		    }
		    else
		    {
		    	$this->session->set_flashdata('error',$error);
		    	redirect($this->agent->referrer());
		    }

		}
		else
		{
			redirect($this->agent->referrer());
		}
	}
	
	
	
	public function edit_emp_info()
	{
		$error = '';
		$success = '';

		
		if(isset($_POST['empinfoform'])) 
		{
			
			$valid = 1;
			$user_id    = $this->input->post('user_id', true);
            $user_fillname = $this->input->post('user_fillname', true);
			$user_name        = $this->input->post('user_name', true);
			$user_email    = $this->input->post('user_email', true);
            $job_number = $this->input->post('job_number', true);
			
		    		    
		    	$form_data = array(
		    	    'user_fillname'     => secure_data($user_fillname),
		    	    'user_name'        => secure_data($user_name),
					'user_email'            => secure_data($user_email),
					'job_number' => secure_data($job_number),
	            );
	            $this->Model_admin->edit_employee($user_id,$form_data);


	            $success = "تم الحفظ بنجاح";
	            
        		$this->session->set_flashdata('success',$success);
        		redirect($this->agent->referrer());
		    
		}
		else
		{
			redirect($this->agent->referrer());
		}
	}

	
	public function edit_emp_users()
	{
		$error = '';
		$success = '';
		
		
		if(isset($_POST['empusersform'])) 
		{
			
			$valid = 1;
			$user_id    = $this->input->post('user_id', true);
            $user_twasol = $this->input->post('user_twasol', true);
			$user_shahn        = $this->input->post('user_shahn', true);
			$user_jawwi    = $this->input->post('user_jawwi', true);
			$user_cubic_plus       = $this->input->post('user_cubic_plus', true);
		    		    
		    	$form_data = array(
		    	    'user_twasol'     => secure_data($user_twasol),
		    	    'user_shahn'        => secure_data($user_shahn),
					'user_jawwi'            => secure_data($user_jawwi),
					'user_cubic_plus'           => secure_data($user_cubic_plus)
	            );
	            $this->Model_admin->edit_employee($user_id,$form_data);


	            $success = "تم الحفظ بنجاح";
	            
        		$this->session->set_flashdata('success',$success);
        		redirect($this->agent->referrer());
		    
		} else {
			redirect($this->agent->referrer());
		}
	}

		
	function add_tswya()
	{
		$error = '';
		$success = '';

		
		if(isset($_POST['tswyaform'])) 
		{
			
			$valid = 1;
			$date = $this->input->post('date', true);
			$user_id    = $this->input->post('user_id', true);            
			$reason    = $this->input->post('reason', true);            
			$amount    = $this->input->post('insertedAmount', true);            
			

			if(empty($reason)) {
				$valid = 0;
				$error .= "ادخل السبب".'<br>';
			}

			if(empty($amount)) {
				$valid = 0;
				$error .= "ادخل المبلغ".'<br>';
			}
			if(empty($user_id)) {
				$valid = 0;
				$error .= "اختر الموظف".'<br>';
			}

			if ($valid == 1) {
				$form_data = array(
					'settlement_user'     => secure_data($user_id),
					'settlement_amount'     => secure_data($amount),
					'settlement_reason'        => secure_data($reason),
					'settlement_date'        => secure_data($date)
				);
				$this->Model_admin->insert_settlement($form_data);


				$success = "تم الحفظ بنجاح";
				
				$this->session->set_flashdata('success',$success);
				redirect($this->agent->referrer());
			} else {
				$this->session->set_flashdata('error',$error);
				redirect($this->agent->referrer());
			}
		} else {
			redirect($this->agent->referrer());
		}

	}

	public function add_user_custody()
	{
		$error = '';
		$success = '';

		
		if(isset($_POST['addcustodyform'])) 
		{
			
			$valid = 1;
			$user_create_date = date('Y-m-d H:i:s');
			$user_id    = $this->input->post('user_id', true);            
			$custody_type    = $this->input->post('custody_type', true);            
			$serials    = $this->input->post('serials', true);            
		    		    

			if(empty($custody_type)) {
            	$valid = 0;
			    $error .= "ادخل نوع العهدة".'<br>';
            }

			if (isset($serials)) {
				if (count($serials) == 0) {
					$valid = 0;
					$error .= "ادخل السيريالات".'<br>';
				}
				
				$found_serials = [];
				foreach ($serials as $serial) {
					if ($serial == '') {
						$valid = 0;
						$error .= 'يوجد سيريال فارغ' . '<br><br>';
						break;
					} else if (in_array($serial, $found_serials)) {
						$valid = 0;
						$error .= 'يوجد سيريال متكرر';
						break;
					}
					array_push($found_serials, $serial);
				}
			} else {
				$valid = 0;
				$error .= "ادخل السيريالات".'<br>';
			}
			

		    if($valid == 1) {
				foreach ($serials as $serial) {
					$form_data = array(
						'custody_user'     => secure_data($user_id),
						'custody_type'     => secure_data($custody_type),
						'custody_product_unified' => 0,
						'custody_date'        => secure_data($user_create_date),
						'custody_serial'            => secure_data($serial),
						'custody_note'           => ''
					);

                    $this->Model_admin->insert_custody($form_data);
					$this->Sales->add_sale([
						'userid' => $user_id,
						'identifier' => $serial,
						'user_custody_id' => $this->db->insert_id(),
						'date_created' => date('Y-m-d H:i:s')
					]);

                    if (!$this->Serials->check_for_serial($serial)) {
                        $this->Serials->update_serial(['location' => 1], $serial);
						$serialObj = $this->Serials->get_serials(['serial' => $serial])[0];

						$user_id = $this->session->userdata('user_id');
						$this->Products->check_low_product_and_update($user_id, $serialObj->productid, null, $this->Serials, $this->Barcodes, $this->Notifications);

                        $old_shelf_number = $this->Serials->get_serials(['serial' => $serial])[0]->shelf_number;
                        $this->Serials->update_serial(['shelf_number' => "$old_shelf_number-movedToEmployee"], $serial);
                        $this->Operations->add_operation(['serial' => $serial, 'operation' => 'تم تسجيل المنتج في عهدة ' . $this->Model_admin->get_user_by_id($user_id)['user_fillname'], 'sales_order' => '0'], $this->session->userdata('user_id'));
                    }
					
				}


	            $success = "تم الحفظ بنجاح";
	            
        		$this->session->set_flashdata('success',$success);
				redirect($this->agent->referrer());
			} else {
		    	$this->session->set_flashdata('error',$error);
		    	redirect($this->agent->referrer());
		    }

		} else {
			redirect($this->agent->referrer());
		}
	}


  	public function delete_employee() {
		if(isset($_POST['form2'])) {
			$user_id    = $this->input->post('user_id', true);
	
			if(!$this->session->userdata('user_id')) {
				redirect(base_url());
				exit;
			}

			$this->Model_admin->delete_employee($user_id);
			$success = "تم الحذف بنجاح";
			$this->session->set_flashdata('success',$success);
			redirect(base_url().MOD_VALUE.'index.php/admin/employees');
		}
    }

	public function edit_lock()
	{
		$error = '';
		$success = '';

		
		if(isset($_POST['form'])) 
		{
			
			$valid = 1;
			$user_lock_id           = $this->input->post('user_lock_id', true);
            $user_lock_cash        = $this->input->post('user_lock_cash', true);
			$user_lock_span        = $this->input->post('user_lock_span', true);

            if(empty($user_lock_cash)) {
            	$valid = 0;
			    $error .= "ادخل المبلغ كاش ".'<br>';
            }
            

			if(empty($user_lock_span)) {
            	$valid = 0;
			    $error .= "ادخل المبلغ بطاقة ".'<br>';
            }

           
		    if($valid == 1)
		    {

		    	$form_data = array(
		    	    'user_lock_cash'        => secure_data($user_lock_cash),
		    	    'user_lock_span'        => secure_data($user_lock_span)
	            );
	            $this->Model_admin->edit_lock($user_lock_id,$form_data);


	            $success = "تم الحفظ بنجاح";
	            
        		$this->session->set_flashdata('success',$success);
        		redirect($this->agent->referrer());
		    }
		    else
		    {
		    	$this->session->set_flashdata('error',$error);
		    	redirect($this->agent->referrer());
		    }

		}
		else
		{
			redirect($this->agent->referrer());
		}
	}
  
  
  	public function delete_lock()
    {
		if (isset($_POST['form2'])) {
			$user_lock_id    = $this->input->post('user_lock_id', true);
	
			if(!$this->session->userdata('user_id')) {
				redirect(base_url());
				exit;
			}

			$this->Model_admin->delete_lock($user_lock_id);
			$success = "تم الحذف بنجاح";
			$this->session->set_flashdata('success', $success);
			redirect(base_url().MOD_VALUE.'admin/lock-track');
		}
    }
    
    
	function settlements()
	{
		if(!in_array($this->session->userdata('user_id'), $this->get_admins())) {
		    redirect(base_url());
		}
		//$this->data['locktrack'] = $this->Model_admin->getlock();
		//$this->load->view('view_admin_lock_admin',$this->data);
		
		//$this->data['excel'] = $this->Excel_import_model->select();
		
        // Get employees
        $employees = $this->Model_admin->getemployees();
        $this->data['employees'] = $employees;
        $this->load->view('view_admin_settlements',$this->data);
	}

	function lock_admin()
	{
		if(!in_array($this->session->userdata('user_id'), $this->get_admins())) {
		    redirect(base_url());
		}
		
		
		$this->data['excel'] = $this->Excel_import_model->select();
        $this->load->view('view_admin_lock_admin',$this->data);
	}
	
	
	function upload_form()
	{
		if(!in_array($this->session->userdata('user_id'), $this->get_admins())) {
		    redirect(base_url());
		}

        $page = $this->input->get('page');
        if ($page == null) {
            $page = 0;
            $next = 2;
            $previous = 0;
        } else if ($page <= 0) {
            $page = 0;
            $next = 2;
            $previous = 0;
        } else {
            $next = $page + 1;
            $previous = $page - 1;
            $page -= 1;
        }

        $length = 20;
        $offset = $page * $length;

        $this->data['page'] = $page;
        $this->data['length'] = $length;
        $this->data['next'] = $next;
        $this->data['previous'] = $previous;
        $items = $this->Excel_import_model->select();
		$this->data['excel'] = array_slice($items, $offset, $length);
        $this->load->view('view_admin_upload_form', $this->data);
	}
	


public function delete_excellock()
    {
  if(isset($_POST['form2'])) 
		{
        $insert_excel_id    = $this->input->post('insert_excel_id', true);
  
    	if(!in_array($this->session->userdata('user_id'), $this->get_admins())) {
		    redirect(base_url());
            exit;
		}

        $this->Model_admin->delete_excellock($insert_excel_id);
        $success = "تم الحذف بنجاح";
		$this->session->set_flashdata('success',$success);
		redirect(base_url().MOD_VALUE.'admin/upload-form');
    }
    }
	
	public function edit_excellock()
	{
		$error = '';
		$success = '';

		
		if(isset($_POST['form1'])) 
		{
			
			$valid = 1;
			$insert_excel_uid           = $this->input->post('insert_excel_id', true);
            $insert_excel_twasel        = $this->input->post('insert_excel_twasel', true);

            if(empty($insert_excel_twasel)) {
            	$valid = 0;
			    $error .= "ادخل مبيعات تواصل	  ".'<br>';
            }
            


           
		    if($valid == 1)
		    {

		    	$form_data = array(
		    	    'insert_excel_twasel'        => secure_data($insert_excel_twasel)
	            );
	            $this->Model_admin->edit_excellock($insert_excel_uid,$form_data);


	            $success = "تم الحفظ بنجاح";
	            
        		$this->session->set_flashdata('success',$success);
        		redirect($this->agent->referrer());
		    }
		    else
		    {
		    	$this->session->set_flashdata('error',$error);
		    	redirect($this->agent->referrer());
		    }

		}
		else
		{
			redirect($this->agent->referrer());
		}
	}
	
		public function edit_lock_track()
	{
		$error = '';
		$success = '';

		
		if(isset($_POST['form1'])) 
		{
			
			$valid = 1;
			$insert_excel_uid           = $this->input->post('insert_excel_id', true);
            $insert_excel_electronic    = $this->input->post('insert_excel_electronic', true);
            $insert_excel_jowy          = $this->input->post('insert_excel_jowy', true);
            $insert_excel_quickplus     = $this->input->post('insert_excel_quickplus', true);

		    if($valid == 1)
		    {

		    	$form_data = array(
		    	    'insert_excel_electronic'        => secure_data($insert_excel_electronic),
		    	    'insert_excel_jowy'              => secure_data($insert_excel_jowy),
		    	    'insert_excel_quickplus'         => secure_data($insert_excel_quickplus)
	            );
	            $this->Model_admin->edit_excellock($insert_excel_uid,$form_data);


	            $success = "تم الحفظ بنجاح";
	            
        		$this->session->set_flashdata('success',$success);
        		redirect($this->agent->referrer());
		    }
		    else
		    {
		    	$this->session->set_flashdata('error',$error);
		    	redirect($this->agent->referrer());
		    }

		}
		else
		{
			redirect($this->agent->referrer());
		}
	}
  

}
