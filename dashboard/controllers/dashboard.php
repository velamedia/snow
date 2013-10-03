<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 *
 * Module template
 *
 * @author 		jjperezaguinaga
 * @package 	PyroCMS
 * @subpackage 	Contractors Module
 * @category 	Modules
 * @license 	Apache License v2.0
 */
class Dashboard extends Public_Controller
{

	public $id = 0;


	/**
	 * Constructor method
	 *
	 * @author jjperezaguinaga
	 * @access public
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
		// Load the required classes
		// LOAD MODELS, LANGUAGE AND HELPERS (IF REQUIRED)
			$this->load->model('inquiry_m');
			$this->load->model('clients');
			$this->load->model('suppliers');
			$this->load->model('quotations');
			$this->load->model('supplier_entries');
			$this->load->model('quotation_comparison');
			$this->load->model('client_records');
			$this->load->model('purchase_order');

			$this->lang->load('dashboard');
			//$this->load->helper('html');
			$this->load->helper('email');
			$this->load->library('email');
	}
	
	/**
	 * Index method
	 *
	 * @access public
	 * @return void
	 */
	public function index()
	{
	//RENDER VIEW
		$this->template
		->append_css('module::style.css', 'dashboard')
		->title($this->module_details['name'])
		->build('index');
	}

	public function generate_salesref()
	{
		
	//GET DATA FROM MODEL
		//get customer details
        $client = $this->clients->get_client($this->input->post('client'));

        $client_country_substr = strtoupper(substr($client->country, 0,3));
        $client_name_substr = strtoupper(substr($client->name, 0,3));
       
       	$date = date('Ymd');

       	$sales_rep = $this->current_user->username;

       	$hash_salt = substr(md5(rand()), 0, 5);

       	echo $sales_ref = $date."/".$client_country_substr."/".$client_name_substr."/".$sales_rep."/".$hash_salt;
	}

	public function regenerate_salesref($client_id)
	{
		
	//GET DATA FROM MODEL
		//get customer details
        $client = $this->clients->get_client($client_id);

        $client_country_substr = strtoupper(substr($client->country, 0,3));
        $client_name_substr = strtoupper(substr($client->name, 0,3));
       
       	$date = date('Ymd');

       	$sales_rep = $this->current_user->username;

       	$hash_salt = substr(md5(rand()), 0, 5);

       	$sales_ref = $date."/".$client_country_substr."/".$client_name_substr."/".$sales_rep."/".$hash_salt;

       	return $sales_ref;
	}

	public function add_inquiry()
	{

	//GET DATA FROM MODEL
		$clients = $this->clients->get_all_clients();

	//GET SUBMITED DATA, PASS IT TO THE MODEL FOR INSERTING TO DB AND RETURN A STATUS
	$inquiry_metadata = array(
				'sales_ref'        => $this->input->post('sales_ref'),
				'title'            => $this->input->post('title'),
				'customer'         => $this->input->post('customer'),
				'location'         => $this->input->post('location'),
				'date_of_inquiry'  => format_date(strtotime($this->input->post('date_of_inquiry')), 'Y-m-d H:i:s'),
				'product_type'     => $this->input->post('product_type')
			);

	$items_inquired = $this->input->post('item');
	$items_part_no  = $this->input->post('part_no');
	$items_quantities = $this->input->post('quantity');
	$items_terms  = $this->input->post('terms');
	$cif_port  = $this->input->post('cif_port');

	if($inquiry_metadata['sales_ref']){

		$inserted_record_id = $this->inquiry_m->insert_inquiry($inquiry_metadata); //the id of the inserted record
		if ($inserted_record_id != 0)
		{
			//insert the items/products inquired to the database
			$i = 0;
			foreach ($items_inquired as $item_inquired) {
				
				//check if row is empty
				if($item_inquired != ''){
					$product_inquired = array(
						'inquiry_id'     => $inserted_record_id,
						'item'           => $item_inquired,
						'part_no'        => $items_part_no[$i],
						'quantity'       => $items_quantities[$i],
						'terms'          => $items_terms[$i],
						'cif_port'       => $cif_port[$i],
						'accepted_price' => '',
						'accepted_quantity' => '',
						'supplier_id'    => 0
					);

					$this->inquiry_m->insert_products_inquired($product_inquired);
				}

				$i++;
			}

			$this->session->set_flashdata('success', sprintf(lang('snow.inquiry.add_success'), $this->input->post('title')) );
			redirect($this->uri->uri_string);
		}
		else
		{
			//echo $this->db->last_query();
			$this->session->set_flashdata('error', lang('snow.inquiry.add_error') );
			redirect($this->uri->uri_string);
		}
	}

	//RENDER VIEW
		$this->template
		->append_css('module::style.css', 'dashboard')
		->append_css('module::buttons.css', 'dashboard')
		->append_css('module::jquery-ui-1.8.18.custom.css', 'dashboard')
		->append_css('module::blue_tablesorter_style.css', 'dashboard')
		->append_css('module::chosen.css', 'dashboard')
		->append_css('module::screen.css', 'dashboard')
		->append_js('module::chosen.jquery.js', 'dashboard')
		->append_js('module::jquery.ui.core.js', 'dashboard')
		->append_js('module::jquery.ui.datepicker.js', 'dashboard')
		->append_js('module::jquery.validate.min.js', 'dashboard')
		->set('clients', $clients)
		->title('Add Inquiry')
		->build('add_inquiry');
	}

