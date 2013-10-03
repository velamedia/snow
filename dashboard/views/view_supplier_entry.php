<?php
if (group_has_role('dashboard', 'view_module')) {

echo form_open(); 

//get the passed url variables
$quotation_id = $this->uri->segment(4);
$supplier_id = $this->uri->segment(6);

//just one record to ectract the date submited
$supplier_entry = $this->supplier_entries->get_supplier_entries($quotation_id,$supplier_id,$products_inquired[0]['id']); 

?>

    <table width="100%">
        <tr>
            <td width="12%"><span class="labels">Sales Ref</span></td>
            <td><?php echo $inquiry[0]['sales_ref']; ?></td>
        </tr>
        <tr>
            <td><span class="labels">Supplier Name</span></td>
            <td><?php echo $supplier->name; ?></td>
        </tr>
        <tr>
            <td><span class="labels">Payment Terms</span></td>
            <td><?php echo $supplier_entry->payment_terms; ?></td>
        </tr>
        <tr>
            <td><span class="labels">Prefered Currency</span></td>
            <td><?php echo $supplier_entry->currency; ?></td>
        </tr>
        <tr>
            <td><span class="labels">Date Submited</span></td>
            <td><?php echo $supplier_entry->date_submited; ?></td>
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
                <th>Price</th>
                <th>Quantity Offered</th>
                <!-- <th>Date of availability</th> -->
            </tr>
        </thead>
        <tbody>
            <tbody>
                <?php 
                    $i =0;
                    $j = $i+1;
                    foreach ($products_inquired as $product_inquired) {

                        //get supplier entries/quotations for each item
                        $supplier_entries = $this->supplier_entries->get_supplier_entries($quotation_id,$supplier_id,$product_inquired['id']);
                        
                        echo "<tr>
                                <td>".$j."</td>
                                <td>
                                    ".$product_inquired['item']."
                                </td>
                                <td>".$product_inquired['part_no']."</td>
                                <td>".$product_inquired['quantity']."</td>
                                <td align='left'>".$supplier_entries->price."</td>
                                <td align='center'>".$supplier_entries->quantity_offered."</td>
                                
                              </tr>";
                              //<td align='center'>".$supplier_entries->date_of_availability."</td>
                       
                    $j++; $i++;
                    }
                ?>
        </tbody>
    </table>
    <br />
    <table>
        <tr>
            <td valign="top"><span class="labels">Other Comments:</span></td>
            <td><pre><?php echo $supplier_entry->other_comments; ?></pre></td>
        </tr>
    </table>
<?php echo form_close(); ?>

<script type="text/javascript">
    $(document).ready(function() 
        { 
            $("#myTable").tablesorter();
        } 
    ); 
</script>

<?php
}else{
    $this->session->set_flashdata('error', lang('snow.view_error') );
    redirect('access-denied');
}
?>