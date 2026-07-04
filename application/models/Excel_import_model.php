<?php
class Excel_import_model extends CI_Model
{
 function select() {
    $this->db->select('*');
    $this->db->from('tbl_insert_excel');
    $this->db->order_by('insert_excel_id', 'DESC');
    // Add New Feature
    $this->db->where('CAST(insert_excel_twasel AS DECIMAL(12,2)) >', 0);
    // End
    $query = $this->db->get();
    return $query->result_array();
}

function select2() {
    $this->db->select('*');
    $this->db->from('tbl_insert_excel');
    $this->db->order_by('insert_excel_id', 'DESC');
    $this->db->where('insert_excel_twasel =', ''); // تأكد من أن القيمة ليست فارغة
    $query = $this->db->get();
    return $query->result_array();
}

// Add New Feature
public function get_employee_sold_devices($user_id) {
    $this->db->select('insert_excel_id, insert_excel_date, insert_excel_ordern, insert_excel_new_ordern, insert_excel_description, insert_excel_product_serial_number, insert_excel_twasel, insert_excel_jowy, insert_excel_quickplus');
    $this->db->from('tbl_insert_excel');
    $this->db->where('insert_excel_uid', $user_id);
    $this->db->where("(CAST(insert_excel_twasel AS DECIMAL(12,2)) > 0 OR CAST(insert_excel_jowy AS DECIMAL(12,2)) > 0 OR CAST(insert_excel_quickplus AS DECIMAL(12,2)) > 0)", null, false);
    $this->db->where("(insert_excel_ordern != '' OR insert_excel_new_ordern != '' OR insert_excel_description != '' OR insert_excel_product_serial_number != '')", null, false);
    $this->db->order_by('insert_excel_date', 'DESC');
    $this->db->order_by('insert_excel_id', 'DESC');
    $query = $this->db->get();
    $records = $query->result_array();

    foreach ($records as $index => $record) {
        $records[$index]['insert_excel_sales_amount'] = 0;
        $records[$index]['insert_excel_sales_type'] = '';

        if ((float) $record['insert_excel_twasel'] > 0) {
            $records[$index]['insert_excel_sales_amount'] = (float) $record['insert_excel_twasel'];
            $records[$index]['insert_excel_sales_type'] = 'مبيعات تواصل';
        } else if ((float) $record['insert_excel_jowy'] > 0) {
            $records[$index]['insert_excel_sales_amount'] = (float) $record['insert_excel_jowy'];
            $records[$index]['insert_excel_sales_type'] = 'مبيعات جوي';
        } else if ((float) $record['insert_excel_quickplus'] > 0) {
            $records[$index]['insert_excel_sales_amount'] = (float) $record['insert_excel_quickplus'];
            $records[$index]['insert_excel_sales_type'] = 'مبيعات كوبيك بلس';
        }
    }

    return $records;
}
// End


// function selectbyid($id,$date) 
//     {
//         $this->db->select('*');
//         $this->db->from('tbl_insert_excel');
//         $this->db->where('insert_excel_uid', $id);
//         $this->db->where('insert_excel_date', $date);
//         $query = $this->db->get();
//         return $query->first_row('array');
//     }

// In Excel_import_model.php



public function selectbyid($user_id, $start_date, $end_date) {
    $this->db->select('*');
    $this->db->from('tbl_insert_excel');
    $this->db->where('insert_excel_uid', $user_id);
    if ($start_date != '') {
        $this->db->where('insert_excel_date >=', $start_date);
    }
    
    $this->db->where('insert_excel_date <=', $end_date);
    $query = $this->db->get();
    return $query->result_array();  
}

public function selectall() {
    $this->db->select('insert_excel_uid, insert_excel_date, SUM(insert_excel_twasel) as insert_excel_twasel, SUM(insert_excel_electronic) as insert_excel_electronic, SUM(insert_excel_jowy) as insert_excel_jowy, SUM(insert_excel_quickplus) as insert_excel_quickplus');
    $this->db->from('tbl_insert_excel');
    $this->db->group_by(['insert_excel_uid', 'insert_excel_date']);
    $this->db->order_by('insert_excel_date', 'DESC');
    $query = $this->db->get();
    return $query->result_array();  
}

 function insert_sales($data)
 {
   $this->db->insert('tbl_insert_excel', $data);
    //return $this->db->insert('tbl_insert_excel', $data);
 }

