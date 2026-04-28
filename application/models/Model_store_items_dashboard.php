<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

class Model_store_items_dashboard extends CI_Model {
    
    function __construct() {
        parent::__construct();
        $this->load->database();
    }

    /**
     * Get paginated store items with search and sorting (PHP 5.6 compatible)
     * @param array $params - Contains: sort, order, name, category, page, itemsPerPage
     * @return array - Returns array with 'items', 'totalItems', 'totalPages', 'currentPage', 'itemsPerPage'
     */
    public function getItemsWithPagination($params = array()) {
        // Set default values using isset ternary (PHP 5.6 compatible)
        $sortColumn = isset($params['sort']) ? $params['sort'] : 'item_description';
        $sortOrder = isset($params['order']) ? $params['order'] : 'ASC';
        $searchName = isset($params['name']) ? $params['name'] : '';
        $searchCategory = isset($params['category']) ? $params['category'] : '';
        $page = isset($params['page']) ? intval($params['page']) : 1;
        $itemsPerPage = isset($params['itemsPerPage']) ? intval($params['itemsPerPage']) : 15;

        // List of allowed columns for sorting
        $allowedSort = array('item_description', 'item_code', 'item_category', 'total_quantity');
        if (!in_array($sortColumn, $allowedSort)) {
            $sortColumn = 'item_description';
        }
        
        // Validate sort order
        $sortOrder = strtoupper($sortOrder) === 'DESC' ? 'DESC' : 'ASC';

        // Count total items
        $countQuery = $this->db->select('COUNT(DISTINCT item_description) as total', FALSE)
            ->from('current_store_items');
        
        if (!empty($searchName)) {
            $countQuery->group_start();
            $countQuery->like('item_description', $searchName);
            $countQuery->or_like('item_code', $searchName);
            $countQuery->group_end();
        }

        if (!empty($searchCategory)) {
            $countQuery->where('item_category', $searchCategory);
        }

        $countResult = $countQuery->get();
        $row = $countResult->row();
        $totalItems = isset($row->total) ? $row->total : 0;

        // Calculate pagination
        $totalPages = ceil($totalItems / $itemsPerPage);
        $page = max(1, min($totalPages, $page));
        $offset = ($page - 1) * $itemsPerPage;

        // Fetch paginated items
        $this->db->select('
            item_description,
            item_category,
            item_code,
            (
                COUNT(DISTINCT CASE WHEN LOWER(TRIM(serial_control)) != \'no\' THEN serial_number END)
                +
                COALESCE(SUM(CASE WHEN LOWER(TRIM(serial_control)) = \'no\' THEN quantity_total END), 0)
            ) AS total_quantity
        ', FALSE);

        $this->db->from('current_store_items');

        if (!empty($searchName)) {
            $this->db->group_start();
            $this->db->like('item_description', $searchName);
            $this->db->or_like('item_code', $searchName);
            $this->db->group_end();
        }

        if (!empty($searchCategory)) {
            $this->db->where('item_category', $searchCategory);
        }

        $this->db->group_by(array('item_description', 'item_category', 'item_code'));
        $this->db->order_by($sortColumn, $sortOrder);
        $this->db->limit($itemsPerPage, $offset);

        $query = $this->db->get();
        $items = $query->result_array();

        return array(
            'items' => $items,
            'totalItems' => $totalItems,
            'totalPages' => $totalPages,
            'currentPage' => $page,
            'itemsPerPage' => $itemsPerPage
        );
    }

    /**
     * Get dashboard statistics (PHP 5.6 compatible)
     * @return array - Returns array with total_products, total_categories, total_quantity
     */
    public function getDashboardStats() {
        $this->db->select('
            COUNT(DISTINCT item_description) as total_products,
            COUNT(DISTINCT item_category) as total_categories,
            SUM(quantity_total) as total_quantity
        ', FALSE);
        
        $query = $this->db->get('current_store_items');
        $row = $query->row_array();

        return array(
            'total_products' => isset($row['total_products']) ? $row['total_products'] : 0,
            'total_categories' => isset($row['total_categories']) ? $row['total_categories'] : 0,
            'total_quantity' => isset($row['total_quantity']) ? $row['total_quantity'] : 0
        );
    }

    /**
     * Get total number of rows in current_store_items
     * @return int - Total number of records
     */
    public function getTotalRows() {
        return $this->db->count_all('current_store_items');
    }
}
?>
