<?php
defined('ABSPATH') || die();
require_once DNM_PLUGIN_DIR_PATH . '/includes/helpers/DNM_Helper.php';
$page_url = esc_url(DNM_Helper::get_page_url('donation-orders'));

$order_data = array(
	'order_id'       => 0,
	'name'           => '',
	'email'          => '',
	'phone'          => '',
	'city'           => '',
	'state'          => '',
	'address'        => '',
	'amount'         => '',
	'payment_method' => '',
	'reference_id'      => '',
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
?>
<div class="container py-5">
	<div class="row">
		<div class="col-lg-8 mx-auto">
			<form action="<?php echo esc_url(admin_url('admin-ajax.php')); ?>" method="post" id="dnm-save-order-form">
				<?php wp_nonce_field('dnm_save_order', 'nonce'); ?>
				<input type="hidden" name="action" value="dnm-save-order">
				<input type="hidden" name="order_id" id="order_id" value="<?php echo esc_attr($order->ID); ?>">
				<h2 class="mb-4">Add New Order</h2>
				<div class="row">
					<?php
					echo DNM_Helper::generate_form_field('name', 'Name', 'text', $order_data['name']);
					echo DNM_Helper::generate_form_field('email', 'Email', 'email', $order_data['email']);
					echo DNM_Helper::generate_form_field('phone', 'Phone', 'text', $order_data['phone']);
					echo DNM_Helper::generate_form_field('city', 'District', 'text', $order_data['city']);
					echo DNM_Helper::generate_form_field('state', 'State', 'text', $order_data['state']);
					echo DNM_Helper::generate_form_field('address', 'Address', 'text', $order_data['address']);
					echo DNM_Helper::generate_form_field('amount', 'Amount', 'number', $order_data['amount']);
					echo DNM_Helper::generate_form_field('payment_method', 'Payment Method', 'text', $order_data['payment_method']);
					echo DNM_Helper::generate_form_field('reference_id', 'Reference ID', 'text', $order_data['reference_id']);
					if (!$order_id) {
						echo DNM_Helper::generate_form_field('type', 'Type', 'select', array('11000' => 'Fixed 11000', 'custom' => 'CUSTOM', 'membership' => 'MEMBERSHIP'));
					}

					?>
				</div>
				<button type="submit" class="btn btn-dark" id="dnm-save-order-btn">Submit</button>
			</form>
		</div>
	</div>
</div>
