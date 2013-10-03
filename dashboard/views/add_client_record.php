<?php

if (group_has_role('dashboard', 'view_module')) {

//input fields 
$initial_contact = array('id'=>'initial_contact','name'=>'initial_contact','type'=>'text','size'=>'35');
$location        = array('id'=>'location','name'=>'location','type'=>'text','size'=>'35', 'value'=>$client->location); 
$address         = array('id'=>'address','name'=>'address','type'=>'textarea','cols'=>37,'rows'=>10, 'value'=>$client->address);
$customer_type   = array('id'=>'customer_type','name'=>'customer_type','type'=>'text','size'=>'35'); 
$industry        = array('id'=>'industry','name'=>'industry','type'=>'text','size'=>'35', 'value'=>$client->industry); 
$tel             = array('id'=>'tel','name'=>'tel','type'=>'text','size'=>'35', 'value'=>$client->tel); 
$last_contact    = array('id'=>'last_contact','name'=>'last_contact','type'=>'text','size'=>'35');
$contact_method  = array('id'=>'contact_method','name'=>'contact_method','type'=>'text','size'=>'35'); 
$feedback        = array('id'=>'feedback','name'=>'feedback','type'=>'textarea','cols'=>37,'rows'=>10);

$form_attributes = array('id' => 'add_client_record_form');
echo form_open(uri_string(), $form_attributes); 

$fetched_client_id = $client->id;
?>

    <table width="100%">
        <tr>
            <td><span class="labels">Customer/Client</span></td>
             <td>
                <select data-placeholder="Please select a client..." class="chzn-select" style="width:335px;" tabindex="4" name="client" id="client">
                    <option value=""></option>
                    <?php 
                        foreach ($clients as $client) {
                            $value = $client['id'];
                            if($fetched_client_id == $value){
                                $selected = "selected='selected'";
                            }else{
                                $selected = "";
                            }
                           echo "<option value='".$value."' ".$selected.">".$client['name']."</option>";
                        }
                    ?>
                </select>
            </td>
        </tr>
        <!-- <tr>
            <td><span class="labels">Sales Ref</span></td>
             <td>
                <select data-placeholder="Please select a sales ref..." class="chzn-select" style="width:335px;" tabindex="4" name="sales_ref">
                    <option value=""></option>
                    <?php 
                        foreach ($sales_refs as $sales_ref) {
                            $value = $sales_ref['sales_ref'];
                           echo "<option value='".$value."'>".$sales_ref['sales_ref']."</option>";
                        }
                    ?>
                </select>
            </td>
        </tr> -->
        <tr>
            <td><span class="labels">Initial Contact</span></td>
            <td><?php echo form_input($initial_contact); ?><br /><br /></td>
        </tr>
        <tr>
            <td><span class="labels">Location</span></td>
            <td><?php echo form_input($location); ?><br /><br /></td>
        </tr>
        <tr>
            <td valign="top"><span class="labels">Address</span></td>
            <td><?php echo form_textarea($address); ?><br /><br /></td>
        </tr>
        <tr>
            <td><span class="labels">Customer Type</span></td>
            <td><?php echo form_input($customer_type); ?><br /><br /></td>
        </tr>
        <tr>
            <td><span class="labels">Industry</span></td>
            <td><?php echo form_input($industry); ?><br /><br /></td>
        </tr>
        <tr>
            <td><span class="labels">Tel NO.</span></td>
            <td><?php echo form_input($tel); ?><br /><br /></td>
        </tr>
        <tr>
            <td><span class="labels">Last Contact</span></td>
            <td><?php echo form_input($last_contact); ?><br /><br /></td>
        </tr>
        
        <tr>
            <td><span class="labels">Method of Contact</span></td>
            <td><?php echo form_input($contact_method); ?><br /><br /></td>
        </tr>
        <tr>
            <td  valign="top"><span class="labels">Feedback</span></td>
            <td><?php echo form_textarea($feedback); ?><br /><br /></td>
        </tr>
        <tr><td><br /><br /></td></tr>
    </table>
    <?php echo form_submit('submit_client_record', lang('snow.form_submit'), 'class=" btn green"'); ?>
<?php echo form_close(); ?>
<script type="text/javascript">
    $(document).ready(function() 
        { 
            $(".chzn-select").chosen();

            jQuery("#client").change(function() {
                jQuery('#add_client_record_form').submit();
            });

            jQuery("#initial_contact").datepicker({ changeMonth: true,changeYear: true,dateFormat: 'yy-mm-dd',showOtherMonths: true,showWeek: true,weekHeader: 'Week', altFormat: "DD, d MM, yy" });
            jQuery("#last_contact").datepicker({ changeMonth: true,changeYear: true,dateFormat: 'yy-mm-dd',showOtherMonths: true,showWeek: true,weekHeader: 'Week', altFormat: "DD, d MM, yy" });
        } 
    ); 
</script>
<?php
}else{
    $this->session->set_flashdata('error', lang('snow.view_error') );
    redirect('access-denied');
}
?>