	public function inquiry_csv_upload()
	{
	//GET DATA FROM MODEL
		$clients = $this->clients->get_all_clients();

	//UPLOAD FILE AND READ IT'S DATA
		if($this->input->post()){
			$inquiry_metadata = array(
				'sales_ref'        => $this->input->post('sales_ref'),
				'title'            => $this->input->post('title'),
				'customer'         => $this->input->post('customer'),
				'location'         => $this->input->post('location'),
				'date_of_inquiry'  => format_date(strtotime($this->input->post('date_of_inquiry')), 'Y-m-d H:i:s'),
				'product_type'     => $this->input->post('product_type')
			);


			$config['upload_path'] = 'uploads/';
			$config['allowed_types'] = 'csv|xls|xlsx|txt';
			$config['max_size']	= '10000';

			$this->load->library('upload', $config);

			$field_name = "csv_file";
			if ( !$this->upload->do_upload($field_name))
			{
				//echo $this->upload->display_errors();
				$this->session->set_flashdata('error', lang('snow.inquiry.add_error') );
				redirect($this->uri->uri_string);
			}
			else
			{

				$inserted_record_id = $this->inquiry_m->insert_inquiry($inquiry_metadata); //the id of the inserted record
				if ($inserted_record_id != 0)
				{
					//insert the items/products inquired to the database
					$uploaded_file = $this->upload->data();

					$uploaded_file_name = $uploaded_file['full_path'];

					//extract inquired items data from the uploaded csv file
					$fp = fopen($uploaded_file_name,'r') or die("can't open file");

					ini_set("auto_detect_line_endings", true);
					fgetcsv($fp, 2621440, ","); //read first line so that it is skipped in the loop

					while($csv_line = fgetcsv($fp,2621440)) { //allow max file to 10MB
		
						for ($i = 0, $j = count($csv_line); $i < $j; $i++) {
							$item_inquired = $csv_line[0];
							$part_no       = $csv_line[1];
							$quantity      = $csv_line[2];
							$terms         = $csv_line[3];
							$cif_port      = $csv_line[4];	 

							$product_inquired = array(
								'inquiry_id'     => $inserted_record_id,
								'item'           => $item_inquired,
								'part_no'        => $part_no,
								'quantity'       => $quantity,
								'terms'          => $terms,
								'cif_port'       => $cif_port,
								'accepted_price' => '',
								'accepted_quantity' => '',
								'supplier_id'    => 0
							);
						}

						if($product_inquired['item'] != ''){
							$this->inquiry_m->insert_products_inquired($product_inquired);
						}
					}

					$this->session->set_flashdata('success', sprintf(lang('snow.inquiry.add_success'), $this->input->post('title')) );
					redirect("dashboard/edit_inquiry/$inserted_record_id");
				}
				else
				{
					//echo $this->db->last_query();
					$this->session->set_flashdata('error', lang('snow.inquiry.add_error') );
					redirect($this->uri->uri_string);
				}
				
			}
		}

	//RENDER VIEW
		$this->template
		->append_css('module::style.css', 'dashboard')
		->append_css('module::buttons.css', 'dashboard')
		->append_css('module::jquery-ui-1.8.18.custom.css', 'dashboard')
		->append_css('module::blue_tablesorter_style.css', 'dashboard')
		->append_css('module::chosen.css', 'dashboard')
		->append_css('module::screen.css', 'dashboard')
		->append_js('module::chosen.jquery.js', 'dashboard')
		->append_js('module::jquery.ui.core.js', 'dashboard')
		->append_js('module::jquery.ui.datepicker.js', 'dashboard')
		->append_js('module::jquery.validate.min.js', 'dashboard')
		->title('Inquiry CSV Upload')
		->set('clients', $clients)
		->build('inquiry_csv_upload');
	}

	public function inquiries()
	{
	//GET DATA FROM MODEL
		$inquiries = $this->inquiry_m->get_all_inquiries();

	//RENDER VIEW
		$this->template
		->append_css('module::style.css', 'dashboard')
		->append_css('module::buttons.css', 'dashboard')
		->append_css('module::blue_tablesorter_style.css', 'dashboard')
		->append_js('module::jquery.tablesorter.min.js', 'dashboard')
		->title('Inquiries')
		->set('inquiries', $inquiries)
		->build('inquiries');
	}

	public function edit_inquiry($inquiry_id)
	{

	//GET DATA FROM MODELS
		$inquiry = $this->inquiry_m->get_inquiry($inquiry_id);
		$products_inquired = $this->inquiry_m->get_products_inquired($inquiry_id);
		$clients = $this->clients->get_all_clients();

	//GET SUBMITED DATA, PASS IT TO THE MODEL FOR UPDATING TO DB AND RETURN A STATUS
	$inquiry_data = array(
				'id'               => $this->input->post('inquiry_id'),
				'sales_ref'        => $this->input->post('sales_ref'),
				'title'            => $this->input->post('title'),
				'customer'         => $this->input->post('customer'),
				'location'         => $this->input->post('location'),
				'date_of_inquiry'  => format_date(strtotime($this->input->post('date_of_inquiry')), 'Y-m-d'),
				'product_type'     => $this->input->post('product_type')
			);

	if($inquiry_data['sales_ref']){

		$item_id = $this->input->post('item_id');
		$items_inquired = $this->input->post('item');
		$items_part_no  = $this->input->post('part_no');
		$items_quantities = $this->input->post('quantity');
		$items_terms  = $this->input->post('terms');
		$cif_port  = $this->input->post('cif_port');

		$this->inquiry_m->update_inquiry($inquiry_data);
		
		//insert the items/products inquired to the database
		$i = 0;
		foreach ($items_inquired as $item_inquired) {
			
			$product_inquired = array(
				'id'             => $item_id[$i],
				'inquiry_id'     => $inquiry_data['id'],
				'item'           => $item_inquired,
				'part_no'        => $items_part_no[$i],
				'quantity'       => $items_quantities[$i],
				'terms'          => $items_terms[$i],
				'cif_port'       => $cif_port[$i]
			);

			//update existing rows
			if($item_id[$i] != ''){
				$affected_rows = $this->inquiry_m->update_products_inquired($product_inquired);
				
				// if ($affected_rows > 0){
				// 	$this->session->set_flashdata('success', sprintf(lang('snow.inquiry.update_success'), $this->input->post('title')) );
				// }else
				// {
				// 	$this->session->set_flashdata('error', lang('snow.inquiry.update_error') );
				// }

			}else{
				//skip empty rows
				if($item_inquired != ''){
					$product_inquired = array_merge($product_inquired, array('accepted_price' => '','supplier_id' => 0));
					unset($product_inquired['id']);
					$this->inquiry_m->insert_products_inquired($product_inquired);
				}
			}

			$i++;
		}

		$this->session->set_flashdata('success', sprintf(lang('snow.inquiry.update_success'), $this->input->post('title')) );
		redirect($this->uri->uri_string);
	}

	//RENDER VIEW
		$this->template
		->append_css('module::style.css', 'dashboard')
		->append_css('module::buttons.css', 'dashboard')
		->append_css('module::jquery-ui-1.8.18.custom.css', 'dashboard')
		->append_css('module::blue_tablesorter_style.css', 'dashboard')
		->append_css('module::chosen.css', 'dashboard')
		->append_js('module::chosen.jquery.js', 'dashboard')
		->append_js('module::jquery.ui.core.js', 'dashboard')
		->append_js('module::jquery.ui.datepicker.js', 'dashboard')
		->title('Edit Inquiry')
		->set('inquiry_id', $inquiry_id)
		->set('inquiry', $inquiry)
		->set('products_inquired', $products_inquired)
		->set('clients', $clients)
		->build('edit_inquiry');
	}
	
