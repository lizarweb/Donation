<?php
defined( 'ABSPATH' ) || die();

require_once DNM_PLUGIN_DIR_PATH . 'includes/helpers/DNM_Config.php';
?>

<div class="dnm-container">
	<div class="dnm-row">
		<div class="dnm-col-12">
			<form action="<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>" method="post" id="dnm-save-fixed-registration-form_ref">
				<?php wp_nonce_field( 'dnm_save_fixed_registration_form_ref', 'nonce' ); ?>
				<input type="hidden" name="action" value="dnm_save_fixed_registration_form_ref">

				<div class="dnm-mb-3">
					<label for="name" class="dnm-form-label"><?php echo __( 'Full Name', 'donation' ); ?><span class="text-danger">*</span></label>
					<input type="text" class="dnm-form-control" id="name" name="name" placeholder="<?php echo __( 'Enter Full Name Here', 'donation' ); ?>" autocomplete="name" required>
				</div>
				<div class="dnm-mb-3">
					<label for="email" class="dnm-form-label"><?php echo __( 'Email', 'donation' ); ?></label><span class="text-danger">*</span></label>
					<input type="email" class="dnm-form-control" id="email" name="email" placeholder="<?php echo __( 'Enter Email Here', 'donation' ); ?>" autocomplete="email" required>
				</div>
				<div class="dnm-mb-3">
					<label for="phone" class="dnm-form-label"><?php echo __( 'Phone', 'donation' ); ?><span class="text-danger">*</span></label>
					<input type="text" class="dnm-form-control" id="phone" name="phone" placeholder="<?php echo __( 'Enter Phone Number Here', 'donation' ); ?>" autocomplete="tel" required>
				</div>
				<div class="dnm-mb-3">
					<label for="state" class="dnm-form-label"><?php echo __( 'State', 'donation' ); ?></label>
					<select class="dnm-form-control" id="state" name="state" required>
						<option value=""><?php echo __( 'Select State', 'donation' ); ?></option>
						<?php
						$states = DNM_Helper::indian_states_list();
						foreach ( $states as $state ) {
							echo '<option value="' . $state . '">' . $state . '</option>';
						}
						?>
					</select>
				</div>
				<div class="dnm-mb-3">
					<label for="city" class="dnm-form-label"><?php echo __( 'District', 'donation' ); ?></label>
					<input type="text" class="dnm-form-control" id="city" name="city" placeholder="<?php echo __( 'Enter District Here', 'donation' ); ?>" autocomplete="address-level2">
				</div>
				<div class="dnm-mb-3">
					<label for="address" class="dnm-form-label"><?php echo __( 'Address', 'donation' ); ?></label>
					<textarea class="dnm-form-control" id="address" name="address" placeholder="<?php echo __( 'Enter Address Here', 'donation' ); ?>" autocomplete="street-address"></textarea>
				</div>

				<div class="dnm-mb-3">
					<label for="reference_id" class="dnm-form-label"><?php echo __( 'Refrence ID (Optional)', 'donation' ); ?></label>
					<input type="text" class="dnm-form-control" id="reference_id" name="reference_id" placeholder="<?php echo __( 'Enter Refrence ID (Optional) Here', 'donation' ); ?>" required>
				</div>

				<!-- <div class="dnm-mb-3">
					<label for="amount" class="dnm-form-label"><?php echo __( 'Amount', 'donation' ); ?></label>
					<input type="number" class="dnm-form-control" id="amount" name="amount" placeholder="<?php echo __( 'Enter Amount Here', 'donation' ); ?>" autocomplete="address-level1">
				</div> -->


				<!-- <div class="dnm-mb-3">
					<label for="payment_method" class="dnm-form-label"><?php echo __( 'Payment Method', 'donation' ); ?></label>
					<select class="dnm-form-control" id="payment_method" name="payment_method">
						<option value=""><?php echo __( 'Select Payment Method', 'donation' ); ?></option>
						<option value="phone_pay"><?php echo __( 'Phone Pay', 'donation' ); ?></option>
						<option value="rocket"><?php echo __( 'Rocket', 'donation' ); ?></option>
						<option value="stripe"><?php echo __( 'Stripe', 'donation' ); ?></option>
					</select>
				</div> -->

				<button type="submit" class="dnm-btn dnm-btn-primary">
					<?php echo __( 'Add Member', 'donation' ); ?>
				</button>
			</form>
		</div>
	</div>
</div>
