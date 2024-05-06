<?php
?>

<div class="container-fluid">
	<div class="row">
		<div class="col-12 mt-3">
			<h2>Settings</h2>
		</div>
		<!-- Vertical nav tabs -->
		<div class="col-md-3">
			<div class="container">
				<div class="row">
					<div class="card">
						<div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
							<button class="nav-link active text-start" id="v-pills-general-tab" data-bs-toggle="pill" data-bs-target="#v-pills-general" type="button" role="tab" aria-controls="v-pills-general" aria-selected="true">
								<i class="bi bi-gear-fill"></i> General Settings
							</button>
							<button class="nav-link text-start" id="v-pills-payment-tab" data-bs-toggle="pill" data-bs-target="#v-pills-payment" type="button" role="tab" aria-controls="v-pills-payment" aria-selected="false">
								<i class="bi bi-credit-card-fill"></i> Payment Settings
							</button>
							<button class="nav-link text-start" id="v-pills-shortcodes-tab" data-bs-toggle="pill" data-bs-target="#v-pills-shortcodes" type="button" role="tab" aria-controls="v-pills-shortcodes" aria-selected="false">
								<i class="bi bi-code-square"></i> Shortcodes
							</button>
						</div>
					</div>
				</div>
			</div>
		</div>

		<!-- Tab panes -->
		<div class="col-md-9">
			<div class="container">
				<div class="row">
					<div class="card">
						<div class="tab-content" id="v-pills-tabContent">
							<div class="tab-pane fade show active" id="v-pills-general" role="tabpanel" aria-labelledby="v-pills-general-tab">
								<!-- General settings form -->
								<form>
									<h1 class="p-2 mb-4">General Settings</h1>
									<div class="container">
										<div class="row">
											<div class="col-md-6">
												<div class="mb-3">
													<label for="input1" class="form-label">Date Format</label>
													<select class="form-select" id="input1">
														<option selected>Choose...</option>
														<option value="1">Option 1</option>
														<option value="2">Option 2</option>
														<option value="3">Option 3</option>
													</select>

												</div>
												<div class="mb-3">
													<label for="input2" class="form-label">Currency</label>
													<select class="form-select" id="input2">
														<option selected>Choose...</option>
														<option value="1">Option 1</option>
														<option value="2">Option 2</option>
														<option value="3">Option 3</option>
													</select>

												</div>

												<div class="mb-3">
													<label for="logo" class="form-label">Upload Logo</label>
													<input class="form-control" type="file" id="logo" name="logo" accept="image/png, image/jpeg">
												</div>

												<div class="mb-3">
													<label for="input3" class="form-label">Input 3</label>
													<input type="text" class="form-control" id="input3" placeholder="Dummy Input 3">
												</div>


											</div>
										</div>
									</div>

									<button type="button" class="btn btn-dark">Save General Settings</button>
								</form>
							</div>
							<div class="tab-pane fade" id="v-pills-payment" role="tabpanel" aria-labelledby="v-pills-payment-tab">
								<!-- Payment settings form -->
								<form>
									<h1 class="p-2 mb-4">Payment Settings</h1>
									<button type="button" class="btn btn-dark">Save Payment Settings</button>
								</form>
							</div>
							<div class="tab-pane fade" id="v-pills-shortcodes" role="tabpanel" aria-labelledby="v-pills-shortcodes-tab">
								<!-- Shortcodes settings form -->
								<form>
									<h1 class="p-2 mb-4">Shortcodes</h1>
									<div class="row g-3 align-items-center">
										<div class="col-sm-8">
											<label for="donation_shortcode" class="form-label">Donation Registration Form Shortcode (Click To Copy):</label>
										</div>
										<div class="col-sm-4">
											<button class="btn btn-dark w-100" type="button" id="donation_shortcode">[donation_registration_form]</button>
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