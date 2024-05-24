<?php
defined('ABSPATH') || die();
require_once DNM_PLUGIN_DIR_PATH . '/includes/helpers/DNM_Helper.php';
$page_url = esc_url(DNM_Helper::get_page_url('donation-orders'));

$order_data = array(
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
		foreach ($order_data as $key => $value) {
			$order_data[$key] = sanitize_text_field($order->$key);
		}
	}
}
$logo = DNM_Helper::get_logo();

// get customer details reference_id from the order

$reference_code = 'MP' . $order->reference_code;
// $customers = DNM_Order::get_customers_by_reference_id($reference_code);

$customers = DNM_Database::getReferencedCustomers($reference_code);

?>
<div class="container mt-3">
	<div class="row justify-content-center">
		<div class="col-md-10">
			<div class="card shadow">
				<div class="card-header bg-dark text-white rounded">
					<h5 class="mb-0">Referenced Customers</h5>
				</div>
				<div class="card-body">
					<div class="table-responsive">
						<table class="table table-hover table-striped">
							<thead>
								<tr>
									<th>#</th>
									<th>Name</th>
									<th>Email</th>
									<th>Amount</th>
									<th>Phone</th>
									<th>Address</th>
								</tr>
							</thead>
							<tbody>
								<?php
								$total_commission = 0;
								$commission_percentage = DNM_Helper::get_referenced_discount();
								$count = 1;
								if (!empty($customers)) {
									foreach ($customers as $customer) {
										echo '<tr>';
										echo '<td>' . esc_html($count) . '</td>';
										echo '<td>' . esc_html($customer->name) . '</td>';
										echo '<td>' . esc_html($customer->email) . '</td>';
										echo '<td>' . esc_html(DNM_Config::get_amount_text($customer->orders[0]->amount)) . '</td>';
										echo '<td>' . esc_html($customer->phone) . '</td>';
										echo '<td>' . esc_html($customer->address) . '</td>';
										echo '</tr>';
										$count++;

										// calculate $total_commission.
										$commission = $customer->orders[0]->amount * ($commission_percentage / 100);
										$total_commission += $commission;
									}
								} else {
									echo '<tr><td colspan="5" class="text-center">This user does not have referenced users.</td></tr>';
								}
								?>
							</tbody>
						</table>

						<strong><p>Total commission earned: <?php echo esc_html(DNM_Config::get_amount_text($total_commission)); ?></p></strong>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>