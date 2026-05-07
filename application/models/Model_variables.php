<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

class Model_variables extends CI_Model {
    protected $table;
    
    function __construct() {
        parent::__construct();
        $this->table = 'tbl_variables';
    }

    function getdata($name) {
        $this->db->where('name', $name);
        $query = $this->db->get($this->table);
        $result = $query->row();
        return $result ? $result->value : null;
    }

    function getmany($names) {
        if (empty($names)) {
            return [];
        }

        $this->db->where_in('name', $names);
        $query = $this->db->get($this->table);
        $rows = $query->result();
        $result = [];

        foreach ($rows as $row) {
            $result[$row->name] = $row->value;
        }

        return $result;
    }

    function hasdata($name) {
        $this->db->where('name', $name);
        return $this->db->count_all_results($this->table) > 0;
    }

    function setdata($name, $value) {
        if (!$this->hasdata($name)) {
            $this->db->insert($this->table, ['name' => $name, 'value' => $value]);
            return;
        }
        $this->db->where('name', $name);
        $this->db->update($this->table, ['value' => $value]);
    }

    function remdata($name) {
        $this->db->where('name', $name);
        $this->db->delete($this->table);
    }
}
