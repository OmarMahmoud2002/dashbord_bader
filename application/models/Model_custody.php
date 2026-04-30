<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

class Model_custody extends CI_Model {
    protected $table;

    public function __construct()
    {
        parent::__construct();
        $this->table = 'tbl_user_custody';
    }

    public function get_custodys($where = array()) 
    {
        $this->db->order_by('id', 'asc');
        $this->db->where($where);
        $query = $this->db->get($this->table);
        return $query->result_array();
    }

    public function get_custody($where = array()) 
    {
        $this->db->order_by('id', 'asc');
        $this->db->where($where);
        $query = $this->db->get($this->table);
        return $query->row_array();
    }

    public function add_custody($data) {
        $this->db->insert($this->table, $data);
    }

    public function delete_custody_by_serial($serial)
    {
        $this->db->where('custody_serial', $serial);
        $this->db->delete($this->table);
    }

    public function check_for_serial($serial) {
        $this->db->where('serial_number', $serial);

        $query = $this->db->get($this->table);
        if (empty($query->result_array())) {
            return false;
        } else {
            return true;
        }
    }

    public function delete_custody($where) {
        $this->db->delete($this->table, $where);
    }
    
}

?>