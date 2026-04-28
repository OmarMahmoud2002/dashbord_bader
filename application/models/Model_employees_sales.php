<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

class Model_employees_sales extends CI_Model {
    public function __construct()
    {
        parent::__construct();
        $this->table = 'tbl_employees_sales';
    }

    public function get_upload_record_by_date($individualsDate) {
        $query = $this->db->get_where($this->table, array('individuals_date' => $individualsDate));
        return $query->row();
    }

    public function insert_upload_record($filepath, $filename, $individualsDate, $businessDate) {
        # check if the filepath is found and then updates data
        $query = $this->db->get_where($this->table, array('filepath' => $filepath));
        if ($query->num_rows() > 0) {
            $this->db->update($this->table, array('filename' => $filename, 'individuals_date' => $individualsDate, 'business_date' => $businessDate,), array('filepath' => $filepath));
        } else {
            $this->db->insert($this->table, array('filepath' => $filepath, 'filename' => $filename, 'individuals_date' => $individualsDate, 'business_date' => $businessDate,));
        }
    }

    public function get_upload_record_by_filepath($filepath) {
        $query = $this->db->get_where($this->table, array('filepath' => $filepath));
        return $query->row();
    }
}