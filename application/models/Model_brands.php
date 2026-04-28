<?php 

defined('BASEPATH') OR exit('No direct script access allowed');

class Model_brands extends CI_Model {
    
    function __construct() {
        parent::__construct();
        $this->table = 'tbl_brands';
    }
    
    function get_brands($where = array()) {
        $this->db->order_by('id', 'asc');
        $this->db->where($where);
        $query = $this->db->get($this->table);
        return $query->result();
    }

    function get_brand_by_index($index, $where = []) {
        $brands = $this->get_brands($where);
        if ($index == null) {
            return false;
        } else if (count($brands) > intval($index)) {
            return $brands[intval($index)];
        } else {
            return false;
        }
    }

    function get_brands_names() {
        $this->db->order_by('id', 'asc');
        $query = $this->db->query("SELECT brand FROM $this->table");
        return array_column($query->result_array(), 'brand');
    }
    
    function get_brands_length($where = array()) {
        return count($this->get_brands($where));
    }

    function add_brand($data) {
        $this->db->insert($this->table, $data);
    }

    function delete_brand($data) {
        $this->db->delete($this->table, $data);
    }
}