<?php 

//input fields 
$validity_of_quotation = array('id'=>'validity_of_quotation','name'=>'validity_of_quotation','type'=>'text','size'=>'31','class'=>'required'); 

$price = array('id'=>'price','name'=>'price[]','type'=>'text','size'=>'16'); 
$quantity_offered = array('id'=>'quantity_offered','name'=>'quantity_offered[]','type'=>'text','size'=>'16'); 
//$date_of_availability = array('id'=>'date_of_availability','name'=>'date_of_availability[]','type'=>'text','size'=>'13', 'class'=>'date_of_availability'); 

//payment options
$payment_options = array(
    '' => 'Select a payment option',
    '100% Advance'  => '100% Advance',
    '10% Advance, 90% copy of documents'    => '10% &nbsp;Advance, 90% copy of documents',
    '10% Advance, 90% on delivery'   => '10% &nbsp;Advance, 90% on delivery',
    '100% on copy of documents' => '100% on copy of documents',
    '30% Advance, 70% on copy of documents' => '30% &nbsp;Advance, 70% on copy of documents',
    '30 Days OA' => '30 &nbsp;Days OA',
    '60 Days OA' => '60 &nbsp;Days OA',
    '90 Days OA' => '90 &nbsp;Days OA',
    '120 Days OA' => '120 Days OA',
    '150 Days OA' => '150 Days OA',
    '180 Days OA' => '180 Days OA',
    '30 Days DA' => '30 &nbsp;Days DA',
    '60 Days DA' => '60 &nbsp;Days DA',
    '90 Days DA' => '90 &nbsp;Days DA',
    '120 Days DA' => '120 Days DA',
    '150 Days DA' => '150 Days DA',
    '180 Days DA' => '180 Days DA',
    'LC Sight' => 'LC Sight',
    '90 Days LC' => '90 Days LC'
);

//currencies
$currency_options = array(
    '' => 'Select your preferred currency',
    'USD'  => 'US Dollar ($)',
    'INR'    => 'Indian Rupee',
    'ZAR'   => 'South African Rand',
    'CNY' => 'Renminbi (China)',
    'KES' => 'Kenya Shilling'
);

$url = uri_string().'?quotation='.$this->input->get('quotation').'&supplier='.$this->input->get('supplier');

$form_attributes = array('id' => 'supplier_entry_form');
echo form_open($url,$form_attributes); 

parse_str($_SERVER['QUERY_STRING'],$_GET);
//get the passed url variables
$quotation_id = base64_decode($this->input->get('quotation'));
$supplier_id = base64_decode($this->input->get('supplier'));

?>

    <table width="100%">
        <tr>
            <td width="20%"><span class="labels">Sales Ref</span></td>
            <td><?php echo $inquiry[0]['sales_ref']; ?><br /><br /></td>
        </tr>
        <tr>
            <td><span class="labels">Supplier Name</span></td>
            <td><?php echo $supplier->name; ?><br /><br /></td>
        </tr>
        <tr>
            <td><span class="labels">Payment Terms</span></td>
            <td><?php echo form_dropdown('payment_terms', $payment_options,'', 'class="chzn-select required" required id="payment_terms" style="width:307px;"'); ?><br /><br /></td>
        </tr>
        <tr>
            <td><span class="labels">Prefered Currency</span></td>
            <td><?php echo form_dropdown('currency', $currency_options,'', 'class="chzn-select required" required id="currency" style="width:307px;"'); ?><br /><br /></td>
        </tr>
        <tr>
            <td><span class="labels">Validity of quotation till?</span></td>
            <td><?php echo form_input($validity_of_quotation); ?><br /><br /></td>
        </tr>
        <tr><td><br /></td></tr>
    </table>
    <table id="myTable" class="tablesorter" width="100%">
        <thead>
            <tr>
                <th>#</th>
                <th width="">Item</th>
                <th>Part No/ Pack Size</th>
                <th>Quantity (Ltrs/kgs - if agrochemicals; pcs if motor parts or other items)</th>
                <th width="17%">Price per Unit Quantity</th>
                <th width="17%">Quantity Offered</th>
                <!-- <th width="12%">Date of availability</th> -->
            </tr>
        </thead>
        <tbody>
            <tbody>
                <?php 
                    $i =1;
                    foreach ($products_inquired as $product_inquired) {

                        echo "<tr>
                                <td>".$i."</td>
                                <td>
                                    ".$product_inquired['item'].
                                    form_hidden('item_id[]', $product_inquired['id'])."
                                </td>
                                <td>".$product_inquired['part_no']."</td>
                                <td>".$product_inquired['quantity']."</td>
                                <td>".form_input($price)."</td>
                                <td>".form_input($quantity_offered)."</td>
                                
                              </tr>";

                              //<td>".form_input($date_of_availability)."</td>
                       
                    $i++;
                    }
                ?>
        </tbody>
    </table>
    <br />
    <table>
        <tr>
            <td valign="top"><b>Other comments</b></td>
            <td><textarea name="other_comments" cols="135" rows="5"></textarea></td>
        </tr>
    </table>
    <?php echo form_submit('submit', lang('snow.form_submit'), 'class="btn green"'); ?>
    <?php echo form_hidden('quotation_id', $quotation_id); ?>
    <?php echo form_hidden('supplier', $supplier_id); ?>
<?php echo form_close(); ?>

<script type="text/javascript">
    $(document).ready(function() 
        { 
            //$("#myTable").tablesorter();

            $(".chzn-select").chosen();

            jQuery("#validity_of_quotation").datepicker({ changeMonth: true,changeYear: true,dateFormat: 'yy-mm-dd',showOtherMonths: true,showWeek: true,weekHeader: 'Week', altFormat: "DD, d MM, yy" });

            jQuery(".date_of_availability").datepicker({ changeMonth: true,changeYear: true,dateFormat: 'yy-mm-dd',showOtherMonths: true,showWeek: true,weekHeader: 'Week', altFormat: "DD, d MM, yy" });
        
            $.validator.setDefaults({ ignore: ":hidden:not(select)" });

            // validate form
            $("#supplier_entry_form").validate({
                rules: {
                    payment_terms: "required",
                    validity_of_quotation: "required"
                },
                messages: {
                    payment_terms: "Please select your preferred payment terms.",
                    validity_of_quotation: "Please select a date when this quotation is valid till"
                }
            });
        } 
    ); 
</script>
