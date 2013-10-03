<?php defined('BASEPATH') or exit('No direct script access allowed');

/**
 *
 * Generate, view purchase orders for items inquired
 *
 * @author 		www.velamedia.biz
 * @license 	Apache License v2.0
 */
class Purchase_order extends MY_Model {

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

		// Load the required models
		$this->load->model('inquiry_m');
		$this->load->model('suppliers');
		
	}

	public function generate_purchase_order($inquiry_id,$supplier_id)
	{
		include 'addons/shared_addons/modules/dashboard/helpers/excel_xml.php';

		//GET REQUIRED DATA FROM MODEL
		$inquiry                = $this->inquiry_m->get_inquiry($inquiry_id);
		$products_qouted        = $this->inquiry_m->get_products_qouted($inquiry_id, $supplier_id);
		$supplier_currency      = $this->inquiry_m->get_supplier_quotation_currency($inquiry_id, $supplier_id);
		$supplier_payment_terms = $this->inquiry_m->get_supplier_quotation_payment_terms($inquiry_id, $supplier_id);
		$supplier               = $this->suppliers->get_supplier($supplier_id);

		$excel = new excel_xml();

		$header_style = array(
			'bold'  => 1,
			'size'  => '13',
			'color' => '#000',
			'bgcolor' => '#FFFFFF'
		);
		$excel->add_style('header', $header_style);

		$boldrow_style = array(
			'align' => 'Left',
			'bold'  => 1
		);
		$excel->add_style('boldrow_style', $boldrow_style);

		$total_amount_style = array(
			'align' => 'Right',
			'bold'  => 1
		);
		$excel->add_style('total_amount_style', $total_amount_style);

		$columns_header_style = array(
			'bold'  => 1,
			'size'  => '10',
			'color' => '#FFFFFF',
			'bgcolor' => '#731F05'
		);
		$excel->add_style('columns_header', $columns_header_style);

		$row_style = array(
			'align' => 'Right'
		);
		$excel->add_style('row_style', $row_style);

		$excel->add_row(array('', '', 'Snow International Trading LTD Purchase Order|4'), 'header');
		$excel->add_row(array());
		$excel->add_row(array());
		$excel->add_row(array());

		$excel->add_row(array(
			'*Sales Ref:*' ,
			$inquiry[0]['sales_ref'],'','',
			'*Date:*',
			date('Y-m-d')
		));
		$excel->add_row(array());

		$excel->add_row(array(
			'*To (Supplier)*' ,
			$supplier->name
		));

		$excel->add_row(array(
			'*Expiry Date*' ,
			''
		));
		$excel->add_row(array());

		if($inquiry[0]['product_type'] == 'Agro-chemicals'){
			$excel->add_row(array(
				'Please Supply the following goods as per your quotation.|4' ,
				''
			), 'boldrow_style');

			$quantity_label = 'Ltrs/kgs';
		}else{
			$quantity_label = 'pcs';
		}

		$excel->add_row(array(
			'',
			'Item',
			'Part No/ Pack Size',
			'Quantity ('.$quantity_label.')',
			'Price per Unit',
			'Total Amount'
		), 'columns_header');

		$i =1;
        foreach ($products_qouted as $product_qouted) {
            $amount = $product_qouted['accepted_quantity']*$product_qouted['accepted_price'];
            $total_amount +=$amount;
            $cif_port = $product_qouted['cif_port'];

            $data_array = array('', 
        					$product_qouted['item'], 
        					$product_qouted['part_no'],
        					$product_qouted['accepted_quantity'],
        					number_format($product_qouted['accepted_price'],2),
        					number_format($amount,2)
            			   );
			$excel->add_row($data_array, 'row_style');
        $i++;
        }

        $excel->add_row(array(
			'',
			'',
			'',
			'',
			'',
			$supplier_currency." ".number_format($total_amount,2)
		), 'total_amount_style');

        $excel->add_row(array());

		$excel->add_row(array(
				'TERMS AND CONDITIONS :|4'
			), 'boldrow_style');
		$excel->add_row(array(
				'PLEASE MENTION OUR PURCHASE ORDER REFERENCE IN ALL YOUR DISPATCH DOCUMENTS.|5'
			));

		$excel->add_row(array());
		$excel->add_row(array());

		$excel->add_row(array(
			'*Payment terms:*' ,
			$supplier_payment_terms
		));

		if($inquiry[0]['product_type'] != 'Motor Parts'){ 
            $port_of_discharge = $cif_port;
        }else{
            $port_of_discharge = "FOB";
        }
		$excel->add_row(array(
			'*Port of Discharge:*' ,
			$port_of_discharge
		));

		$excel->add_row(array(
			'*Consignee:*' ,
			'MUKPAR TANZANIA LIMITED, P O BOX 16, MWANZA, TANZANIA'
		));

		$excel->add_row(array(
			'*Other Conditions:*' ,
			''
		));

		$excel->create_worksheet("Purchase Order");

		$xml = $excel->generate();

		$filename_salesref = str_replace('/', '_', $inquiry[0]['sales_ref']);
		$filename_supplier = str_replace(' ', '_', $supplier->name);
		$filename_supplier = str_replace(',', ' ', $filename_supplier);
		
		$excel->download('SIT Purchase Order for '.$filename_salesref.' - '.$filename_supplier.'.xls');

	}
	
}