	public function delete_inquiry($inquiry_id)
	{
	
		$affected_rows = $this->inquiry_m->delete_inquiry($inquiry_id);
		
		if ($affected_rows > 0)
		{
			$this->session->set_flashdata('success', sprintf(lang('snow.inquiry.delete_success'), '') );
			redirect('dashboard/inquiries');
		}else
		{
			$this->session->set_flashdata('error', sprintf(lang('snow.inquiry.delete_error'), '') );
			redirect('dashboard/inquiries');
		}

	}

	public function add_client()
	{
		//GET SUBMITED DATA, PASS IT TO THE MODEL FOR INSERTING TO DB AND RETURN A STATUS
		$client_data = array(
			'name'                 => $this->input->post('name'),
			'location'             => $this->input->post('location'),
			'industry'             => $this->input->post('industry'),
			'email'                => $this->input->post('email'),
			'tel'                  => $this->input->post('tel'),
			'address'              => $this->input->post('address'),
			'city'                 => $this->input->post('city'),
			'country'              => $this->input->post('country'),
			'contact_person'       => $this->input->post('contact_person'),
			'contact_person_email' => $this->input->post('contact_person_email'),
			'contact_person_tel'   => $this->input->post('contact_person_tel')
		);

		if($client_data['name']){

			$inserted_record_id = $this->clients->insert_client($client_data); //the id of the inserted record
			if ($inserted_record_id != 0)
			{

				$this->session->set_flashdata('success', sprintf(lang('snow.client.add_success'), $client_data['name']) );
				redirect($this->uri->uri_string);
			}
			else
			{
				$this->session->set_flashdata('error', lang('snow.client.add_error') );
				redirect($this->uri->uri_string);
			}
		}

		//RENDER VIEW
		$this->template
		->append_css('module::style.css', 'dashboard')
		->append_css('module::buttons.css', 'dashboard')
		->title('Add client')
		->build('add_client');
	}

	public function edit_client($client_id)
	{
	//GET DATA FROM MODEL
		$client = $this->clients->get_client($client_id);

	//GET SUBMITED DATA, PASS IT TO THE MODEL FOR UPDATING TO DB AND RETURN A STATUS
		$client_data = array(
			'id'                   => $this->input->post('client_id'),
			'name'                 => $this->input->post('name'),
			'location'             => $this->input->post('location'),
			'industry'             => $this->input->post('industry'),
			'email'                => $this->input->post('email'),
			'tel'                  => $this->input->post('tel'),
			'address'              => $this->input->post('address'),
			'city'                 => $this->input->post('city'),
			'country'              => $this->input->post('country'),
			'contact_person'       => $this->input->post('contact_person'),
			'contact_person_email' => $this->input->post('contact_person_email'),
			'contact_person_tel'   => $this->input->post('contact_person_tel')
		);

		if($client_data['id']){

			$affected_rows = $this->clients->update_client($client_data); 
			if ($affected_rows > 0)
			{

				$this->session->set_flashdata('success', sprintf(lang('snow.client.update_success'), $client_data['name']) );
				redirect($this->uri->uri_string);
			}
			else
			{
				$this->session->set_flashdata('error', lang('snow.client.update_error') );
				redirect($this->uri->uri_string);
			}
		}

	//RENDER VIEW
		$this->template
		->append_css('module::style.css', 'dashboard')
		->append_css('module::buttons.css', 'dashboard')
		->title('Edit client')
		->set('client', $client)
		->set('client_id', $client_id)
		->build('edit_client');
	}

	public function clients()
	{
	//GET DATA FROM MODEL
		$clients = $this->clients->get_all_clients();

	//RENDER VIEW
		$this->template
		->append_css('module::style.css', 'dashboard')
		->append_css('module::buttons.css', 'dashboard')
		->append_css('module::blue_tablesorter_style.css', 'dashboard')
		->append_js('module::jquery.tablesorter.min.js', 'dashboard')
		->title('clients')
		->set('clients', $clients)
		->build('clients');
	}

	public function view_client($client_id)
	{
	//GET DATA FROM MODEL
		$client = $this->clients->get_client($client_id);

	//RENDER VIEW
		$this->template
		->append_css('module::style.css', 'dashboard')
		->append_css('module::buttons.css', 'dashboard')
		->title('View client')
		->set('client', $client)
		->build('view_client');
	}

	public function delete_client($client_id)
	{
	
		$affected_rows = $this->clients->delete_client($client_id);
		
		if ($affected_rows > 0)
		{
			$this->session->set_flashdata('success', sprintf(lang('snow.client.delete_success'), '') );
			redirect('dashboard/clients');
		}else
		{
			$this->session->set_flashdata('error', sprintf(lang('snow.client.delete_error'), '') );
			redirect('dashboard/clients');
		}

	}

	public function add_supplier()
	{
		//GET SUBMITED DATA, PASS IT TO THE MODEL FOR INSERTING TO DB AND RETURN A STATUS
		$supplier_data = array(
					'name'                 => $this->input->post('name'),
					'industry'             => $this->input->post('industry'),
					'email'                => $this->input->post('email'),
					'tel'                  => $this->input->post('tel'),
					'address'              => $this->input->post('address'),
					'city'                 => $this->input->post('city'),
					'country'              => $this->input->post('country'),
					'contact_person'       => $this->input->post('contact_person'),
					'contact_person_email' => $this->input->post('contact_person_email'),
					'contact_person_tel'   => $this->input->post('contact_person_tel')
				);

		if($supplier_data['name']){

			$inserted_record_id = $this->suppliers->insert_supplier($supplier_data); //the id of the inserted record
			if ($inserted_record_id != 0)
			{

				$this->session->set_flashdata('success', sprintf(lang('snow.supplier.add_success'), $supplier_data['name']) );
				redirect($this->uri->uri_string);
			}
			else
			{
				$this->session->set_flashdata('error', lang('snow.supplier.add_error') );
				redirect($this->uri->uri_string);
			}
		}

		//RENDER VIEW
		$this->template
		->append_css('module::style.css', 'dashboard')
		->append_css('module::buttons.css', 'dashboard')
		->title('Add Supplier')
		->build('add_supplier');
	}

