<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
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
class Admin extends Admin_Controller
{
	public $id = 0;

	/**
	 * Validation rules for your module
	 *
	 * @var array
	 * @access private
	 *
	private $contractor_validation_rules = array(
		array(
			'field' => 'field_name',
			'label' => 'lang:contractor.field_name_label',
			'rules' => 'trim|max_length[255]|required'
		)
	);*/


	/**
	 * Constructor method
	 *
	 * @author Yorick Peterse - PyroCMS Dev Team
	 * @access public
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
		// Load the required classes
		// LOAD MODELS, LANGUAGE AND HELPERS (IF REQUIRED)
			//$this->load->model('...');
			$this->lang->load('dashboard');
			//$this->load->helper('html');
		// ITS AN OVERALL GOOD IDEA TO SET UP A PARTIAL WITH THE MENU
			//$this->template->set_partial('shortcuts', 'admin/partials/shortcuts');
	}

	/**
	 * List all existing albums
	 *
	 * @access public
	 * @return void
	 */
	public function index()
	{
		//LIKELY TO GET DATA FROM MODEL
		//RENDER TEMPLATE
			//$this->template
			//->title($this->module_details['name'])
			//->set('data', $data_obtained)
			//->build('index', $data);
			
			$this->template
			->title($this->module_details['name'])
			->build('admin/index');
	}
	
}
