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
class Suppliers extends MY_Model {

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

	public function insert_supplier($data)
	{
		$this->db->insert($this->db->dbprefix('snow_suppliers'), $data);
		return $this->db->insert_id(); //return the id of the inserted record
	}

	public function update_supplier($data)
	{
		$this->db->update($this->db->dbprefix('snow_suppliers'), $data, array('id' => $data['id']));
		return $this->db->affected_rows();
	}

	public function delete_supplier($supplier_id)
	{
		$this->db->delete($this->db->dbprefix('snow_suppliers'), array('id' => $supplier_id));
		return $this->db->affected_rows();
	}

	public function get_all_suppliers()
	{
		return $this->db->get($this->db->dbprefix('snow_suppliers'))->result_array();
	}

	public function get_supplier($supplier)
	{
		return $this->db->get_where($this->db->dbprefix('snow_suppliers'), array('id' => $supplier))->row();
	}

	public function get_suppliers($supplier_ids)
	{
		//get the suppliers
		foreach ($supplier_ids as $supplier_id) {
			$supplier_idss[] = $supplier_id['supplier_id'];
		}
		
		$supplier_idsss = implode(',', $supplier_idss);
		$snow_suppliers_table = $this->db->dbprefix('snow_suppliers');
        $query = $this->db->query("SELECT DISTINCT `id`,`name` FROM `$snow_suppliers_table` WHERE `id` IN(".$supplier_idsss.")");
        if($query){
        	$suppliers = $query->result(); //returns an object
        }

		return $suppliers;
		
	}
	
}