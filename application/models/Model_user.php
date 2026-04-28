<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Model_user extends CI_Model
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

    function check_url($email, $token) 
    {
        $this->db->select('*');
        $this->db->from('tbl_user');
        $this->db->where('user_email', $email);
        $this->db->where('user_token', $token);
        $query = $this->db->get();
        return $query->first_row('array');
    }

    public function check_duplicate_email($user_email)
    {
        $this->db->select('*');
        $this->db->from('tbl_user');
        $this->db->where('user_email', $user_email);
        $query = $this->db->get();
        return $query->num_rows();
    }

    public function registration($data) {
        $this->db->insert('tbl_user',$data);
        return $this->db->insert_id();
    }
    
    public function sendlock($data) {
        $this->db->insert('tbl_user_lock',$data);
        return $this->db->insert_id();
    }

    public function registration_confirm_check_url($email,$token)
    {
        $this->db->select('*');
        $this->db->from('tbl_user');
        $this->db->where('user_email', $email);
        $this->db->where('user_token', $token);
        $query = $this->db->get();
        return $query->num_rows();
    }

    public function registration_confirm_update($email,$token,$data) {
        $this->db->where('user_email',$email);
        $this->db->where('user_token',$token);
        $this->db->update('tbl_user',$data);
    }

    public function user_profile_edit($data,$id) {
        $this->db->where('user_id',$id);
        $this->db->update('tbl_user',$data);
    }

   

public function check_duplicate_day($uid,$date)
    {
        $this->db->select('*');
        $this->db->from('tbl_user_lock');
        $this->db->where('user_lock_userid', $uid);
        $this->db->where('user_lock_time', $date);
        $query = $this->db->get();
        return $query->first_row('array');
    }
    
    
    

}