<?php
class Excel_import_model extends CI_Model
{
 private $table = 'tbl_insert_excel';

 function __construct()
 {
    parent::__construct();
    $this->ensure_sales_metadata_columns();
 }

 private function ensure_sales_metadata_columns()
 {
    $columns = array(
        'insert_excel_new_ordern' => array(
            'type' => 'VARCHAR',
            'constraint' => 120,
            'null' => FALSE,
            'default' => '',
            'after' => 'insert_excel_ordern'
        ),
        'insert_excel_description' => array(
            'type' => 'VARCHAR',
            'constraint' => 255,
            'null' => FALSE,
            'default' => '',
            'after' => 'insert_excel_new_ordern'
        ),
        'insert_excel_product_serial_number' => array(
            'type' => 'VARCHAR',
            'constraint' => 255,
            'null' => FALSE,
            'default' => '',
            'after' => 'insert_excel_description'
        ),
        'insert_excel_duplicate_identifier' => array(
            'type' => 'VARCHAR',
            'constraint' => 120,
            'null' => FALSE,
            'default' => '',
            'after' => 'insert_excel_product_serial_number'
        )
    );

    foreach ($columns as $column_name => $definition) {
        if (!$this->db->field_exists($column_name, $this->table)) {
            $this->load->dbforge();
            $this->dbforge->add_column($this->table, array($column_name => $definition));
        }
    }
 }

 function select() {
    $this->db->select('*');
    $this->db->from($this->table);
    $this->db->order_by('insert_excel_id', 'DESC');
    // Add New Feature
    $this->db->where('CAST(insert_excel_twasel AS DECIMAL(12,2)) >', 0);
    // End
    $query = $this->db->get();
    return $query->result_array();
}

function select2() {
    $this->db->select('*');
    $this->db->from($this->table);
    $this->db->order_by('insert_excel_id', 'DESC');
    $this->db->where('insert_excel_twasel =', ''); // تأكد من أن القيمة ليست فارغة
    $query = $this->db->get();
    return $query->result_array();
}

// Add New Feature
public function get_employee_sold_devices($user_id) {
    $this->db->select('insert_excel_id, insert_excel_date, insert_excel_ordern, insert_excel_new_ordern, insert_excel_description, insert_excel_product_serial_number, insert_excel_twasel, insert_excel_jowy, insert_excel_quickplus');
    $this->db->from($this->table);
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
    $this->db->from($this->table);
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
    $this->db->from($this->table);
    $this->db->group_by(['insert_excel_uid', 'insert_excel_date']);
    $this->db->order_by('insert_excel_date', 'DESC');
    $query = $this->db->get();
    return $query->result_array();  
}

 function insert_sales($data)
 {
   $this->db->insert($this->table, $data);
    //return $this->db->insert('tbl_insert_excel', $data);
 }

 function insert($data)
 {
    // Add New Feature
    foreach ($data as $index => $record) {
        $data[$index]['insert_excel_electronic'] = isset($record['insert_excel_electronic']) ? $record['insert_excel_electronic'] : '0';
        $data[$index]['insert_excel_jowy'] = isset($record['insert_excel_jowy']) ? $record['insert_excel_jowy'] : '0';
        $data[$index]['insert_excel_quickplus'] = isset($record['insert_excel_quickplus']) ? $record['insert_excel_quickplus'] : '0';
        $data[$index]['insert_excel_duplicate_identifier'] = isset($record['insert_excel_duplicate_identifier']) ? $record['insert_excel_duplicate_identifier'] : '';
    }

    return $this->insert_erp_sales($data);
    // End

 }

 // Add New Feature
 private function normalize_sales_duplicate_value($value)
 {
     $value = strtolower(trim((string) $value));
     $value = str_replace(array(',', ' '), '', $value);

     if ($value !== '' && preg_match('/^-?\d+(?:\.\d+)?(?:e[+-]?\d+)?$/i', $value)) {
         return number_format((float) $value, 0, '.', '');
     }

     return $value;
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
         'insert_excel_duplicate_identifier' => $this->normalize_sales_duplicate_value(isset($record['insert_excel_duplicate_identifier']) ? $record['insert_excel_duplicate_identifier'] : ''),
         'insert_excel_uid' => $this->normalize_sales_duplicate_value(isset($record['insert_excel_uid']) ? $record['insert_excel_uid'] : ''),
         'insert_excel_twasel' => $this->normalize_sales_duplicate_amount(isset($record['insert_excel_twasel']) ? $record['insert_excel_twasel'] : 0),
         'insert_excel_electronic' => $this->normalize_sales_duplicate_amount(isset($record['insert_excel_electronic']) ? $record['insert_excel_electronic'] : 0),
         'insert_excel_jowy' => $this->normalize_sales_duplicate_amount(isset($record['insert_excel_jowy']) ? $record['insert_excel_jowy'] : 0),
         'insert_excel_quickplus' => $this->normalize_sales_duplicate_amount(isset($record['insert_excel_quickplus']) ? $record['insert_excel_quickplus'] : 0)
     );

