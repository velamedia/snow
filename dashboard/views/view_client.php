<?php
if (group_has_role('dashboard', 'view_module')) {
?>
<table width="50%">
    <tr>
        <td><span class="labels">Name</span></td>
        <td><?php echo $client->name; ?></td>
    </tr>
    <tr>
        <td><span class="labels">Industry</span></td>
        <td>
            <?php echo $client->industry; ?>
        </td>
    </tr>
    <tr>
        <td><span class="labels">Location</span></td>
        <td><?php echo $client->location; ?></td>
    </tr>
    <tr>
        <td><span class="labels">E-mail</span></td>
        <td><?php echo $client->email; ?></td>
    </tr>
    <tr>
        <td><span class="labels">Tel NO.</span></td>
        <td><?php echo $client->tel; ?></td>
    </tr>
    <tr>
        <td valign="top"><span class="labels">Address</span></td>
        <td><?php echo $client->address; ?></td>
    </tr>
    <tr>
        <td><span class="labels">City</span></td>
        <td><?php echo $client->city; ?></td>
    </tr>
    <tr>
        <td><span class="labels">Country</span></td>
        <td><?php echo $client->country; ?></td>
    </tr>
    <tr>
        <td><span class="labels">Contact Person</span></td>
        <td><?php echo $client->contact_person; ?></td>
    </tr>
    <tr>
        <td><span class="labels">Contact Person email</span></td>
        <td><?php echo $client->contact_person_email; ?></td>
    </tr>
    <tr>
        <td><span class="labels">Contact Person Tel No.</span></td>
        <td><?php echo $client->contact_person_tel; ?></td>
    </tr>
    <tr><td><br /></td></tr>
</table>

<?php echo anchor('/dashboard/edit_client/'.$client->id, 'Edit', 'class="btn i_plus yellow icon"'); ?>

<?php
}else{
    $this->session->set_flashdata('error', lang('snow.view_error') );
    redirect('access-denied');
}
?>