<?php
defined( 'ABSPATH' ) || die();

require_once DNM_PLUGIN_DIR_PATH . 'includes/helpers/DNM_Config.php';
?>

<div class="dnm-container">
	<div class="dnm-row">
		<div class="dnm-col-12">
			<form action="<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>" method="post" id="dnm-registration-form">
				<?php wp_nonce_field( 'dnm_registration', 'nonce' ); ?>
				<input type="hidden" name="action" value="dnm-registration">
				<div class="dnm-mb-3">
					<label for="name" class="dnm-form-label"><?php echo __( 'Name', 'donation' ); ?></label>
					<input type="text" class="dnm-form-control" id="name" name="name" autocomplete="name">
				</div>
				<div class="dnm-mb-3">
					<label for="email" class="dnm-form-label"><?php echo __( 'Email', 'donation' ); ?></label>
					<input type="email" class="dnm-form-control" id="email" name="email" autocomplete="email">
				</div>
				<div class="dnm-mb-3">
					<label for="phone" class="dnm-form-label"><?php echo __( 'Phone', 'donation' ); ?></label>
					<input type="text" class="dnm-form-control" id="phone" name="phone" autocomplete="tel">
				</div>
				<div class="dnm-mb-3">
					<label for="address" class="dnm-form-label"><?php echo __( 'Address', 'donation' ); ?></label>
					<textarea class="dnm-form-control" id="address" name="address" autocomplete="street-address"></textarea>
				</div>
				<div class="dnm-mb-3">
					<label for="amount" class="dnm-form-label"><?php echo __( 'Amount', 'donation' ); ?></label>
					<select class="dnm-form-control dnm-mb-3" id="dnm-amount" name="type">
						<option value="11000"> <?php esc_attr_e( DNM_Config::get_amount_text( 11000 ) ); ?></option>
						<option value="custom"><?php echo __( 'Custom', 'donation' ); ?></option>
					</select>
					<input type="number" class="dnm-form-control dnm-mt-3" id="amount" name="amount" style="display: none;" min="1">
				</div>

				<div class="dnm-mb-3">
					<label for="payment_method" class="dnm-form-label"><?php echo __( 'Payment Method', 'donation' ); ?></label>
					<select class="dnm-form-control" id="payment_method" name="payment_method">
						<option value=""><?php echo __( 'Select Payment Method', 'donation' ); ?></option>
						<option value="phone_pay"><?php echo __( 'Phone Pay', 'donation' ); ?></option>
						<option value="rocket"><?php echo __( 'Rocket', 'donation' ); ?></option>
						<option value="stripe"><?php echo __( 'Stripe', 'donation' ); ?></option>
					</select>
				</div>

				<button type="submit" class="dnm-btn dnm-btn-primary">
					<?php echo __( 'Pay Now', 'donation' ); ?>
				</button>
			</form>
		</div>
	</div>
</div>