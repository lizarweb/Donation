<?php
defined('ABSPATH') || die();

require_once DNM_PLUGIN_DIR_PATH . 'admin/inc/DNM_Database.php';
require_once DNM_PLUGIN_DIR_PATH . 'includes/helpers/DNM_Helper.php';
require_once DNM_PLUGIN_DIR_PATH . 'includes/helpers/DNM_Config.php';
require_once DNM_PLUGIN_DIR_PATH . 'admin/inc/manager/orders/DNM_Order.php';

if (is_user_logged_in()) {
	$current_user = wp_get_current_user();
	$user_id      = $current_user->ID;


	$customer_data = DNM_Database::getRecords(DNM_CUSTOMERS, 'user_id', $user_id);

	// Check if customer data exists
	if ($customer_data) {
		// Extract customer details from the first record
		$customer_id      = $customer_data[0]->ID;
		$customer_name    = $customer_data[0]->name;
		$customer_phone   = $customer_data[0]->phone;
		$customer_email   = $customer_data[0]->email;
		$customer_city    = $customer_data[0]->city;
		$customer_state   = $customer_data[0]->state;
		$customer_address = $customer_data[0]->address;
		$subscription_status = $customer_data[0]->Subscription_status;

		// Generate a reference ID for the customer
		$customer_reference_id = 'MP' . $customer_id;
	} else {
		// Handle the case where the account does not exist in the donation system
		echo '<div class="alert alert-danger text-center" role="alert">';
		echo '<p>The specified account does not exist in our donation system.</p>';
		echo '</div>';

		return;
	}

	// get order data
	$order_data = DNM_Database::getRecords(DNM_ORDERS, 'customer_id', $customer_id);

	// get invoice data

	$invoice_data = array(
		'order_id'       => 0,
		'name'           => '',
		'email'          => '',
		'phone'          => '',
		'address'        => '',
		'amount'         => '',
		'payment_method' => '',
	);
	if (isset($_GET['id'])) {
		$order_id = absint($_GET['id']);
		if (0 !== $order_id) {
			$order = DNM_Order::get_order($order_id);
			if (!$order) {
				wp_safe_redirect($page_url);
				exit;
			}
			foreach ($invoice_data as $key => $value) {
				$invoice_data[$key] = sanitize_text_field($order->$key);
			}
			
		}
	}
	$logo = DNM_Helper::get_logo();
?>


	<div class="container">
		<div class="row">
			<div class="card">
				<div class="card-body">
					<div class="pb-2">
						<h4 class="card-title mb-3">Subscription Status : 
							<?php 
								if ($subscription_status == 'active') {
									echo '<span class="badge bg-success">' . ucfirst($subscription_status) . '</span>';
								} else if ($subscription_status == 'inactive') {
									echo '<span class="badge bg-warning">' . ucfirst($subscription_status) . '</span>';
								} else {
									echo '<span class="badge bg-secondary">' . ucfirst($subscription_status) . '</span>';
								}
							?>
						
						</h4>

						<button class="btn btn-primary" data-order-id="<?php echo $order_data[0]->ID; ?>" id="subscription-activate-btn"> Pay Now</button> <span> To Make Payment and setup auto pay. </span> <br> <br>
						<button class="btn btn-primary" data-order-id="<?php echo $order_data[0]->ID; ?>" id="subscription-verify-btn">Verify Payment Subscription</button> <span>Click this button to verify the payment and auto-pay.</span>

					</div>
				</div>
			</div>


			<div class="col-xl-8">
				<div class="card">
					<div class="card-body pb-0">
						<div class="row align-items-center">
							<ul class="nav nav-tabs nav-tabs-custom border-bottom-0 nav-justified flex-sm-column flex-md-row" role="tablist">
								<li class="nav-item" role="presentation">
									<a class="nav-link px-4 active" data-bs-toggle="tab" href="#payment-history" role="tab" aria-selected="true">
										<span class="d-block"><i class="mdi mdi-menu-open"></i></span>
										<span>Payment History</span>
									</a>
								</li>
								<li class="nav-item" role="presentation">
									<a class="nav-link px-4" data-bs-toggle="tab" href="#referenced-users" role="tab" aria-selected="false">
										<span class="d-block"><i class="fas fa-home"></i></span>
										<span>Referenced Users</span>
									</a>
								</li>
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
												<th>Date</th>
												<th>Print</th>
											</tr>
										</thead>
										<tbody>
											<?php
											if (!empty($order_data)) {
												foreach ($order_data as $order) {
													echo '<tr>';
													echo '<td>' . esc_html($order->transaction_id) . '</td>';
													echo '<td>' . esc_html($order->amount) . '</td>';
													echo '<td>' . esc_html(DNM_Config::date_format_text($order->created_at)) . '</td>';
													echo '<td><a href="' . esc_url(get_permalink() . '?action=view&id=' . $order->ID) . '" class="btn btn-sm btn-primary">View</a></td>';
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
												<th>Amount</th>
												<th>Referenced amount</th>
												<th>Status</th>
											</tr>
										</thead>
										<tbody>
											<?php
											
											$total_commission  = 0;
											$referenced_users = DNM_Database::getReferencedCustomers($customer_reference_id);
											$commission_percentage = DNM_Helper::get_referenced_discount();
											if (!empty($referenced_users)) {
												foreach ($referenced_users as $user) {
											// 		echo '<pre>';
											// var_dump($user); die;
											$commission_ref = 0;
											if ($user->Subscription_status === 'active') {
												$commission_ref = esc_html($user->orders[0]->amount * $commission_percentage / 100);
												// You can now use $commission as needed
											}
													echo '<tr>';
													echo '<td>' . esc_html($user->name) . '</td>';
													echo '<td>' . esc_html($user->email) . '</td>';
													echo '<td>' . esc_html($user->phone) . '</td>';
													echo '<td>' . esc_html($user->orders[0]->amount) . '</td>';
													echo '<td>' . esc_html($commission_ref)  . '</td>';
													echo '<td>' . esc_html(ucfirst($user->Subscription_status)) . '</td>';
													echo '</tr>';
													if ($user->Subscription_status === 'active') {
														$commission = $user->orders[0]->amount * ($commission_percentage / 100);
														$total_commission += $commission;
													}
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
							<h4 class="card-title mb-3">Reference Balance : <?php echo DNM_Config::get_amount_text($total_commission); ?></h4>

							<ul class="ps-3 mb-0">
								<li>Reference Percentage : <?php echo $commission_percentage; ?>%</li>
							</ul>

						</div>
						
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
											<td><?php echo $customer_name; ?></td>
										</tr>
										<tr>
											<th scope="row">Phone</th>
											<td><?php echo $customer_phone; ?></td>
										</tr>
										<tr>
											<th scope="row">Email</th>
											<td><?php echo $customer_email; ?></td>
										</tr>
										<tr>
											<th scope="row">District</th>
											<td><?php echo $customer_city; ?></td>
										</tr>
										<tr>
											<th scope="row">State</th>
											<td><?php echo $customer_state; ?></td>
										</tr>
										<tr>
											<th scope="row">Address</th>
											<td><?php echo $customer_address; ?></td>
										</tr>
										<tr>
											<th scope="row">Reference ID</th>
											<td><?php echo $customer_reference_id; ?></td>
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