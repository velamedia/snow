<?php 
if (group_has_role('dashboard', 'view_module')) {

$sales_ref = array('id'=>'sales_ref','name'=>'sales_ref','type'=>'text'); 
$customer = array('id'=>'customer','name'=>'customer','type'=>'text'); 
$location = array('id'=>'location','name'=>'location','type'=>'text'); 
$date_of_inquiry = array('id'=>'date_of_inquiry','name'=>'date_of_inquiry','type'=>'text');

echo form_open(uri_string()); 
?>

<table width="100%">
    <tr>
        <td width="7%"><span class="labels">Sales Rep</span></td>
        <td width="73%">{{user:username}}</td>
        <td>
            <?php echo anchor('/dashboard/add_inquiry', 'Add new', 'class="btn i_plus green icon"'); ?>
            <?php echo anchor('/dashboard/inquiry_csv_upload', 'CSV Upload', 'class="btn i_plus green icon"'); ?>
        </td>
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
            <th></th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        <?php 
            $i =1;
            foreach ($inquiries as $inquiry) {
                if($inquiry['status'] == -1){
                    $class = 'handled';
                    $link = 'dashboard/view_sent_inquiry/'.$inquiry['id'];
                    $edit_link = 'dashboard/regenerate_inquiry/'.$inquiry['id'];
                    $anchor_title = 'Regenerate';
                }elseif($inquiry['status'] == 1){
                    $class = 'handled';
                    $link = 'dashboard/confirmed_inquiry/'.$inquiry['id'];
                    $edit_link = 'dashboard/regenerate_inquiry/'.$inquiry['id'];
                    $anchor_title = 'Regenerate';
                }else{
                    $class = 'pending';
                    $link = 'dashboard/send_quotation/'.$inquiry['id'];
                    $edit_link = 'dashboard/edit_inquiry/'.$inquiry['id'];
                    $anchor_title = 'Edit';
                }

                $delete_link = 'dashboard/delete_inquiry/'.$inquiry['id'];

                //get customer details
                $client = $this->clients->get_client($inquiry['customer']);

                echo "<tr>
                        <td class='".$class."'>".$i."</td>
                        <td class='".$class."'><a href='".$link."'>".$inquiry['title']."</a></td>
                        <td class='".$class."'>".$inquiry['sales_ref']."</td>
                        <td class='".$class."'>".$client->name."</td>
                        <td class='".$class."'>".$inquiry['product_type']."</td>
                        <td class='".$class."'>".format_date(strtotime($inquiry['date_of_inquiry']), 'Y-m-d')."</td>
                        <td class='".$class."' align='center'><a href='".$edit_link."'>".$anchor_title."</a></td>
                        <td class='".$class."' align='center'><a href='".$delete_link."'><img style='margin: 0 0 0 0;' src='addons/shared_addons/modules/dashboard/img/delete.png' /></a></td>
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