<?php 

defined('BASEPATH') OR exit('No direct script access allowed');

class Model_users extends CI_Model {
    protected $table;

    public function __construct() {
        parent::__construct();
        $this->table = 'tbl_user';
    }

    public function get_users($where = array()) {
        $this->db->order_by('id', 'asc');
        $this->db->where($where);
        $query = $this->db->get($this->table);
        return $query->result();
    }

    public function get_one_user_by_user_id($id) {
        $this->db->where(array('user_id' => $id));
        $query = $this->db->get($this->table);
        return $query->result()[0];
    }
}
?>