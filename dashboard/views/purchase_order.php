<?php
if (group_has_role('dashboard', 'view_module')) {

    //get customer details
    $client = $this->clients->get_client($inquiry[0]['customer']);

    $expiry_date = array('id'=>'expiry_date','name'=>'expiry_date','type'=>'text');

    $form_attributes = array('id' => 'purchase_order_form');
    echo form_open(uri_string(), $form_attributes); 
?>

    <table width="100%">
        <!-- <tr>
            <td width="11%"><span class="labels">Sales Rep</span></td>
            <td><?php echo $sales_rep_user_data->username; ?></td>
        </tr> -->
        <tr>
            <td width="15%"><span class="labels">Sales Ref</span></td>
            <td><?php echo $inquiry[0]['sales_ref']; ?></td>
        </tr>
        <!-- <tr>
            <td><span class="labels">Customer name</span></td>
            <td><?php echo $client->name; ?></td>
        </tr> -->
        <tr>
            <td><span class="labels">Date</span></td>
            <td><?php echo date('Y-m-d'); ?></td>
        </tr>
        <tr>
            <td><span class="labels">To (Supplier)</span></td>
            <td><?php echo $supplier->name; ?></td>
        </tr>
        <?php if($inquiry[0]['product_type'] == 'Agro-chemicals'){ ?>
        <tr>
            <td><span class="labels">Expiry Date</span></td>
            <td><?php echo form_input($expiry_date); ?></td>
        </tr>
        <?php } ?>
        <tr><td><br /></td></tr>
    </table>
    
    <table width="100%">
        <tr>
            <td><span class="labels">Please Supply the following goods as per your quotation.</span></td>
        </tr>
    </table>

    <table id="myTable" class="tablesorter" width="100%">
        <thead>
            <tr>
                <th width="2%">#</th>
                <th width="25%">Item</th>
                <th>Part No/ Pack Size</th>
                <th width="20%">Quantity (Ltrs/kgs - if agrochemicals; pcs if motor parts or other items)</th>
                <th>Price per Unit</th>
                <th>Total Amount</th>
            </tr>
        </thead>
        <tbody>
            <tbody>
                <?php 
                    $i =1;
                    foreach ($products_qouted as $product_qouted) {
                        $amount = $product_qouted['accepted_quantity']*$product_qouted['accepted_price'];
                        $total_amount +=$amount;
                        $cif_port = $product_qouted['cif_port'];

                        echo "<tr>
                                <td>".$i."</td>
                                <td>".$product_qouted['item']."</td>
                                <td>".$product_qouted['part_no']."</td>
                                <td>".$product_qouted['accepted_quantity']."</td>
                                <td>".number_format($product_qouted['accepted_price'],2)."</td>
                                <td>".number_format($amount,2)."</td>
                              </tr>";
                    $i++;
                    }
                    echo "<tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td><b>".$supplier_currency." ".number_format($total_amount,2)."</b></td>
                          </tr>";
                ?>

        </tbody>
    </table>

    <table width="100%">
        <tr>
            <td><span class="labels"><br />TERMS AND CONDITIONS :</span></td>
        </tr>
        <tr>
            <td><span>PLEASE MENTION OUR PURCHASE ORDER REFERENCE IN ALL YOUR DISPATCH DOCUMENTS</span></td>
        </tr>
        <tr>
            <td><br /></td>
        </tr>
    </table>
    <table width="100%">
        <tr>
            <td width="15%"><span class="labels">Payment terms:</span></td>
            <td><?php echo $supplier_payment_terms; ?></td>
        </tr>
        <tr>
            <td><span class="labels">Port of Discharge:</span></td>
            <td>
                <?php 
                    if($inquiry[0]['product_type'] != 'Motor Parts'){ 
                        echo $cif_port;
                    }else{
                        echo "FOB";
                    }
                ?>
            </td>
        </tr>
        <tr>
            <td><span class="labels">Consignee:</span></td>
            <td>MUKPAR TANZANIA LIMITED, P O BOX 16, MWANZA, TANZANIA</td>
        </tr>
        <tr>
            <td valign="top"><span class="labels">Other Conditions:</span></td>
            <td><textarea name="other_conditions" cols="135" rows="5"></textarea></td>
        </tr>
    </table>
    

    <?php echo form_submit('submit', lang('snow.form_submit'), 'class="btn green"'); ?>
<?php echo form_close(); ?>

<script type="text/javascript">
    $(document).ready(function() 
        { 
            $("#myTable").tablesorter(); 
            jQuery("#expiry_date").datepicker({ changeMonth: true,changeYear: true,dateFormat: 'yy-mm-dd',showOtherMonths: true,showWeek: true,weekHeader: 'Week', altFormat: "DD, d MM, yy" });

            var error_container = $('div.container');
            // validate form
            // $("#purchase_order_form").validate({
            //     rules: {
            //         expiry_date: "required"
            //     },
            //     messages: {
            //         expiry_date: "Please select an expiry date for the Agro-chemicals"
            //     }
            // });
        } 
    ); 
</script>

<?php
}else{
    $this->session->set_flashdata('error', lang('snow.view_error') );
    redirect('access-denied');
}
?>