     return sha1(json_encode($duplicate_fields));
 }

 private function get_sales_order_values($record)
 {
     $orders = array();
     $order_fields = array('insert_excel_ordern', 'insert_excel_new_ordern');

     foreach ($order_fields as $order_field) {
         $order = $this->normalize_sales_duplicate_value(isset($record[$order_field]) ? $record[$order_field] : '');
         if ($order !== '') {
             $orders[$order] = true;
         }
     }

     return array_keys($orders);
 }

 private function get_sales_identifier_value($record)
 {
     return $this->normalize_sales_duplicate_value(isset($record['insert_excel_duplicate_identifier']) ? $record['insert_excel_duplicate_identifier'] : '');
 }

 private function get_sales_pair_duplicate_keys($record)
 {
     $orders = $this->get_sales_order_values($record);
     $identifier = $this->get_sales_identifier_value($record);

     if (empty($orders) || $identifier === '') {
         return array();
     }

     $keys = array();
     foreach ($orders as $order) {
         $keys[] = $order . '|' . $identifier;
     }

     return $keys;
 }

 private function get_sales_order_user_duplicate_keys($record)
 {
     $orders = $this->get_sales_order_values($record);
     $user_id = $this->normalize_sales_duplicate_value(isset($record['insert_excel_uid']) ? $record['insert_excel_uid'] : '');

     if (empty($orders) || $user_id === '') {
         return array();
     }

     $keys = array();
     foreach ($orders as $order) {
         $keys[] = $order . '|' . $user_id;
     }

     return $keys;
 }

 private function has_any_duplicate_key($keys, $existing_keys)
 {
     foreach ($keys as $key) {
         if (isset($existing_keys[$key])) {
             return true;
         }
     }

     return false;
 }

 private function should_check_twasel_duplicate_pair($record)
 {
     if (isset($record['insert_excel_check_twasel_duplicate'])) {
         return (string) $record['insert_excel_check_twasel_duplicate'] === '1';
     }

     return isset($record['insert_excel_twasel']) && (float) $record['insert_excel_twasel'] > 0;
 }

 public function insert_erp_sales($data)
 {
     if (empty($data)) {
         return 0;
     }

     $sales_columns = array('insert_excel_twasel', 'insert_excel_jowy', 'insert_excel_quickplus');
     $existing_records = $this->db->select('insert_excel_uid, insert_excel_date, insert_excel_ordern, insert_excel_new_ordern, insert_excel_description, insert_excel_product_serial_number, insert_excel_duplicate_identifier, insert_excel_twasel, insert_excel_electronic, insert_excel_jowy, insert_excel_quickplus')
                                  ->from($this->table)
                                  ->get()
                                  ->result_array();

     $existing_keys = array();
     $existing_pair_keys = array();
     $existing_order_user_fallback_keys = array();
     foreach ($existing_records as $existing_record) {
         $existing_keys[$this->get_sales_duplicate_key($existing_record)] = true;

         if ($this->should_check_twasel_duplicate_pair($existing_record)) {
             $pair_duplicate_keys = $this->get_sales_pair_duplicate_keys($existing_record);
             foreach ($pair_duplicate_keys as $pair_duplicate_key) {
                 $existing_pair_keys[$pair_duplicate_key] = true;
             }

             if ($this->get_sales_identifier_value($existing_record) === '') {
                 $order_user_duplicate_keys = $this->get_sales_order_user_duplicate_keys($existing_record);
                 foreach ($order_user_duplicate_keys as $order_user_duplicate_key) {
                     $existing_order_user_fallback_keys[$order_user_duplicate_key] = true;
                 }
             }
         }
     }

     $incoming_keys = array();
     $incoming_pair_keys = array();
     $incoming_order_user_fallback_keys = array();
     $filtered_data = array();
     foreach ($data as $record) {
         $record['insert_excel_duplicate_identifier'] = isset($record['insert_excel_duplicate_identifier']) ? $record['insert_excel_duplicate_identifier'] : '';
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

         if (!isset($record['insert_excel_check_twasel_duplicate'])) {
             $record['insert_excel_check_twasel_duplicate'] = ((isset($record['insert_excel_twasel']) && (float) $record['insert_excel_twasel'] > 0) ? '1' : '0');
         }

         $should_check_pair_duplicate = $this->should_check_twasel_duplicate_pair($record);
         $record_duplicate_key = $this->get_sales_duplicate_key($record);
         $pair_duplicate_keys = $this->get_sales_pair_duplicate_keys($record);
         $order_user_duplicate_keys = $this->get_sales_order_user_duplicate_keys($record);

         if (isset($existing_keys[$record_duplicate_key]) || isset($incoming_keys[$record_duplicate_key])) {
             continue;
         }

         if ($should_check_pair_duplicate && ($this->has_any_duplicate_key($pair_duplicate_keys, $existing_pair_keys) || $this->has_any_duplicate_key($pair_duplicate_keys, $incoming_pair_keys))) {
             continue;
         }

         if ($should_check_pair_duplicate && $this->has_any_duplicate_key($order_user_duplicate_keys, $existing_order_user_fallback_keys)) {
             continue;
         }

         if ($should_check_pair_duplicate && empty($pair_duplicate_keys) && $this->has_any_duplicate_key($order_user_duplicate_keys, $incoming_order_user_fallback_keys)) {
             continue;
         }

         $incoming_keys[$record_duplicate_key] = true;
         if ($should_check_pair_duplicate && !empty($pair_duplicate_keys)) {
             foreach ($pair_duplicate_keys as $pair_duplicate_key) {
                 $incoming_pair_keys[$pair_duplicate_key] = true;
             }
         }
         if ($should_check_pair_duplicate && empty($pair_duplicate_keys)) {
             foreach ($order_user_duplicate_keys as $order_user_duplicate_key) {
                 $incoming_order_user_fallback_keys[$order_user_duplicate_key] = true;
             }
         }
         unset($record['insert_excel_check_twasel_duplicate']);
         $filtered_data[] = $record;
     }

     if (!empty($filtered_data)) {
         $this->db->insert_batch($this->table, $filtered_data);
         return count($filtered_data);
     }

     return 0;
 }
 // End
 
  
    public function delete_records_by_date($date) {
        $this->db->where('insert_excel_date', $date);
        $this->db->delete($this->table);
    }


 
 
}
