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
class Quotations extends MY_Model {

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

	public function send_quotation($data)
	{

		$this->db->insert($this->db->dbprefix('snow_quotations'), $data);
		$inserted_id = $this->db->insert_id();

		if($inserted_id){
			//update the inquiry status to 'handled but not confirmed'
			$update_data = array('status' => '-1');
			$where = "id = ".$data['inquiry_id'];
			$update_query = $this->db->update_string($this->db->dbprefix('snow_inquiries'), $update_data, $where); 
			$this->db->query($update_query);
		}
		
		return $inserted_id; //return the id of the inserted record
	}

	public function get_all_quotations()
	{
		// return $this->db->get($this->db->dbprefix('snow_quotations'))->result_array();
		$snow_quotations_table = $this->db->dbprefix('snow_quotations');
        $query = $this->db->query("SELECT * FROM `$snow_quotations_table` ORDER BY `timestamp` DESC");
        
        $quotations = $query->result_array(); //returns an object

		return $quotations;
	}

	public function get_quotation($quotation_id)
	{
		return $this->db->get_where($this->db->dbprefix('snow_quotations'), array('id' => $quotation_id))->row();
	}

	public function get_quotation_by_inquiry($inquiry_id)
	{
		return $this->db->get_where($this->db->dbprefix('snow_quotations'), array('inquiry_id' => $inquiry_id))->row();
	}
	
}