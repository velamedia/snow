<?php
if (group_has_role('dashboard', 'view_module')) {
 
echo form_open(uri_string()); 
?>

<table>
    <tr>
        <td><span class="labels">Sales Rep</span></td>
        <td>{{user:username}}</td>
    </tr>

    <tr><td><br /></td></tr>
</table>
<table id="myTable" class="tablesorter" width="100%">
    <thead>
        <tr>
            <th width="1%">#</th>
            <th>Quotation</th>
            <th>Sales Ref</th>
            <th>Customer Name</th>
            <th width="">Last Supplier to Send</th>
            <th width="9%">Date</th>
        </tr>
    </thead>
    <tbody>
        <?php 
            $i =1;
            $snow_inquiries_table = $this->db->dbprefix('snow_inquiries');
            $snow_supplier_entries_table = $this->db->dbprefix('snow_supplier_entries');
            foreach ($quotations as $quotation) {
                
                //get associated inquiry
                $query = $this->db->query("SELECT `sales_ref`,`title`,`customer` FROM `$snow_inquiries_table` WHERE `id`='".$quotation['inquiry_id']."'");
                $inquiry = $query->row(); 

                //get customer details
                $client = $this->clients->get_client($inquiry->customer);

                //get last supplier to send quotation
                $query = $this->db->query("SELECT DISTINCT `supplier_id` FROM `$snow_supplier_entries_table` WHERE `quotation_id`='".$quotation['id']."' AND `date_submited` = (SELECT max( `date_submited` ) FROM `$snow_supplier_entries_table` WHERE `quotation_id`='".$quotation['id']."') ");
                $supplier = $query->row();
                $supplier_id = $supplier->supplier_id;

                $supplier = $this->suppliers->get_supplier($supplier_id);

                echo "<tr>
                        <td>".$i."</td>
                        <td><a href='dashboard/quotation_comparison/".$quotation['id']."'>".$inquiry->title."</a></td>
                        <td>".$inquiry->sales_ref."</td>
                        <td>".$client->name."</td>
                        <td>".$supplier->name."</td>
                        <td>".$quotation['date']."</td>
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