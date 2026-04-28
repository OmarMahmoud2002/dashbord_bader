<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User extends MY_Controller {

	function __construct()
	{
        parent::__construct();
        $this->load->model('Model_user');
        $this->load->model('Model_admin');
        $this->load->model('Excel_import_model');
        $this->load->model('Model_store_items', 'Store');
        $this->load->model('Model_users', 'Users');
        $this->load->model('Model_requests', 'Requests');
        $this->load->model('Model_notifications', 'Notifications');
		$this->load->model('Model_custody', 'Custody');
        $this->notifications();
    }

    private function notifications() {
        $notifications = $this->Notifications->get_notifications(array('user_id' => $this->session->userdata('user_id')));
        $this->data['notifications'] = $notifications;
        $this->data['notifications_count'] = $this->Notifications->get_notifications_length(
			array(
				'status' => 'unread', 
				'user_id' => $this->session->userdata('user_id'),
			)
		);
    }

    function delete_notification() {
        $id = $this->input->post('noti_id');
        $this->Notifications->delete_notification(array('id' => $id, 'user_id' => $this->session->userdata('user_id')));
        echo 'deleted';
    }

    private function get_employees() {
        $data = $this->Model_admin->getemployees();
        $ids = [];
        foreach ($data as $employee) {
            array_push($ids, strval($employee['user_id']));
        }
        return $ids;
    }

	private function get_admins() {
		$ids = array();
		foreach ($this->Model_admin->getadmins() as $admin) {
			array_push($ids, strval($admin['user_id']));
		}
		
		return $ids;
	}

	public function index()
	{
        if(!in_array($this->session->userdata('user_id'), $this->get_employees())) {
		    redirect(base_url());
		}
		redirect(base_url() . 'user/products');
	}
	
	function login()
	{
		$error = '';
		$success = '';

		

		if(isset($_POST['form_login'])) 
		{
			$user_name = $this->input->post('user_name',true);
            $user_password = $this->input->post('user_password',true);

            $chk = $this->Model_user->check_access($user_name,$user_password);
            
            if(!$chk) 
            {
				$error = "خطأ اسم المستخدم او كلمة المرور";
                $this->session->set_flashdata('error',$error);
                redirect($this->agent->referrer());
            }
            else 
            {

	           	$user_data = array(
					'user_id'        => $chk['user_id'],
					'user_name'      => $chk['user_name'],
					'user_email'     => $chk['user_email'],
					'user_password'  => $chk['user_password']
                );
                $this->session->set_userdata($user_data);
                
                if ($chk['user_type'] == 'user'){
            	        redirect(base_url().MOD_VALUE.'user/products');
            	    }else{
            	        redirect(base_url().MOD_VALUE.'admin/dashboard');
            	    }
            }

		}
		else
		{
			redirect($this->agent->referrer());
		}
	}

	function products() {
		if(!in_array($this->session->userdata('user_id'), $this->get_employees())) {
		    redirect(base_url());
		}
		
		$fromdate = $this->input->get('from_date');
		$todate = $this->input->get('to_date');

		if (empty($fromdate)) {
			$fromdate = date('Y-m-d H:i:s');
		}

		if (empty($todate)) {
			$todate = date('Y-m-d 23:59:59');
		} else {
			$todate = date('Y-m-d 23:59:59', strtotime($todate));
		}


		$user_custodies = $this->Custody->get_custodys(['user_id' => $this->session->userdata('user_id'), 'date_created >=' => $fromdate, 'date_created <=' => $todate]);
		$this->data['user_products'] = $user_custodies;
		$this->data['fromdate'] = $fromdate;
		$this->data['todate'] = $todate;

		$this->load->view('view_user_products', $this->data);

	}

    function request_product_return () {
        if(!in_array($this->session->userdata('user_id'), $this->get_employees())) {
		    redirect(base_url());
		}

		if ($_SERVER['REQUEST_METHOD'] == 'POST') {

			$custody_id = $this->input->post('id');
			$custody = $this->Custody->get_custodys(['id' => $custody_id])[0];

			$serial_number = $custody['serial_number'];
			$serial_control = 'yes';
			if ($serial_number == null) {
				$barcode = $custody['barcode'];
				$serial_control = 'no';
			}

			$employee = $this->Users->get_one_user_by_user_id($this->session->userdata('user_id'));

			// Notify all admins

			$admins = $this->get_admins();
			foreach ($admins as $admin) {
				$this->Notifications->add_notification([
					'user_id' => $admin,
					'description' => 'قام ' . $employee->user_fillname . ' بطلب إرجاع منتج',
					'link' => base_url() . MOD_VALUE . 'admin/products/search?serial_control=' . $serial_control . '&serial=' . ($serial_control == 'no' ? $barcode : $serial_number),
					'status' => 'unread',
					'date_created' => date('Y-m-d H:i:s'),
				]);
			}
		}
    }

  function lock()
	{
		if(!in_array($this->session->userdata('user_id'), $this->get_employees())) {
		    redirect(base_url());
		}
		
		$this->load->view('view_user_lock', $this->data);
	}


public function sendlock()
	{
		$error = '';
		$success = '';

		if(isset($_POST['form_lock'])) 
		{

			$valid = 1;

			$user_lock_cash        = $this->input->post('user_lock_cash', true);
			$user_lock_span        = $this->input->post('user_lock_span', true);
			$user_lock_time        = $this->input->post('user_lock_time', true);
			
			$user_lock_electronic  = $this->input->post('user_lock_electronic', true);
			$user_lock_jowy        = $this->input->post('user_lock_jowy', true);
			$user_lock_quick_plus  = $this->input->post('user_lock_quick_plus', true);
			


			$userid               = $this->input->post('user_id', true);
	        
	        if(isset($user_lock_time)){
	            $send_date = $user_lock_time;
	            
	            if(empty($user_lock_time)) {
					$valid = 0;
					$error .= "ادخل التاريخ".'<br>';
				}
	        }else{
	            $send_date = date('Y-m-d');
	        }
	        
	        if(isset($userid)){
	            $user_id   =	$userid;
	            if (empty($user_id)) {
					$valid = 0;
					$error .= "اختر الموظف ".'<br>';
				}
	        }else{
	            $user_id  =	$this->session->userdata('user_id');
	        }
	        
	        
			if($user_lock_cash < 0 ) {
            	$valid = 0;
			    $error .= "ادخل المبلغ كاش (نقدي)	".'<br>';
            }

            if($user_lock_span < 0 ) {
            	$valid = 0;
			    $error .= "ادخل المبلغ بطاقة (سبان)".'<br>';
            }
            
            if($user_lock_cash ==0 and $user_lock_span == 0 ) {
            	$valid = 0;
			    $error .= "ادخل المبلغ كاش + بطاقة (سبان)".'<br>';
            }
            
            if($valid == 1)
		    {
		        
		  $chk = $this->Model_user->check_duplicate_day($user_id,$send_date);
            if($chk) {
            	$valid = 0;
            	$error .= "لم يتم الحفظ تم اضافة التقفيلة لهذا اليوم".'<br>';
            	if(!isset($userid)){
            	$error .= "للتعديل تواصل مع مدير النظام".'<br>';
            	}
            	$this->session->set_flashdata('error',$error);
		    	redirect($this->agent->referrer());
            }else{
		        $form_data = array(
		            'user_lock_userid'        => secure_data($user_id),
					'user_lock_cash'        => secure_data($user_lock_cash),
					'user_lock_span'       => secure_data($user_lock_span),
					'user_lock_time'       => secure_data($send_date)
	            );
	            $this->Model_user->sendlock($form_data);
		        
		        $form_data_sales = array(
		            'insert_excel_uid'        => secure_data($user_id),
		            'insert_excel_date'          => secure_data($send_date),
					'insert_excel_electronic' => secure_data($user_lock_electronic),
					'insert_excel_jowy'       => secure_data($user_lock_jowy),
					'insert_excel_quickplus'  => secure_data($user_lock_quick_plus)
	            );
	           $this->Excel_import_model->insert_sales($form_data_sales);
		        
		        
		        
	            $success = "تم الحفظ بنجاح";
	            
        		$this->session->set_flashdata('success',$success);
        		redirect($this->agent->referrer());
		    }}
		    else
		    {
		    	$this->session->set_flashdata('error',$error);
		    	redirect($this->agent->referrer());
		    }

            
}
}


	function logout() 
	{
        $this->session->sess_destroy();
        redirect(base_url());
    }

    
}