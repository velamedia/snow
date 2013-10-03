<?php
if (group_has_role('dashboard', 'view_module')) {

//echo form_open(uri_string()); 
?>

<table width="100%">
    <tr>
        <td width="7%"><span class="labels">Sales Rep</span></td>
        <td width="83%">{{user:username}}</td>
        <td><?php echo anchor('/dashboard/add_client_record', 'Add new', 'class="btn i_plus green icon"'); ?></td>
    </tr>

    <tr><td><br /></td></tr>
</table>
<table id="myTable" class="tablesorter" width="100%">
<thead>
    <tr>
        <th>#</th>
        <th width="">Customer</th>
       <!--  <th>Sales Ref</th> -->
        <th width="">Initial Contact</th>
        <!-- 
        <th>Address</th>
        <th>Customer Type</th>
        <th>Industry</th> -->
        <th>Last Contact</th>
        <th>Location</th>
        <th>Method of Contact</th>
        <th>Tel. No</th>
        <!-- <th>Feedback</th> -->
        <th>Delete</th>
    </tr>
</thead>
<tbody>
    <?php 
        $i =1;
        foreach ($client_records as $client_record) {
            $link = 'dashboard/client_record/'.$client_record['id'];
            $delete_link = 'dashboard/delete_client_record/'.$client_record['id'];
            $client = $this->clients->get_client($client_record['customer']);

            echo "<tr>
                    <td>".$i."</td>
                    <td><a href='".$link."'>".$client->name."</a></td>
                    
                    <td>".format_date(strtotime($client_record['initial_contact']), 'Y-m-d')."</td>
                    <td>".format_date(strtotime($client_record['last_contact']), 'Y-m-d')."</td>
                    <td>".$client_record['location']."</td>
                    <td>".$client_record['contact_method']."</td>
                    <td>".$client_record['tel']."</td>
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