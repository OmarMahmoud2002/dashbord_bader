<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

class Model_products extends CI_Model {
    protected $table;
    
    function __construct() {
        parent::__construct();
        $this->table = 'tbl_products';
    }
    
    function get_products($where = array()) {
        $this->db->order_by('id', 'asc');
        $this->db->where($where);
        $query = $this->db->get($this->table);
        return $query->result();
    }


    function get_low_products(){
        $products = $this->get_products(['low' => 1]);
        return $products;
    }

    function check_product_data($data) {
        $productName = $data['name'];
        $categoryCode = $data['categoryCode'];
        $brandSelected = $data['brand'];
        $categorySelected = $data['category'];

        $status = false;

        if ($productName == '') {
            $msg = 'يرجي إدخال اسم المنتج';
        } else if ($categoryCode == '') {
            $msg = 'يرجي إدخال رمز الصنف';
        } else if (!$brandSelected) {
            $msg = 'يرجي إختيار العلامة التجارية';
        } else if ($categorySelected == '') {
            $msg = 'يرجي إختيار التصنيف';
        } else if (count($this->get_products(['categoryCode' => $categoryCode])) > 0) {
            $msg = 'رمز الصنف مسجل بالفعل لإحدي المنتجات';
        } else if (count($this->get_products(['name' => $productName])) > 0) {
            $msg = 'يوجد منتج بالفعل بنفس الإسم';
        } else {
            $status = true;
            $msg = null;
        }

        return ['status' => $status, 'msg' => $msg];
    }

    function check_low_product_and_update($user_id, $id, $barcode, $serials_model, $barcodes_model, $notifications_model) {
        $product = $this->get_one_product_by_id($id);
        if ($product) {
            if ($product->unified) {
                // barcode
                $barcodeObj = $barcodes_model->get_barcodes(['barcode' => $barcode])[0];
                
                $remaining_in_storage = $barcodeObj->quantity - ($barcodeObj->registered_count + $barcodeObj->delivered_count);
                if ($remaining_in_storage <= $product->minimum_number) {
                    $this->Notifications->add_notification([
                        'user_id' => $user_id,
                        'description' => 'أصبحت الكمية المتاحة في المخزون للمنتج (' . $product->name . ') هي ' . $remaining_in_storage,
                        'link' => base_url('/admin/products/edit?id=' . $product->id),
                        'status' => 'unread',
                        'date_created' => date('Y-m-d H:i:s'),
                    ]);

                    $this->update_product(['low' => 1, 'last_updated' => date('Y-m-d H:i:s')], $id);
                } else {
                    $this->update_product(['low' => 0, 'last_updated' => date('Y-m-d H:i:s')], $id);
                }

            } else {
                // serial
                $remaining_in_storage = count($serials_model->get_serials(['productid' => $id, 'location' => 0]));
                if ($remaining_in_storage <= $product->minimum_number) {
                    $notifications_model->add_notification([
                        'user_id' => $user_id,
                        'description' => 'أصبحت الكمية المتاحة في المنتج (' . $product->name . ') هي ' . $remaining_in_storage,
                        'link' => base_url('/admin/products/edit?id=' . $product->id),
                        'status' => 'unread',
                        'date_created' => date('Y-m-d H:i:s'),
                    ]);

                    $this->update_product(['low' => 1, 'last_updated' => date('Y-m-d H:i:s')], $id);
                } else {
                    $this->update_product(['low' => 0, 'last_updated' => date('Y-m-d H:i:s')], $id);
                }
            }
        }
    }

    function get_products_length($where = array()) {
        return count($this->get_products($where));
    }
    
    function add_product($data) {
        $this->db->insert($this->table, $data);
    }
    
    function get_one_product_by_id($id) {
        $this->db->order_by('id', 'asc');
        $this->db->where(array('id' => $id));
        $query = $this->db->get($this->table);

        $result = $query->result();
        if (empty($result)) {
            return null;
        }
        return $result[0];
    }

    function unique($arr) {
        $new_array = array();
        $ids = array();
        foreach ($arr as $item) {
            if (in_array($item->id, $ids)) {
                continue;
            }
            array_push($ids, $item->id);
            array_push($new_array, $item);
        }
        return $new_array;
    }

    function get_products_joined_serials($shelf_number) {
        $query = $this->db->query("SELECT p.id, p.name, p.unified, COUNT(serial) AS serial FROM tbl_serials as s INNER JOIN tbl_products as p ON p.id = s.productid  WHERE s.shelf_number = '$shelf_number' GROUP BY p.id;");
        return $query->result();
    }

    function get_products_joined_barcodes() {
        $query = $this->db->query('SELECT * FROM tbl_products as p INNER JOIN tbl_barcodes as b ON p.id = b.productid;');
        return $query->result();
    }
    
    function update_product($data, $id) {
        $this->db->update($this->table, $data, array('id' => $id));
    }

    function delete_product($data) {
        $rows = $this->get_products($data);
        $this->db->delete($this->table, $data);
        return $rows;
    }

    function delete_products_of($of, $ofField, $whereField) {
        $rows = array();
        foreach ($of as $item) {
            $rows = array_merge($rows, $this->get_products(array($whereField => $item->$ofField)));
        }
        foreach ($of as $item) {
            $this->delete_product(array($whereField => $item->$ofField));
        }
        return $rows;
    }

    function delete_all_product_infos($id, $Serials_Model, $Barcodes_Model, $Custodys_Model, $Requests_Model, $Op_Model, $MAIN) {
        $product = $this->get_one_product_by_id($id);
        $serials = $Serials_Model->get_serials(array('productid' => $id));

	    $this->delete_product(array('id' => $id));
		if ($product->image != 'default_product_image.png') {
			unlink('./public/img/products/' . $product->image);
		}
		if ($product->unified == 'yes') {
			$barcode = $Barcodes_Model->get_barcodes(['productid' => $id])[0];
			$Barcodes_Model->delete_barcode(['productid' => $id]);
			$Custodys_Model->delete_custody_by_serial($barcode->barcode);
			$Requests_Model->update_product_verification($barcode->barcode, 0);
		} else {
			$Serials_Model->delete_serial(array('productid' => $id));
			foreach ($serials as $serial) {
                $Op_Model->add_operation(['serial' => $serial->serial, 'operation' => 'تم ازالة المنتج من المخزون', 'sales_order' => '0'], $MAIN->session->userdata('user_id'));
				$Custodys_Model->delete_custody_by_serial($serial->serial);
				$Requests_Model->update_product_verification($serial->serial, 0);
			}
		}
	}
}

?>
