<?php
/*
Template Name: Payment Callback
*/

// Prevents wordpress from sending 404 error
//
status_header( 200 );

$log = DigLabs_Stripe_Payments::log();

if( $_SERVER[ 'REQUEST_METHOD' ] != 'POST' )
{
    // This is not a post return an indication that
    //	the URL has been reached successfully. This
    //	helps troubleshooting.
    //
    echo "Your webhook is available at this URL ;-)";
    exit;
}

// The processor. TBD by the post data.
//
$processor = null;

$json = isset( $_POST[ 'json' ] ) ? $_POST[ 'json' ] : null;
if( is_null( $json ) )
{
    $body = @file_get_contents( 'php://input' );
    $json = json_decode( $body );

    $log->info( "Event Processor: " . json_encode( $json ) );
    $processor = new DigLabs_Stripe_Webhook_Event_Processor();
}
else
{
    $log->info( "Legacy Processor" );
    $processor = new DigLabs_Stripe_Webhook_Legacy_Processor();
}
$processor->process( $json );
