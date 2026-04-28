<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

class Model_settings extends CI_Model {
    function __construct() {
        parent::__construct();
        $this->table = 'tbl_settings';
    }

    public function get_fields($where = array()) {
        $this->db->order_by('id', 'asc');
        $this->db->where($where);
        $query = $this->db->get($this->table);
        $result = $query->result();
        return $this->rows2Array($result);
    }

    public function rows2Array($fields_rows) {
        $result = [];
        foreach ($fields_rows as $row) {
            $result[$row->field] = $row->value;
        }
        return $result;
    }

    public function update_fields($fields) {
        foreach ($fields as $field => $value) {
            $this->update_field($field, $value);
        }
    }

    public function update_field($field, $value) {
        $this->db->update(
            $this->table, 
            ['value' => $value],
            ['field' => $field]
        );
    }
}