 function insert($data)
 {
    // Add New Feature
    foreach ($data as $index => $record) {
        $data[$index]['insert_excel_electronic'] = isset($record['insert_excel_electronic']) ? $record['insert_excel_electronic'] : '0';
        $data[$index]['insert_excel_jowy'] = isset($record['insert_excel_jowy']) ? $record['insert_excel_jowy'] : '0';
        $data[$index]['insert_excel_quickplus'] = isset($record['insert_excel_quickplus']) ? $record['insert_excel_quickplus'] : '0';
    }

    return $this->insert_erp_sales($data);
    // End

 }

 // Add New Feature
 private function normalize_sales_duplicate_value($value)
 {
     return strtolower(trim((string) $value));
 }

 private function normalize_sales_duplicate_amount($value)
 {
     $value = str_replace(array(',', ' '), '', (string) $value);
     return is_numeric($value) ? number_format((float) $value, 2, '.', '') : '0.00';
 }

 private function get_sales_duplicate_key($record)
 {
     $duplicate_fields = array(
         'insert_excel_date' => $this->normalize_sales_duplicate_value(isset($record['insert_excel_date']) ? $record['insert_excel_date'] : ''),
         'insert_excel_ordern' => $this->normalize_sales_duplicate_value(isset($record['insert_excel_ordern']) ? $record['insert_excel_ordern'] : ''),
         'insert_excel_new_ordern' => $this->normalize_sales_duplicate_value(isset($record['insert_excel_new_ordern']) ? $record['insert_excel_new_ordern'] : ''),
         'insert_excel_description' => $this->normalize_sales_duplicate_value(isset($record['insert_excel_description']) ? $record['insert_excel_description'] : ''),
         'insert_excel_product_serial_number' => $this->normalize_sales_duplicate_value(isset($record['insert_excel_product_serial_number']) ? $record['insert_excel_product_serial_number'] : ''),
         'insert_excel_uid' => $this->normalize_sales_duplicate_value(isset($record['insert_excel_uid']) ? $record['insert_excel_uid'] : ''),
         'insert_excel_twasel' => $this->normalize_sales_duplicate_amount(isset($record['insert_excel_twasel']) ? $record['insert_excel_twasel'] : 0),
         'insert_excel_electronic' => $this->normalize_sales_duplicate_amount(isset($record['insert_excel_electronic']) ? $record['insert_excel_electronic'] : 0),
         'insert_excel_jowy' => $this->normalize_sales_duplicate_amount(isset($record['insert_excel_jowy']) ? $record['insert_excel_jowy'] : 0),
         'insert_excel_quickplus' => $this->normalize_sales_duplicate_amount(isset($record['insert_excel_quickplus']) ? $record['insert_excel_quickplus'] : 0)
     );

     return sha1(json_encode($duplicate_fields));
 }

 public function insert_erp_sales($data)
 {
     if (empty($data)) {
         return 0;
     }

     $sales_columns = array('insert_excel_twasel', 'insert_excel_jowy', 'insert_excel_quickplus');
     $lookup_keys = array();
     foreach ($data as $record) {
         $lookup_keys[] = $record['insert_excel_uid'] . '|' . $record['insert_excel_date'];
     }
     $lookup_keys = array_unique($lookup_keys);

     $existing_records = $this->db->select('insert_excel_uid, insert_excel_date, insert_excel_ordern, insert_excel_new_ordern, insert_excel_description, insert_excel_product_serial_number, insert_excel_twasel, insert_excel_electronic, insert_excel_jowy, insert_excel_quickplus')
                                  ->from('tbl_insert_excel')
                                  ->where_in("CONCAT(insert_excel_uid, '|', insert_excel_date)", $lookup_keys)
                                  ->get()
                                  ->result_array();

     $existing_keys = array();
     foreach ($existing_records as $existing_record) {
         $existing_keys[$this->get_sales_duplicate_key($existing_record)] = true;
     }

     $incoming_keys = array();
     $filtered_data = array();
     foreach ($data as $record) {
         $record_sales_column = '';
         foreach ($sales_columns as $sales_column) {
             if (isset($record[$sales_column]) && (float) $record[$sales_column] > 0) {
                 $record_sales_column = $sales_column;
                 break;
             }
         }

         if ($record_sales_column === '') {
             continue;
         }

         $record_duplicate_key = $this->get_sales_duplicate_key($record);
         if (isset($existing_keys[$record_duplicate_key]) || isset($incoming_keys[$record_duplicate_key])) {
             continue;
         }

         $incoming_keys[$record_duplicate_key] = true;
         $filtered_data[] = $record;
     }

     if (!empty($filtered_data)) {
         $this->db->insert_batch('tbl_insert_excel', $filtered_data);
         return count($filtered_data);
     }

     return 0;
 }
 // End
 
  
    public function delete_records_by_date($date) {
        $this->db->where('insert_excel_date', $date);
        $this->db->delete('tbl_insert_excel');
    }


 
 
}
