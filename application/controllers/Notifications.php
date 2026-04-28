<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Notifications extends MY_Controller {

	function __construct()
	{
        parent::__construct();
        $this->load->model('Model_notifications', 'Notifications');
    }

    
    function delete_notification() {
        $id = $this->input->post('noti_id');
        $this->Notifications->delete_notification(array('id' => $id, 'user_id' => $this->session->userdata('user_id')));
        echo 'deleted';
    }
}