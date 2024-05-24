<?php
defined('ABSPATH') || die();

require_once DNM_PLUGIN_DIR_PATH . 'admin/inc/DNM_Database.php';
require_once DNM_PLUGIN_DIR_PATH . 'includes/helpers/DNM_Helper.php';
require_once DNM_PLUGIN_DIR_PATH . 'includes/helpers/DNM_Config.php';

if (is_user_logged_in()) {
	$current_user = wp_get_current_user();
	$user_id = $current_user->ID;


	$customer_data = DNM_Database::getRecords(DNM_CUSTOMERS, 'user_id', $user_id);

	// Check if customer data exists
	if ($customer_data) {
		// Extract customer details from the first record
		$customerId    = $customer_data[0]->ID;
		$customerName  = $customer_data[0]->name;
		$customerPhone = $customer_data[0]->phone;
		$customerEmail = $customer_data[0]->email;
		$customerCity  = $customer_data[0]->city;
		$customerState = $customer_data[0]->state;
		$customerAddress = $customer_data[0]->address;

		// Generate a reference ID for the customer
		$referenceId = 'MP' . $customerId;
	} else {
		// Handle the case where the account does not exist in the donation system
		echo '<div class="alert alert-danger text-center" role="alert">';
		echo '<p>The specified account does not exist in our donation system.</p>';
		echo '</div>';

		return;
	}

	// get order data 
	$order_data = DNM_Database::getRecords(DNM_ORDERS, 'customer_id', $id);


	?>
	<div class="container">
		<div class="row">
			<div class="col-xl-8">
				<div class="card">
					<div class="card-body pb-0">
						<div class="row align-items-center">
							<ul class="nav nav-tabs nav-tabs-custom border-bottom-0 nav-justified" role="tablist">
								<li class="nav-item" role="presentation">
									<a class="nav-link px-4 active" data-bs-toggle="tab" href="#payment-history" role="tab" aria-selected="true">
										<span class="d-block d-sm-none"><i class="mdi mdi-menu-open"></i></span>
										<span class="d-none d-sm-block">Payment History</span>
									</a>
								</li>
								<li class="nav-item" role="presentation">
									<a class="nav-link px-4" data-bs-toggle="tab" href="#referenced-users" role="tab" aria-selected="false">
										<span class="d-block d-sm-none"><i class="fas fa-home"></i></span>
										<span class="d-none d-sm-block">Referenced Users</span>
									</a>
								</li>
								<!-- <li class="nav-item" role="presentation">
									<a class="nav-link px-4" data-bs-toggle="tab" href="#team" role="tab" aria-selected="false">
										<span class="d-block d-sm-none"><i class="mdi mdi-account-group-outline"></i></span>
										<span class="d-none d-sm-block">Team</span>
									</a>
								</li> -->
							</ul>
						</div>
					</div>
				</div>

				<div class="card">
					<div class="tab-content p-4">
						<div class="tab-pane active show" id="payment-history" role="tabpanel">
							<h4 class="card-title mb-4">Payment History</h4>
							<div class="row">
								<div class="col-xl-12">
									<table class="table sm-table table-bordered table-responsive">
										<thead>
											<tr>
												<th>Transaction ID</th>
												<th>Amount</th>
												<!-- <th>Payment Method</th> -->
												<!-- <th>Payment Type</th> -->
												<th>Date</th>
											</tr>
										</thead>
										<tbody>
											<?php
											if (!empty($order_data)) {
												foreach ($order_data as $order) {
													echo '<tr>';
													echo '<td>' . esc_html($order->transaction_id) . '</td>';
													echo '<td>' . esc_html($order->amount) . '</td>';
													// echo '<td>' . esc_html($order->payment_method) . '</td>';
													// echo '<td>' . esc_html(ucfirst($order->type)) . '</td>';
													echo '<td>' . esc_html(DNM_Config::date_format_text($order->created_at)) . '</td>';
													echo '</tr>';
												}
											} else {
												echo '<tr><td colspan="5" class="text-center">No payment history found.</td></tr>';
											}
											?>
										</tbody>
									</table>
								</div>
							</div>
						</div>
						<div class="tab-pane" id="referenced-users" role="tabpanel">
							<h4 class="card-title mb-4">Referenced Users</h4>
							<div class="row">
								<div class="col-xl-12">
									<table class="table sm-table table-bordered table-responsive">
										<thead>
											<tr>
												<th>Name</th>
												<th>Email</th>
												<th>Phone</th>
												<!-- <th>City</th> -->
												<!-- <th>State</th> -->
												<!-- <th>Address</th> -->
												<th>Referenced BY</th>
											</tr>
										</thead>
										<tbody>
											<?php
											$referenced_users = DNM_Database::getRecords(DNM_CUSTOMERS, 'reference_id', $reference_id);
											if (!empty($referenced_users)) {
												foreach ($referenced_users as $user) {
													echo '<tr>';
													echo '<td>' . esc_html($user->name) . '</td>';
													echo '<td>' . esc_html($user->email) . '</td>';
													echo '<td>' . esc_html($user->phone) . '</td>';
													// echo '<td>' . esc_html($user->city) . '</td>';
													// echo '<td>' . esc_html($user->state) . '</td>';
													// echo '<td>' . esc_html($user->address) . '</td>';
													echo '<td>' . esc_html($user->reference_id) . '</td>';
													echo '</tr>';
												}
											} else {
												echo '<tr><td colspan="7" class="text-center">No referenced users found.</td></tr>';
											}
											?>
										</tbody>
									</table>
								</div>
							</div>
						</div>
						<div class="tab-pane" id="team" role="tabpanel">
							<h4 class="card-title mb-4">Team</h4>
							<div class="row">
								<div class="col-xl-12">

								</div>
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="col-xl-4">
				<div class="card">
					<div class="card-body">
						<div class="pb-2">
							<h4 class="card-title mb-3">Wallet Balance : <?php echo 0.00;  ?></h4>

							<ul class="ps-3 mb-0">
								<li>Discount percentage : </li>
							</ul>

						</div>
						<!-- <hr> -->
						<!-- <div class="pt-2">
							<h4 class="card-title mb-4">My Skill</h4>
							<div class="d-flex gap-2 flex-wrap">
								<span class="badge badge-soft-secondary p-2">HTML</span>
								<span class="badge badge-soft-secondary p-2">Bootstrap</span>
								<span class="badge badge-soft-secondary p-2">Scss</span>
								<span class="badge badge-soft-secondary p-2">Javascript</span>
								<span class="badge badge-soft-secondary p-2">React</span>
								<span class="badge badge-soft-secondary p-2">Angular</span>
							</div>
						</div> -->
					</div>
				</div>

				<div class="card">
					<div class="card-body">
						<div>
							<h4 class="card-title mb-4">Account Details</h4>
							<div class="table-responsive">
								<table class="table table-bordered mb-0">
									<tbody>
										<tr>
											<th scope="row">Name</th>
											<td><?php echo $name; ?></td>
										</tr>
										<tr>
											<th scope="row">Phone</th>
											<td><?php echo $phone; ?></td>
										</tr>
										<tr>
											<th scope="row">Email</th>
											<td><?php echo $email; ?></td>
										</tr>
										<tr>
											<th scope="row">City</th>
											<td><?php echo $city; ?></td>
										</tr>
										<tr>
											<th scope="row">State</th>
											<td><?php echo $state; ?></td>
										</tr>
										<tr>
											<th scope="row">Address</th>
											<td><?php echo $address; ?></td>
										</tr>
										<tr>
											<th scope="row">Reference ID</th>
											<td><?php echo $reference_id; ?></td>
										</tr>
									</tbody>
								</table>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
<?php
} else {
	$args = array(
		'echo'           => true,
		'remember'       => true,
		'redirect'       => (is_ssl() ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'],
		'form_id'        => 'loginform',
		'id_username'    => 'user_login',
		'id_password'    => 'user_pass',
		'id_remember'    => 'rememberme',
		'id_submit'      => 'wp-submit',
		'label_username' => __('Username'),
		'label_password' => __('Password'),
		'label_remember' => __('Remember Me'),
		'label_log_in'   => __('Log In'),
		'value_username' => '',
		'value_remember' => false,
	);
?>
	<div class="container mt-5">
		<div class="card">
			<div class="card-header">
				<h2><?php echo __('Login', 'donation'); ?></h2>
			</div>
			<div class="card-body">
				<?php wp_login_form($args); ?>
			</div>
		</div>
	</div>
<?php
}
?>