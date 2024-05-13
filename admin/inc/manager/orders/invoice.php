<?php
defined( 'ABSPATH' ) || die();
require_once DNM_PLUGIN_DIR_PATH . '/includes/helpers/DNM_Helper.php';
$page_url = esc_url( DNM_Helper::get_page_url( 'donation-orders' ) );

$order_data = array(
	'order_id'       => 0,
	'name'           => '',
	'email'          => '',
	'phone'          => '',
	'address'        => '',
	'amount'         => '',
	'payment_method' => '',
);
if ( isset( $_GET['id'] ) ) {
	$order_id = absint( $_GET['id'] );
	if ( 0 !== $order_id ) {
		$order = DNM_Order::get_order( $order_id );
		if ( ! $order ) {
			wp_safe_redirect( $page_url );
			exit;
		}
		foreach ( $order_data as $key => $value ) {
			$order_data[ $key ] = sanitize_text_field( $order->$key );
		}
	}
}
?>
<div class="container-fluid mt-5 mb-3">
	<div class="row d-flex justify-content-center">
		<div class="col-md-8">
			<button id="dnm-print-invoice" class="btn btn-dark" data-styles='["<?php echo esc_url( DNM_PLUGIN_URL . '/assets/css/bootstrap.min.css' ); ?>"]' data-title="Print Receipt">Print Receipt</button>
			<div class="card" id="printableArea" style="width: 100%!important;">
				<div class="d-flex flex-row p-2"> <img src="https://i.imgur.com/vzlPPh3.png" width="48">
					<div class="d-flex flex-column"> <span class="font-weight-bold">Tax Invoice</span> <small><?php echo DNM_Helper::get_prefix().$order_data['order_id'] ?></small> </div>
				</div>
				<hr>
				<div class="table-responsive p-2">
					<table class="table table-borderless">
						<tbody>
							<tr class="add">
								<td>To</td>
								<td>From</td>
							</tr>
							<tr class="content">
                                <td class="font-weight-bold">Donation Company Name <br> Email: Dantion@test.com <br> India Rajasthan</td>
								<td class="font-weight-bold"><?php echo $order_data['name']; ?> <br>Email: <?php echo $order_data['email'] ?> <br> <?php echo $order_data['address'] ?></td>
							</tr>
						</tbody>
					</table>
				</div>
				<hr>
				<div class="products p-2">
					<table class="table table-borderless">
						<tbody>
							<tr class="add">
								<td>Description</td>
								<td>Days</td>
								<td>Price</td>
								<td class="text-center">Total</td>
							</tr>
							<tr class="content">
								<td>Marketing Collateral</td>
								<td>3</td>
								<td>$1,500</td>
								<td class="text-center">$4,500</td>
							</tr>
						</tbody>
					</table>
				</div>
				<hr>
				<div class="products p-2">
					<table class="table table-borderless">
						<tbody>
							<tr class="add">
								<td></td>
								<td>Subtotal</td>
								<td>GST(10%)</td>
								<td class="text-center">Total</td>
							</tr>
							<tr class="content">
								<td></td>
								<td>$40,000</td>
								<td>2,500</td>
								<td class="text-center">$42,500</td>
							</tr>
						</tbody>
					</table>
				</div>
				<!-- <hr>
				<div class="address p-2">
					<table class="table table-borderless">
						<tbody>
							<tr class="add">
								<td>Bank Details</td>
							</tr>
							<tr class="content">
								<td> Bank Name : ADS BANK <br> Swift Code : ADS1234Q <br> Account Holder : Jelly Pepper <br> Account Number : 5454542WQR <br> </td>
							</tr>
						</tbody>
					</table>
				</div> -->
			</div>
		</div>
	</div>
</div>
<!-- <iframe id="printFrame" style="display:none;"></iframe> -->