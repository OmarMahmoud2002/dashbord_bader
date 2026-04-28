<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

class Model_shelves extends CI_Model {    
    function __construct() {
        parent::__construct();
        $this->table = 'tbl_shelves';
    }

    public function get_shelves($where = array()) {
        $this->db->order_by('id', 'asc');
        $this->db->where($where);
        $query = $this->db->get($this->table);
        $result = $query->result();
        return $result;
    }
    
    function get_shelves_length($where = array()) {
        return count($this->get_shelves($where));
    }

    function check_for_shelf($shelf_number) {
        $shelves = $this->get_shelves(array('shelf_number' => $shelf_number));
        if (count($shelves) == 0) {
            return FALSE;
        }
        return TRUE;
    }

    function get_one_shelf_by_id($id){
        $shelf = $this->get_shelves(array('id' => $id))[0];
        return $shelf;
    }
    
    public function add_shelf($data) {
        if ($this->check_for_shelf($data['shelf_number'])) {
            return ['status' => false, 'description' => 'الرف موجود بالفعل'];
            
        }

        $this->db->insert($this->table, $data);
        return ['status'=> true];
    }
    
    public function update_shelf($currentID, $new) {
        $this->db->update($this->table, array('shelf_number' => $new), array('id' => $currentID));
    }
    
    public function update_shelf_by_shelf_number($field, $value, $shelf_number) {
        $this->db->set($field, $value, False);
        $this->db->where(array('shelf_number' => $shelf_number));
        $this->db->update($this->table);
    }
    
    public function delete_shelf($data) {
        $rows = $this->get_shelves($data);
        $this->db->delete($this->table, $data);
        return $rows;
    }
    
    function delete_shelves_of($of, $ofField, $whereField) {
        $rows = array();
        foreach ($of as $item) {
            $rows = array_merge($rows, $this->get_shelves(array($whereField => $item->$ofField)));
        }
        $this->delete_shelf(array($whereField => $item->$ofField));
        return $rows;
    }

}


?>