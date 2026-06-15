<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Shelves extends MY_Controller {

	function __construct()
	{
        parent::__construct();
        $this->load->model('Model_admin');
        $this->load->model('Model_store_items', 'Store');
        $this->load->model('Model_shelves', 'Shelves');
        $this->load->model('Model_custody', 'Custodys');
        $this->load->model('Model_requests', 'Requests');
        $this->load->model('Model_notifications', 'Notifications');
        $this->load->model('Model_operations', 'Operations');
        notifications($this);
        ini_set('memory_limit', '512M');
    }

    private function get_admins() {
		$ids = array();
		foreach ($this->Model_admin->getadmins() as $admin) {
			array_push($ids, strval($admin['user_id']));
		}
		return $ids;
	}

    public function index() {
        if(!in_array($this->session->userdata('user_id'), $this->get_admins())) {
            redirect(base_url());
        }
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $action = $this->input->post('action');


            if ($action == 'add') {
                $shelf_num = $this->input->post('shelf_num');
                $result = $this->Shelves->add_shelf(['shelf_number' => $shelf_num]);

                if ($result['status'] == false) {
                    $this->session->set_flashdata('error', $result['description']);
                }
                
            } else if ($action == 'rearrange_shelf') {
                $serials = $this->input->post('serials');
                $shelf_id = $this->input->post('shelf_id');
                $posters = $this->input->post('posters');
                $errors = '';

                if (!is_array($serials)) {
                    $serials = array();
                }

                if (!is_array($posters)) {
                    $posters = array();
                }

                foreach ($serials as $serial_number) {
                    if ($serial_number == '') {
                        $errors .= 'السيريال فارغ' . '<br><br>';
                        continue;
                    }

                    $item = $this->Store->get_store_item(['serial_number' => $serial_number]);
                    if ($item) {
                        if ($item['serial_control'] == 'yes') {
                            $this->Operations->add_operation(['serial' => $item['serial_number'], 'operation' => 'تم نقل المنتج إلى رف ' . $this->Shelves->get_one_shelf_by_id($shelf_id)->shelf_number, 'sales_order' => '0'], $this->session->userdata('user_id'));
                        }
                        $this->Store->move_item_to_shelf($item['item_code'], $item['serial_control'], $item['serial_number'], $item['barcode'], $shelf_id);
                    } else {
                        $errors .= 'السيريال غير موجود' . '<br>';
                        continue;
                    }
                }

                if ($errors != '') {
                    $this->session->set_flashdata('shelf_reorder_error', $errors);
                }

            }
        }

		$this->data['shelves'] = $this->Shelves->get_shelves();
		$this->load->view('view_admin_shelves', $this->data);
    }

    public function check_serial() {
        if(!in_array($this->session->userdata('user_id'), $this->get_admins())) {
            $this->output
                ->set_status_header(403)
                ->set_content_type('application/json')
                ->set_output(json_encode(array(
                    'status' => 'error',
                    'exists' => false,
                    'description' => 'غير مصرح'
                )));
            return;
        }

        $serial_number = trim((string) $this->input->post('serial'));

        if ($serial_number === '') {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(array(
                    'status' => 'error',
                    'exists' => false,
                    'description' => 'السيريال فارغ'
                )));
            return;
        }

        $item = $this->Store->get_store_item(array('serial_number' => $serial_number));

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode(array(
                'status' => 'success',
                'exists' => !empty($item),
                'serial' => $serial_number
            )));
    }

    public function delete_shelf() {
        if(!in_array($this->session->userdata('user_id'), $this->get_admins())) {
            redirect(base_url());
        }
        
        $id = $this->input->get('id');

        $this->Shelves->delete_shelf(array('id' => $id));

        $items = $this->Store->get_items_in_shelf($id);

        foreach ($items as $item) {
            if ($item['serial_control'] == 'yes') {
                $this->Operations->add_operation(['serial' => $item['serial_number'], 'operation' => 'تم إزالة المنتج من الرف بسبب حذف الرف', 'sales_order' => '0'], $this->session->userdata('user_id'));
            }

            $this->Store->remove_item_from_shelf($item['item_code'], $item['serial_control'], $item['serial_number'], $item['barcode']);
        }

        redirect(base_url('admin/shelves'));
    }

    public function show() {
        if(!in_array($this->session->userdata('user_id'), $this->get_admins())) {
            redirect(base_url());
        }

        $id = $this->input->get('id');
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $action = $this->input->post('action');

            if ($action == 'delete') {
                $item_id = $this->input->post('itemid');
                $item = $this->Store->get_store_item(['id' => $item_id]);
                $this->Store->remove_item_from_shelf($item['item_code'], $item['serial_control'], $item['serial_number'], $item['barcode']);
            }
        }

        $this->data['shelf'] = $this->Shelves->get_one_shelf_by_id($id);
        $this->data['items'] = $this->Store->get_items_in_shelf($id);
        $this->load->view('view_admin_shelves_products', $this->data);
    }
}

?>
