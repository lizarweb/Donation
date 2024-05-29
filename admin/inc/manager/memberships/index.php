<?php 

defined( 'ABSPATH' ) || die();

require_once DNM_PLUGIN_DIR_PATH . '/includes/helpers/DNM_Helper.php';

$page_url = DNM_Helper::get_page_url('donation-orders');
?>
<div class="container-fluid mt-3">
    <div class="row ">
        <main class="col-md-12 ms-sm-auto col-lg-12 px-md-4">
            <nav class="navbar navbar-light bg-light  rounded">
                <div class="container-fluid">
                    <a class="navbar-brand"><strong>Orders</strong></a>
                    <form class="d-flex">
                        <a href="<?php echo esc_url( $page_url . '&action=save' ); ?>" class="btn btn-dark" ><i class="bi bi-plus-circle-fill"></i> Add New</a>
                    </form>
                </div>
            </nav>
            <div class="table-responsive small">
                <table id="memberships_orders_table" class="table table-sm table-striped display" style="width:100%">
                    <thead>
                        <tr>
                            <th>#ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Amount</th>
                            <th>Created at</th>
                           
                            
                            <th>Transaction ID</th>
                            <th>Reference ID</th>
                            <th>Referenced By</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody> </tbody>
                    <tfoot>
                        <tr>
                            <th>#ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Amount</th>
                            <th>Created at</th>
                           
                            
                            <th>Transaction ID</th>
                            <th>Reference ID</th>
                            <th>Referenced By</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </main>
    </div>
</div>