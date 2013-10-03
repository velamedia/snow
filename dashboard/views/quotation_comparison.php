<?php
if (group_has_role('dashboard', 'view_module')) {

?>
<table width="100%">
    <tr>
        <td width="13%"><span class="labels">Sales Rep</span></td>
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

<?php echo form_open();  ?>
<table id="myTable" class="tablesorter" width="100%">
    <thead>
        <tr>
            <th>#</th>
            <th>Item</th>
            <th>Part No/ Pack Size</th>
            <th width="15%">Quantity (Ltrs/kgs - if agrochemicals; pcs if motor parts or other items)</th>
            <th>Terms - CIF/ FOB</th>
             <?php 
                foreach ($suppliers as $supplier) {
                    echo "<th style='width:250px;'>".$supplier->name."</th>";
                    $supplier_ids_array[] = $supplier->id;
                }
                $supplier_ids = implode(',', $supplier_ids_array);
            ?>
            
        </tr>
    </thead>
    <tbody>
        <tbody>
            <?php 
                $i =1;
                $snow_supplier_entries_table = $this->db->dbprefix('snow_supplier_entries');   
                foreach ($products_inquired as $product_inquired) {

                    echo "<tr>
                            <td>".$i."</td>
                            <td>
                                ".$product_inquired['item'].
                                form_hidden('item_id[]', $product_inquired['id'])."
                            </td>
                            <td align='center'>".$product_inquired['part_no']."</td>
                            <td align='center'>".$product_inquired['quantity']."</td>
                            <td align='center'>".$product_inquired['terms']."</td>";

                            foreach ($suppliers as $supplier) {
                                //get supplier quoted prices
                                $query = $this->db->query("SELECT `price`,`quantity_offered`,`currency` FROM `$snow_supplier_entries_table` WHERE `item_id`='".$product_inquired['id']."' AND `supplier_id`='".$supplier->id."'");
                                $result = $query->row();
                                $price = $result->price;
                                $currency = $result->currency;
                                $quantity = $result->quantity_offered;

                                
                                $supplier_id = array('supplier_id['.$product_inquired['id'].'_'.$supplier->id.']', $supplier->id);
                                $confirm_checkbox = array('name'=>'accepted['.$product_inquired['id'].']', 'value'=>$price, 'id'=>'accepted_price_'.$i.'_'.$supplier->id);
                                $accepted_quantity = array('id'=>'accepted_quantity_'.$i.'_'.$supplier->id,'name'=>'accepted_quantity['.$product_inquired['id'].'_'.$supplier->id.']','type'=>'text', 'placeholder'=>'accepted quantity','onKeyup'=>"getTotalAmount(".$supplier->id.",".$i.",".$product_inquired['id'].");");
                                $total_amount = array('id'=>'total_amount_'.$i.'_'.$supplier->id,'name'=>'total_amount['.$product_inquired['id'].']','type'=>'text', 'placeholder'=>'total amount');

                                echo "<td><table><tr>
                                                    <td align='right'>
                                                        ".form_radio($confirm_checkbox)."
                                                        <input type='hidden' name='supplier_id[".$product_inquired['id']."]' id='supplier_id_".$i.'_'.$supplier->id."' value='".$supplier->id."' />
                                                        <input type='hidden' name='product_selected_supplier[]' id='product_selected_supplier_".$i.'_'.$supplier->id."' />
                                                    </td>
                                                    <td>
                                                        ".$currency." ".$price." - Price<br /> ".$quantity." - Quantity<br />
                                                        ".form_input($accepted_quantity)."<br />
                                                        ".form_input($total_amount)."
                                                    </td>
                                    </tr></table></td>";

                            }
                        
                    echo "</tr>";
                $i++;
                }
            ?>

    </tbody>
</table>
<?php echo form_hidden('supplier_ids', $supplier_ids); ?>
<?php echo form_hidden('inquiry_id', $inquiry_id); ?>
<?php echo form_submit('submit', lang('snow.confirm_quotation'), 'class="btn green"'); ?>
<?php echo form_close(); ?>
<script type="text/javascript">
    $(document).ready(function() 
        { 
            $("#myTable").tablesorter(); 
        } 
    );

    Number.prototype.formatMoney = function(c, d, t){
        var n = this, 
            c = isNaN(c = Math.abs(c)) ? 2 : c, 
            d = d == undefined ? "." : d, 
            t = t == undefined ? "," : t, 
            s = n < 0 ? "-" : "", 
            i = parseInt(n = Math.abs(+n || 0).toFixed(c)) + "", 
            j = (j = i.length) > 3 ? j % 3 : 0;
           return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
     }

    function getTotalAmount( supplier_id, table_row_id, product_id) {
        
        var price = $("#accepted_price_"+table_row_id+"_"+supplier_id+"").val().replace(',','');
        var accepted_quantity = $("#accepted_quantity_"+table_row_id+"_"+supplier_id+"").val();
        var total_amount = price*accepted_quantity;

        var supplier_ids = $("input[name=supplier_ids]").val().replace('"','');
        //var supplier_ids = $("input[name=supplier_ids]").val();

        var supplier_ids_array = supplier_ids.split(",");

        // var supplier_ids_array = [];
        // $.each(supplier_ids, function( index, value ) {
        //     //alert(value);
        //     if (value != ',') {
        //         supplier_ids_array.push(value);
        //     }
        // });
        

        var formated_total_amount = (total_amount).formatMoney(2);
        
        $.each(supplier_ids_array, function( index, value ) {
        
            if (value == supplier_id) {
                $("#accepted_price_"+table_row_id+"_"+supplier_id+"").prop("checked", true);
                $("#total_amount_"+table_row_id+"_"+supplier_id+"").val(formated_total_amount);
                $("#product_selected_supplier_"+table_row_id+"_"+supplier_id+"").val(supplier_id);
            }else{
                $("#accepted_price_"+table_row_id+"_"+value+"").prop("checked", false);
                $("#accepted_quantity_"+table_row_id+"_"+value+"").val('');
                $("#total_amount_"+table_row_id+"_"+value+"").val('');
                $("#product_selected_supplier_"+table_row_id+"_"+value+"").val('');
            }
        });
    }
</script>

<?php
}else{
    $this->session->set_flashdata('error', lang('snow.view_error') );
    redirect('access-denied');
}
?>