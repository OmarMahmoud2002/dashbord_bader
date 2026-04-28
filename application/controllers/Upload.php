<?php
// application/controllers/Upload.php
class Upload extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->helper(array('form', 'url'));
        $this->load->library('upload');
        $this->load->library('excel'); // PHPExcel or PhpSpreadsheet
    }

   

    public function do_upload() {
        $config['upload_path']   = './uploads/';
        $config['allowed_types'] = 'xls|xlsx';
        $config['max_size']      = 2048; // 2MB max size

        $this->upload->initialize($config);

        if (!$this->upload->do_upload('userfile')) {
            $error = array('error' => $this->upload->display_errors());
            $this->load->view('upload_form', $error);
        } else {
            $data = $this->upload->data();
            $file_path = $data['full_path'];

            // Load Excel data
            $this->load->library('excel');
            $objPHPExcel = PHPExcel_IOFactory::load($file_path);
            $sheet = $objPHPExcel->getActiveSheet();
            $excel_data = $sheet->toArray(null, true, true, true);

            // Get database fields
           // $db_fields = $this->fields_model->get_db_fields();

            // Show form for field mapping
            $this->load->view('map_fields', array('excel_data' => $excel_data, 'db_fields' => $db_fields, 'file_path' => $file_path));
        }
    }
    
   
    // Other methods ...

    public function process_data() {
        $file_path = $this->input->post('file_path');
        $field_map = $this->input->post('field_map');

        // Load Excel data
        $this->load->library('excel');
        $objPHPExcel = PHPExcel_IOFactory::load($file_path);
        $sheet = $objPHPExcel->getActiveSheet();
        $data = $sheet->toArray(null, true, true, true);

        // Process data based on field mapping
        $processed_data = [];
        foreach ($data as $index => $row) {
            if ($index == 1) continue; // Skip header row

            $filtered_row = [];
            foreach ($field_map as $header => $db_field) {
                if (!empty($db_field)) {
                    $filtered_row[$db_field] = isset($row[$header]) ? $row[$header] : null;
                }
            }
            $processed_data[] = $filtered_row;
        }

        // Example: Save processed data or perform other actions
        // $this->db->insert_batch('your_table', $processed_data);

        $this->load->view('process_success', array('data' => $processed_data));
    }


}
