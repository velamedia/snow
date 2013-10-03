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
class Client_records extends MY_Model {

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

	public function insert_client_record($data)
	{
		$this->db->insert($this->db->dbprefix('snow_client_records'), $data);
		return $this->db->insert_id(); //return the id of the inserted record
	}

	public function update_client_record($data,$id)
	{
		$snow_client_records_table = $this->db->dbprefix('snow_client_records');

		$where = "id = $id";

		$update_query = $this->db->update_string($snow_client_records_table, $data, $where);
		$result = $this->db->query($update_query);

		return $result;
	}

	public function delete_client_record($client_record_id)
	{
		$this->db->delete($this->db->dbprefix('snow_client_records'), array('id' => $client_record_id));
		return $this->db->affected_rows();
	}

	public function get_all_client_records()
	{
		return $this->db->get($this->db->dbprefix('snow_client_records'))->result_array();
	}

	public function get_client_record($id)
	{
		return $this->db->get_where($this->db->dbprefix('snow_client_records'), array('id' => $id))->row();
	}

	public function get_sales_refs()
	{
		
		$snow_inquiries_table = $this->db->dbprefix('snow_inquiries');
        $query = $this->db->query("SELECT DISTINCT `sales_ref` FROM `$snow_inquiries_table` ORDER BY `sales_ref` ASC");
        if($query){
        	$sales_refs = $query->result_array(); //returns an object
        }

		return $sales_refs;
		
	}
	
}