	public function edit_supplier($supplier_id)
	{
	//GET DATA FROM MODEL
		$supplier = $this->suppliers->get_supplier($supplier_id);

	//GET SUBMITED DATA, PASS IT TO THE MODEL FOR UPDATING TO DB AND RETURN A STATUS
		$supplier_data = array(
			'id'                   => $this->input->post('supplier_id'),
			'name'                 => $this->input->post('name'),
			'industry'             => $this->input->post('industry'),
			'email'                => $this->input->post('email'),
			'tel'                  => $this->input->post('tel'),
			'address'              => $this->input->post('address'),
			'city'                 => $this->input->post('city'),
			'country'              => $this->input->post('country'),
			'contact_person'       => $this->input->post('contact_person'),
			'contact_person_email' => $this->input->post('contact_person_email'),
			'contact_person_tel'   => $this->input->post('contact_person_tel')
		);

		if($supplier_data['id']){

			$affected_rows = $this->suppliers->update_supplier($supplier_data); 
			if ($affected_rows > 0)
			{

				$this->session->set_flashdata('success', sprintf(lang('snow.supplier.update_success'), $supplier_data['name']) );
				redirect($this->uri->uri_string);
			}
			else
			{
				$this->session->set_flashdata('error', lang('snow.supplier.update_error') );
				redirect($this->uri->uri_string);
			}
		}

	//RENDER VIEW
		$this->template
		->append_css('module::style.css', 'dashboard')
		->append_css('module::buttons.css', 'dashboard')
		->title('Edit Supplier')
		->set('supplier', $supplier)
		->set('supplier_id', $supplier_id)
		->build('edit_supplier');
	}

	public function suppliers()
	{
	//GET DATA FROM MODEL
		$suppliers = $this->suppliers->get_all_suppliers();

	//RENDER VIEW
		$this->template
		->append_css('module::style.css', 'dashboard')
		->append_css('module::buttons.css', 'dashboard')
		->append_css('module::blue_tablesorter_style.css', 'dashboard')
		->append_js('module::jquery.tablesorter.min.js', 'dashboard')
		->title('Suppliers')
		->set('suppliers', $suppliers)
		->build('suppliers');
	}

	public function view_supplier($supplier_id)
	{
	//GET DATA FROM MODEL
		$supplier = $this->suppliers->get_supplier($supplier_id);

	//RENDER VIEW
		$this->template
		->append_css('module::style.css', 'dashboard')
		->append_css('module::buttons.css', 'dashboard')
		->title('View Supplier')
		->set('supplier', $supplier)
		->build('view_supplier');
	}

	public function delete_supplier($supplier_id)
	{
	
		$affected_rows = $this->suppliers->delete_supplier($supplier_id);
		
		if ($affected_rows > 0)
		{
			$this->session->set_flashdata('success', sprintf(lang('snow.supplier.delete_success'), '') );
			redirect('dashboard/suppliers');
		}else
		{
			$this->session->set_flashdata('error', sprintf(lang('snow.supplier.delete_error'), '') );
			redirect('dashboard/suppliers');
		}

	}

	public function send_quotation($inquiry_id)
	{

		require_once 'addons/shared_addons/libraries/swift_mailer/lib/swift_required.php';

	//GET DATA FROM MODELS
		$inquiry = $this->inquiry_m->get_inquiry($inquiry_id);
		$products_inquired = $this->inquiry_m->get_products_inquired($inquiry_id);
		$suppliers = $this->suppliers->get_all_suppliers();

		foreach ($suppliers as $supplier) {
			$industry_key = $supplier['industry'];
			$supplier_key = $supplier['id'].'->'.$supplier['name'].'->'.$supplier['email'];
			//echo "<br />";
			if (!isset($myArr[$industry_key])) {
				$myArr[$industry_key] = array($supplier_key => $supplier['name']);
			} else {
				$myArr[$industry_key][$supplier_key] = $supplier['name'];
			}
		}

		$date = date('Y-m-d H:i:s', time());


	//send quotation
		if($this->input->post('suppliers')){
		
			$selected_suppliers = $this->input->post('suppliers');

			//extract supplier's ids
			foreach ($selected_suppliers as $selected_supplier) {
				$selected_supplier = explode('->', $selected_supplier);
				$selected_supplier_ids[] = $selected_supplier[0];
			}

			//save quotation record to db
			$quotation_data  = array('inquiry_id' => $inquiry_id,
									 'suppliers'  => implode(',', $selected_supplier_ids),
									 'sales_rep'  => $this->current_user->id,
									 'date'       => $date
									 );
			$inserted_record_id = $this->quotations->send_quotation($quotation_data);


			foreach ($selected_suppliers as $selected_supplier) {
				
				$selected_supplier = explode('->', $selected_supplier);
				$selected_supplier_name = $selected_supplier[1];
				$selected_supplier_email = $selected_supplier[2];

				$body = '<div id="contact_info" style="font-weight:bold;">
		                    Snow International Trading LImited<br>
		                    Suite 1508, 15/F, Empress Plaza<br>
		                    17-19 Chatham Road South<br>
		                    Tsim Sha Tsui<br>
		                    Kowloon<br>
		                    Hong Kong<br><br>

		                </div>';

		        $body .='<table>
	                        <tr>
	                            <td><span style="font-weight:bold;">Sales Ref</span></td>
	                            <td>'.$inquiry[0]['sales_ref'].'</td>
	                        </tr>
	                        <tr>
	                            <td><span style="font-weight:bold;">To</span></td>
	                            <td>'.$selected_supplier_name.'</td>
	                        </tr>
	                        <tr><td><span style="font-weight:bold;">Date</span></td><td>'.$date.'</td></tr>
	                        <tr><td><br /></td></tr>
	                    </table>';

	            $body .= '<table id="myTable" class="tablesorter" width="100%" border="1">
					        <thead>
					            <tr>
					                <th width="3%">#</th>
					                <th>Item</th>
					                <th>Part No/ Pack Size</th>
					                <th>Quantity (Ltrs/kgs - if agrochemicals; pcs if motor parts or other items)</th>
					                <th>Terms - CIF/ FOB</th>
					                <th>IF CIF - PORT?</th>
					            </tr>
					        </thead>
					        <tbody>
					            <tbody>';

					            $i =1;
			                    foreach ($products_inquired as $product_inquired) {

			                        $body .= "<tr>
			                                <td>".$i."</td>
			                                <td>".$product_inquired['item']."</td>
			                                <td>".$product_inquired['part_no']."</td>
			                                <td>".$product_inquired['quantity']."</td>
			                                <td>".$product_inquired['terms']."</td>
			                                <td>".$product_inquired['cif_port']."</td>
			                              </tr>";
			                    	$i++;
			                    }

			    $quotation_id = base64_encode($inserted_record_id);
			    $selected_supplier_id = base64_encode($selected_supplier[0]);
			    $supplier_entry_link = BASE_URL.'dashboard/supplier_entry/?quotation='.$quotation_id.'&supplier='.$selected_supplier_id;
			    
			    $body .= '<tr><td colspan="6"><br /><br /></td></tr>
			    		<tr>
		                	<td colspan="6"> <a href="'.$supplier_entry_link.'">Click here to complete quotation '.$supplier_entry_link.'</a> </td>
		              	</tr>';
			    
			    $body .= '</tbody></table>';      

				$this->email->from('kk@snow-trading.com', 'Snow International Trading LTD');
				$this->email->to($selected_supplier_email);

				$this->email->subject('Snow International Trading LTD Quotation');
				$this->email->message($body);

				$this->email->send();

				//echo $this->email->print_debugger();
				$this->email->clear(TRUE);




			 	// $subject = 'Snow International Trading LTD Quotation';
				// $to_email = 'To: '.$selected_supplier_name.'<'.$selected_supplier_email.'>';
				// $headers = "MIME-Version: 1.0" . "\r\n";
				// //$headers .= 'Cc: wamugu@gmail.com' . "\r\n";
				// $headers .= "Content-type:text/html;charset=iso-8859-1" . "\r\n";
				// $headers .= 'From: Snow International Trading LTD <kk@snow-trading.com>';
				// $headers .= "Reply-To: kk@snow-trading.com\r\n";
				// $headers .= "Return-Path: kk@snow-trading.com\r\n";
				// mail($to_email, $subject, $body, $headers);




				// $transport = Swift_SmtpTransport::newInstance('smtp.gmail.com', 465, "ssl")
				//   ->setUsername('dev@velamedia.biz')
				//   ->setPassword('17593o0o');

				// $mailer = Swift_Mailer::newInstance($transport);

				// $message = Swift_Message::newInstance('Snow International Trading LTD Quotation')
				//   ->setFrom(array('kk@snow-trading.com' => 'Snow International Trading LTD'))
				//   ->setTo(array($selected_supplier_email => $selected_supplier_name))
				//   ->setReplyTo(array('kk@snow-trading.com' => 'Snow International Trading LTD'))
				//   ->setContentType('text/html')
				//   ->setBody($body, 'text/html');

				// $result = $mailer->send($message);
			}

			if($inserted_record_id){
				$this->session->set_flashdata('success', 'Quotation successfully submited.');
				redirect('dashboard/quotations');
			}else{
				$this->session->set_flashdata('error', 'There already exists a quotation for that inquiry');
				redirect('dashboard/inquiries');
			}
			
		}

	//RENDER VIEW
		$this->template
		->append_css('module::style.css', 'dashboard')
		->append_css('module::buttons.css', 'dashboard')
		->append_css('module::blue_tablesorter_style.css', 'dashboard')
		->append_css('module::chosen.css', 'dashboard')
		->append_js('module::jquery.tablesorter.min.js', 'dashboard')
		->append_js('module::chosen.jquery.js', 'dashboard')
		->title('Send quotation')
		->set('inquiry', $inquiry)
		->set('suppliers', $myArr)
		//->set('selected_suppliers', $selected_suppliers)
		->set('products_inquired', $products_inquired)
		->build('send_quotation');
	}

