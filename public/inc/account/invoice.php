<?php
defined('ABSPATH') || die();
require_once DNM_PLUGIN_DIR_PATH . '/includes/helpers/DNM_Helper.php';
require_once DNM_PLUGIN_DIR_PATH . 'admin/inc/manager/orders/DNM_Order.php';
$page_url = esc_url(DNM_Helper::get_page_url('donation-orders'));

$order_data = array(
	'order_id'       => 0,
	'name'           => '',
	'email'          => '',
	'phone'          => '',
	'address'        => '',
	'city'        => '',
	'state'        => '',
	'amount'         => '',
	'payment_method' => '',
);
if (isset($_GET['id']) || isset($order_id)) {

	if ($order_id) {
		$order = DNM_Order::get_order($order_id);
		foreach ($order_data as $key => $value) {
			$order_data[$key] = sanitize_text_field($order->$key);
		}
	} else {
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
}
$logo = DNM_Helper::get_logo();
?>
<div class="container-fluid mt-2 ">
	<div class="row d-flex justify-content-center">
		<div class="col-md-8">
			<button id="dnm-print-invoice" class="btn btn-dark" data-styles='["<?php echo esc_url(DNM_PLUGIN_URL . '/assets/css/bootstrap.min.css'); ?>"]' data-title="Print Receipt">Print Receipt</button>
			<div class="card mt-3 p-4" id="printableArea" style="width: 100%!important;">
				<div class="row">
					<div class="col-9 ">
						<div class="d-flex flex-column">
							<h2 class="font-weight-bold">महाराणा प्रताप स्मारक अभियान</h2>
							<span>रॉयल ग्रामीण विकास समिति</span>
							<span>4-E-16, Rangbari, Kota City, District-Kota, Rajasthan, 324005 <br> 80G No. AABAR6629NF20217</span>
							<span>Phone no.: 07444066444</span>
							<span>Email: contact@maharanapratap.in</span>
							<span>State: Rajasthan</span>
						</div>
					</div>
					<div class="col-3 text-end">
						<div class="p-2 fs-6">
							<img src="<?php echo esc_url_raw($logo); ?>" width="100">
						</div>
					</div>
				</div>
				<hr class="bg-danger text-danger">
				<div class="table-responsive p-2">
					<h3 class="text-center text-danger">Payment In </h3>
					<table class="table table-borderless">
						<tbody>
							<tr class="add">
								<td><strong>Received From</strong></td>
								<td><strong>Receipt Details</strong></td>
							</tr>
							<tr class="content">
								<td>
									<strong><?php echo $order_data['name']; ?></strong> <br>

									<?php echo $order_data['address']; ?> <br>
									<?php echo $order_data['city']; ?>,  <?php echo $order_data['state']; ?><br>

									Contact No.: <?php echo $order_data['phone']; ?> <br> <br>

									<strong>Description</strong> <br> <br>

									Donation for Maharana Pratap Memorial <br> <br>

									(Contribution to Royal Gramin Vikas Samiti for <br> Maharana Pratap Memorial have been notified for <br> 50% deduction from Taxable Income under Section 80G <br> of Income Tax act, 1961) <br> <br>

									महाराणा प्रताप स्मारक के लिए दान <br> <br>

									(आयकर अधिनियम, 1961 की धारा 80 जी के तहत <br> "कर योग्य" आय से 50% कटौती के लिए महाराणा प्रताप स्मारक <br> के लिए रॉयल ग्रामीण विकास समिति के योगदान को <br> अधिसूचित किया गया है।) <br> <br>

									<strong>Amount In Words</strong> <br>

									<?php
									$f = new NumberFormatter('en', NumberFormatter::SPELLOUT);
									echo ucwords($f->format($order_data['amount']));
									?>
								</td>
								<td class="font-weight-bold">
									Receipt No. : <?php echo DNM_Helper::get_prefix() . $order_data['order_id']; ?> <br>
									Date : <?php echo DNM_Config::date_format_text($order->created_at); ?> <br>

									<br> <br>

									Received : <?php echo (DNM_Config::get_amount_text($order_data['amount'])); ?><br>
									Payment Mode : Online Transfer <br> <br>

									<img class="img-fluid fs-6" width="150px" src="<?php echo DNM_PLUGIN_URL . '/assets/images/signature.png'; ?>" alt="signature"><br>
									<strong class="text-center"> Authorized Signatory </strong>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>