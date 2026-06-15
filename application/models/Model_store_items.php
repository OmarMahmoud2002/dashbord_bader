<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Model_store_items extends CI_Model
{
    protected $table;
    protected $shelves_table;

	public function __construct()
	{
		parent::__construct();
        $this->table = 'current_store_items';
        $this->shelves_table = 'tbl_shelves_items';
	}

    public function total_quantity_select_query() {
        $this->db->select("
            CASE
                WHEN LOWER(TRIM(i.serial_control)) = 'no'
                    THEN i.quantity_total - COUNT(DISTINCT CASE WHEN c.item_code = i.item_code THEN c.id END) - COUNT(DISTINCT CASE WHEN r.item_code = i.item_code THEN r.id END)
                ELSE
                    COUNT(DISTINCT CASE WHEN LOWER(TRIM(i.serial_control)) = 'yes' AND c.serial_number IS NULL AND r.serial_number IS NULL THEN i.serial_number END)
            END AS total_quantity
        ", False);
    }

    public function join_custodys() {
        $this->db->join('tbl_user_custody c', 'i.serial_number = c.serial_number OR (i.serial_control = "no" AND i.item_code = c.item_code)', 'left');
    }

    public function join_requests() {
        $this->db->join('tbl_requests r', 'i.serial_number = r.serial_number OR (i.serial_control = "no" AND i.item_code = r.item_code)', 'left');
    }

	public function get_store_items($where = array())
	{
		$this->db->from($this->table);
		if (count($where) > 0) {
			$this->db->where($where);
		}
		$query = $this->db->get();
		return $query->result_array();
	}

    public function get_store_items_per_page(
        $limit,
        $start,
        $sortColumn = 'item_description',
        $sortOrder = 'ASC',
        $searchCategory = '',
        $searchName = ''
    ) {
        // جلب البيانات مع تجميع العدد - CodeIgniter Query Builder (PHP 5.6 compatible)
        $this->db->select("
            i.item_description,
            i.item_category,
            i.item_code
        ", FALSE);
        $this->total_quantity_select_query();
        
        $this->db->from($this->table . ' AS i');
        $this->join_custodys();
        $this->join_requests();
        
        if (!empty($searchName)) {
            $this->db->group_start();
            $this->db->like('i.item_description', $searchName);
            $this->db->or_like('i.item_code', $searchName);
            $this->db->group_end();
        }

        if (!empty($searchCategory)) {
            $this->db->where('i.item_category', $searchCategory);
        }
        
        $this->db->group_by(array('i.item_description', 'i.item_category', 'i.item_code', 'i.serial_control', 'i.quantity_total'));
        $this->db->order_by($sortColumn, $sortOrder);
        $this->db->limit($limit, $start);
        

        $query = $this->db->get();
        return $query->result_array();
    }


    public function get_low_products() {
        // جلب البيانات مع تجميع العدد - CodeIgniter Query Builder (PHP 5.6 compatible)
        $sql = "
            SELECT
                i.item_description,
                i.item_category,
                i.item_code,
                i.low_quantity,
                i.updated_at,
                CASE
                    WHEN LOWER(TRIM(i.serial_control)) = 'no'
                        THEN i.quantity_total - COUNT(DISTINCT CASE WHEN c.item_code = i.item_code THEN c.id END) - COUNT(DISTINCT CASE WHEN r.item_code = i.item_code THEN r.id END)
                    ELSE
                        COUNT(DISTINCT CASE WHEN LOWER(TRIM(i.serial_control)) = 'yes' AND c.serial_number IS NULL AND r.serial_number IS NULL THEN i.serial_number END)
                END AS total_quantity

            FROM current_store_items i

            LEFT JOIN tbl_user_custody c
                ON i.serial_number = c.serial_number OR (i.serial_control = 'no' AND i.item_code = c.item_code)

            LEFT JOIN tbl_requests r
                ON i.serial_number = r.serial_number OR (i.serial_control = 'no' AND i.item_code = r.item_code)


            GROUP BY
                i.item_description,
                i.item_category,
                i.item_code,
                i.serial_control,
                i.quantity_total,
                i.low_quantity,
                i.updated_at

            HAVING total_quantity <= i.low_quantity

            ORDER BY total_quantity ASC;
        ";

        $query = $this->db->query($sql);
        return $query->result_array();
    }

    public function get_stats() {
        // Get dashboard statistics - CodeIgniter Query Builder (PHP 5.6 compatible)
        $this->db->select('
            COUNT(DISTINCT item_description) as total_products,
            COUNT(DISTINCT item_category) as total_categories,
            SUM(quantity_total) as total_quantity
        ', FALSE);
        
        $this->db->from($this->table);
        
        $query = $this->db->get();
        $stats = $query->row_array();
        
        return array(
            'total_products' => isset($stats['total_products']) ? $stats['total_products'] : 0,
            'total_categories' => isset($stats['total_categories']) ? $stats['total_categories'] : 0,
            'total_quantity' => isset($stats['total_quantity']) ? $stats['total_quantity'] : 0
        );
    }

    function get_unique_store_items_count_by_search($searchName, $searchCategory)
    {
        $this->db->from($this->table);
        $this->db->select('COUNT(DISTINCT item_description) AS total');
        
        if (!empty($searchName)) {
            $this->db->group_start();
            $this->db->like('item_description', $searchName);
            $this->db->or_like('item_code', $searchName);
            $this->db->group_end();
        }

        if (!empty($searchCategory)) {
            $this->db->where('item_category', $searchCategory);
        }
        
        $query = $this->db->get();
        return $query->row_array()['total'];
    }

	public function get_store_items_length($where = array())
	{
		$this->db->from($this->table);
		$this->db->select('COUNT(*) AS count');
		if (count($where) > 0) {
			$this->db->where($where);
		}
		$query = $this->db->get();
		return $query->row_array()['count'];
	}


    function get_store_unique_items_count($where = array()) {
        $this->total_quantity_select_query();
        
        $this->db->from($this->table . ' AS i');
        $this->join_custodys();
        $this->join_requests();

        $this->db->where($where);
        $query = $this->db->get();
        return $query->row_array()['total_quantity'];
    }

    function get_store_item($where = array()) {
        $this->db->select("*");
        $this->db->from($this->table);
        $this->db->where($where);
        $query = $this->db->get();
        return $query->row_array();
    }

    function get_remaining_quantity_by_item_code($item_code) {
        $sql = "
            SELECT
                i.id,
                i.item_description,
                i.item_category,
                i.item_code,
                CASE
                    WHEN LOWER(TRIM(i.serial_control)) = 'no'
                        THEN i.quantity_total - COUNT(DISTINCT CASE WHEN c.item_code = i.item_code THEN c.id END) - COUNT(DISTINCT CASE WHEN r.item_code = i.item_code THEN r.id END)
                    ELSE
                        COUNT(DISTINCT CASE WHEN LOWER(TRIM(i.serial_control)) = 'yes' AND c.serial_number IS NULL AND r.serial_number IS NULL THEN i.serial_number END)
                END AS total_quantity

            FROM current_store_items i

            LEFT JOIN tbl_user_custody c
                ON i.serial_number = c.serial_number OR (i.serial_control = 'no' AND i.item_code = c.item_code)

            LEFT JOIN tbl_requests r
                ON i.serial_number = r.serial_number OR (i.serial_control = 'no' AND i.item_code = r.item_code)


            GROUP BY
                i.item_description,
                i.item_category,
                i.item_code

            HAVING i.item_code = ?

            ORDER BY total_quantity ASC;
        ";

        $query = $this->db->query($sql, array($item_code));
        return $query->result_array()[0]['total_quantity'];
    }

    function get_serial_control_statuses($where = array()) {
        $this->db->select("DISTINCT LOWER(TRIM(serial_control)) AS serial_status");
        $this->db->from($this->table);
        $this->db->where($where);
        $query = $this->db->get();
        return array_column($query->result_array(), 'serial_status');
    }

    function get_serials($where = array()) {
        $this->db->select('i.serial_number AS serial_number');

        $this->db->from($this->table . ' AS i');
        $this->join_custodys();
        $this->join_requests();

        $this->db->where($where);
        $this->db->order_by('i.serial_number', 'ASC');

        $query = $this->db->get();
        return array_column($query->result_array(), 'serial_number');
    }


    function get_items_by_serialpart($serialpart, $where = array()) {
        $this->db->select('*');
        $this->db->from($this->table);
        $this->db->like('serial_number', $serialpart, 'after');
        $this->db->where($where);
        $query = $this->db->get();        
        return $query->result_array();
    }

    function get_items_count_in_shelf($shelf_id) {
        $this->total_quantity_select_query();

        $this->db->from($this->shelves_table . ' AS i');
        $this->db->where('shelf_id', $shelf_id);

        $this->db->join('tbl_user_custody c', 'i.serial_number = c.serial_number OR (i.serial_control = "no" AND i.item_code = c.item_code)', 'left');
        $this->db->join('tbl_requests r', 'i.serial_number = r.serial_number OR (i.serial_control = "no" AND i.item_code = r.item_code)', 'left');

        $this->db->group_by(array('i.serial_control', 'i.quantity_total'));

        $query = $this->db->get();
        $result = array_column($query->result_array(), 'total_quantity');
        return !empty($result) ? $result[0] : 0;
    }

    function get_items_in_shelf($shelf_id) {
        $this->db->select('*');

        $this->db->from($this->shelves_table . ' AS i');
        $this->db->where(['i.shelf_id' => $shelf_id]);

        $query = $this->db->get();
        return $query->result_array();
    }

    function check_item_exists_in_shelves_table($shelf_id, $item_code, $serial_control, $serial_number, $barcode) {
        $this->db->select('*');
        $this->db->from($this->shelves_table);
        $this->db->where(['item_code' => $item_code, 'serial_control' => $serial_control, 'serial_number' => $serial_number, 'barcode' => $barcode]);
        $query = $this->db->get();
        return $query->row_array();
    }

    function get_item_shelf($item_code, $serial_number, $barcode) {
        $this->db->select('*');
        $this->db->from($this->shelves_table);
        $this->db->where(['item_code' => $item_code, 'serial_number' => $serial_number, 'barcode' => $barcode]);
        $query = $this->db->get();
        $result = $query->row_array();

        if (empty($result)) return null;
        
        return $result['shelf_id'];
    }


    function add_item_to_shelf($item_code, $serial_control, $serial_number, $barcode,  $shelf_id) {
        $this->db->insert($this->shelves_table, ['item_code' => $item_code, 'serial_control' => $serial_control, 'serial_number' => $serial_number, 'barcode' => $barcode, 'shelf_id' => $shelf_id, 'quantity_total' => 1]);
        return $this->db->insert_id();
    }

    function move_item_to_shelf($item_code, $serial_control, $serial_number, $barcode, $shelf_id) {
        if ($this->check_item_exists_in_shelves_table($shelf_id, $item_code, $serial_control, $serial_number, $barcode)) {
            # move it to the wanted shelf
            $this->db->where('item_code', $item_code);
            $this->db->update($this->shelves_table, ['shelf_id' => $shelf_id]);
            
        } else {
            # add it to the wanted shelf
            $this->add_item_to_shelf($item_code, $serial_control, $serial_number, $barcode, $shelf_id);
        }
    }

    public function remove_item_from_shelf($item_code, $serial_control, $serial_number, $barcode) {
        $this->db->where(['item_code' => $item_code, 'serial_control' => $serial_control, 'serial_number' => $serial_number, 'barcode' => $barcode]);
        $this->db->delete($this->shelves_table);
        return $this->db->affected_rows();
    }

	public function add_store_item($data)
	{
		$this->db->insert($this->table, $data);
		return $this->db->insert_id();
	}

	public function update_store_item($data, $where = array())
	{
		$this->db->where($where);
		$this->db->update($this->table, $data);
		return $this->db->affected_rows();
	}

	public function delete_store_item($data)
	{
		$this->db->where($data);
		$this->db->delete($this->table);
		return $this->db->affected_rows();
	}

	public function delete_store_items_of($of, $ofField, $whereField)
	{
		$this->db->where($ofField, $of);
		$this->db->delete($this->table);
		return $this->db->affected_rows();
	}
}