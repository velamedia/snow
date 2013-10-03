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
class Clients extends MY_Model {

	/**
	 * Get all the current clients registered in the system
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

	public function insert_client($data)
	{
		$this->db->insert($this->db->dbprefix('snow_clients'), $data);
		return $this->db->insert_id(); //return the id of the inserted record
	}

	public function update_client($data)
	{
		$this->db->update($this->db->dbprefix('snow_clients'), $data, array('id' => $data['id']));
		return $this->db->affected_rows();
	}

	public function delete_client($client_id)
	{
		$this->db->delete($this->db->dbprefix('snow_clients'), array('id' => $client_id));
		return $this->db->affected_rows();
	}

	public function get_all_clients()
	{
		return $this->db->get($this->db->dbprefix('snow_clients'))->result_array();
	}

	public function get_client($client)
	{
		return $this->db->get_where($this->db->dbprefix('snow_clients'), array('id' => $client))->row();
	}

	public function get_clients($client_ids)
	{
		//get the clients
		foreach ($client_ids as $client_id) {
			$client_idss[] = $client_id['client_id'];
		}
		
		$client_idsss = implode(',', $client_idss);
		$snow_clients_table = $this->db->dbprefix('snow_clients');
        $query = $this->db->query("SELECT DISTINCT `id`,`name` FROM `$snow_clients_table` WHERE `id` IN(".$client_idsss.")");
        if($query){
        	$clients = $query->result(); //returns an object
        }

		return $clients;
		
	}
	
}