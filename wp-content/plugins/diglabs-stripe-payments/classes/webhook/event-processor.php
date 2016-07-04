<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if( !class_exists( 'DigLabs_Stripe_Webhook_Event_Processor' ) )
{
    class DigLabs_Stripe_Webhook_Event_Processor implements DigLabs_Stripe_Webhook_Processor_Interface
    {
        // Flag to enable emails using the stripe.com webhook tests.
        //	Stripe.com generates fake events, but they don't have real
        //	data backing them up. So calls to fetch invoices or customers
        //	throw exceptions. Setting this flag to true, allows these
        //	exceptions to be ignored.
        private $testing = false;

        private $events_processed = array('charge.succeeded', 'invoice.payment_failed', 'charge.refunded');

        public function process($data)
        {
            // There are some longer running processes that can follow. However,
            //  this function is called by the stripe.com webhook callback.
            //
            if( !$this->testing )
            {
                ob_end_clean();
                header( "Connection: close" );
                ignore_user_abort(); // optional
                ob_start();
                echo( 'notification received' );
                $size = ob_get_length();
                header( "Content-Length: $size" );
                ob_end_flush(); // Strange behaviour, will not work
                flush(); // Unless both are called !
            }

            // Now that stripe.com has their response, we can continue to process.
            //
            $log    = DigLabs_Stripe_Payments::log();
            $prelog = "CORE: ";

            $body = @file_get_contents( 'php://input' );
            $json = json_decode( $body );
            echo "ID: " . $json->id . "\n";
            echo "TYPE: " . $json->type . "\n";
            $log->info( $prelog . "Processing: " . $json->id );

            // Set the api keys based upon the post data (insecure)
            //
            $settings = new DigLabs_Stripe_Helpers_Settings();
            if($json->livemode==true)
            {
                Stripe::setApiKey($settings->liveSecretKey);
            }
            else
            {
                Stripe::setApiKey($settings->testSecretKey);
            }

            // For better security retrieve the event from stripe.com
            //
            $event = $json;
            try
            {
                $event = Stripe_Event::retrieve($json->id);   // 1-4 seconds
            }
            catch (Exception $e)
            {
                if(!$this->testing)
                {
                    $log->error($prelog . 'Event not found. id=' . $json->id);
                    return;
                }
            }

            // Reset the api keys based upon the data received from stripe.com (secure)
            //
            if($event->livemode==true)
            {
                Stripe::setApiKey($settings->liveSecretKey);
            }
            else
            {
                Stripe::setApiKey($settings->testSecretKey);
            }

            // Collect event data
            //
            $country_helper = new DigLabs_Stripe_I18N_Country_Helper();
            $country_iso    = $settings->getCountryIso();
            $type           = $event->type;
            $prelog         = 'CORE: ID=' . $event->id . ', ';
            $log->info( $prelog . '------------------------- WEBHOOK START -------------------------' );
            $log->info( $prelog . 'TYPE=' . $type );
            if( !in_array( $type, $this->events_processed ) )
            {
                // Raise this for others to process and exit.
                //
                do_action( 'stripe_payment_notification', $event, $log );
                if( $type == 'invoice.created' && !$event->data->object->closed )
                {
                    // Raise this in a special handler to allow others to add invoice items.
                    //
                    do_action( 'stripe_invoice_created_notification', $event, $log );

                    // Add tax to the invoice.
                    //
                    $customer_id = $event->data->object->customer;
                    try
                    {
                        $customer    = Stripe_Customer::retrieve( $customer_id );
                        $meta        = json_decode( $customer->metadata->diglabs );
                        $country     = isset( $meta->country ) ? $meta->country : null;
                        $state       = isset( $meta->state ) ? $meta->state : null;
                        $tax_rate    = $settings->getTaxRate( $country, $state );
                        if( $tax_rate >= 0 )
                        {
                            // First get the latest copy of the invoice to ensure we have a correct total.
                            // Others may have added line items.
                            //
                            $invoice_id = $event->data->object->id;
                            $invoice    = Stripe_Invoice::retrieve( $invoice_id );
                            $sub_total  = $invoice->total;
                            $tax        = intval( ceil( $sub_total * $tax_rate / 100.0 ) );
                            $total      = $sub_total + $tax;
                            $currency   = $settings->getCurrencySymbol();

                            $msg = "Added tax. Invoice ID: " . $invoice_id . ", Subtotal: " . $sub_total . ", Tax: " . $tax . ", Total: " . $total;
                            echo "$msg\n";
                            $log->info( $msg );
                            Stripe_InvoiceItem::create( array(
                                                            "customer"    => $customer_id,
                                                            "amount"      => $tax,
                                                            "currency"    => $currency,
                                                            "invoice"     => $invoice_id,
                                                            "description" => 'Tax'
                                                        ) );
                        }
                    }
                    catch( Exception $e )
                    {
                        $log->error( $e->getMessage() );
                    }
                }
                return;
            }

            // Parse the event.
            //
            $parser = new DigLabs_Stripe_Webhook_Stripe_Email_Parser();
            $result = $parser->parse_event( $event );
            $log->debug( $prelog . 'DATA=' . json_encode( (array)$result ) );

            // Package up a subset of data to be emailed to the customer.
            //
            $data               = array();
            $data[ 'Personal' ] = 'section';
            $data[ 'Name' ]     = $result->name;
            if( isset( $result->email ) )
            {
                $data[ 'Email' ] = $result->email;
            }

            // Product section
            //
            if( isset( $result->product ) || isset( $result->subscription ) || count( $result->plans ) > 0 )
            {
                $data[ 'Product(s)' ] = 'section';
            }
            if( isset( $result->product ) )
            {
                $data[ 'Product' ] = $result->product;
            }
            if( isset( $result->subscription ) )
            {
                $data[ 'Subscription' ] = $result->subscription;
            }
            foreach( $result->plans as $i => $plan )
            {
                $name = 'Plan';
                if( count( $result->plans ) > 1 )
                {
                    $name .= ' #' . ( $i + 1 );
                }
                $value         = $plan[ 'name' ] . ' (' . $plan[ 'interval' ] . ')';
                $data[ $name ] = $value;
            }

            // Payment information.
            //
            $data[ 'Billing Information' ] = 'section';
            $data[ 'Invoice Id' ]          = $result->charge_id;
            $data[ 'Card Type' ]           = $result->card_type;
            $data[ 'Card Last 4' ]         = $result->card_last4;

            // Line items and total
            //
            $data[ 'Charges and Total' ] = 'section';
            $line_items                  = array_reverse( $result->lines );
            foreach( $line_items as $description => $amount )
            {
                $data[ $description ] = $country_helper->currency( $amount, $country_iso );
            }
            $data[ 'Total' ] = $country_helper->currency( $result->amount, $country_iso );

            // Allow for others to filter the data shown in the email.
            //
            $data = apply_filters( 'stripe_payment_data_filter', $data );

            // Raise the notification to anyone who has registered for this action
            //
            do_action( 'stripe_payment_notification', $event, $log, array(&$data) );

            // Send out a confirmation email
            //
            if( $this->testing )
            {
                var_dump( $data );
            }
            $email = new DigLabs_Stripe_Helpers_Email();
            $email->sendReceipt( $result->to_address, $result->subject, $result->subject, $result->body, $data );

            $log->info( $prelog . '------------------------- WEBHOOK STOP  -------------------------' );
        }
    }
}