<?php
if (group_has_role('dashboard', 'view_module')) {
//get customer details
$client = $this->clients->get_client($inquiry[0]['customer']);

echo form_open(uri_string());
?>

    <table width="100%">
        <tr>
            <td width="11%"><span class="labels">Sales Rep</span></td>
            <td>{{user:username}}</td>
        </tr>
        <tr>
            <td><span class="labels">Sales Ref</span></td>
            <td><?php echo $inquiry[0]['sales_ref']; ?></td>
        </tr>
        <tr>
            <td><span class="labels">Customer name</span></td>
            <td><?php echo $client->name; ?></td>
        </tr>
        <tr><td><br /></td></tr>
    </table>

    <fieldset>
        <legend>Generate Purchase Order</legend>
        <table width="100%">
        <tr>
            <td width="10%"><span class="labels">Select supplier</span></td>
            <td width="25%"><?php echo form_dropdown('supplier', $confirmed_suppliers_array,'', 'class="chzn-select" id="supplier" placeholder="Select a supplier" style="width:307px;"'); ?></td>
            <td><?php echo form_submit('submit', 'Generate Purchase Order', 'class="btn green"'); ?></td>
            <td></td>
        </tr>
        <tr><td><br /></td></tr>
    </table>
    </fieldset>
    
    <table id="myTable" class="tablesorter" width="100%">
        <thead>
            <tr>
                <th width="2%">#</th>
                <th width="20%">Item</th>
                <th>Part No/ Pack Size</th>
                <th width="18%">Quantity (Ltrs/kgs - if agrochemicals; pcs if motor parts or other items)</th>
                <th>Terms - CIF/ FOB</th>
                <th>IF CIF - PORT?</th>
                <th>Supplier</th>
                <th>Accepted Price per Unit</th>
                <th>Accepted Quantity</th>
                <th>Total Amount</th>
                
            </tr>
        </thead>
        <tbody>
            <tbody>
                <?php 
                    $i =1;
                    foreach ($products_inquired as $product_inquired) {

                        //get supplier
                        $supplier = $this->suppliers->get_supplier($product_inquired['supplier_id']);

                        //get supplier currency
                        $currency = $this->inquiry_m->get_supplier_quotation_currency($inquiry[0]['id'],$product_inquired['supplier_id']);

                        $amount = $product_inquired['accepted_price']*$product_inquired['accepted_quantity'];
                        $total_amount += $amount;

                        echo "<tr>
                                <td>".$i."</td>
                                <td>".$product_inquired['item']."</td>
                                <td>".$product_inquired['part_no']."</td>
                                <td>".$product_inquired['quantity']."</td>
                                <td>".$product_inquired['terms']."</td>
                                <td>".$product_inquired['cif_port']."</td>
                                <td>".$supplier->name."</td>
                                <td>".$currency." ".number_format($product_inquired['accepted_price'],2)."</td>
                                <td>".$product_inquired['accepted_quantity']."</td>
                                <td>".$currency." ".number_format($amount,2)."</td>
                              </tr>";
                    $i++;
                    }
                ?>
                <!-- <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td>Total Amount</td>
                    <td><?php echo number_format($total_amount,2); ?></td>
                    
                </tr> -->
        </tbody>
    </table>
    
<?php echo form_close(); ?>
<script type="text/javascript">
    $(document).ready(function() 
        { 
            $("#myTable").tablesorter(); 
            $(".chzn-select").chosen();
        } 
    ); 
</script>

<?php
}else{
    $this->session->set_flashdata('error', lang('snow.view_error') );
    redirect('access-denied');
}
?>