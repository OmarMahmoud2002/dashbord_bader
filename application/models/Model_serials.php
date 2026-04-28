<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

class Model_serials extends CI_Model {
    
    function __construct() {
        parent::__construct();
        $this->table = 'tbl_serials';
    }
    
    function get_serials($where = array()) {
        $this->db->order_by('id', 'asc');
        $this->db->where($where);
        $query = $this->db->get($this->table);
        return $query->result();
    }

    function get_serials_like($like, $where) {
        $this->db->like('serial', $like, 'before');
        $this->db->where($where);
        $query = $this->db->get($this->table);
        return $query->result();
    }

    function get_serial_like_by_category($like, $categoryId) {
        $sql = "SELECT * FROM $this->table WHERE serial LIKE '%$like' AND categoryId = $categoryId";
        $query = $this->db->query($sql);
        return $query->result();
    }
    
    function get_serials_length($where = array()) {
        return count($this->get_serials($where));
    }
    
    function check_for_serial($serial) {
        $serials = $this->get_serials(array('serial' => $serial));
        if (count($serials) == 0) {
            return TRUE;
        }
        return FALSE;
    }
    
    function add_serial($data) {
        if ($data['poster_number'] == '') {
            return ['status' => 'wrong', 'description' => 'رجاء إدخال رقم الملصق'];
        }

        if (count_chars($data['serial']) < 6) {
            return ['status' => 'wrong', 'description' => 'السيريال يحب ان يكون علي الأقل ستة أرقام'];
        }

        if ($this->check_for_serial($data['serial'])) {
            $this->db->insert($this->table, $data);
            return ['status'=> 'ok'];
        } else {
            return ['status' => 'wrong', 'description' => 'السيريال موجود بالفعل'];
        }
    }
    
    function update_serial_shelf($shelf_number, $old_shelf_number) {
        $this->db->set('shelf_number', $shelf_number);
        $this->db->where(array('shelf_number' => $old_shelf_number));
        $this->db->update($this->table);
    }
    
    function update_serial($data, $serial) {
        $this->db->update($this->table, $data, array('serial' => $serial));
    }
    
    function delete_serial($data) {
        $rows = $this->get_serials($data);
        $this->db->delete($this->table, $data);
        return $rows;
    }

    function delete_serials_of($of, $ofField, $whereField) {
        $rows = array();
        foreach ($of as $item) {
            $rows = array_merge($rows, $this->get_serials(array($whereField => $item->$ofField)));
        }
        foreach ($of as $item) {
            $this->delete_serial(array($whereField => $item->$ofField));
        }

        return $rows;
    }
}

?>
