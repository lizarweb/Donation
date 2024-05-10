<?php
defined( 'ABSPATH' ) || die();
require_once DNM_PLUGIN_DIR_PATH . '/includes/helpers/DNM_Helper.php';

$page_url = DNM_Helper::get_page_url('donation-orders');

$order_id = 0;
?>
<div class="container py-5">
    <div class="row">
        <div class="col-lg-6 mx-auto">
            <form action="<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>" method="post" id="dnm-save-order-form">
                <input type="hidden" name="action" value="dnm_save_order">
                <input type="hidden" name="nonce" value="<?php echo esc_attr( wp_create_nonce( 'dnm_save_order' ) ); ?>">
                <input type="hidden" name="order_id" id="order_id" value="<?php echo esc_attr( $order_id ); ?>">
                <h2 class="mb-4">Order Form</h2>

                <!-- Customer Fields -->
                <div class="mb-3">
                    <label for="name" class="form-label">Name:</label>
                    <input type="text" id="name" name="name" class="form-control">
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email:</label>
                    <input type="email" id="email" name="email" class="form-control">
                </div>
                <div class="mb-3">
                    <label for="phone" class="form-label">Phone:</label>
                    <input type="text" id="phone" name="phone" class="form-control">
                </div>
                <div class="mb-3">
                    <label for="address" class="form-label">Address:</label>
                    <input type="text" id="address" name="address" class="form-control">
                </div>

                <!-- Order Fields -->
                <div class="mb-3">
                    <label for="amount" class="form-label">Amount:</label>
                    <input type="number" id="amount" name="amount" class="form-control">
                </div>
                <div class="mb-3">
                    <label for="payment_method" class="form-label">Payment Method:</label>
                    <input type="text" id="payment_method" name="payment_method" class="form-control">
                </div>

                <button type="submit" class="btn btn-dark" id="dnm-save-order-btn">Submit</button>
            </form>
        </div>
    </div>
</div>