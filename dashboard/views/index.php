<?php
if(is_logged_in()){

?>

<div id="wrapper">
	<!-- <div id="top"></div> -->
	<div id="container">
		<div id="left_col">
			<!-- <div class="icon">
				<a href="dashboard/add_inquiry"><img src="addons/shared_addons/modules/dashboard/img/add_inquiry.jpg"></a>
				<p>ADD INQUIRY</p>
			</div> -->
			<div class="icon">
				<a href="dashboard/inquiries"><img src="addons/shared_addons/modules/dashboard/img/inquiries.jpg"></a>
				<p>INQUIRIES</p>
			</div>
			<div class="icon">
				<a href="dashboard/quotations"><img src="addons/shared_addons/modules/dashboard/img/qoutations.jpg"></a>
				<p>QUOTATIONS</p>
			</div>
			<div class="icon">
				<a href="dashboard/supplier_entries"><img src="addons/shared_addons/modules/dashboard/img/send_qoutation.jpg"></a>
				<p>SUPPLIERS ENTRIES</p>
			</div>
			<div class="icon">
				<a href="dashboard/confirmed_orders"><img src="addons/shared_addons/modules/dashboard/img/confirmed_orders.jpg"></a>
				<p>CONFIRMED ORDERS</p>
			</div>
			<div class="icon">
				<a href="dashboard/unconfirmed_orders"><img src="addons/shared_addons/modules/dashboard/img/unconfirmed_orders.jpg"></a>
				<p>UNCONFIRMED ORDERS</p>
			</div>
			<div class="icon">
				<a href="dashboard/clients_records"><img src="addons/shared_addons/modules/dashboard/img/client_record.jpg"></a>
				<p>CLIENTS RECORDS</p>
			</div>
			<div class="icon">
				<a href="dashboard/clients"><img src="addons/shared_addons/modules/dashboard/img/clients.jpg"></a>
				<p>CLIENTS</p>
			</div>
			<div class="icon">
				<a href="dashboard/suppliers"><img src="addons/shared_addons/modules/dashboard/img/suppliers.jpg"></a>
				<p>SUPPLIERS</p>
			</div>
		</div>

		<!-- <div id="right_col">
			<div id="user">active users</div>
			<div id="orders">Latest Orders</div>
		</div> -->
	</div>
</div> 

<?php
}else{
    redirect('users/login');
}
?>