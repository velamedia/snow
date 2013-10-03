<?php 
if (group_has_role('dashboard', 'view_module')) {

//input fields 
$sales_ref = array('id'=>'sales_ref','name'=>'sales_ref','type'=>'text', 'value'=>$sales_ref, 'class'=>'form_input'); 
$title = array('id'=>'title','name'=>'title','type'=>'text','value' => $inquiry[0]['title'], 'class'=>'form_input'); 
$customer = array('id'=>'customer','name'=>'customer','type'=>'text','value' => $inquiry[0]['customer'], 'class'=>'form_input'); 
$location = array('id'=>'location','name'=>'location','type'=>'text','value' => $inquiry[0]['location'], 'class'=>'form_input'); 
$date_of_inquiry = array('id'=>'date_of_inquiry','name'=>'date_of_inquiry','type'=>'text','value' => date('Y-m-d', strtotime($inquiry[0]['date_of_inquiry'])), 'class'=>'form_input');

echo form_open(uri_string()); 
?>

    <table>
        <tr>
            <td><span class="labels">Customer/Client</span></td>
             <td>
                <select data-placeholder="Please select a client..." class="chzn-select" style="width:360px;" tabindex="4" name="customer" id="customer">
                    <option value=""></option>
                    <?php 
                        foreach ($clients as $client) {
                            $value = $client['id'];
                            if($inquiry[0]['customer'] == $value){
                                $selected = "selected='selected'";
                            }else{
                                $selected = "";
                            }
                           echo "<option value='".$value."' customer ".$selected.">".$client['name']."</option>";
                        }
                    ?>
                </select>
            </td>
        </tr>
        <tr>
            <td><span class="labels">Sales Ref</span></td>
            <td><?php echo form_input($sales_ref); ?></td>
        </tr>
        <tr>
            <td><span class="labels">Title</span></td>
            <td><?php echo form_input($title); ?></td>
        </tr>
        <tr>
            <td><span class="labels">Location</span></td>
            <td><?php echo form_input($location); ?></td>
        </tr>
        <tr>
            <td><span class="labels">Date of Inquiry</span></td>
            <td><?php echo form_input($date_of_inquiry); ?></td>
        </tr>
        <tr>
            <td><span class="labels">Product(s) of Inquiry</span></td>
            <td>
                <select data-placeholder="Please select..." class="chzn-select" style="width:357px;" tabindex="4" name="product_type">
                    <option value=""></option>
                    <option value="Agro-chemicals">Agro-chemicals</option>
                    <option value="Industrial chemicals">Industrial chemicals</option>
                    <option value="Motor Parts">Motor Parts</option>
                    <option value="Others">Others</option>
                </select>
            </td>
        </tr>
        <tr><td><br /></td></tr>
    </table>
    <table id="myTable" class="tablesorter" width="100%">
        <thead>
            <tr>
                <th width="30%">Item</th>
                <th>Part No/ Pack Size</th>
                <th width="20%">Quantity (Ltrs/kgs - if agrochemicals; pcs if motor parts or other items)</th>
                <th>Terms - CIF/ FOB</th>
                <th>IF CIF - PORT?</th>
            </tr>
        </thead>
        <tbody>
            <?php 
                foreach ($products_inquired as $product_inquired) {
            ?>
            <tr>
                <td>
                    <input type="hidden" name="item_id[]" value="<?php echo $product_inquired['id']; ?>" />
                    <input type="text" name="item[]" value="<?php echo $product_inquired['item']; ?>" />
                </td>
                <td><input type="text" name="part_no[]" value="<?php echo $product_inquired['part_no']; ?>" /></td>
                <td><input type="text" name="quantity[]" value="<?php echo $product_inquired['quantity']; ?>" /></td>
                <td><input type="text" name="terms[]" value="<?php echo $product_inquired['terms']; ?>" /></td>
                <td><input type="text" name="cif_port[]" value="<?php echo $product_inquired['cif_port']; ?>" /></td>
            </tr>
            <?php
                }
            ?>
            <tr>
                <td class="value" colspan="5">
                    <table id="added" class="tablesorter" width="100%">
                        <tr></tr>
                    </table>    
                </td>
            </tr>
            <tr>
                <td></td><td></td><td></td><td></td><td align="right" nowrap><button class="btn purple" id="addAnother">Add Another</button></td>
            </tr>
        </tbody>
    </table>
    <?php echo form_hidden('inquiry_id', $inquiry_id); ?>
    <?php echo form_submit('submit', lang('snow.form_submit'), 'class="btn green"'); ?>
    <?php echo form_close(); ?>

    <script type="text/javascript">
        $(document).ready(function() 
            { 

                $(".chzn-select").chosen();

                jQuery("#date_of_inquiry").datepicker({ changeMonth: true,changeYear: true,dateFormat: 'yy-mm-dd',showOtherMonths: true,showWeek: true,weekHeader: 'Week', altFormat: "DD, d MM, yy" });

                jQuery("#customer").change(function(evt){
                    evt.preventDefault(); 

                    var client = $("#customer").val();
                    //var value = $("#customer").chosen().val();
                    jQuery.post('dashboard/generate_salesref', {client:client}, function(response_data){
                        jQuery('#sales_ref').val(response_data);
                    });
                    
                });

                jQuery('.remove').live('click', function() {
                    var rowCount = jQuery('#added tr').length - 5;
                    jQuery(this).parent().parent().remove();
                });
                jQuery("#addAnother").click(function(evt){
                    evt.preventDefault(); 
                    var i = 2;
                    var num = 1;
                    var i = jQuery('#added tr').length+1;
                    var num = jQuery('#added tr').length;
                    var trsize = jQuery("#tr"+num).size();
                    if (trsize == 1) {
                        i += 1;
                        num += 1;
                    }

                    var j = i+1;

                    var tds = '<tr id="tr'+j+'">';

                    tds += '<td width="23%"><input type="hidden" name="item_id[]" /><input type="text" name="item[]" size="19"/></td>';
                    tds += '<td width="19.5%"><input type="text" name="part_no[]" size="20"/></td>';
                    tds += '<td width="22%"><input type="text" name="quantity[]" size="19"/></td>';
                    tds += '<td width="19%"><input type="text" name="terms[]" size="20"/></td>';
                    tds += '<td width="20%"><input type="text" name="cif_port[]" size="15"/></td>';
                    tds += '<td width="2%"><a href="javascript:void(0)" class="remove"><b><img src="addons/shared_addons/modules/dashboard/img/close.png" alt="Remove" /></b></a></td>';
                    tds += '</tr>';

                    tds += '<tr id="tr'+j+'">';
                    tds += '<td width="23%"><input type="hidden" name="item_id[]" /><input type="text" name="item[]" size="19"/></td>';
                    tds += '<td width="19.5%"><input type="text" name="part_no[]" size="20"/></td>';
                    tds += '<td width="21%"><input type="text" name="quantity[]" size="19"/></td>';
                    tds += '<td width="19%"><input type="text" name="terms[]" size="20"/></td>';
                    tds += '<td width="20%"><input type="text" name="cif_port[]" size="15"/></td>';
                    tds += '<td width="2%"><a href="javascript:void(0)" class="remove"><b><img src="addons/shared_addons/modules/dashboard/img/close.png" alt="Remove" /></b></a></td>';
                    tds += '</tr>';

                    tds += '<tr id="tr'+j+'">';
                    tds += '<td width="23%"><input type="hidden" name="item_id[]" /><input type="text" name="item[]" size="19"/></td>';
                    tds += '<td width="19.5%"><input type="text" name="part_no[]" size="20"/></td>';
                    tds += '<td width="22%"><input type="text" name="quantity[]" size="19"/></td>';
                    tds += '<td width="19%"><input type="text" name="terms[]" size="20"/></td>';
                    tds += '<td width="20%"><input type="text" name="cif_port[]" size="15"/></td>';
                    tds += '<td width="2%"><a href="javascript:void(0)" class="remove"><b><img src="addons/shared_addons/modules/dashboard/img/close.png" alt="Remove" /></b></a></td>';
                    tds += '</tr>';

                    tds += '<tr id="tr'+j+'">';
                    tds += '<td width="23%"><input type="hidden" name="item_id[]" /><input type="text" name="item[]" size="19"/></td>';
                    tds += '<td width="19.5%"><input type="text" name="part_no[]" size="20"/></td>';
                    tds += '<td width="22%"><input type="text" name="quantity[]" size="19"/></td>';
                    tds += '<td width="19%"><input type="text" name="terms[]" size="20"/></td>';
                    tds += '<td width="20%"><input type="text" name="cif_port[]" size="15"/></td>';
                    tds += '<td width="2%"><a href="javascript:void(0)" class="remove"><b><img src="addons/shared_addons/modules/dashboard/img/close.png" alt="Remove" /></b></a></td>';
                    tds += '</tr>';

                    tds += '<tr id="tr'+j+'">';
                    tds += '<td width="23%"><input type="hidden" name="item_id[]" /><input type="text" name="item[]" size="19"/></td>';
                    tds += '<td width="19.5%"><input type="text" name="part_no[]" size="20"/></td>';
                    tds += '<td width="22%"><input type="text" name="quantity[]" size="19"/></td>';
                    tds += '<td width="19%"><input type="text" name="terms[]" size="20"/></td>';
                    tds += '<td width="20%"><input type="text" name="cif_port[]" size="15"/></td>';
                    tds += '<td width="2%"><a href="javascript:void(0)" class="remove"><b><img src="addons/shared_addons/modules/dashboard/img/close.png" alt="Remove" /></b></a></td>';
                    tds += '</tr>';
                    
                    jQuery("#added tr:last").before(tds); 

                });
            } 
        ); 
    </script>

<?php
}else{
    $this->session->set_flashdata('error', lang('snow.view_error') );
    redirect('access-denied');
}
?>
