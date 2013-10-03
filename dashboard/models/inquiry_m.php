<?php defined('BASEPATH') or exit('No direct script access allowed');

/**
 *
 * The galleries module enables users to create albums, upload photos and manage their existing albums.
 *
 * @author 		jjperezaguinaga
 * @package 	PyroCMS
 * @subpackage 	Contractors Module
 * @category 	Modules
 * @license 	Apache License v2.0
 */
class Inquiry_m extends MY_Model {

	/**
	 * Get all the current suppliers registered in the system
	 *
	 * @author jjperezaguinaga
	 * @access public
	 * @return mixed
	 */
	 public function __construct()
	{
		switch (ENVIRONMENT)
	{
		case 'local':
		case 'dev':
			$this->db->db_debug = TRUE;
		break;

		case 'development':
			$this->db->db_debug = TRUE;
		break;

		case 'qa':
		case 'live':
			$this->db->db_debug = FALSE;
		break;

		default:
			$this->db->db_debug = FALSE;
	}
		
	}

	public function insert_inquiry($data)
	{
		$this->db->insert($this->db->dbprefix('snow_inquiries'), $data);
		return $this->db->insert_id(); //return the id of the inserted record
	}

	public function update_inquiry($data)
	{
		$this->db->update($this->db->dbprefix('snow_inquiries'), $data, array('id' => $data['id']));
		return $this->db->affected_rows();
	}

	public function insert_products_inquired($product_inquired)
	{
		$this->db->insert($this->db->dbprefix('snow_products_inquired'), $product_inquired);
		return $this->db->insert_id(); //return the id of the inserted record
	}

	public function update_products_inquired($product_inquired)
	{
		$this->db->update($this->db->dbprefix('snow_products_inquired'), $product_inquired, array('id' => $product_inquired['id']));
		return $this->db->affected_rows();

		// $where = "id = ".$data['id']." ";
		// $update_query = $this->db->update_string($this->db->dbprefix('snow_products_inquired'), $product_inquired, $where);
		// $result = $this->db->query($update_query);

		return $result;
	}

	public function get_all_inquiries()
	{
		//return $this->db->get($this->db->dbprefix('snow_inquiries'))->result_array();
		$snow_inquiries_table = $this->db->dbprefix('snow_inquiries');
        $query = $this->db->query("SELECT * FROM `$snow_inquiries_table` ORDER BY `timestamp` DESC");
        
        $inquiries = $query->result_array(); //returns an object

		return $inquiries;
	}

	public function get_inquiry($inquiry_id)
	{
		return $this->db->get_where($this->db->dbprefix('snow_inquiries'), array('id' => $inquiry_id))->result_array();
	}

	public function get_products_inquired($inquiry_id)
	{
		return $this->db->get_where($this->db->dbprefix('snow_products_inquired'), array('inquiry_id' => $inquiry_id))->result_array();
	}

	public function get_confirmed_inquiries()
	{
		$snow_inquiries_table = $this->db->dbprefix('snow_inquiries');
        $query = $this->db->query("SELECT * FROM `$snow_inquiries_table` WHERE `status` =1 ORDER BY `timestamp` DESC");
        
        $confirmed_inquiries = $query->result_array(); //returns an object

		return $confirmed_inquiries;
		// return $this->db->get_where($this->db->dbprefix('snow_inquiries'), array('status' => 1))->result_array();
	}

	public function get_unconfirmed_inquiries()
	{
		$snow_inquiries_table = $this->db->dbprefix('snow_inquiries');
        $query = $this->db->query("SELECT * FROM `$snow_inquiries_table` WHERE `status` ='-1' ORDER BY `timestamp` DESC");
        
        $unconfirmed_inquiries = $query->result_array(); //returns an object

		return $unconfirmed_inquiries;
		//return $this->db->get_where($this->db->dbprefix('snow_inquiries'), array('status' => -1))->result_array();
	}

	public function delete_inquiry($inquiry_id)
	{
		$this->db->delete($this->db->dbprefix('snow_inquiries'), array('id' => $inquiry_id));
		return $this->db->affected_rows();
	}

	public function get_supplier_quotation_currency($inquiry_id,$supplier_id)
	{
		//get quotation id
		$snow_quotations_table = $this->db->dbprefix('snow_quotations');
  		$query = $this->db->query("SELECT `id` FROM `$snow_quotations_table` WHERE `inquiry_id`='".$inquiry_id."'");
		$quotation = $query->row(); //returns an array
		$quotation_id = $quotation->id;

		//get currency
		$snow_supplier_entries = $this->db->dbprefix('snow_supplier_entries');
  		$query = $this->db->query("SELECT `currency` FROM `$snow_supplier_entries` WHERE `quotation_id`='".$quotation_id."' AND `supplier_id`='".$supplier_id."'");
		$quotation_currency = $query->row(); //returns an array
		$currency = $quotation_currency->currency;

		return $currency;

	}

	public function get_supplier_quotation_payment_terms($inquiry_id,$supplier_id)
	{
		//get quotation id
		$snow_quotations_table = $this->db->dbprefix('snow_quotations');
  		$query = $this->db->query("SELECT `id` FROM `$snow_quotations_table` WHERE `inquiry_id`='".$inquiry_id."'");
		$quotation = $query->row(); //returns an array
		$quotation_id = $quotation->id;

		//get payment_terms
		$snow_supplier_entries = $this->db->dbprefix('snow_supplier_entries');
  		$query = $this->db->query("SELECT `payment_terms` FROM `$snow_supplier_entries` WHERE `quotation_id`='".$quotation_id."' AND `supplier_id`='".$supplier_id."'");
		$quotation_payment_terms = $query->row(); //returns an array
		$payment_terms = $quotation_payment_terms->payment_terms;

		return $payment_terms;

	}

	public function get_confirmed_suppliers($inquiry_id)
	{
		//get selected/confirmed suppliers for the products inquired
		$snow_products_inquired = $this->db->dbprefix('snow_products_inquired');
  		$query = $this->db->query("SELECT `supplier_id` FROM `$snow_products_inquired` WHERE `inquiry_id`='".$inquiry_id."'");
		$supplier_ids = $query->result_array(); //returns an array

		return $supplier_ids;

	}

	public function get_products_qouted($inquiry_id, $supplier_id)
	{
	
		$snow_products_inquired_table = $this->db->dbprefix('snow_products_inquired');
  		$query = $this->db->query("SELECT * FROM `$snow_products_inquired_table` WHERE `inquiry_id`='".$inquiry_id."' AND `supplier_id`='".$supplier_id."'");
		$products_qouted = $query->result_array(); //returns an array
		
		return $products_qouted;

	}
	
}