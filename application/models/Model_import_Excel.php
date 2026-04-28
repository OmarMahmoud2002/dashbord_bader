<?php
class Excel_import_model extends CI_Model
{
 function select()
 {
  $this->db->select('*');
        $this->db->from('tbl_insert_excel');
        $this->db->order_by('insert_excel_id', 'DESC');
        $query = $this->db->get();
        return $query->result_array();
 }

function selectbyid($id,$date) 
    {
        $this->db->select('*');
        $this->db->from('tbl_insert_excel');
        $this->db->where('insert_excel_uid', $id);
        $this->db->where('insert_excel_date', $date);
        $query = $this->db->get();
        return $query->first_row('array');
    }

 function insert($data)
 {
   $this->db->insert_batch('tbl_insert_excel', $data);
    
 }
 
}
