<?php
defined('ABSPATH') || die();

require_once DNM_PLUGIN_DIR_PATH . 'includes/helpers/DNM_Config.php';
?>

<div class="container">
    <div class="row">
        <div class="col-12">
            <!-- <h1><?php echo __('Your generous donation helps us continue our work', 'school-management'); ?></h1> -->
            <form action="<?php echo esc_url(admin_url('admin-ajax.php')); ?>" method="post" id="dnm-registration-form">
                <?php wp_nonce_field('dnm_registration', 'nonce'); ?>
                <input type="hidden" name="action" value="dnm-registration">
                <div class="mb-3">
                    <label for="name" class="form-label"><?php echo __('Name', 'school-management'); ?></label>
                    <input type="text" class="form-control" id="name" name="name" >
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label"><?php echo __('Email', 'school-management'); ?></label>
                    <input type="email" class="form-control" id="email" name="email" >
                </div>
                <div class="mb-3">
                    <label for="phone" class="form-label"><?php echo __('Phone', 'school-management'); ?></label>
                    <input type="text" class="form-control" id="phone" name="phone" >
                </div>
                <div class="mb-3">
                    <label for="address" class="form-label"><?php echo __('Address', 'school-management'); ?></label>
                    <textarea class="form-control" id="address" name="address" ></textarea>
                </div>
                <div class="mb-3">
                    <label for="amount" class="form-label"><?php echo __('Amount', 'school-management'); ?></label>
                    <select class="form-control" id="dnm-amount" name="amount" >
                        <option value="11000"> <?php esc_attr_e(DNM_Config::get_amount_text(11000)); ?></option>
                        <option value="custom">Custom</option>
                    </select>
                    <input type="number" class="form-control mt-3" id="customAmount" name="customAmount" style="display: none;" min="1">
                </div>
                <button type="submit" class="btn btn-primary"><?php echo __('Pay Now', 'school-management'); ?></button>
            </form>
        </div>
    </div>
</div>
