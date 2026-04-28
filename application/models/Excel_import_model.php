<?php
class Excel_import_model extends CI_Model
{
 function select() {
    $this->db->select('*');
    $this->db->from('tbl_insert_excel');
    $this->db->order_by('insert_excel_id', 'DESC');
    $this->db->where('insert_excel_twasel !=', ''); // تأكد من أن القيمة ليست فارغة
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
   //$this->db->insert_batch('tbl_insert_excel', $data);
            //return $this->db->insert('tbl_insert_excel', $data);
            
             // Retrieve existing records from the database
    $existing_records = $this->db->select('insert_excel_uid, insert_excel_date')
                                  ->from('tbl_insert_excel')
                                  ->where_in('CONCAT(insert_excel_uid, insert_excel_date)', array_map(function($record) {
                                      return $record['insert_excel_uid'] . $record['insert_excel_date'];
                                  }, $data))
                                  ->get()
                                  ->result_array();

    // Convert existing records to a searchable format
    $existing_records_set = array_map(function($record) {
        return $record['insert_excel_uid'] . $record['insert_excel_date'];
    }, $existing_records);

    // Filter the input data
    $filtered_data = array_filter($data, function($record) use ($existing_records_set) {
        return !in_array($record['insert_excel_uid'] . $record['insert_excel_date'], $existing_records_set);
    });

    // Insert only the filtered data
    if (!empty($filtered_data)) {
        $this->db->insert_batch('tbl_insert_excel', $filtered_data);
    }

 }
 
  
    public function delete_records_by_date($date) {
        $this->db->where('insert_excel_date', $date);
        $this->db->delete('tbl_insert_excel');
    }


 
 
}
