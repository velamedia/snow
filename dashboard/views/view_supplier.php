<?php
if (group_has_role('dashboard', 'view_module')) {
?>
<table width="50%">
    <tr>
        <td><span class="labels">Name of Company</span></td>
        <td><?php echo $supplier->name; ?></td>
    </tr>
    <tr>
        <td><span class="labels">Industry</span></td>
        <td>
            <?php echo $supplier->industry; ?>
        </td>
    </tr>
    <tr>
        <td><span class="labels">E-mail</span></td>
        <td><?php echo $supplier->email; ?></td>
    </tr>
    <tr>
        <td><span class="labels">Tel NO.</span></td>
        <td><?php echo $supplier->tel; ?></td>
    </tr>
    <tr>
        <td valign="top"><span class="labels">Address</span></td>
        <td><?php echo $supplier->address; ?></td>
    </tr>
    <tr>
        <td><span class="labels">City</span></td>
        <td><?php echo $supplier->city; ?></td>
    </tr>
    <tr>
        <td><span class="labels">Country</span></td>
        <td><?php echo $supplier->country; ?></td>
    </tr>
    <tr>
        <td><span class="labels">Contact Person</span></td>
        <td><?php echo $supplier->contact_person; ?></td>
    </tr>
    <tr>
        <td><span class="labels">Contact Person email</span></td>
        <td><?php echo $supplier->contact_person_email; ?></td>
    </tr>
    <tr>
        <td><span class="labels">Contact Person Tel No.</span></td>
        <td><?php echo $supplier->contact_person_tel; ?></td>
    </tr>
    <tr><td><br /></td></tr>
</table>

<?php echo anchor('/dashboard/edit_supplier/'.$supplier->id, 'Edit', 'class="btn i_plus yellow icon"'); ?>

<?php
}else{
    $this->session->set_flashdata('error', lang('snow.view_error') );
    redirect('access-denied');
}
?>