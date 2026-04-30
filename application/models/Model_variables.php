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

    function setdata($name, $value) {
        if (!$this->getdata($name)) {
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