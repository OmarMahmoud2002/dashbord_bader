<?php 

defined('BASEPATH') OR exit('No direct script access allowed');

class Model_categories extends CI_Model {
    protected $table;
    
    function __construct() {
        parent::__construct();
        $this->table = 'tbl_categories';
    }
    
    function get_categories($where = array()) {
        $this->db->order_by('id', 'asc');
        $this->db->where($where);
        $query = $this->db->get($this->table);
        return $query->result();
    }
    
    function get_categories_length($where = array()) {
        return count($this->get_categories($where));
    }
    
    function get_one_category_by_name($name) {
        $this->db->order_by('id', 'asc');
        $this->db->where(array('category' => $name));
        $query = $this->db->get($this->table);
        return $query->result();
    }

    function get_category_by_index($index, $where = []) {
        $categories = $this->get_categories($where);
        if ($index == null) {
            return false;
        } else if (count($categories) > intval($index)) {
            return $categories[intval($index)];
        } else {
            return false;
        }
    }

    function find_category_by_brand($brand, $category) {
        $this->db->order_by('id', 'asc');
        $this->db->where(['brand' => $brand, 'category' => $category]);
        $query = $this->db->get($this->table);
        return boolval(count($query->result()));
    }

    function unique($arr) {
        $new_array = array();
        $ids = array();
        foreach ($arr as $item) {
            if (in_array($item->id, $ids)) {
                continue;
            }
            array_push($ids, $item->id);
            array_push($new_array, $item);
        }
        return $new_array;
    }
    
    function add_category($data) {
        $this->db->insert($this->table, $data);
    }
    
    function update_category($field, $value, $where) {
        $this->db->set($field, $value);
        $this->db->where($where);
        $this->db->update($this->table);
    }
    
    function delete_category($data) {
        $rows = $this->get_categories($data);
        $this->db->delete($this->table, $data);
        return $rows;
    }

    function delete_categories_of($of, $ofField, $whereField) {
        $rows = array();
        foreach ($of as $item) {
            $rows = array_merge($rows, $this->get_categories(array($whereField => $item->$ofField)));
        }
        $this->delete_category(array($whereField => $item->$ofField));
        return $rows;
    }
}

?>