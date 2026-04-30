<?php 

defined('BASEPATH') OR exit('No direct script access allowed');

class Model_cart extends CI_Model {
    protected $table;

    function __construct() {
        parent::__construct();
        $this->table = 'tbl_cart';
    }
    
    function get_cart_items($where = array()) {
        $this->db->order_by('id', 'asc');
        $this->db->where($where);
        $query = $this->db->get($this->table);
        return $query->result();
    }

    function search_item($where) {
        $items = $this->get_cart_items($where);
        if (count($items) > 0) {
            return true;
        }
        return false;
    }

    function count_items($where) {
        $items = $this->get_cart_items($where);
        return count($items);
    }

    function add_cart_item($data) {
        $this->db->insert($this->table, $data);
    }
    
    function update_cart_item($itemid, $data) {
        $this->db->where(['id' => $itemid]);
        $this->db->update($this->table, $data);
    }
    
    function delete_cart_item($data) {
        $this->db->delete($this->table, $data);
    }

    function delete_all_items() {
        $this->db->empty_table($this->table);
    }
}

?>