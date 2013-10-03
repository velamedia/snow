<?php 
if (group_has_role('dashboard', 'view_module')) {

//input fields 
$sales_ref = array('id'=>'sales_ref','name'=>'sales_ref','type'=>'text'); 
$title = array('id'=>'title','name'=>'title','type'=>'text'); 
$customer = array('id'=>'customer','name'=>'customer','type'=>'text'); 
$location = array('id'=>'location','name'=>'location','type'=>'text'); 
$date_of_inquiry = array('id'=>'date_of_inquiry','name'=>'date_of_inquiry','type'=>'text');

$form_attributes = array('id' => 'add_inquiry_form');
echo form_open(uri_string(), $form_attributes); 
?>

    <table>
        <tr><td colspan="5"><div class="error_container" id="error_container"></div></td></tr>
        <tr>
            <td><label class="labels">Customer/Client</label></td>
             <td>
                <select data-placeholder="Please select a client..." class="chzn-select" style="width:203px;" tabindex="4" name="customer" id="customer">
                    <option value=""></option>
                    <?php 
                        foreach ($clients as $client) {
                            $value = $client['id'];
                            echo "<option value='".$value."' customer>".$client['name']."</option>";
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
            <td><label class="labels">Title</label></td>
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
                <select data-placeholder="Please select..." class="chzn-select" style="width:203px;" tabindex="4" name="product_type">
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
            <tr>
                <td><input type="text" name="item[]" /></td>
                <td><input type="text" name="part_no[]" /></td>
                <td><input type="text" name="quantity[]" /></td>
                <td><input type="text" name="terms[]" /></td>
                <td><input type="text" name="cif_port[]" /></td>
            </tr>
            <tr>
                <td><input type="text" name="item[]" /></td>
                <td><input type="text" name="part_no[]" /></td>
                <td><input type="text" name="quantity[]" /></td>
                <td><input type="text" name="terms[]" /></td>
                <td><input type="text" name="cif_port[]" /></td>
            </tr>
            <tr>
                <td><input type="text" name="item[]" /></td>
                <td><input type="text" name="part_no[]" /></td>
                <td><input type="text" name="quantity[]" /></td>
                <td><input type="text" name="terms[]" /></td>
                <td><input type="text" name="cif_port[]" /></td>
            </tr>
            <tr>
                <td><input type="text" name="item[]" /></td>
                <td><input type="text" name="part_no[]" /></td>
                <td><input type="text" name="quantity[]" /></td>
                <td><input type="text" name="terms[]" /></td>
                <td><input type="text" name="cif_port[]" /></td>
            </tr>
            <tr>
                <td><input type="text" name="item[]" /></td>
                <td><input type="text" name="part_no[]" /></td>
                <td><input type="text" name="quantity[]" /></td>
                <td><input type="text" name="terms[]" /></td>
                <td><input type="text" name="cif_port[]" /></td>
            </tr>
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

                    tds += '<td width="23%"><input type="text" name="item[]" size="19"/></td>';
                    tds += '<td width="19.5%"><input type="text" name="part_no[]" size="20"/></td>';
                    tds += '<td width="22%"><input type="text" name="quantity[]" size="19"/></td>';
                    tds += '<td width="19%"><input type="text" name="terms[]" size="20"/></td>';
                    tds += '<td width="20%"><input type="text" name="cif_port[]" size="15"/></td>';
                    tds += '<td width="2%"><a href="javascript:void(0)" class="remove"><b><img src="addons/shared_addons/modules/dashboard/img/close.png" alt="Remove" /></b></a></td>';
                    tds += '</tr>';

                    tds += '<tr id="tr'+j+'">';
                    tds += '<td width="23%"><input type="text" name="item[]" size="19"/></td>';
                    tds += '<td width="19.5%"><input type="text" name="part_no[]" size="20"/></td>';
                    tds += '<td width="21%"><input type="text" name="quantity[]" size="19"/></td>';
                    tds += '<td width="19%"><input type="text" name="terms[]" size="20"/></td>';
                    tds += '<td width="20%"><input type="text" name="cif_port[]" size="15"/></td>';
                    tds += '<td width="2%"><a href="javascript:void(0)" class="remove"><b><img src="addons/shared_addons/modules/dashboard/img/close.png" alt="Remove" /></b></a></td>';
                    tds += '</tr>';

                    tds += '<tr id="tr'+j+'">';
                    tds += '<td width="23%"><input type="text" name="item[]" size="19"/></td>';
                    tds += '<td width="19.5%"><input type="text" name="part_no[]" size="20"/></td>';
                    tds += '<td width="22%"><input type="text" name="quantity[]" size="19"/></td>';
                    tds += '<td width="19%"><input type="text" name="terms[]" size="20"/></td>';
                    tds += '<td width="20%"><input type="text" name="cif_port[]" size="15"/></td>';
                    tds += '<td width="2%"><a href="javascript:void(0)" class="remove"><b><img src="addons/shared_addons/modules/dashboard/img/close.png" alt="Remove" /></b></a></td>';
                    tds += '</tr>';

                    tds += '<tr id="tr'+j+'">';
                    tds += '<td width="23%"><input type="text" name="item[]" size="19"/></td>';
                    tds += '<td width="19.5%"><input type="text" name="part_no[]" size="20"/></td>';
                    tds += '<td width="22%"><input type="text" name="quantity[]" size="19"/></td>';
                    tds += '<td width="19%"><input type="text" name="terms[]" size="20"/></td>';
                    tds += '<td width="20%"><input type="text" name="cif_port[]" size="15"/></td>';
                    tds += '<td width="2%"><a href="javascript:void(0)" class="remove"><b><img src="addons/shared_addons/modules/dashboard/img/close.png" alt="Remove" /></b></a></td>';
                    tds += '</tr>';

                    tds += '<tr id="tr'+j+'">';
                    tds += '<td width="23%"><input type="text" name="item[]" size="19"/></td>';
                    tds += '<td width="19.5%"><input type="text" name="part_no[]" size="20"/></td>';
                    tds += '<td width="22%"><input type="text" name="quantity[]" size="19"/></td>';
                    tds += '<td width="19%"><input type="text" name="terms[]" size="20"/></td>';
                    tds += '<td width="20%"><input type="text" name="cif_port[]" size="15"/></td>';
                    tds += '<td width="2%"><a href="javascript:void(0)" class="remove"><b><img src="addons/shared_addons/modules/dashboard/img/close.png" alt="Remove" /></b></a></td>';
                    tds += '</tr>';
                    
                    jQuery("#added tr:last").before(tds); 

                });

                $.validator.setDefaults({ ignore: ":hidden:not(select)" });

                var error_container = $('div.container');

                // validate form
                $("#add_inquiry_form").validate({
                    rules: {
                        title: "required",
                        customer: "required",
                        date_of_inquiry: "required",
                        product_type: "required"
                    },
                    messages: {
                        title: "Please enter a title for the inquiry.",
                        customer: "Please select the inquiry's customer",
                        date_of_inquiry: "Please select the date of inquiry",
                        product_type: "Please select the type of product inquired by the client"
                    }
                    //,errorLabelContainer: $("div.error_container")
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