	public function regenerate_inquiry($inquiry_id)
	{

	//GET DATA FROM MODELS
		$inquiry = $this->inquiry_m->get_inquiry($inquiry_id);
		$products_inquired = $this->inquiry_m->get_products_inquired($inquiry_id);
		$clients = $this->clients->get_all_clients();
		$sales_ref = $this->regenerate_salesref($inquiry[0]['customer']);

	//GET SUBMITED DATA, PASS IT TO THE MODEL FOR INSERTING TO DB AND RETURN A STATUS
	$inquiry_metadata = array(
				'sales_ref'        => $this->input->post('sales_ref'),
				'title'            => $this->input->post('title'),
				'customer'         => $this->input->post('customer'),
				'location'         => $this->input->post('location'),
				'date_of_inquiry'  => format_date(strtotime($this->input->post('date_of_inquiry')), 'Y-m-d H:i:s'),
				'product_type'     => $this->input->post('product_type')
			);

	$items_inquired = $this->input->post('item');
	$items_part_no  = $this->input->post('part_no');
	$items_quantities = $this->input->post('quantity');
	$items_terms  = $this->input->post('terms');
	$cif_port  = $this->input->post('cif_port');

	if($inquiry_metadata['sales_ref']){

		$inserted_record_id = $this->inquiry_m->insert_inquiry($inquiry_metadata); //the id of the inserted record
		if ($inserted_record_id != 0)
		{
			//insert the items/products inquired to the database
			$i = 0;
			foreach ($items_inquired as $item_inquired) {
				
				//check if row is empty
				if($item_inquired != ''){
					$product_inquired = array(
						'inquiry_id'     => $inserted_record_id,
						'item'           => $item_inquired,
						'part_no'        => $items_part_no[$i],
						'quantity'       => $items_quantities[$i],
						'terms'          => $items_terms[$i],
						'cif_port'       => $cif_port[$i],
						'accepted_price' => '',
						'accepted_quantity' => '',
						'supplier_id'    => 0
					);

					$this->inquiry_m->insert_products_inquired($product_inquired);
				}

				$i++;
			}

			$this->session->set_flashdata('success', sprintf(lang('snow.inquiry.add_success'), $this->input->post('title')) );
			redirect($this->uri->uri_string);
		}
		else
		{
			$this->session->set_flashdata('error', lang('snow.inquiry.add_error') );
			redirect($this->uri->uri_string);
		}
	}

	//RENDER VIEW
		$this->template
		->append_css('module::style.css', 'dashboard')
		->append_css('module::buttons.css', 'dashboard')
		->append_css('module::jquery-ui-1.8.18.custom.css', 'dashboard')
		->append_css('module::blue_tablesorter_style.css', 'dashboard')
		->append_css('module::chosen.css', 'dashboard')
		->append_css('module::screen.css', 'dashboard')
		->append_js('module::chosen.jquery.js', 'dashboard')
		->append_js('module::jquery.ui.core.js', 'dashboard')
		->append_js('module::jquery.ui.datepicker.js', 'dashboard')
		->append_js('module::jquery.validate.min.js', 'dashboard')
		->title('Regenerate inquiry')
		->set('inquiry', $inquiry)
		->set('clients', $clients)
		->set('sales_ref', $sales_ref)
		->set('products_inquired', $products_inquired)
		->build('regenerate_inquiry');
	}

