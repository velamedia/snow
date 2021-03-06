<?php

if (group_has_role('dashboard', 'view_module')) {

//input fields 
$name = array('id'=>'name','name'=>'name','type'=>'text','size'=>'35','value' => $supplier->name); 
//$industry = array('id'=>'industry','name'=>'industry','type'=>'text','size'=>'35'); 
$email = array('id'=>'email','name'=>'email','type'=>'text','size'=>'35','value' => $supplier->email); 
$tel = array('id'=>'tel','name'=>'tel','type'=>'text','size'=>'35','value' => $supplier->tel); 
$address = array('id'=>'address','name'=>'address','type'=>'textarea','cols'=>37,'rows'=>10,'value' => $supplier->address);
$city = array('id'=>'city','name'=>'city','type'=>'text','size'=>'35','value' => $supplier->city); 
$country = array('id'=>'country','name'=>'country','type'=>'text','size'=>'35','value' => $supplier->country); 
$contact_person = array('id'=>'contact_person','name'=>'contact_person','type'=>'text','size'=>'35','value' => $supplier->contact_person); 
$contact_person_email = array('id'=>'contact_person_email','name'=>'contact_person_email','type'=>'text','size'=>'35','value' => $supplier->contact_person_email); 
$contact_person_tel = array('id'=>'contact_person_tel','name'=>'contact_person_tel','type'=>'text','size'=>'35','value' => $supplier->contact_person_tel); 

echo form_open(uri_string()); 
?>

    <table>
        <tr>
            <td><span class="labels">Name of Company</span></td>
            <td><?php echo form_input($name); ?></td>
        </tr>
        <tr>
            <td><span class="labels">Industry</span></td>
            <td>
                <select name="industry">
                    <option>Select</option>
                    <option value="Agro-chemicals">Agro-chemicals</option>
                    <option value="Industrial chemicals">Industrial chemicals</option>
                    <option value="Motor Parts">Motor Parts</option>
                    <option value="Others">Others</option>
                </select>
            </td>
        </tr>
        <tr>
            <td><span class="labels">E-mail</span></td>
            <td><?php echo form_input($email); ?></td>
        </tr>
        <tr>
            <td><span class="labels">Tel NO.</span></td>
            <td><?php echo form_input($tel); ?></td>
        </tr>
        <tr>
            <td valign="top"><span class="labels">Address</span></td>
            <td><?php echo form_textarea($address); ?></td>
        </tr>
        <tr>
            <td><span class="labels">City</span></td>
            <td><?php echo form_input($city); ?></td>
        </tr>
        <tr>
            <td><span class="labels">Country</span></td>
            <td><?php echo form_input($country); ?></td>
        </tr>
        <tr>
            <td><span class="labels">Contact Person</span></td>
            <td><?php echo form_input($contact_person); ?></td>
        </tr>
        <tr>
            <td><span class="labels">Contact Person email</span></td>
            <td><?php echo form_input($contact_person_email); ?></td>
        </tr>
        <tr>
            <td><span class="labels">Contact Person Tel No.</span></td>
            <td><?php echo form_input($contact_person_tel); ?></td>
        </tr>
        <tr><td><br /></td></tr>
    </table>
    <?php echo form_hidden('supplier_id', $supplier_id); ?>
    <?php echo form_submit('submit', lang('snow.form_submit'), 'class=" btn green"'); ?>
<?php echo form_close(); ?>

<?php
}else{
    $this->session->set_flashdata('error', lang('snow.view_error') );
    redirect('access-denied');
}
?>