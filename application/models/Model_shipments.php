<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

class Model_shipments extends CI_Model {
    protected $table;
    
    function __construct() {
        parent::__construct();
        $this->table = 'tbl_shipments';
    }

    public function get_shipments($where = array()) {
        $this->db->order_by('id', 'asc');
        $this->db->where($where);
        $query = $this->db->get($this->table);
        $result = $query->result();
        return $result;
    }

    public function get_shipments_by_lastdate($where = array()) {
        $this->db->order_by('date_created', 'desc');
        $this->db->where($where);
        $query = $this->db->get($this->table);
        $result = $query->result();
        return $result;
    }

    public function add_shipment($data) {
        $values = array_values($data);
        if (in_array('', $values)) {
            return ['status' => 'wrong', 'description' => 'يرجي ملئ كل البيانات'];
        }

        $this->db->insert($this->table, $data);
        return ['status' => 'ok'];
    }

    public function delete_shipment($shipment_id) {
        $this->db->delete($this->table, ['id' => $shipment_id]);
    }
}

?>