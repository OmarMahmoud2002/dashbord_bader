<?php 

defined('BASEPATH') OR exit('No direct script access allowed');

class Model_barcodes extends CI_Model {

    function __construct() {
        parent::__construct();
        $this->table = 'tbl_barcodes';
    }

    function get_barcodes($where = array()) {
        $this->db->order_by('id', 'asc');
        $this->db->where($where);
        $query = $this->db->get($this->table);
        return $query->result();
    }

    function get_barcode_shelves($barcodeObj) {
        $obj = $barcodeObj;
        $shelves = [];
        if (!is_null(json_decode($obj->shelf_info, true))) {
            foreach (json_decode($obj->shelf_info, true) as $shelf => $quantity) {
                array_push($shelves, explode('shelf', $shelf)[1]);
            }
        }
        return $shelves;
    }

    function add_barcode($data) {
        $this->db->insert($this->table, $data);
    }

    function update_barcode($data, $barcode) {
        $this->db->where('barcode', $barcode);
        $this->db->update($this->table, $data);
    }

    function change_barcode_shelf_quantity($barcode, $shelf_number, $quantity) {
        $barcodeObj = $this->get_barcodes(['barcode' => $barcode])[0];
        $availble_quantity = $barcodeObj->quantity_remaining;
        $shelves_info = json_decode($barcodeObj->shelf_info, true);

        if (is_null($shelves_info) == true) {
            $shelves_info = [];
            $shelf_quantity = 0;
        } else if (key_exists('shelf' . $shelf_number, $shelves_info)) {
            $shelf_quantity = $shelves_info['shelf' . $shelf_number];
        } else {
            $shelf_quantity = 0;
        }
        
        $new_quantity = $shelf_quantity + $quantity;
        

        if ($quantity <= $availble_quantity) {
            $shelves_info['shelf' . $shelf_number] = $new_quantity;
            $this->decrease_remaining_quantity($barcodeObj->productid, $quantity);
            

            $this->update_barcode([
                'shelf_info' => json_encode($shelves_info)
            ], $barcode);
            return '';
        } else {
            return 'الكمية المحددة أكثر من الموجود';
        }
    }

    function delete_barcode_shelf($obj, $shelf_number) {
        $shelf_info = json_decode($obj->shelf_info, true);
        $key = 'shelf' . $shelf_number;
        $this->Barcodes->increase_remaining_quantity($obj->productid, $shelf_info[$key]);
        unset($shelf_info[$key]);
        $this->Barcodes->update_barcode(['shelf_info' => json_encode($shelf_info)], $obj->barcode);
    }

    function update_barcode_registered_count($barcode, $amount = 1) {
        $this->db->where('barcode', $barcode);
        $this->db->set('registered_count', "registered_count + $amount", false);
        $this->db->update($this->table);
    }

    function update_barcode_delivered_count($barcode, $amount = 1) {
        $this->db->where('barcode', $barcode);
        $this->db->set('delivered_count', "delivered_count + $amount", false);
        $this->db->update($this->table);
    }

    function check_for_barcode($barcode) {
        if (count($this->get_barcodes(['barcode' => $barcode])) == 0) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    function increase_quantity_number($id, $increment) {
        $this->db->set("quantity", "quantity + $increment", False);
        $this->db->set("quantity_remaining", "quantity_remaining + $increment", False);
        $this->db->where(array('productid' => $id));
        $this->db->update($this->table);
    }

    function increase_remaining_quantity($id, $increment) {
        $this->db->set("quantity_remaining", "quantity_remaining + $increment", False);
        $this->db->where(array('productid' => $id));
        $this->db->update($this->table);
    }

    function decrease_remaining_quantity($id, $decrement) {
        $this->db->set("quantity_remaining", "quantity_remaining - $decrement", False);
        $this->db->where(array('productid' => $id));
        $this->db->update($this->table);
    }

    function delete_barcode($data) {
        $this->db->where($data);
        $this->db->delete($this->table);
    }

    function delete_barcodes_of($of, $ofField, $whereField) {
        $rows = array();
        foreach ($of as $item) {
            $rows = array_merge($rows, $this->get_barcodes(array($whereField => $item->$ofField)));
        }
        foreach ($of as $item) {
            $this->delete_barcode(array($whereField => $item->$ofField));
        }
        return $rows;
    }
}
?>