	public function view_sent_inquiry($inquiry_id)
	{
	//GET DATA FROM MODEL
		$inquiry = $this->inquiry_m->get_inquiry($inquiry_id);
		$products_inquired = $this->inquiry_m->get_products_inquired($inquiry_id);
		$inquiry_quotation = $this->quotations->get_quotation_by_inquiry($inquiry_id);
	
	//RENDER VIEW
		$this->template
		->append_css('module::style.css', 'dashboard')
		->append_css('module::blue_tablesorter_style.css', 'dashboard')
		->append_js('module::jquery.tablesorter.min.js', 'dashboard')
		->title('Confirmed inquiry')
		->set('inquiry', $inquiry)
		->set('inquiry_quotation', $inquiry_quotation)
		->set('products_inquired', $products_inquired)
		->build('view_sent_inquiry');
	}

	public function quotations()
	{
	//GET DATA FROM MODEL
		$quotations = $this->quotations->get_all_quotations();

	//RENDER VIEW
		$this->template
		->append_css('module::style.css', 'dashboard')
		->append_css('module::blue_tablesorter_style.css', 'dashboard')
		->append_js('module::jquery.tablesorter.min.js', 'dashboard')
		->title('Quotations')
		->set('quotations', $quotations)
		->build('quotations');
	}

	public function supplier_entry()
	{

		parse_str($_SERVER['QUERY_STRING'],$_GET);
		//get the passed url variables
		$quotation_id = base64_decode($this->input->get('quotation'));
		$supplier = base64_decode($this->input->get('supplier'));
		
		$date = date('Y-m-d H:i:s', time());

	//GET DATA FROM MODEL
		$quotation = $this->quotations->get_quotation($quotation_id);
		$inquiry = $this->inquiry_m->get_inquiry($quotation->inquiry_id);
		$products_inquired = $this->inquiry_m->get_products_inquired($quotation->inquiry_id);
		$supplier = $this->suppliers->get_supplier($supplier);

	//PASS SUBMITED DATA TO MODEL FOR SAVING TO THE DB
		$quotation_id = $this->input->post('quotation_id');
		$supplier_id = $this->input->post('supplier');
		$payment_terms = $this->input->post('payment_terms');
		$currency = $this->input->post('currency');
		$validity_of_quotation = $this->input->post('validity_of_quotation');
		$item_ids = $this->input->post('item_id');
		$item_prices = $this->input->post('price');
		$quantity_offered = $this->input->post('quantity_offered');
		$other_comments = $this->input->post('other_comments');

		if($item_prices){
			$i = 0;
			foreach ($item_prices as $item_price) {
				$supplier_entry_data  = array(
					'quotation_id'         => $quotation_id,
					'item_id'              => $item_ids[$i],
					'price'                => $item_price,
					'quantity_offered'     => $quantity_offered[$i],
					//'date_of_availability' => format_date(strtotime($date_of_availability[$i]), 'Y-m-d H:i:s'),
					'supplier_id'          => $supplier->id,
					'payment_terms'        => $payment_terms,
					'currency'        	   => $currency,
					'validity_of_quotation'=> format_date(strtotime($validity_of_quotation), 'Y-m-d'),
					'other_comments'       => $other_comments
					);

				$this->supplier_entries->insert_supplier_entry($supplier_entry_data);
				$i++;
			}

			$this->session->set_flashdata('success', sprintf(lang('snow.supplier_entry.add_success')));
			$url = $this->uri->uri_string.'?quotation='.$this->input->get('quotation').'&supplier='.$this->input->get('supplier');
			redirect($url);
		}

	//RENDER VIEW
		$this->template
		->append_css('module::style.css', 'dashboard')
		->append_css('module::buttons.css', 'dashboard')
		->append_css('module::jquery-ui-1.8.18.custom.css', 'dashboard')
		->append_css('module::blue_tablesorter_style.css', 'dashboard')
		->append_css('module::chosen.css', 'dashboard')
		->append_css('module::screen.css', 'dashboard')
		->append_js('module::chosen.jquery.js', 'dashboard')
		->append_js('module::jquery.ui.core.js', 'dashboard')
		->append_js('module::jquery.ui.datepicker.js', 'dashboard')
		->append_js('module::jquery.validate.min.js', 'dashboard')
		->title('Supplier Entry Page')
		->set('quotation', $quotation)
		->set('inquiry', $inquiry)
		->set('products_inquired', $products_inquired)
		->set('supplier', $supplier)
		->build('supplier_entry');
	}

	public function supplier_entries()
	{
	//GET DATA FROM MODEL
		$supplier_entries = $this->supplier_entries->get_all_supplier_entries();

	//RENDER VIEW
		$this->template
		->append_css('module::style.css', 'dashboard')
		->append_css('module::blue_tablesorter_style.css', 'dashboard')
		->append_js('module::jquery.tablesorter.min.js', 'dashboard')
		->title('Supplier Entries')
		->set('supplier_entries', $supplier_entries)
		->build('supplier_entries');
	}


	public function view_supplier_entry()
	{

		//get the passed url variables
		$quotation_id = $this->uri->segment(4);
		$supplier = $this->uri->segment(6);
		
		$date = date('Y-m-d H:i:s', time());

	//GET DATA FROM MODEL
		$quotation = $this->quotations->get_quotation($quotation_id);
		$inquiry = $this->inquiry_m->get_inquiry($quotation->inquiry_id);
		$products_inquired = $this->inquiry_m->get_products_inquired($quotation->inquiry_id);
		$supplier = $this->suppliers->get_supplier($supplier);

	//RENDER VIEW
		$this->template
		->append_css('module::style.css', 'dashboard')
		->append_css('module::blue_tablesorter_style.css', 'dashboard')
		->title('Supplier Entry')
		->set('quotation', $quotation)
		->set('inquiry', $inquiry)
		->set('products_inquired', $products_inquired)
		//->set('supplier_entries', $supplier_entries)
		->set('supplier', $supplier)
		->build('view_supplier_entry');
	}

