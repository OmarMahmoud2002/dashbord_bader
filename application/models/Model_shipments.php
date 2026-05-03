<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

class Model_shipments extends CI_Model {
    protected $table;
    
    function __construct() {
        parent::__construct();
        $this->table = 'tbl_shipments';
    }

    public function ensure_delivery_date_column() {
        if (!$this->db->field_exists('delivery_date', $this->table)) {
            $this->load->dbforge();
            $this->dbforge->add_column($this->table, [
                'delivery_date' => [
                    'type' => 'DATE',
                    'null' => TRUE,
                    'after' => 'date_created',
                ],
            ]);
        }
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

    public function deliver_shipment($identifier, $delivery_date) {
        $identifier = strtoupper(trim($identifier));
        $delivery_date = trim($delivery_date);

        if ($identifier == '' || $delivery_date == '') {
            return ['status' => 'wrong', 'description' => 'يرجى كتابة رقم الشحنة أو رقم الكيس وتاريخ التسليم'];
        }

        $date = DateTime::createFromFormat('Y-m-d', $delivery_date);
        if (!$date || $date->format('Y-m-d') !== $delivery_date) {
            return ['status' => 'wrong', 'description' => 'تاريخ التسليم غير صحيح'];
        }

        $shipment = $this->find_shipment_by_identifier($identifier);
        if (!$shipment) {
            return ['status' => 'wrong', 'description' => 'لم يتم العثور على شحنة بهذا الرقم أو رقم الكيس'];
        }

        $this->db->where('id', $shipment->id);
        $this->db->update($this->table, ['delivery_date' => $delivery_date]);

        return ['status' => 'ok', 'description' => 'تم تسجيل تاريخ تسليم الشحنة'];
    }

    private function find_shipment_by_identifier($identifier) {
        $shipments = $this->get_shipments_by_lastdate();

        foreach ($shipments as $shipment) {
            if (strtoupper(trim($shipment->shipment_number)) == $identifier) {
                return $shipment;
            }
        }

        foreach ($shipments as $shipment) {
            $packs = json_decode($shipment->packs, true);
            if (!is_array($packs)) {
                continue;
            }

            foreach ($packs as $pack) {
                if (strtoupper(trim($pack)) == $identifier) {
                    return $shipment;
                }
            }
        }

        return null;
    }

    public function delete_shipment($shipment_id) {
        $this->db->delete($this->table, ['id' => $shipment_id]);
    }
}

?>
