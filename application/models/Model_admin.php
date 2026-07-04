<?php
defined('BASEPATH') OR exit('No direct script access allowed');
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;

class Model_admin extends CI_Model
{
    function check_access($username, $password) 
    {
        $this->db->select('*');
        $this->db->from('tbl_user');
        $this->db->where('user_name', $username);
        $this->db->where('user_password', md5($password));
        $query = $this->db->get();
        return $query->first_row('array');
    }

   
    public function check_duplicate_username($user_name)
    {
        $this->db->select('*');
        $this->db->from('tbl_user');
        $this->db->where('user_name', $user_name);
        $query = $this->db->get();
        return $query->num_rows();
    }
    
    function username_duplication_check_edit($user_name,$user_id)
    {
        $sql = 'SELECT * FROM tbl_user WHERE user_name=? AND user_id!=?';
        $query = $this->db->query($sql,array($user_name,$user_id));
        return $query->num_rows();
    }
  

    public function registration($data) {
        $this->db->insert('tbl_user',$data);
        return $this->db->insert_id();
    }
    
    public function getemployees()
    {
        $this->db->select('*');
        $this->db->from('tbl_user');
        $this->db->where('user_type', 'user');
        $this->db->where('is_deleted', 0);
        $this->db->order_by('user_id', 'asc');
        $query = $this->db->get();
        return $query->result_array();
    }

    public function getsupervisors()
    {
        $this->db->select('*');
        $this->db->from('tbl_user');
        $this->db->where('user_type', 'admin');
        $this->db->where('is_deleted', 0);
        $this->db->order_by('user_id', 'asc');
        $query = $this->db->get();
        return $query->result_array();
    }
    
   
    public function getemployeeshassales($date_start=null,$date_end=null)
    {
        $this->db->select('*');
        $this->db->from('tbl_user');
        $this->db->where('user_type', 'user');
        $this->db->where('is_deleted', 0);
        $this->db->join('tbl_insert_excel', 'tbl_user.user_id = tbl_insert_excel.insert_excel_uid');
        $this->db->where('tbl_insert_excel.insert_excel_twasel IS NOT NULL');
        //$this->db->where('tbl_insert_excel.insert_excel_twasel >', 0.00);
        $this->db->where('CAST(tbl_insert_excel.insert_excel_twasel AS FLOAT) >', 0); // Convert VARCHAR column 'status' to FLOAT and compare
        if ($date_start && $date_end){
        $this->db->where('tbl_insert_excel.insert_excel_date >=', $date_start);
        $this->db->where('tbl_insert_excel.insert_excel_date <=', $date_end);
        }
       // $this->db->order_by('tbl_insert_excel.insert_excel_id', 'desc');
        $query = $this->db->get();
    //    echo $this->db->last_query();
        $result = $query->result_array();
        return $result;
        
    }  
    
    public function getadmins()
    {
        $this->db->select('*');
        $this->db->from('tbl_user');
        $this->db->where('user_type', 'admin');
        $this->db->order_by('user_id', 'asc');
        $query = $this->db->get();
        return $query->result_array();
    }
    
        public function getlock()
    {
        $this->db->select('*');
        $this->db->from('tbl_user_lock');
        $this->db->order_by('user_lock_id ', 'asc');
        $query = $this->db->get();
        return $query->result_array();
    }
    
   
    
    function getlock_by_userid($id, $start_date, $end_date) {
        $this->db->select('*');
        $this->db->from('tbl_user_lock');
        $this->db->where('user_lock_userid', $id);
        if ($start_date != '') {
            $this->db->where('user_lock_time >=', $start_date);
        }
        
        $this->db->where('user_lock_time <=', $end_date);
        $query = $this->db->get();
        return $query->row_array();
    }
    
    
    function get_user_by_id($id)
    {
        $sql = "SELECT * FROM tbl_user WHERE user_id=?";
        $query = $this->db->query($sql,[$id]);
        return $query->first_row('array');
    }
    
    function get_user_by_uid($id)
    {
        // Add New Feature
        $id = trim((string) $id);
        $this->db->select('*');
        $this->db->from('tbl_user');
        $this->db->where('user_type', 'user');
        $this->db->group_start();
        $this->db->where('user_employee_Id', $id);
        if ($this->db->field_exists('job_number', 'tbl_user')) {
            $this->db->or_where('job_number', $id);
        }
        $this->db->group_end();
        $query = $this->db->get();
        // End
        return $query->first_row('array');
    }
    
    
    public function get_user_custody($user, $type)
    {
        $this->db->select('*');
        $this->db->order_by('user_custody_id', 'asc');
        $this->db->from('tbl_user_custody');
        $this->db->where('custody_user', $user);
        $this->db->where('custody_type', $type);
        
        $query = $this->db->get();
        return $query->result_array();
    }
    
    public function get_settlement($id, $start_date, $end_date) {
        $this->db->select('*');
        $this->db->from('tbl_settlement');
        $this->db->where('settlement_user', $id);
        if ($start_date != '') {
            $this->db->where('settlement_date >=', $start_date);
        }
        
        $this->db->where('settlement_date <=', $end_date);
        $query = $this->db->get();
        return $query->result_array();
    }
    
    function edit_employee($id, $data)
    {
        $this->db->where('user_id',$id);
        $this->db->update('tbl_user',$data);
    }
    
    function delete_employee($id)
    {
        $this->db->where('user_id',$id);
        $this->db->delete('tbl_user');
    }

    function edit_lock($id, $data)
    {
        $this->db->where('user_lock_id',$id);
        $this->db->update('tbl_user_lock',$data);
    }
    
    function delete_lock($id)
    {
        $this->db->where('user_lock_id',$id);
        $this->db->delete('tbl_user_lock');
    }
    
    
    public function insert_custody($data) {
        return $this->db->insert('tbl_user_custody', $data);
    }
    
    public function insert_settlement($data) {
        return $this->db->insert('tbl_settlement', $data);
    }
    
    public function insert($data) {
        return $this->db->insert('tbl_insert_excel', $data);
    }
    
    function delete_excellock($id)
    {
        $this->db->where('insert_excel_id ',$id);
        $this->db->delete('tbl_insert_excel');
    }
    
    function edit_excellock($id, $data)
    {
        $this->db->where('insert_excel_id',$id);
        $this->db->update('tbl_insert_excel',$data);
    }
    
    public function get_total_twasel($data) {
        $this->db->select_sum('insert_excel_twasel');
        $this->db->where('insert_excel_date', $data);
        $query = $this->db->get('tbl_insert_excel');
        $result = $query->row();
        return $result->insert_excel_twasel;
    }
    public function get_total_electronic($data) {
        $this->db->select_sum('insert_excel_electronic');
        $this->db->where('insert_excel_date', $data);
        $query = $this->db->get('tbl_insert_excel');
        $result = $query->row();
        return $result->insert_excel_electronic;
    }
    public function get_total_jowy($data) {
        $this->db->select_sum('insert_excel_jowy');
        $this->db->where('insert_excel_date', $data);
        $query = $this->db->get('tbl_insert_excel');
        $result = $query->row();
        return $result->insert_excel_jowy;
    }
    
    public function get_total_quickplus($data) {
        $this->db->select_sum('insert_excel_quickplus');
        $this->db->where('insert_excel_date', $data);
        $query = $this->db->get('tbl_insert_excel');
        $result = $query->row();
        return $result->insert_excel_quickplus;
    }
    
}
