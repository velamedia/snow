<?php
if (group_has_role('dashboard', 'view_module')) {

echo form_open(uri_string()); 

?>

    <table>
        <tr>
            <td><span class="labels">Sales Rep</span></td>
            <td>{{user:username}}</td>
        </tr>
        <tr>
            <td><span class="labels">Sales Ref</span></td>
            <td><?php echo $inquiry[0]['sales_ref']; ?></td>
        </tr>
        <tr>
            <td><span class="labels">Suppliers to send quote to</span></td>
            <td>
                
                <?php echo form_dropdown('suppliers[]', $suppliers, '','class="chzn-select" data-placeholder="Choose Supplier(s)..." multiple="multiple" style="width:350px;"'); ?>
            </td>
        </tr>
        <tr><td><br /></td></tr>
    </table>
    
    <table id="myTable" class="tablesorter" width="100%">
        <thead>
            <tr>
                <th>#</th>
                <th width="30%">Item</th>
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
    <?php echo form_submit('submit', lang('snow.form_submit'), 'class="btn green"'); ?>
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