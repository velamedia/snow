<?php

if (group_has_role('dashboard', 'view_module')) {

//input fields  
$initial_contact = array('id'=>'initial_contact','name'=>'initial_contact','value' => $client_record->initial_contact,'type'=>'text','size'=>'35');
$location        = array('id'=>'location','name'=>'location','value' => $client_record->location,'type'=>'text','size'=>'35'); 
$address         = array('id'=>'address','name'=>'address','value' => $client_record->address, 'type'=>'textarea','cols'=>37,'rows'=>10);
$customer_type   = array('id'=>'customer_type','name'=>'customer_type','value' => $client_record->customer_type,'type'=>'text','size'=>'35'); 
$industry        = array('id'=>'industry','name'=>'industry','value' => $client_record->industry,'type'=>'text','size'=>'35'); 
$tel             = array('id'=>'tel','name'=>'tel','value' => $client_record->tel,'type'=>'text','size'=>'35'); 
$last_contact    = array('id'=>'last_contact','name'=>'last_contact','value' => $client_record->last_contact,'type'=>'text','size'=>'35');
$contact_method  = array('id'=>'contact_method','name'=>'contact_method','value' => $client_record->contact_method,'type'=>'text','size'=>'35'); 
$feedback        = array('id'=>'feedback','name'=>'feedback','value' => $client_record->feedback,'type'=>'textarea','cols'=>37,'rows'=>10);

echo form_open(uri_string()); 

$client = $this->clients->get_client($client_record->customer);
?>

    <table width="100%">
        <tr>
            <td width="15%"><span class="labels">Customer Name</span></td>
            <td><span class="labels"><?php echo $client->name; ?></span><br /><br /><br /></td>
        </tr>
       <!--  <tr>
            <td><span class="labels">Sales Ref</span></td>
             <td>
                <select data-placeholder="Please select a sales ref..." class="chzn-select" style="width:335px;" tabindex="4" name="sales_ref">
                    <option value=""></option>
                    <?php 
                        foreach ($sales_refs as $sales_ref) {
                            $value = $sales_ref['sales_ref'];

                            if($client_record->sales_ref == $value){
                                $selected = "selected='selected'";
                            }else{
                                $selected = "";
                            }
                           echo "<option value='".$value."' ".$selected.">".$sales_ref['sales_ref']."</option>";
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
    <?php echo form_hidden('client_record_id', $client_record_id); ?>
    <?php echo form_submit('submit', lang('snow.form_submit'), 'class=" btn green"'); ?>
<?php echo form_close(); ?>
<script type="text/javascript">
    $(document).ready(function() 
        { 
            $(".chzn-select").chosen();

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