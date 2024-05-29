<?php
defined( 'ABSPATH' ) || die();
require_once DNM_PLUGIN_DIR_PATH . 'includes/helpers/DNM_Config.php';
?>

<div class="dnm-container">
	<div class="dnm-row">
		<div class="dnm-col-12">
			<form action="<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>" method="post" id="dnm-save-membership-registration-form">
				<?php wp_nonce_field( 'dnm_save_membership_registration_form', 'nonce' ); ?>
				<input type="hidden" name="action" value="dnm_save_membership_registration_form">

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
					<input type="text" class="dnm-form-control" id="city" name="city" placeholder="<?php echo __( 'Enter District Here', 'donation' ); ?>" autocomplete="address-level2" required>
				</div>
				<div class="dnm-mb-3">
					<label for="address" class="dnm-form-label"><?php echo __( 'Address', 'donation' ); ?></label>
					<textarea class="dnm-form-control" id="address" name="address" placeholder="<?php echo __( 'Enter Address Here', 'donation' ); ?>" autocomplete="street-address"></textarea>
				</div>

				<div class="dnm-mb-3">
					<label for="reference_id" class="dnm-form-label"><?php echo __( 'Refrence ID (Optional)', 'donation' ); ?></label>
					<input type="text" class="dnm-form-control" id="reference_id" name="reference_id" placeholder="<?php echo __( 'Enter Refrence ID (Optional) Here', 'donation' ); ?>">
				</div>

				<!-- <div class="dnm-mb-3">
					<label for="reference_id" class="dnm-form-label"><?php echo __( 'AuthRequestId (Required)', 'donation' ); ?></label><span class="text-danger">*</span></label>
					<input type="text" class="dnm-form-control" id="reference_id" name="reference_id" placeholder="<?php echo __( 'Enter Refrence ID (Optional) Here', 'donation' ); ?>">
				</div> -->

				<div class="dnm-mb-3">
					<label for="amount" class="dnm-form-label"><?php echo __( 'Amount', 'donation' ); ?></label>
					<div class="dnm-form-check">
						<input class="dnm-form-check-input" type="radio" id="amount_101" name="amount" value="101">
						<label class="dnm-form-check-label" for="amount_101"><?php esc_attr_e( DNM_Config::get_amount_text( 101 ) ); ?></label>
					</div>

					<div class="dnm-form-check">
						<input class="dnm-form-check-input" type="radio" id="amount_251" name="amount" value="251">
						<label class="dnm-form-check-label" for="amount_251"><?php esc_attr_e( DNM_Config::get_amount_text( 251 ) ); ?></label>
					</div>

					<div class="dnm-form-check">
						<input class="dnm-form-check-input" type="radio" id="amount_501" name="amount" value="501">
						<label class="dnm-form-check-label" for="amount_501"><?php esc_attr_e( DNM_Config::get_amount_text( 501 ) ); ?></label>
					</div>
					<div class="dnm-form-check">
						<input class="dnm-form-check-input" type="radio" id="amount_11000" name="amount" value="1100" checked>
						<label class="dnm-form-check-label" for="amount_11000"><?php esc_attr_e( DNM_Config::get_amount_text( 1100 ) ); ?></label>
					</div>
				</div>

				<button type="submit" class="dnm-btn dnm-btn-primary">
					<?php echo __( 'Pay Now', 'donation' ); ?>
				</button>
			</form>
		</div>
	</div>
</div>
