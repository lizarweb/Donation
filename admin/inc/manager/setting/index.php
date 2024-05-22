<?php
defined('ABSPATH') || die();
require_once DNM_PLUGIN_DIR_PATH . '/includes/helpers/DNM_Config.php';
require_once DNM_PLUGIN_DIR_PATH . '/includes/helpers/DNM_Helper.php';

$currency        = DNM_Helper::currency_symbols();
$active_currency = DNM_Config::get_currency();

$date_formats       = DNM_Helper::date_formats();
$active_date_format = DNM_Config::date_format();

$prefix             = DNM_Helper::get_prefix();
$logo               = DNM_Helper::get_logo();
$phone_pay_settings = DNM_Config::get_phone_pay_settings();

$email_enable = DNM_Config::get_email_settings();
?>

<div class="container-fluid">
	<div class="row">
		<div class="col-12 mt-3">
			<h2><?php esc_html_e('Settings', 'donation'); ?></h2>
		</div>
		<!-- Vertical nav tabs -->
		<div class="col-lg-3">
			<div class="container-fluid">
				<div class="row">
					<div class="card">
						<div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
							<button class="nav-link active text-start" id="v-pills-general-tab" data-bs-toggle="pill" data-bs-target="#v-pills-general" type="button" role="tab" aria-controls="v-pills-general" aria-selected="true">
								<i class="bi bi-gear"></i> <?php echo __('General Settings', 'donation'); ?>
							</button>
							<button class="nav-link text-start" id="v-pills-payment-tab" data-bs-toggle="pill" data-bs-target="#v-pills-payment" type="button" role="tab" aria-controls="v-pills-payment" aria-selected="false">
								<i class="bi bi-credit-card"></i> <?php echo __('Payment Settings', 'donation'); ?>
							</button>
							<button class="nav-link text-start" id="v-pills-email-tab" data-bs-toggle="pill" data-bs-target="#v-pills-email" type="button" role="tab" aria-controls="v-pills-email" aria-selected="false">
								<i class="bi bi-envelope"></i> <?php echo __('Email Settings', 'donation'); ?>
							</button>
							<button class="nav-link text-start" id="v-pills-email-template-tab" data-bs-toggle="pill" data-bs-target="#v-pills-email-template" type="button" role="tab" aria-controls="v-pills-email-template" aria-selected="false">
								<i class="bi bi-mailbox"></i> <?php echo __('Email Templates', 'donation'); ?>
							</button>
							<button class="nav-link text-start" id="v-pills-shortcodes-tab" data-bs-toggle="pill" data-bs-target="#v-pills-shortcodes" type="button" role="tab" aria-controls="v-pills-shortcodes" aria-selected="false">
							<i class="bi bi-code-slash"></i> <?php echo __('Shortcodes', 'donation'); ?>
							</button>
						</div>
					</div>
				</div>
			</div>
		</div>

		<!-- Tab panes -->
		<div class="col-lg-9">
			<div class="container-fluid">
				<div class="row">
					<div class="card">
						<div class="tab-content" id="v-pills-tabContent">
							<div class="tab-pane fade show active" id="v-pills-general" role="tabpanel" aria-labelledby="v-pills-general-tab">
								<!-- General settings form -->
								<form action="<?php echo esc_url(admin_url('admin-ajax.php')); ?>" method="post" id="dnm-save-settings-form" enctype="multipart/form-data">
									<?php wp_nonce_field('dnm_save_general_settings', 'nonce'); ?>
									<input type="hidden" name="action" value="dnm-save-general-settings">
									<h1 class="p-2 mb-4"><?php echo __('General Settings', 'donation'); ?></h1>
									<div class="container">
										<div class="row">
											<div class="col-md-6">
												<div class="mb-3">
													<label for="currency" class="form-label"><?php echo __('Currency', 'donation'); ?></label>
													<select name="currency" class="form-select" id="currency">
														<?php foreach ($currency as $key => $value) { ?>
															<option value="<?php echo $key; ?>" <?php echo $key == $active_currency ? 'selected' : ''; ?>><?php echo $key; ?></option>
														<?php } ?>
													</select>
												</div>
												<div class="mb-3">
													<label for="date_format" class="form-label"><?php echo __('Date Format', 'donation'); ?></label>
													<select name="date_format" class="form-select" id="date_format">
														<?php foreach ($date_formats as $key => $value) { ?>
															<option value="<?php echo $key; ?>" <?php echo $key == $active_date_format ? 'selected' : ''; ?>><?php echo $value; ?></option>
														<?php } ?>
													</select>

												</div>
												<div class="mb-3">
													<label for="order_id_prefix" class="form-label"><?php echo __('Order ID Prefix', 'donation'); ?></label>
													<input name="prefix" type="text" class="form-control" id="order_id_prefix" value="<?php echo esc_attr($prefix); ?>">
												</div>

												<div class="mb-3">
													<label for="logo" class="form-label"><?php echo __('Upload Logo', 'donation'); ?></label>
													<input class="form-control" type="file" id="logo" name="logo" accept="image/png, image/jpeg">
												</div>

											</div>
											<div class="col-md-6">
												<div class="mw-50">
													<img src="<?php echo $logo; ?>" class="img-fluid py-3 " alt="Responsive image">
												</div>
											</div>
										</div>
										<button type="submit" class="btn btn-dark" id="dnm-save-settings-btn"><?php echo __('Save General Settings', 'donation'); ?></button>
									</div>
								</form>
							</div>
							<div class="tab-pane fade" id="v-pills-payment" role="tabpanel" aria-labelledby="v-pills-payment-tab">

								<!-- Payment settings form -->
								<form action="<?php echo esc_url(admin_url('admin-ajax.php')); ?>" method="post" id="dnm-save-payment-settings-form" enctype="multipart/form-data">
									<?php wp_nonce_field('dnm_save_payment_settings', 'nonce'); ?>
									<input type="hidden" name="action" value="dnm-save-payment-settings">
									<h1 class="p-2 mb-4"><?php echo __('Payment Settings', 'donation'); ?></h1>
									<div class="container">
										<div class="row">
											<div class="col-md-6">
												<div class="accordion" id="paymentSettingsAccordion">
													<div class="accordion-item">
														<h2 class="accordion-header" id="headingOne">
															<button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
																PhonePay
															</button>
														</h2>
														<div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#paymentSettingsAccordion">
															<div class="accordion-body">
																<div class="form-check mb-3 ">
																	<input class="form-check-input" type="checkbox" id="phone_pay_enable" name="phone_pay_enable" <?php checked($phone_pay_settings['phone_pay_enable'], 1); ?>>
																	<label class="form-check-label" for="phone_pay_enable">Enable Payment Gateway</label>
																</div>
																<div class="form-group mb-3">
																	<label for="phone_pay_redirect_url">PhonePay Redirect URL</label>
																	<input type="text" class="form-control" id="phone_pay_redirect_url" name="phone_pay_redirect_url" value="<?php echo esc_attr($phone_pay_settings['phone_pay_redirect_url']); ?>">
																</div>
																<div class="form-group mb-3">
																	<label for="phone_pay_mode">Select Payment Mode:</label>
																	<select class="form-select" id="phone_pay_mode" name="phone_pay_mode">
																		<option value="DEV" <?php selected($phone_pay_settings['phone_pay_mode'], 'DEV'); ?>>DEV</option>
																		<option value="PROD" <?php selected($phone_pay_settings['phone_pay_mode'], 'PROD'); ?>>PROD</option>
																	</select>
																</div>

																<div class="form-group mb-3">
																	<label for="phone_pay_merchant_id">PhonePay Merchant ID</label>
																	<input type="text" class="form-control" id="phone_pay_merchant_id" name="phone_pay_merchant_id" value="<?php echo esc_attr($phone_pay_settings['phone_pay_merchant_id']); ?>">
																</div>
																<div class="form-group mb-3">
																	<label for="phone_pay_merchant_user_id">PhonePay Merchant USER ID</label>
																	<input type="text" class="form-control" id="phone_pay_merchant_user_id" name="phone_pay_merchant_user_id" value="<?php echo esc_attr($phone_pay_settings['phone_pay_merchant_user_id']); ?>">
																</div>
																<div class="form-group mb-3">
																	<label for="phone_pay_salt_key">PhonePay Salt Key</label>
																	<input type="text" class="form-control" id="phone_pay_salt_key" name="phone_pay_salt_key" value="<?php echo esc_attr($phone_pay_settings['phone_pay_salt_key']); ?>">
																</div>
																<div class="form-group mb-3">
																	<label for="phone_pay_salt_index">PhonePay Salt Index</label>
																	<input type="text" class="form-control" id="phone_pay_salt_index" name="phone_pay_salt_index" value="<?php echo esc_attr($phone_pay_settings['phone_pay_salt_index']); ?>">
																</div>
															</div>
														</div>
													</div>
													<!-- <div class="accordion-item">
														<h2 class="accordion-header" id="headingTwo">
															<button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
																paymentGateway
															</button>
														</h2>
														<div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#paymentSettingsAccordion">
															<div class="accordion-body">
																<div class="form-group">
																	<label for="gpay-user">GPay User</label>
																	<input type="text" class="form-control" id="gpay-user" name="gpayUser">
																</div>
																<div class="form-group">
																	<label for="gpay-pass">GPay Password</label>
																	<input type="password" class="form-control" id="gpay-pass" name="gpayPass">
																</div>
															</div>
														</div>
													</div> -->
													<!-- Add more accordion items here for "Extra" -->
												</div>
											</div>
										</div>
										<button type="submit" class="btn btn-dark mt-3" id="dnm-save-payment-setting-btn"><?php echo __('Save Payment Settings', 'donation'); ?></button>
									</div>
								</form>
							</div>

							<div class="tab-pane fade" id="v-pills-email" role="tabpanel" aria-labelledby="v-pills-email-tab">

								<!-- Email settings form -->
								<form action="<?php echo esc_url(admin_url('admin-ajax.php')); ?>" method="post" id="dnm-save-email-settings-form" enctype="multipart/form-data">
									<?php wp_nonce_field('dnm_save_email_settings', 'nonce'); ?>
									<input type="hidden" name="action" value="dnm-save-email-settings">
									<h1 class="p-2 mb-4"><?php echo __('Email Settings', 'donation'); ?></h1>

									<div class="container">
										<div class="row">
											<div class="col-md-6">
												<div class="form-check mb-3 ">
													<input class="form-check-input" type="checkbox" id="email_enable" name="email_enable" <?php checked($email_enable['email_enable'], 1); ?>>
													<label class="form-check-label" for="email_enable">Enable email Notfications</label>
												</div>
											</div>
											<div class="col-md-6">

											</div>
										</div>
										<button type="submit" class="btn btn-dark" id="dnm-save-settings-btn"><?php echo __('Save Email Settings', 'donation'); ?></button>
									</div>

								</form>
							</div>

							<div class="tab-pane fade" id="v-pills-email-template" role="tabpanel" aria-labelledby="v-pills-email-template-tab">

							<?php 
							$email_tempates = DNM_Config::get_email_templates();

							?>

								<!-- Email settings form -->
								<form action="<?php echo esc_url(admin_url('admin-ajax.php')); ?>" method="post" id="dnm-save-email-template-settings-form" enctype="multipart/form-data">
									<?php wp_nonce_field('dnm_save_email_template_settings', 'nonce'); ?>
									<input type="hidden" name="action" value="dnm-save-email-template-settings">
									<h1 class="p-2 mb-4"><?php echo __('Email Templates', 'donation'); ?></h1>

									<div class="container">
										<div class="row mb-3">
											<div class="col-md-12">
												<div class="accordion" id="emailAccordion">
													<div class="accordion-item">
														<h2 class="accordion-header" id="headingOne">
															<button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="false" aria-controls="collapseOne">
																Payment Confirmation Email
															</button>
														</h2>
														<div id="collapseOne" class="accordion-collapse collapse" aria-labelledby="headingOne" data-bs-parent="#emailAccordion">
															<div class="accordion-body">
																<form>
																	<div class="mb-3">
																		<label for="payment_canfirm_subject" class="form-label">Email Subject</label>
																		<input type="text" class="form-control" id="payment_canfirm_subject" name="payment_canfirm_subject" value="<?php echo $email_tempates['payment_canfirm_subject'] ?>">
																	</div>
																	<div class="mb-3">
																		<label for="payment_canfirm_body" class="form-label">Email Body</label>
																		<textarea class="form-control" id="payment_canfirm_body" rows="3" name="payment_canfirm_body"><?php echo $email_tempates['payment_canfirm_body'] ?></textarea>
																	</div>
																</form>
															</div>
														</div>
													</div>
													<!-- <div class="accordion-item">
														<h2 class="accordion-header" id="headingTwo">
															<button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
																Email Template 2
															</button>
														</h2>
														<div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#emailAccordion">
															<div class="accordion-body">
																<form>
																	<div class="mb-3">
																		<label for="emailSubject2" class="form-label">Email Subject</label>
																		<input type="text" class="form-control" id="emailSubject2">
																	</div>
																	<div class="mb-3">
																		<label for="emailBody2" class="form-label">Email Body</label>
																		<textarea class="form-control" id="emailBody2" rows="3"></textarea>
																	</div>
																</form>
															</div>
														</div>
													</div> -->
												</div>
											</div>
											<div class="col-md-6">

											</div>
										</div>
										<button type="submit" class="btn btn-dark" id="dnm-save-settings-btn"><?php echo __('Save Email Templates', 'donation'); ?></button>
									</div>

								</form>
							</div>
							<div class="tab-pane fade" id="v-pills-shortcodes" role="tabpanel" aria-labelledby="v-pills-shortcodes-tab">
								<!-- Shortcodes settings form -->
								<form>
									<h1 class="p-2 mb-4"><?php echo __('Shortcodes', 'donation'); ?></h1>

									<div class="container">
										<div class="row">
											<div class="row align-items-center">
												<div class="col-sm-8 mb-3">
													<label for="donation_shortcode" class="form-label"> <?php echo __('Donation Registration Form Shortcode (Click To Copy)', 'donation'); ?></label>
												</div>
												<div class="col-sm-4 mb-3">
													<button class="btn btn-dark w-100" type="button" id="donation_shortcode">[donation_registration_form]</button>
												</div>
												<div class="col-sm-8 mb-3">
													<label for="fixed_form" class="form-label"> <?php echo __('Fixed Registration Shortcode (Click To Copy)', 'donation'); ?></label>
												</div>
												<div class="col-sm-4 mb-3">
													<button class="btn btn-dark w-100" type="button" id="fixed_form">[fixed_registration_form]</button>
												</div>
												<div class="col-sm-8 mb-3">
													<label for="member_form" class="form-label"> <?php echo __('Membership Registration Shortcode (Click To Copy)', 'donation'); ?></label>
												</div>
												<div class="col-sm-4 mb-3">
													<button class="btn btn-dark w-100" type="button" id="member_form">[member_registration_form]</button>
												</div>
												<div class="col-sm-8 mb-3">
													<label for="payment_status" class="form-label"> <?php echo __('Payment Status (Click To Copy)', 'donation'); ?></label>
												</div>
												<div class="col-sm-4 mb-3">
													<button class="btn btn-dark w-100" type="button" id="payment_status">[payment_status]</button>
												</div>
											</div>
										</div>
									</div>
								</form>
							</div>
						</div>
					</div>

				</div>

			</div>

		</div>
	</div>
</div>