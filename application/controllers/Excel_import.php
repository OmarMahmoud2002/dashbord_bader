<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Excel_import extends CI_Controller
{
 public function __construct()
 {
  parent::__construct();
  $this->load->model('Excel_import_model');
  $this->load->model('Model_admin');
  $this->load->library('excel');
  
 }

 

    public function filter() {
        // Retrieve date range from POST request
        $start_date = $this->input->post('start_date');
        $end_date = $this->input->post('end_date');

        // Validate dates
        if (!$start_date || !$end_date) {
            show_error('Invalid date range');
            return;
        }

        // Get employees
        $employees = $this->Model_admin->getemployees();
        
        // Fetch data based on date range
        $data = [];
        foreach ($employees as $row) {
            $excel = $this->Excel_import_model->selectbyid($row['user_id'], $start_date, $end_date);
            $data[] = [
                'user_id' => $row['user_id'],
                'insert_excel_twasel' => (int)($excel['insert_excel_twasel']),
            ];
        }

        // Pass data to the view
        $this->load->view('view_d', ['data' => $data]);
    }
 
function import()
{
    if (isset($_FILES["file"]["name"])) {
        $success = '';
        $valid = 1;
        $error = '';

        // Fetch the import date from POST data
        $import_date = $this->input->post('import_date', true);

        if (empty($import_date)) {
            $valid = 0;
            $error .= "ادخل التاريخ<br>";
        }

        if ($valid == 1) {
            $path = $_FILES["file"]["tmp_name"];
            $object = PHPExcel_IOFactory::load($path);
            $data = [];

            foreach ($object->getWorksheetIterator() as $worksheet) {
                $highestRow = $worksheet->getHighestRow();
                $highestColumn = $worksheet->getHighestColumn();

                // Iterate through rows starting from row 2 (assuming row 1 is headers)
                for ($row = 2; $row <= $highestRow; $row++) {
                    // Get the status value from column 'A'
                    $status = rtrim($worksheet->getCellByColumnAndRow(3, $row)->getValue());

                    // Only process rows where the status is 'Complete'
                    if ($status === 'Complete') {
                        $insert_excel_uid = rtrim($worksheet->getCellByColumnAndRow(17, $row)->getValue());
                        $dateTime = $worksheet->getCellByColumnAndRow(0, $row)->getValue();
                        $excel_date = (($dateTime - 25569) * 86400);
                        $insert_excel_date = date('Y-m-d', $excel_date);
                        $insert_excel_ordern = rtrim($worksheet->getCellByColumnAndRow(1, $row)->getValue());

                        $c_user_name = $this->Model_admin->get_user_by_uid($insert_excel_uid);
                        $user_id = $c_user_name['user_id'];
                        $insert_excel_twasel = $worksheet->getCellByColumnAndRow(13, $row)->getValue();

                        if ($insert_excel_date === $import_date && ($insert_excel_twasel > 0) && $user_id > 0 ) {
                            $data[] = array(
                                'insert_excel_date' => $insert_excel_date,
                                'insert_excel_uid' => $user_id,
                                'insert_excel_twasel' => $insert_excel_twasel,
                                'insert_excel_ordern' => $insert_excel_ordern
                            );
                        }
                    }
                }
            }

            // Insert data if there are valid records
            if (count($data) > 0) {
                $this->Excel_import_model->insert($data);
                $success = "تم الحفظ بنجاح";
                $this->session->set_flashdata('success', $success);
            } else {
                $error = "لاتوجد بيانات مطابقة للتاريخ المدخل";
                $this->session->set_flashdata('error', $error);
            }

            redirect($this->agent->referrer());
        } else {
            $this->session->set_flashdata('error', $error);
            redirect($this->agent->referrer());
        }
    } else {
        $this->session->set_flashdata('error', 'No file uploaded');
        redirect($this->agent->referrer());
    }
}


 public function insert()
{
    $data['error'] = ''; // Initialize error variable

    if ($this->input->post('submit')) {
        $selected_rows = $this->input->post('selected_rows');

        if (!empty($selected_rows)) {
            foreach ($selected_rows as $index) {
                $row = $this->get_row_data($index);

                if ($row) {
                    $data_to_insert = array(
                        'date' => $row['insert_excel_date'],
                        'user_id' => $row['insert_excel_uid'],
                        'twasel' => $row['insert_excel_twasel'],
                        'electronic' => $row['insert_excel_electronic'],
                        'jowy' => $row['insert_excel_jowy'],
                        'quickplus' => $row['insert_excel_quickplus']
                    );

                    if (!$this->db->insert('tbl_insert_excel', $data_to_insert)) {
                        $data['error'] = 'Error inserting data: ' . $this->db->error()['message'];
                        break;
                    }
                } else {
                    $data['error'] = 'Invalid row data for index ' . $index;
                    break;
                }
            }

            if (empty($data['error'])) {
                $data['success'] = 'Selected rows inserted successfully!';
            }
        } else {
            $data['error'] = 'No rows selected.';
        }
    }

    $this->load->view('column_matching', $data);
}

   
public function delete_by_date() {
    $date = $this->input->post('date');
     $valid = 1;
     $error =1;
    if (empty($date)) {
            $valid = 0;
            $error .= "اختر التاريخ <br>";
        }
      if ($valid == 1) {  
    if ($date) {
        $this->Excel_import_model->delete_records_by_date($date);
         $success = "تم الحفظ بنجاح";
         $this->session->set_flashdata('success', $success);
          redirect($this->agent->referrer());
    }
      }else{
           $this->session->set_flashdata('error', $error);
            redirect($this->agent->referrer());
      }
}



}

?>