<?php
if (group_has_role('dashboard', 'view_module')) {

echo form_open(uri_string()); 
?>

<table width="100%" >
    <tr>
        <td width="10%"><span class="labels">Sales Rep</span></td>
        <td width="80%">{{user:username}}</td>
        <td><?php echo anchor('/dashboard/add_supplier', 'Add new', 'class="btn i_plus green icon"'); ?></td>
    </tr>

    <tr><td><br /></td></tr>
</table>
<table id="myTable" class="tablesorter" width="100%">
    <thead>
        <tr>
            <th width="">#</th>
            <th>Name</th>
            <th>Email</th>
            <th width="">Tel</th>
            <th>Industry</th>
            <th>Edit</th>
            <th>Delete</th>
        </tr>
    </thead>
    <tbody>
        <?php 
            $i =1;
            foreach ($suppliers as $supplier) {
                $edit_link = 'dashboard/edit_supplier/'.$supplier['id'];
                $delete_link = 'dashboard/delete_supplier/'.$supplier['id'];

                echo "<tr>
                        <td>".$i."</td>
                        <td><a href='dashboard/view_supplier/".$supplier['id']."'>".$supplier['name']."</a></td>
                        <td>".$supplier['email']."</td>
                        <td>".$supplier['tel']."</td>
                        <td>".$supplier['industry']."</td>
                        <td align='center'><a href='".$edit_link."'>Edit</a></td>
                        <td align='center'><a href='".$delete_link."'><img style='margin: 0 0 0 0;' src='addons/shared_addons/modules/dashboard/img/delete.png' /></a></td>
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