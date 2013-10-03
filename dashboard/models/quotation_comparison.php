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
class Quotation_comparison extends MY_Model {

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
			$this->db->db_debug = FALSE;
		break;

		case 'qa':
		case 'live':
			$this->db->db_debug = FALSE;
		break;

		default:
			$this->db->db_debug = FALSE;
	}
		
	}

	public function get_suppliers($quotation_id)
	{
		//get the suppliers who have submited their quotes
		$snow_supplier_entries_table = $this->db->dbprefix('snow_supplier_entries');
        $query = $this->db->query("SELECT DISTINCT `supplier_id` FROM `$snow_supplier_entries_table` WHERE `quotation_id`='".$quotation_id."'");
        $suppliers = $query->result_array(); //returns an array

		return $suppliers;
	}

	public function confirm_quotation($quotation_id,$accepted_quotes,$accepted_quantities,$item_ids,$supplier_ids)
	{
		//get the inquiry id
		$snow_quotations_table = $this->db->dbprefix('snow_quotations');
  		$query = $this->db->query("SELECT `inquiry_id` FROM `$snow_quotations_table` WHERE `id`='".$quotation_id."'");
		$inquiry = $query->row(); //returns an array
		$inquiry_id = $inquiry->inquiry_id;

		//update the products inquired table
		$snow_products_inquired_table = $this->db->dbprefix('snow_products_inquired');
		$i = 0;
		foreach ($item_ids as $item_id) {
			$accepted_price = ($accepted_quotes[$item_id])? str_replace(',', '', $accepted_quotes[$item_id]): 0;
			$accepted_quantity = ($accepted_quantities[$i])? $accepted_quantities[$i]: 0;
			$supplier_id = ($supplier_ids[$i])? $supplier_ids[$i]: 0;

			$data = array('accepted_price' => $accepted_price, 'accepted_quantity' => $accepted_quantity, 'supplier_id' => $supplier_id);
			$where = "inquiry_id = $inquiry_id  AND id = $item_id";

			$update_query = $this->db->update_string($snow_products_inquired_table, $data, $where);
			$result = $this->db->query($update_query);
			
			$i++;
		}

		return $result;
	}
	
}