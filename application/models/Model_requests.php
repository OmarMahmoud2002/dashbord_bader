<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

class Model_requests extends CI_Model {
    protected $table;
    
    function __construct() {
        parent::__construct();
        $this->table = 'tbl_requests';
    }
    
    function get_requests($where = array()) {
        $this->db->order_by('id', 'asc');
        $this->db->where($where);
        $query = $this->db->get($this->table);
        return $query->result_array();
    }

    function get_requests_by_date($datefrom, $dateto) {
        $this->db->order_by('id', 'asc');
        $this->db->where('date_created >=', $datefrom);
        $this->db->where('date_created <=', $dateto);
        $query = $this->db->get($this->table);
        return $query->result_array();
        
    }

    function get_one_request_by_id($id) {
        $this->db->order_by('id', 'asc');
        $this->db->where(['id' => $id]);
        $query = $this->db->get($this->table);
        return $query->row_array();
    }

    function get_requests_length($where = array()) {
        return count($this->get_requests($where));
    }
    
    function add_request($data) {
        $this->db->insert($this->table, $data);
    }
    
    function update_request($data, $where) {
        $this->db->where($where);
        $this->db->update($this->table, $data);
    }
    
    function delete_request($data) {
        $rows = $this->get_requests($data);
        $this->db->delete($this->table, $data);
        return $rows;
    }

}
?>