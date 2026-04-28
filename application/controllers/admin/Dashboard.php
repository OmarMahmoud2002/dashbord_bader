<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends MY_Controller {

	function __construct()
	{
        parent::__construct();
        $this->load->model('Model_admin');
        $this->load->model('Model_store_items', 'Store');
        $this->load->model('Model_notifications', 'Notifications');
        notifications($this);
        ini_set('memory_limit', '512M');
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

        ////////////////////////////////////////
		$sortColumn = isset($_GET['sort']) ? $_GET['sort'] : 'item_description'; // العمود الافتراضي للفرز
		$sortOrder  = isset($_GET['order']) ? $_GET['order'] : 'ASC';             // ASC أو DESC

		// قائمة الأعمدة المسموح بالفرز عليها
		$allowedSort = array('item_description', 'item_code', 'item_category', 'total_quantity');
		if (!in_array($sortColumn, $allowedSort)) {
			$sortColumn = 'item_description';
		}
		$sortOrder = strtoupper($sortOrder) === 'DESC' ? 'DESC' : 'ASC';
		/////////////////////////////////////////

		// إعداد البحث
		$searchName = isset($_GET['name']) ? $_GET['name'] : '';
		$searchCategory = isset($_GET['category']) ? $_GET['category'] : '';

		// حساب عدد السجلات الكلي
		$totalItems = $this->Store->get_unique_store_items_count_by_search(
			$searchName, $searchCategory
		);

		// ترقيم الصفحات
		$itemsPerPage = 15;
		$totalPages = ceil($totalItems / $itemsPerPage);
		$page = max(1, min($totalPages, intval(isset($_GET['page']) ? $_GET['page'] : 1)));
		$offset = ($page - 1) * $itemsPerPage;

		// جلب البيانات مع تجميع العدد
		$items = $this->Store->get_store_items_per_page(
			$itemsPerPage, $offset,
			$sortColumn, $sortOrder,
			$searchCategory, $searchName
		);

		// حساب إحصائيات إضافية
		$stats = $this->Store->get_stats();

		$totalRows = $this->Store->get_store_items_length();
		
		$this->data['totalRows'] = $totalRows;
		$this->data['items'] = $items;
		$this->data['stats'] = $stats;
		$this->data['totalItems'] = $totalItems;
		$this->data['itemsPerPage'] = $itemsPerPage;
		$this->data['page'] = $page;
		$this->data['totalPages'] = $totalPages;
		$this->data['searchName'] = $searchName;
		$this->data['searchCategory'] = $searchCategory;
		$this->data['sortColumn'] = $sortColumn;
		$this->data['sortOrder'] = $sortOrder;
		$this->load->view('view_admin_dashboard', $this->data);
    }
}

?>