	public function quotation_comparison($quotation_id)
	{
	//GET DATA FROM MODEL
		$quotation = $this->quotations->get_quotation($quotation_id);
		$inquiry = $this->inquiry_m->get_inquiry($quotation->inquiry_id);
		$products_inquired = $this->inquiry_m->get_products_inquired($quotation->inquiry_id);
		$supplier_ids = $this->quotation_comparison->get_suppliers($quotation_id); //get the suppliers who have submited their quotes

		$suppliers = $this->suppliers->get_suppliers($supplier_ids);
		$client = $this->clients->get_client($inquiry[0]['customer']);
	
	//Confirm quotation save selected prices from all suppliers who submitted
		$accepted_quotes = $this->input->post('accepted'); //array with item_id as index and supplier quoted price as value
		$accepted_quantities = array_values(array_filter($this->input->post('accepted_quantity')));
		$item_ids = $this->input->post('item_id');
		$selected_supplier_ids = array_values(array_filter($this->input->post('product_selected_supplier')));
		$inquiry_id = $this->input->post('inquiry_id');

		$result = $this->quotation_comparison->confirm_quotation($quotation_id,$accepted_quotes,$accepted_quantities,$item_ids,$selected_supplier_ids);
		
		if($item_ids){ //run this block of code only when from the quotation comparison page, i.e when confirm quotation is clicked 
			if($result){

				//update the inquiry status to 'Confirmed'
				$data = array('status' => 1);
				$where = "id = '$inquiry_id'";
				$update_query = $this->db->update_string($this->db->dbprefix('snow_inquiries'), $data, $where); 
				$this->db->query($update_query);

				$this->session->set_flashdata('success', lang('snow.quotation_confirmation_success') );
				//redirect('dashboard/confirmed_orders');
				redirect('dashboard/confirmed_inquiry/'.$inquiry_id);
			}else{
				$this->session->set_flashdata('error', lang('snow.quotation_confirmation_error') );
				redirect('dashboard/quotations');
			}
		}
	//RENDER VIEW
		$this->template
		->append_css('module::style.css', 'dashboard')
		->append_css('module::buttons.css', 'dashboard')
		->append_css('module::blue_tablesorter_style.css', 'dashboard')
		->append_js('module::jquery.tablesorter.min.js', 'dashboard')
		->title('Quotation comparison')
		->set('quotation', $quotation)
		->set('inquiry', $inquiry)
		->set('products_inquired', $products_inquired)
		->set('suppliers', $suppliers)
		->set('client', $client)
		->set('inquiry_id', $quotation->inquiry_id)
		->build('quotation_comparison');
	}

	public function confirmed_inquiry($inquiry_id)
	{
	//GET DATA FROM MODEL
		$inquiry = $this->inquiry_m->get_inquiry($inquiry_id);
		$products_inquired = $this->inquiry_m->get_products_inquired($inquiry_id);
		$confirmed_supplier_ids = $this->inquiry_m->get_confirmed_suppliers($inquiry_id);
		$confirmed_suppliers = $this->suppliers->get_suppliers($confirmed_supplier_ids);
		
		$first_select_option = array(0 => 'Select a supplier');
		foreach ($confirmed_suppliers as $supplier) {
			$confirmed_suppliers_array[$supplier->id] = $supplier->name; 
		}
		$confirmed_suppliers_array = $first_select_option + $confirmed_suppliers_array;

		//if user clicks on genereate purchase order
		if($this->input->post()){
			$supplier_id = $this->input->post('supplier');
			redirect("dashboard/purchase_order/inquiry/$inquiry_id/supplier/$supplier_id");
		}

	//RENDER VIEW
		$this->template
		->append_css('module::style.css', 'dashboard')
		->append_css('module::buttons.css', 'dashboard')
		->append_css('module::blue_tablesorter_style.css', 'dashboard')
		->append_css('module::chosen.css', 'dashboard')
		->append_js('module::chosen.jquery.js', 'dashboard')
		->append_js('module::jquery.tablesorter.min.js', 'dashboard')
		->title('Confirmed inquiry')
		->set('inquiry', $inquiry)
		->set('confirmed_suppliers_array', $confirmed_suppliers_array)
		->set('products_inquired', $products_inquired)
		->build('confirmed_inquiry');
	}

	public function purchase_order()
	{
	//GET URL PARAMS
		$inquiry_id = $this->uri->segment(4);
		$supplier_id = $this->uri->segment(6);

	//GET DATA FROM MODEL
		$inquiry                = $this->inquiry_m->get_inquiry($inquiry_id);
		$products_qouted        = $this->inquiry_m->get_products_qouted($inquiry_id, $supplier_id);
		$supplier_currency      = $this->inquiry_m->get_supplier_quotation_currency($inquiry_id, $supplier_id);
		$supplier_payment_terms = $this->inquiry_m->get_supplier_quotation_payment_terms($inquiry_id, $supplier_id);
		$supplier               = $this->suppliers->get_supplier($supplier_id);

	//GENERATE AN EXCEL FILE OF THE SUPPLIER PURCHASE ORDER
		if($this->input->post()){
			$purchase_order = $this->purchase_order->generate_purchase_order($inquiry_id, $supplier_id);

		}

	//RENDER VIEW
		$this->template
		->append_css('module::style.css', 'dashboard')
		->append_css('module::buttons.css', 'dashboard')
		->append_css('module::screen.css', 'dashboard')
		->append_css('module::blue_tablesorter_style.css', 'dashboard')
		->append_css('module::jquery-ui-1.8.18.custom.css', 'dashboard')
		->append_js('module::jquery.tablesorter.min.js', 'dashboard')
		->append_js('module::jquery.ui.core.js', 'dashboard')
		->append_js('module::jquery.ui.datepicker.js', 'dashboard')
		->append_js('module::jquery.validate.min.js', 'dashboard')
		->title('Purchase Order')
		->set('inquiry', $inquiry)
		->set('supplier', $supplier)
		->set('supplier_currency', $supplier_currency)
		->set('supplier_payment_terms', $supplier_payment_terms)
		->set('products_qouted', $products_qouted)
		->build('purchase_order');
	}

	public function confirmed_orders()
	{
	//GET DATA FROM MODEL
		$confirmed_inquiries = $this->inquiry_m->get_confirmed_inquiries();

	//RENDER VIEW
		$this->template
		->append_css('module::style.css', 'dashboard')
		->append_css('module::buttons.css', 'dashboard')
		->append_css('module::blue_tablesorter_style.css', 'dashboard')
		->append_js('module::jquery.tablesorter.min.js', 'dashboard')
		->title('Confirmed Orders')
		->set('confirmed_inquiries', $confirmed_inquiries)
		->build('confirmed_orders');
	}

