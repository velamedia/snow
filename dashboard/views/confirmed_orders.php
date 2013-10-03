<?php 
if (group_has_role('dashboard', 'view_module')) {

echo form_open(uri_string()); 
?>

<table width="100%">
    <tr>
        <td width="7%"><span class="labels">Sales Rep</span></td>
        <td width="83%">{{user:username}}</td>
        <td><?php echo anchor('/dashboard/add_inquiry', 'Add new', 'class="btn i_plus green icon"'); ?></td>
    </tr>

    <tr><td><br /></td></tr>
</table>
<table id="myTable" class="tablesorter" width="100%">
    <thead>
        <tr>
            <th width="">#</th>
            <th>Inquiry</th>
            <th>Sales Ref</th>
            <th width="">Customer Name</th>
            <th>Product(s) of Inquiry</th>
            <th>Date</th>
        </tr>
    </thead>
    <tbody>
        <?php 
            $i =1;
            foreach ($confirmed_inquiries as $inquiry) {
                
                $class = 'handled';
                $link = 'dashboard/confirmed_inquiry/'.$inquiry['id'];

                //get customer details
                $client = $this->clients->get_client($inquiry['customer']);
                
                echo "<tr>
                        <td class='".$class."'>".$i."</td>
                        <td class='".$class."'><a href='".$link."'>".$inquiry['title']."</a></td>
                        <td class='".$class."'>".$inquiry['sales_ref']."</td>
                        <td class='".$class."'>".$client->name."</td>
                        <td class='".$class."'>".$inquiry['product_type']."</td>
                        <td class='".$class."'>".format_date(strtotime($inquiry['date_of_inquiry']), 'Y-m-d')."</td>
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