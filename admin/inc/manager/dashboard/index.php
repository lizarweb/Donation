<?php
defined('ABSPATH') || die();

require_once DNM_PLUGIN_DIR_PATH . 'includes/constants.php';

require_once DNM_PLUGIN_DIR_PATH . 'admin/inc/manager/orders/DNM_Order.php';

// get Fixed orders count
$fixed_orders = DNM_Order::get_orders_count('11000');
$custom_orders = DNM_Order::get_orders_count('custom');
$membership_orders = DNM_Order::get_orders_count('membership');
$customers = DNM_Order::get_customers_count();
// $referenced_users = DNM_Order::get_referenced_users_count();
$total_orders = $fixed_orders + $custom_orders + $membership_orders;

?>
<div class="container-fluid">
  <section id="minimal-statistics">
    <div class="row">
      <div class="col-12 mt-3 mb-1">
        <h4 class="text-uppercase"><?php esc_html_e('Donation Statistics', 'donation'); ?></h4>
        <p><?php esc_html_e('Your generous donation helps us continue our work', 'donation'); ?></p>
      </div>
    </div>
    <div class="row">
      <div class="col-xl-3 col-sm-6 col-12">
        <div class="card">
          <div class="card-body">
            <div class="d-flex align-items-center">
              <div class="flex-grow-1">
                <h3 class="card-title"><?php echo $fixed_orders; ?></h3>
                <p class="card-text">Fixed Donations</p>
              </div>
              <div>
                <i class="bi bi-cash-coin text-danger fs-3"></i>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-xl-3 col-sm-6 col-12">
        <div class="card">
          <div class="card-body">
            <div class="d-flex align-items-center">
              <div class="flex-grow-1">
                <h3 class="card-title"><?php echo $custom_orders; ?></h3>
                <p class="card-text">Custom Donations</p>
              </div>
              <div>
                <i class="bi bi-gift text-success fs-3"></i>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-xl-3 col-sm-6 col-12">
        <div class="card">
          <div class="card-body">
            <div class="d-flex align-items-center">
              <div class="flex-grow-1">
                <h3 class="card-title"><?php echo $membership_orders; ?></h3>
                <p class="card-text">Membership Donations</p>
              </div>
              <div>
                <i class="bi bi-gift text-success fs-3"></i>
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <div class="col-xl-3 col-sm-6 col-12">
        <div class="card">
          <div class="card-body">
            <div class="d-flex align-items-center">
              <div class="flex-grow-1">
                <h3 class="card-title"><?php echo $total_orders; ?></h3>
                <p class="card-text">Total Donations</p>
              </div>
              <div>
                <i class="bi bi-bookmarks text-info fs-3"></i>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-xl-3 col-sm-6 col-12">
        <div class="card">
          <div class="card-body">
            <div class="d-flex align-items-center">
              <div class="flex-grow-1">
                <h3 class="card-title"><?php echo $customers; ?></h3>
                <p class="card-text">Users</p>
              </div>
              <div>
                <i class="bi bi-people text-warning fs-3"></i>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>