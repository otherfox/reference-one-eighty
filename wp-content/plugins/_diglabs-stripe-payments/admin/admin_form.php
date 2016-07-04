<?php

// Tab definitions.
//
$tabs = array(
    'customers' => 'Customers',
    'charges'   => 'Transfers',
    'events'    => 'Events',
    'setup'     => 'Setup'
);

// Get the currently selected tab
//
$tab = 'customers';
if( isset( $_REQUEST[ 'tab' ] ) )
{
    $tab = $_REQUEST[ 'tab' ];
}

?>

<div class="wrap">
    <div id="icon-options-general" class="icon32"></div>
    <h2>Dig Labs Stripe Payments</h2>

    <div class="stripe_payments_info">
        Need help? Try one of these:
        <a href='http://diglabs.com/stripe/'>Home Page</a>
        <a href='http://diglabs.com/stripe/docs/'>Documentation</a>
        <a href='http://diglabs.com/stripe/payment-notification/'>Email &amp; Payment Notifications</a>
        <a href='http://diglabs.com/stripe/https-faq/'>HTTPS</a>
    </div>

    <h2 class="nav-tab-wrapper">
        <?php
        foreach( $tabs as $key => $title )
        {
            $active = ( $key == $tab ) ? 'nav-tab-active' : '';
            echo "<a class='nav-tab $active' href='?page=" . DLSP_ADMIN_PAGE . "&tab=$key'>$title</a>";
        }
        ?>
    </h2>

    <?php require_once $tab . "-form.php"; ?>

</div>