	public function unconfirmed_orders()
	{
	//GET DATA FROM MODEL
		$unconfirmed_inquiries = $this->inquiry_m->get_unconfirmed_inquiries();

	//RENDER VIEW
		$this->template
		->append_css('module::style.css', 'dashboard')
		->append_css('module::buttons.css', 'dashboard')
		->append_css('module::blue_tablesorter_style.css', 'dashboard')
		->append_js('module::jquery.tablesorter.min.js', 'dashboard')
		->title('Unconfirmed Orders')
		->set('unconfirmed_inquiries', $unconfirmed_inquiries)
		->build('unconfirmed_orders');
	}

	public function clients_records()
	{

	//GET DATA FROM MODEL
		$client_records = $this->client_records->get_all_client_records();
	
	//RENDER VIEW
		$this->template
		->append_css('module::style.css', 'dashboard')
		->append_css('module::buttons.css', 'dashboard')
		->append_css('module::blue_tablesorter_style.css', 'dashboard')
		->append_js('module::jquery.tablesorter.min.js', 'dashboard')
		->title('Clients Records')
		->set('client_records', $client_records)
		->build('clients_records');
	}

	public function add_client_record()
	{
	
	//GET DATA FROM MODEL
		$sales_refs = $this->client_records->get_sales_refs();
		$clients = $this->clients->get_all_clients();
		$client = $this->clients->get_client($this->input->post('client'));
	
	//PASS SUBMITED DATA TO MODEL FOR SAVING TO THE DB
		$customer        = $this->input->post('client');
		//$sales_ref       = $this->input->post('sales_ref');
		$initial_contact = $this->input->post('initial_contact');
		$location        = $this->input->post('location');
		$address         = $this->input->post('address');
		$customer_type   = $this->input->post('customer_type');
		$industry 		 = $this->input->post('industry');
		$tel 			 = $this->input->post('tel');
		$last_contact 	 = $this->input->post('last_contact');
		$contact_method  = $this->input->post('contact_method');
		$feedback 		 = $this->input->post('feedback');

		$client_data     = array(
							'customer'        => $customer,
							'sales_ref'       => '',
							'sales_rep'       => $this->current_user->id,
							'initial_contact' => $initial_contact,
							'location'        => $location,
							'address'         => $address,
							'customer_type'   => $customer_type,
							'industry' 		  => $industry,
							'tel' 			  => $tel,
							'last_contact' 	  => $last_contact,
							'contact_method'  => $contact_method,
							'feedback' 		  => $feedback
							);
		if($initial_contact){ //run this block of code only when there is submited data

			$inserted_record_id = $this->client_records->insert_client_record($client_data);

			if($inserted_record_id){
				$this->session->set_flashdata('success', lang('snow.client_record.add_success') );
				redirect($this->uri->uri_string);
			}else{
				$this->session->set_flashdata('error', lang('snow.client_record.add_error') );
				redirect($this->uri->uri_string);
			}
		}

	//RENDER VIEW
		$this->template
		->append_css('module::style.css', 'dashboard')
		->append_css('module::buttons.css', 'dashboard')
		->append_css('module::jquery-ui-1.8.18.custom.css', 'dashboard')
		->append_css('module::chosen.css', 'dashboard')
		->append_js('module::chosen.jquery.js', 'dashboard')
		->append_js('module::jquery.ui.core.js', 'dashboard')
		->append_js('module::jquery.ui.datepicker.js', 'dashboard')
		->title('Add Client Record')
		//->set('sales_refs', $sales_refs)
		->set('clients', $clients)
		->set('client', $client)
		->build('add_client_record');
	}

	public function client_record($id)
	{

	//GET DATA FROM MODEL
		$client_record = $this->client_records->get_client_record($id);
		$sales_refs = $this->client_records->get_sales_refs();

	//PASS SUBMITED DATA TO MODEL FOR UPDATING THE RECORD ON THE DB
		$client_record_id = $this->input->post('client_record_id');
		$initial_contact = $this->input->post('initial_contact');
		$location        = $this->input->post('location');
		$address         = $this->input->post('address');
		$customer_type   = $this->input->post('customer_type');
		$industry 		 = $this->input->post('industry');
		$tel 			 = $this->input->post('tel');
		$last_contact 	 = $this->input->post('last_contact');
		$contact_method  = $this->input->post('contact_method');
		$feedback 		 = $this->input->post('feedback');

		$client_data     = array(
							
							'sales_rep'       => $this->current_user->id,
							'initial_contact' => $initial_contact,
							'location'        => $location,
							'address'         => $address,
							'customer_type'   => $customer_type,
							'industry' 		  => $industry,
							'tel' 			  => $tel,
							'last_contact' 	  => $last_contact,
							'contact_method'  => $contact_method,
							'feedback' 		  => $feedback
							);
		//print_r($client_data);
		if($client_data['initial_contact']){ //run this block of code only when there is submited data

			$update = $this->client_records->update_client_record($client_data,$client_record_id);

			if($update){
				$this->session->set_flashdata('success', lang('snow.client_record.update_success') );
				redirect($this->uri->uri_string);
			}else{
				$this->session->set_flashdata('error', lang('snow.client_record.update_error') );
				redirect($this->uri->uri_string);
			}
		}
	
	//RENDER VIEW
		$this->template
		->append_css('module::style.css', 'dashboard')
		->append_css('module::buttons.css', 'dashboard')
		->append_css('module::jquery-ui-1.8.18.custom.css', 'dashboard')
		->append_css('module::chosen.css', 'dashboard')
		->append_js('module::chosen.jquery.js', 'dashboard')
		->append_js('module::jquery.ui.core.js', 'dashboard')
		->append_js('module::jquery.ui.datepicker.js', 'dashboard')
		->title('Client Record')
		->set('client_record_id', $id)
		->set('client_record', $client_record)
		->set('sales_refs', $sales_refs)
		->build('client_record');
	}

	public function delete_client_record($client_record_id)
	{
	
		$affected_rows = $this->client_records->delete_client_record($client_record_id);
		
		if ($affected_rows > 0)
		{
			$this->session->set_flashdata('success', sprintf(lang('snow.client_record.delete_success'), '') );
			redirect('dashboard/clients_records');
		}else
		{
			$this->session->set_flashdata('error', sprintf(lang('snow.client_record.delete_error'), '') );
			redirect('dashboard/clients_records');
		}

	}

}