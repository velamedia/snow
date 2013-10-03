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
class Supplier_entries extends MY_Model {

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

	public function insert_supplier_entry($supplier_entry_data)
	{
		$this->db->insert($this->db->dbprefix('snow_supplier_entries'), $supplier_entry_data);
		return $this->db->insert_id(); //return the id of the inserted record
	}
	

	public function get_all_supplier_entries()
	{
		//get the suppliers and their submited quotes
		$snow_supplier_entries_table = $this->db->dbprefix('snow_supplier_entries');

        $query = $this->db->query("SELECT DISTINCT `quotation_id`,`supplier_id` FROM `$snow_supplier_entries_table` ORDER BY `date_submited` DESC ");
        $supplier_entries = $query->result_array(); //returns an array

		return $supplier_entries;
	}

	public function get_supplier_entries($quotation_id, $supplier_id, $item_id)
	{
		//get supplier entries/quotations for each item
		$snow_supplier_entries_table = $this->db->dbprefix('snow_supplier_entries');
        $query = $this->db->query("SELECT * FROM `$snow_supplier_entries_table` WHERE `quotation_id` ='$quotation_id' AND `supplier_id` = '$supplier_id' AND `item_id` = '$item_id'");
        $supplier_entries = $query->row(); //returns an array

		return $supplier_entries;
	}
}