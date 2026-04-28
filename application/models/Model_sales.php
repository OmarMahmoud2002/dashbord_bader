<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

class Model_sales extends CI_Model {
    
    function __construct() {
        parent::__construct();
        $this->table = 'tbl_sales';
    }
    
    function get_sales($where = array()) {
        $this->db->order_by('id', 'asc');
        $this->db->where($where);
        $query = $this->db->get($this->table);
        return $query->result();
    }

    function get_sales_by_date($datefrom, $dateto) {

        $this->db->order_by('id', 'asc');
        $this->db->where('date_created >=', $datefrom);
        $this->db->where('date_created <=', $dateto);
        $query = $this->db->get($this->table);
        return $query->result();
    }

    function add_sale($data) {
        $this->db->insert($this->table, $data);
    }

    function delete_sale($where) {
        $this->db->delete($this->table, $where);
    }
}

?>