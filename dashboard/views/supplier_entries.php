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
            <th width="">#</th>
            <th>Supplier</th>
            <th width="">Inquiry</th>
            <th>Sales Ref</th>
            <th>Customer Name</th>
            <th>Date</th>
        </tr>
    </thead>
    <tbody>
        <?php 
            $i =1;
            $snow_inquiries_table = $this->db->dbprefix('snow_inquiries');
            $snow_quotations_table = $this->db->dbprefix('snow_quotations');
            $snow_supplier_entries_table = $this->db->dbprefix('snow_supplier_entries');
            //echo count($supplier_entries);
            foreach ($supplier_entries as $supplier_entry) {

                //get inquiry id
                $query = $this->db->query("SELECT `inquiry_id` FROM `$snow_quotations_table` WHERE `id`='".$supplier_entry['quotation_id']."'");
                $inquiry = $query->row();

                //get associated inquiry
                $query = $this->db->query("SELECT `sales_ref`,`title`,`customer` FROM `$snow_inquiries_table` WHERE `id`='".$inquiry->inquiry_id."'");
                $inquiry = $query->row();

                $supplier = $this->suppliers->get_supplier($supplier_entry['supplier_id']);

                //get date quotation was submited
                $query = $this->db->query("SELECT `date_submited` FROM `$snow_supplier_entries_table` WHERE `quotation_id`='".$supplier_entry['quotation_id']."' AND `supplier_id`='".$supplier->id."'");
                $date_submited = $query->row();

                //get customer details
                $client = $this->clients->get_client($inquiry->customer);

                $url = BASE_URL.'dashboard/view_supplier_entry/quotation/'.$supplier_entry['quotation_id'].'/supplier/'.$supplier->id;

                echo "<tr>
                        <td>".$i."</td>
                        <td>".$supplier->name."</td>
                        <td><a href='".$url."'>".$inquiry->title."</a></td>
                        <td>".$inquiry->sales_ref."</td>
                        <td>".$client->name."</td>
                        <td>".$date_submited->date_submited."</td>
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