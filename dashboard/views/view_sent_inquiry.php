<?php
if (group_has_role('dashboard', 'view_module')) {
    $sales_rep_user_data = $this->ion_auth->get_user($inquiry_quotation->sales_rep);

    //get suppliers whom the quoutation was sent to
    $snow_suppliers_table = $this->db->dbprefix('snow_suppliers');
    $query = $this->db->query("SELECT `name` FROM `$snow_suppliers_table` WHERE `id` IN(".$inquiry_quotation->suppliers.")");
    $suppliers = $query->result(); //returns an object

    //get customer details
    $client = $this->clients->get_client($inquiry[0]['customer']);
?>

    <table width="100%">
        <tr>
            <td width="11%"><span class="labels">Sales Rep</span></td>
            <td><?php echo $sales_rep_user_data->username; ?></td>
        </tr>
        <tr>
            <td><span class="labels">Sales Ref</span></td>
            <td><?php echo $inquiry[0]['sales_ref']; ?></td>
        </tr>
        <tr>
            <td><span class="labels">Customer name</span></td>
            <td><?php echo $client->name; ?></td>
        </tr>
        <tr>
            <td valign="top"><span class="labels">Qoutation sent to</span></td>
            <td><?php 
                foreach ($suppliers as $supplier) {
                    echo "<ul><li>".$supplier->name."</ul></li>";
                }
             ?></td>
        </tr>
        <tr><td><br /></td></tr>
    </table>
    
    <table id="myTable" class="tablesorter" width="100%">
        <thead>
            <tr>
                <th width="2%">#</th>
                <th width="25%">Item</th>
                <th>Part No/ Pack Size</th>
                <th width="20%">Quantity (Ltrs/kgs - if agrochemicals; pcs if motor parts or other items)</th>
                <th>Terms - CIF/ FOB</th>
                <th>IF CIF - PORT?</th>
            </tr>
        </thead>
        <tbody>
            <tbody>
                <?php 
                    $i =1;
                    foreach ($products_inquired as $product_inquired) {

                        //get supplier
                        $supplier = $this->suppliers->get_supplier($product_inquired['supplier_id']);

                        echo "<tr>
                                <td>".$i."</td>
                                <td>".$product_inquired['item']."</td>
                                <td>".$product_inquired['part_no']."</td>
                                <td>".$product_inquired['quantity']."</td>
                                <td>".$product_inquired['terms']."</td>
                                <td>".$product_inquired['cif_port']."</td>
                              </tr>";
                    $i++;
                    }
                ?>
        </tbody>
    </table>

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