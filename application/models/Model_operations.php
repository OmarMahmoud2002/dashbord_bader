<?php 

defined('BASEPATH') OR exit('No direct script access allowed');

class Model_operations extends CI_Model {
    protected $table;
    function __construct() {
        parent::__construct();
        $this->table = 'tbl_operations';
    }

    function get_operations($where = array()) {
        $this->db->order_by('id', 'asc');
        $this->db->where($where);
        $query = $this->db->get($this->table);
        return $query->result();
    }

    function add_operation($data, $user_id) {
        $data['made_by'] = $user_id;
        $data['date_created'] = date('Y-m-d H:i:s');
        $this->db->insert($this->table, $data);
    }

}

?>