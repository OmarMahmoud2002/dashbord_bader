<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
Required table structure:

CREATE TABLE users_schedules (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    schedule_date DATE,
    schedule_type ENUM('add', 'leave', 'store', 'event', 'off'),
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES tbl_user(id),
    UNIQUE KEY unique_user_date (user_id, schedule_date)
);
*/

class Model_employee_schedule extends CI_Model {
    
    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    // Get all users (employees and admins)
    public function get_employees($include_admins = false) {
        $this->db->select('user_id, user_fillname');
        $this->db->from('tbl_user');
        if (!$include_admins) {
            $this->db->where('user_type', 'user');
        }
        $this->db->order_by('user_fillname', 'ASC');
        $query = $this->db->get();
        return $query->result_array();
    }

    function get_supervisors() {
        $this->db->select('user_id, user_fillname');
        $this->db->from('tbl_user');
        $this->db->where('user_type', 'admin');
        $this->db->order_by('user_fillname', 'ASC');
        $query = $this->db->get();
        return $query->result_array();
    }

    // Get schedule for specific week
    public function get_schedule($admin = false) {
        $schedule = array();
        

        
        
        $supervisors = $this->get_supervisors();
        $supervisor_ids = array_column($supervisors, 'user_id');

        $this->db->select('user_id, schedule_date, schedule_type, description');
        $this->db->from('users_schedules');
        
        $query = $this->db->get();
        foreach ($query->result_array() as $row) {
            $user_id = $row['user_id'];
            $date = $row['schedule_date'];
            
            if ($admin) {
                if (in_array($user_id, $supervisor_ids)) {
                    $schedule[$user_id . '_' . $date] = array(
                        'type' => $row['schedule_type'],
                        'status' => $row['description']
                    );
                }
            } else {
                if (!in_array($user_id, $supervisor_ids)) {
                    $schedule[$user_id . '_' . $date] = array(
                        'type' => $row['schedule_type'],
                        'status' => $row['description']
                    );
                }
            }

            
        }
        
        return $schedule;
    }

    // Get schedule for a specific user in a week
    private function get_user_week_schedule($user_id, $start_date, $end_date) {
        $days = array('Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday');
        $schedule = array();
        
        // Initialize schedule with empty cells
        foreach ($days as $day) {
            $schedule[$day] = array(
                'type' => '',
                'time' => '',
                'description' => ''
            );
        }
        
        // Get scheduled days from database
        $this->db->select('schedule_date, schedule_type, description');
        $this->db->from('users_schedules');
        $this->db->where('user_id', $user_id);
        $this->db->where('schedule_date >=', $start_date);
        $this->db->where('schedule_date <=', $end_date);
        $query = $this->db->get();
        
        // Fill in the scheduled days
        foreach ($query->result_array() as $row) {
            $day_of_week = date('l', strtotime($row['schedule_date']));
            $schedule[$day_of_week] = array(
                'type' => $row['schedule_type'],
                'description' => $row['description']
            );
        }
        
        return $schedule;
    }

    // Add or update schedule entry
    public function update_schedule($data) {
        $schedule_data = array(
            'user_id' => $data['user_id'],
            'schedule_date' => $data['date'],
            'schedule_type' => $data['type'],
            'description' => isset($data['description']) ? $data['description'] : '',
            'updated_at' => date('Y-m-d H:i:s')
        );

        // Check if entry exists
        $this->db->where('user_id', $data['user_id']);
        $this->db->where('schedule_date', $data['date']);
        $exists = $this->db->get('users_schedules')->num_rows() > 0;

        if ($exists) {
            // Update existing entry
            $this->db->where('user_id', $data['user_id']);
            $this->db->where('schedule_date', $data['date']);
            return $this->db->update('users_schedules', $schedule_data);
        } else {
            // Insert new entry
            $schedule_data['created_at'] = date('Y-m-d H:i:s');
            return $this->db->insert('users_schedules', $schedule_data);
        }
    }

    // Delete schedule entry
    public function delete_schedule($user_id, $date) {
        return $this->db->delete('users_schedules', array(
            'user_id' => $user_id,
            'schedule_date' => $date
        ));
    }

    // Get schedule statistics for an employee
    public function get_user_stats($user_id, $year, $month) {
        $stats = array(
            'working_days' => 0,
            'leave_days' => 0,
            'store_days' => 0,
            'events' => 0
        );

        $this->db->select('schedule_type, COUNT(*) as count');
        $this->db->from('employee_schedules');
        $this->db->where('user_id', $user_id);
        $this->db->where('YEAR(schedule_date)', $year);
        $this->db->where('MONTH(schedule_date)', $month);
        $this->db->group_by('schedule_type');
        $query = $this->db->get();
        
        foreach ($query->result() as $row) {
            switch ($row->schedule_type) {
                case 'add':
                    $stats['working_days'] = $row->count;
                    break;
                case 'leave':
                    $stats['leave_days'] = $row->count;
                    break;
                case 'store':
                    $stats['store_days'] = $row->count;
                    break;
                case 'event':
                    $stats['events'] = $row->count;
                    break;
            }
        }
        
        return $stats;
    }
}