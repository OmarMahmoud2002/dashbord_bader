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

 // Add New Feature
 private function getTrimmedCellValue($worksheet, $columnIndex, $row)
 {
     $cell = $worksheet->getCellByColumnAndRow($columnIndex, $row);
     $value = $cell->isFormula() ? $cell->getOldCalculatedValue() : $cell->getValue();

     if ($value === null) {
         $value = $cell->getValue();
     }

     return trim((string) $value);
 }
 // End

 // Add New Feature
 private function normalizeIdentifierValue($value)
 {
     $value = trim((string) $value);
     $value = str_replace(array(',', ' '), '', $value);

     if ($value !== '' && preg_match('/^-?\d+(?:\.\d+)?(?:E[+-]?\d+)?$/i', $value)) {
         return number_format((float) $value, 0, '.', '');
     }

     return $value;
 }

 private function getIdentifierCellValue($worksheet, $columnIndex, $row)
 {
     return $this->normalizeIdentifierValue($this->getTrimmedCellValue($worksheet, $columnIndex, $row));
 }
 // End

 // Add New Feature
 private function getExcelDateString($worksheet, $columnIndex, $row)
 {
     $cell = $worksheet->getCellByColumnAndRow($columnIndex, $row);
     $value = $cell->isFormula() ? $cell->getOldCalculatedValue() : $cell->getValue();

     if ($value === null) {
         $value = $cell->getValue();
     }

     if (is_numeric($value)) {
         return gmdate('Y-m-d', PHPExcel_Shared_Date::ExcelToPHP($value));
     }

     $formattedValue = trim((string) $value);
     if ($formattedValue === '') {
         return '';
     }

     // Add New Feature
     $dateFormats = array('Y-m-d', 'd/m/Y', 'm/d/Y', 'd-m-Y', 'm-d-Y', 'Y/m/d', 'd-M-y', 'd-M-Y');
     // End
     foreach ($dateFormats as $dateFormat) {
         $date = DateTime::createFromFormat($dateFormat, $formattedValue);
         if ($date instanceof DateTime) {
             return $date->format('Y-m-d');
         }
     }

     $timestamp = strtotime(str_replace('/', '-', $formattedValue));
     return $timestamp ? date('Y-m-d', $timestamp) : '';
 }

 private function getNumericCellValue($worksheet, $columnIndex, $row)
 {
     $cell = $worksheet->getCellByColumnAndRow($columnIndex, $row);
     $value = $cell->isFormula() ? $cell->getOldCalculatedValue() : $cell->getValue();

     if ($value === null) {
         $value = $cell->getValue();
     }

     $value = str_replace(array(',', 'SAR', 'ريال', ' '), '', (string) $value);
     return is_numeric($value) ? (float) $value : 0;
 }

 private function getErpSalesColumn($salesType)
 {
     // Add New Feature
     $normalizedSalesType = strtolower(trim((string) $salesType));
     $salesTypesMap = array(
         'retail jawwy sales' => 'insert_excel_jowy',
         'qwikplus pos' => 'insert_excel_quickplus',
         'stc siebel retail stores' => 'insert_excel_twasel'
     );

     foreach ($salesTypesMap as $salesTypeName => $salesColumn) {
         if ($normalizedSalesType === $salesTypeName || strpos($normalizedSalesType, $salesTypeName) !== false) {
             return $salesColumn;
         }
     }

     return '';
     // End
 }
 // End

 

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

        if ($valid == 1) {
            $path = $_FILES["file"]["tmp_name"];
            // Add New Feature
            if (!class_exists('ZipArchive') && class_exists('PHPExcel_Settings')) {
                PHPExcel_Settings::setZipClass(PHPExcel_Settings::PCLZIP);
            }
            // End
            $object = PHPExcel_IOFactory::load($path);
            $data = [];

            foreach ($object->getWorksheetIterator() as $worksheet) {
                $highestRow = $worksheet->getHighestRow();
                $highestColumn = $worksheet->getHighestColumn();

                // Iterate through rows starting from row 2 (assuming row 1 is headers)
                for ($row = 2; $row <= $highestRow; $row++) {
                    // Get the status value from column 'A'
                    $status = $this->getTrimmedCellValue($worksheet, 3, $row);

                    // Only process rows where the status is 'Complete'
                    if ($status === 'Complete') {
                        $insert_excel_uid = $this->getTrimmedCellValue($worksheet, 17, $row);
                        $insert_excel_date = $this->getExcelDateString($worksheet, 0, $row);
                        $insert_excel_ordern = $this->getTrimmedCellValue($worksheet, 1, $row);

                        // Add New Feature
                        $insert_excel_new_ordern = $this->getTrimmedCellValue($worksheet, 5, $row);
                        $insert_excel_description = $this->getTrimmedCellValue($worksheet, 8, $row);
                        $insert_excel_product_serial_number = $this->getTrimmedCellValue($worksheet, 10, $row);
                        // End

                        $c_user_name = $this->Model_admin->get_user_by_uid($insert_excel_uid);
                        $user_id = isset($c_user_name['user_id']) ? (int) $c_user_name['user_id'] : 0;
                        $insert_excel_twasel = $worksheet->getCellByColumnAndRow(13, $row)->getValue();

                        if ($insert_excel_date !== '' && ($insert_excel_twasel > 0) && $user_id > 0 ) {
                            $data[] = array(
                                'insert_excel_date' => $insert_excel_date,
                                'insert_excel_uid' => $user_id,
                                'insert_excel_twasel' => $insert_excel_twasel,
                                'insert_excel_ordern' => $insert_excel_ordern,
                                // Add New Feature
                                'insert_excel_new_ordern' => $insert_excel_new_ordern,
                                'insert_excel_description' => $insert_excel_description,
                                'insert_excel_product_serial_number' => $insert_excel_product_serial_number
                                // End
                            );
                        }
                    }
                }
            }

            // Insert data if there are valid records
            if (count($data) > 0) {
                // Add New Feature
                $inserted_count = $this->Excel_import_model->insert($data);
                if ($inserted_count > 0) {
                    $success = "تم الحفظ بنجاح";
                    $this->session->set_flashdata('success', $success);
                } else {
                    $error = "لا توجد بيانات جديدة، قد تكون البيانات مكررة";
                    $this->session->set_flashdata('error', $error);
                }
                // End
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

// Add New Feature
function import_erp()
{
    if (isset($_FILES["file"]["name"])) {
        $valid = 1;
        $error = '';
        if ($valid == 1) {
            $path = $_FILES["file"]["tmp_name"];
            if (!class_exists('ZipArchive') && class_exists('PHPExcel_Settings')) {
                PHPExcel_Settings::setZipClass(PHPExcel_Settings::PCLZIP);
            }

            $object = PHPExcel_IOFactory::load($path);
            $data = array();

            foreach ($object->getWorksheetIterator() as $worksheet) {
                $highestRow = $worksheet->getHighestRow();

                for ($row = 2; $row <= $highestRow; $row++) {
                    $insert_excel_date = $this->getExcelDateString($worksheet, 0, $row);
                    if ($insert_excel_date === '') {
                        continue;
                    }

                    $sales_type = $this->getTrimmedCellValue($worksheet, 10, $row);
                    $sales_column = $this->getErpSalesColumn($sales_type);
                    if ($sales_column === '') {
                        continue;
                    }

                    // Add New Feature
                    $insert_excel_uid = $this->getIdentifierCellValue($worksheet, 3, $row);
                    $c_user_name = $this->Model_admin->get_user_by_uid($insert_excel_uid);
                    if (!$c_user_name) {
                        $insert_excel_uid = $this->getIdentifierCellValue($worksheet, 20, $row);
                        $c_user_name = $this->Model_admin->get_user_by_uid($insert_excel_uid);
                    }
                    // End

                    $user_id = isset($c_user_name['user_id']) ? (int) $c_user_name['user_id'] : 0;
                    if ($user_id <= 0) {
                        continue;
                    }

                    $sales_amount = $this->getNumericCellValue($worksheet, 32, $row);
                    if ($sales_amount <= 0) {
                        continue;
                    }

                    // Add New Feature
                    $insert_excel_ordern = $this->getIdentifierCellValue($worksheet, 16, $row);
                    // End
                    $insert_excel_description = $this->getTrimmedCellValue($worksheet, 13, $row);
                    // Add New Feature
                    $insert_excel_product_serial_number = $this->getIdentifierCellValue($worksheet, 18, $row);
                    // End

                    $record = array(
                        'insert_excel_date' => $insert_excel_date,
                        'insert_excel_uid' => $user_id,
                        'insert_excel_ordern' => $insert_excel_ordern,
                        'insert_excel_new_ordern' => $insert_excel_ordern,
                        'insert_excel_description' => $insert_excel_description,
                        'insert_excel_product_serial_number' => $insert_excel_product_serial_number,
                        'insert_excel_twasel' => '0',
                        'insert_excel_electronic' => '0',
                        'insert_excel_jowy' => '0',
                        'insert_excel_quickplus' => '0'
                    );
                    $record[$sales_column] = $sales_amount;
                    $data[] = $record;
                }
            }

            if (count($data) > 0) {
                $inserted_count = $this->Excel_import_model->insert_erp_sales($data);
                if ($inserted_count > 0) {
                    $this->session->set_flashdata('success', 'تم حفظ بيانات ERP بنجاح');
                } else {
                    $this->session->set_flashdata('error', 'لا توجد بيانات ERP جديدة، قد تكون البيانات مكررة');
                }
            } else {
                // Add New Feature
                $this->session->set_flashdata('error', 'لاتوجد بيانات ERP صالحة للحفظ في الملف');
                // End
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
// End


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
