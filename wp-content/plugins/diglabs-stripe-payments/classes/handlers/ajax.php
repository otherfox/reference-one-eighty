<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'DigLabs_Stripe_Handlers_Ajax' ) )
{
    class DigLabs_Stripe_Handlers_Ajax
    {
        private $response = array();
        private $settings;
        private $coupon;
        private $customer;
        private $plan_id;

        private $amount = 0;
        private $initial_fee = 0;
        private $discount = 0;
        private $sub_total = 0;
        private $tax_rate = 0;
        private $tax = 0;
        private $total = 0;

        public function process()
        {
            $log = DigLabs_Stripe_Payments::log();
            $prelog = "AJAX: ";

            // Get the settings
            $this->settings = new DigLabs_Stripe_Helpers_Settings();

            // Create the response array
            //
            $this->response = array(
                'success' => false,
                'cancel'  => false
            );

            if($_SERVER['REQUEST_METHOD'] === 'POST')
            {
                $log->info($prelog . '------------------------- AJAX START  -------------------------');

                $this->extract_post_data();

                // Allow the user to hook into the ajax processing
                //
                $payment_begin_callbacks = DigLabs_Stripe_Payments::GlobalInstance()->get_payment_begin_callbacks();
                foreach( $payment_begin_callbacks as $callback )
                {
                    $function = $this->get_function( $callback );
                    $log->debug( $prelog . 'PREPAY callback - ' . json_encode( $callback ) . ' @ ' . $function->getFileName() );
                    $params = array( &$this->response );
                    if( $function->getNumberOfParameters() == 2 )
                    {
                        $params[ ] = $log;
                    }
                    call_user_func_array( $callback, $params );
                }

                if(!$this->response['cancel'])
                {
                    Stripe::setApiKey( $this->get_secret_api_key() );

                    // Submit the payment and charge the card.
                    try
                    {
                        $this->get_coupon();

                        // Do the main processing.
                        //
                        if( isset( $this->response[ 'diglabs-cart-checkout' ] ) )
                        {
                            $this->process_cart_payment();
                        }
                        else
                        {
                            $this->get_stripe_customer();

                            if( !$this->is_recurring() )
                            {
                                $this->process_non_recurring();
                            }
                            else
                            {
                                $this->process_recurring();
                            }
                        }

                        $this->add_amounts_to_response();

                        // Allow the user to hook into the ajax processing
                        //
                        $payment_end_callbacks = DigLabs_Stripe_Payments::GlobalInstance()->get_payment_end_callbacks();
                        foreach( $payment_end_callbacks as $callback )
                        {
                            $function = $this->get_function( $callback );
                            $log->debug( $prelog . 'POSTPAY callback - ' . json_encode( $callback ) . ' @ ' . $function->getFileName() );
                            $params = array( &$this->response );
                            if( $function->getNumberOfParameters() == 2 )
                            {
                                $params[ ] = $log;
                            }
                            call_user_func_array( $callback, $params );
                        }

                        // Clear the cart
                        //
                        if( isset( $response[ 'stripe-cart-checkout' ] ) )
                        {
                            // This is cart checkout
                            //
                            $cart = new DigLabs_Stripe_Handlers_Cart();
                            $cart->clear();
                        }
                    }
                    catch (Exception $e)
                    {
                        $log->error($prelog . $e->getMessage());
                        $this->response['error'] = $e->getMessage();
                    }
                }
            }

            // Serialize the response back as JSON
            $log->debug( json_encode( $this->response ) );
            $log->info( $prelog . '------------------------- AJAX STOP  -------------------------' );
            echo json_encode( $this->response );
            die();
        }

        private function convert_to_stripe_metadata( $data )
        {
            $helper = new DigLabs_Stripe_Helpers_Stripe();
            $result = $helper->convert_to_stripe_metadata( $data );

            return $result;
        }

        private function get_function( $callback )
        {
            if( is_array( $callback ) )
            {
                // must be a class method
                list( $class, $method ) = $callback;
                return new ReflectionMethod( $class, $method );
            }

            // class::method syntax
            if( is_string( $callback ) && strpos( $callback, "::" ) !== false )
            {
                list( $class, $method ) = explode( "::", $callback );
                return new ReflectionMethod( $class, $method );
            }

            // assume it's a function
            return new ReflectionFunction( $callback );
        }

        private function extract_post_data()
        {
            // Extract the incoming post data
            //
            $keys = array_keys( $_POST );
            $names_to_remove = array('pubkey', 'description', 'amount', 'action', 'token', 'pword', 'pword1', 'pword2', 'url');
            $meta = array();
            foreach($keys as $key)
            {
                if( !in_array( $key, $names_to_remove ) )
                {
                    $meta[$key] = $_POST[$key];
                }
                $this->response[$key] = $_POST[$key];
            }

            // Finalize the stripe.com meta data
            //
            $this->response['meta'] = json_encode( $meta );
        }

        private function is_recurring()
        {
            $this->plan_id = null;
            $recurring     = false;
            if( isset( $_POST[ 'plan' ] ) )
            {
                $this->plan_id = $_POST[ 'plan' ];
                $recurring     = true;
            }
            $this->response[ 'recurring' ] = $recurring;

            return $recurring;
        }

        private function get_secret_api_key()
        {
            $secret_key               = $this->settings->getSecretKey();
            $this->response[ 'mode' ] = $this->settings->isLive ? 'live' : 'test';
            if( $_POST[ 'pubkey' ] )
            {
                $public_key = $_POST[ 'pubkey' ];
                if( $public_key == $this->settings->testPublicKey )
                {
                    $secret_key               = $this->settings->testSecretKey;
                    $this->response[ 'mode' ] = 'test';
                }
            }
            return $secret_key;
        }

        private function get_coupon()
        {
            // Get any coupon that is being used.
            //
            $this->coupon = null;
            if( isset( $_POST[ 'coupon' ] ) && $_POST[ 'coupon' ] != "" )
            {
                // Try to retrieve the coupon.
                //
                $this->coupon                               = Stripe_Coupon::retrieve( $_POST[ 'coupon' ] );
                $this->response[ 'coupon-id' ]              = $this->coupon->id;
                $this->response[ 'coupon-max-redemptions' ] = $this->coupon->max_redemptions;
                $this->response[ 'coupon-times-redeemed' ]  = $this->coupon->times_redeemed;
                $this->response[ 'coupon-redeem-by' ]       = gmdate( "M d Y H:i:s", $this->coupon->redeem_by ) . ' UTC';
                $this->response[ 'coupon-percent' ]         = $this->coupon->percent_off;

                // Check the validity of the coupon
                //
                if( $this->coupon->times_redeemed >= $this->coupon->max_redemptions )
                {
                    $this->response[ 'error' ] = 'Coupon can only be redeemed ' . intval( $this->coupon->max_redemptions ) . ' time(s)';
                    echo json_encode( $this->response );
                    die();
                }
                $now = gmmktime();
                if( $now > $this->coupon->redeem_by )
                {
                    $this->response[ 'error' ] = 'Coupon expired on ' . gmdate( "M d Y H:i:s", $this->coupon->redeem_by ) . ' UTC';
                    echo json_encode( $this->response );
                    die();
                }

                // If we made it here, the coupon is valid
                //
            }
        }

        private function get_stripe_customer()
        {
            // Create a Stripe customer to allow
            //	charging this card at a later time.
            //
            // Create the data to submit to Stripe's secure processing
            //	Note: Card number data is not accessible. The code can
            //	only access a 'token' that was previously generated by
            //	Stripe via AJAX post.
            //

            $cust_params              = array(
                'card'        => $_POST[ 'token' ],
                'email'       => $_POST[ 'email' ],
                'metadata'    => $this->convert_to_stripe_metadata( $this->response[ 'meta' ] ),
                'expand'      => array( 'default_card' )
            );
            $this->customer                 = Stripe_Customer::create( $cust_params );
            $this->response[ 'cust_id' ]    = $this->customer->id;
            $this->response[ 'card_type' ]  = $this->customer->default_card->type;
            $this->response[ 'card_last4' ] = $this->customer->default_card->last4;
        }

        private function add_amounts_to_response()
        {
            $country_helper = new DigLabs_Stripe_I18N_Country_Helper();
            $country_code   = $this->settings->getCountryIso();

            $this->response[ 'success' ]       = true;
            $this->response[ 'amount' ]        = $this->amount / 100;
            $this->response[ 'amount_str' ]    = $country_helper->currency( $this->amount / 100, $country_code );
            $this->response[ 'discount' ]      = $this->discount / 100;
            $this->response[ 'discount_str' ]  = $country_helper->currency( $this->discount / 100, $country_code );
            $this->response[ 'init_fee' ]      = $this->initial_fee / 100;
            $this->response[ 'init_fee_str' ]  = $country_helper->currency( $this->initial_fee / 100, $country_code );
            $this->response[ 'sub_total' ]     = $this->sub_total / 100;
            $this->response[ 'sub_total_str' ] = $country_helper->currency( $this->sub_total / 100, $country_code );
            $this->response[ 'taxrate' ]       = $this->tax_rate;
            $this->response[ 'tax' ]           = $this->tax / 100;
            $this->response[ 'tax_str' ]       = $country_helper->currency( $this->tax / 100, $country_code );
            $this->response[ 'total' ]         = $this->total / 100;
            $this->response[ 'total_str' ]     = $country_helper->currency( $this->total / 100, $country_code );
        }

        private function process_cart_payment()
        {
            $prelog = "AJAX - CART: ";
            $this->response[ 'type' ] = 'single payment - cart';

            // Get the cart total
            //
            $cart = new DigLabs_Stripe_Handlers_Cart();
            $items = $cart->get_items();
            $cart_total = 0;
            foreach( $items as $item )
            {
                $count = $item->count;
                $unit_cost = $item->unit_cost;
                $total = intval( $count ) * floatval( $unit_cost );
                $cart_total += $total;
            }
            $cart_total = intval( $cart_total * 100 );

            // Ensure we are charging the correct amount
            //	Note: amount is in cents on this call
            //
            $amount = intval( $this->response[ 'amount' ] );
            if( isset( $this->response[ 'discount' ] ) )
            {
                $cart_total = intval( intval( 100 * $cart_total * ( 1 - $this->response[ 'discount' ] / 100.0 ) ) / 100.0 );
            }
            if( $amount != $cart_total )
            {
                $this->log->info( $prelog . "PREPAY CART CHECK: Expected amount (" . $cart_total . ") not equal to the paid amount (" . $amount . ")." );
                $this->response[ 'cancel' ] = true;
                $this->response[ 'error' ]  = "Expected payment amount is " . $cart_total . ".";
                echo json_encode( $this->response );
                die();
            }

            $this->amount    =  $amount;
            $this->sub_total = $this->amount;

            // Create the stripe customer.
            //
            $this->get_stripe_customer();

            // Create an invoice item for each cart item.
            //
            foreach( $items as $item )
            {
                $count = $item->count;
                $unit_cost = $item->unit_cost;
                $total = intval( 100 *intval( $count ) * floatval( $unit_cost ) );
                Stripe_InvoiceItem::create( array(
                                                'customer'    => $this->customer->id,
                                                'amount'      => $total,
                                                'currency'    => $this->settings->getCurrencySymbol(),
                                                'description' => $item->info . ' (QTY=' . $item->count . ')'
                                            ) );
            }

            //	Apply the coupons.
            //
            if( !is_null( $this->coupon ) )
            {
                $this->discount = intval( $this->amount * $this->coupon->percent_off / 100.0 );
                $this->sub_total -= $this->discount;
                Stripe_InvoiceItem::create( array(
                                                'customer'    => $this->customer->id,
                                                'amount'      => -$this->discount,
                                                'currency'    => $this->settings->getCurrencySymbol(),
                                                'description' => 'Discount (' . $this->coupon->percent_off . '%)'
                                            ) );
            }

            // Is there an initial fee on the first
            //	payment?
            //
            if( isset( $this->response[ 'init_fee' ] ) )
            {
                $this->initial_fee = floatval( $this->response[ 'init_fee' ] );
                $this->sub_total += $this->initial_fee;
                Stripe_InvoiceItem::create( array(
                                                'customer'    => $this->customer->id,
                                                'amount'      => $this->initial_fee,
                                                'currency'    => $this->settings->getCurrencySymbol(),
                                                'description' => 'Initial Fee'
                                            ) );
            }

            // Add the tax and calculate the total.
            //
            if( !isset( $this->response[ 'tax-exempt' ] ) )
            {
                $country       = isset( $this->response[ 'country' ] ) ? $this->response[ 'country' ] : null;
                $state         = isset( $this->response[ 'state' ] ) ? $this->response[ 'state' ] : null;
                $this->taxRate = $this->settings->getTaxRate( $country, $state );
                $this->tax     = ceil( $this->sub_total * $this->taxRate / 100.0 );
                if( $this->tax > 0 )
                {
                    Stripe_InvoiceItem::create( array(
                                                    'customer'    => $this->customer->id,
                                                    'amount'      => $this->tax,
                                                    'currency'    => $this->settings->getCurrencySymbol(),
                                                    'description' => 'Tax (' . $this->taxRate . '%)'
                                                ) );
                }
            }
            $this->total = intval( $this->sub_total + $this->tax );

            // Invoice the customer
            //
            $invoice = Stripe_Invoice::create(array(
                                                'customer' => $this->customer->id
                                            ));
            $result = $invoice->pay();

            // Add to the response.
            //
            $this->response[ 'charge' ]         = $result;
            $this->response[ 'id' ]             = $result->id;
            $this->response[ 'transaction_id' ] = $result->id;

            // Clear the cart
            //
            $cart->clear();
        }

        private function process_non_recurring()
        {
            if( isset( $this->response[ 'amount' ] ) )
            {
                // Create a single charge
                $this->response[ 'type' ] = 'single payment';

                // Get the amount from the post.
                //
                $this->amount    = intval( $this->response[ 'amount' ] );
                $this->sub_total = $this->amount;

                //	Apply the coupons.
                //
                if( !is_null( $this->coupon ) )
                {
                    $this->discount = intval( $this->amount * $this->coupon->percent_off / 100.0 );
                    $this->sub_total -= $this->discount;
                }

                // Is there an initial fee on the first
                //	payment?
                //
                if( isset( $this->response[ 'init_fee' ] ) )
                {
                    $this->initial_fee = floatval( $this->response[ 'init_fee' ] );
                    $this->sub_total += $this->initial_fee;
                }

                // Add the tax and calculate the total.
                //
                if( !isset( $this->response[ 'tax-exempt' ] ) )
                {
                    $country       = isset( $this->response[ 'country' ] ) ? $this->response[ 'country' ] : null;
                    $state         = isset( $this->response[ 'state' ] ) ? $this->response[ 'state' ] : null;
                    $this->taxRate = $this->settings->getTaxRate( $country, $state );
                    $this->tax     = ceil( $this->sub_total * $this->taxRate / 100.0 );
                }
                $this->total = intval( $this->sub_total + $this->tax );

                // Create the charge.
                //
                $params = array(
                    'amount'      => $this->total,
                    'currency'    => $this->settings->getCurrencySymbol(),
                    'customer'    => $this->customer->id,
                    'description' => isset( $this->response[ 'description' ] ) ? $this->response[ 'description' ] : 'DigLabs Stripe Plugin Charge',
                    'metadata'    => $this->convert_to_stripe_metadata( $this->response[ 'meta' ] ),
                );
                $charge = Stripe_Charge::create( $params );

                // Add to the response.
                //
                $this->response[ 'charge' ]         = $charge;
                $this->response[ 'id' ]             = $charge->id;
                $this->response[ 'transaction_id' ] = $charge->id;
            }
            else
            {
                // Just create the customer (already done) with no charge.
                //
            }
        }

        private function process_recurring()
        {
            // Create a recurring payment
            $this->response[ 'type' ] = 'recurring payment';

            // Create the data to submit to Stripe's secure processing
            //	Note: Card number data is not accessible. The code can
            //	only access a 'token' that was previously generated by
            //	Stripe via AJAX post.

            $plan = null;
            if( strtolower( $this->plan_id ) == "other"  )
            {
                // Generate a plan name
                //
                $helper = new DigLabs_Stripe_Helpers_Stripe();
                $amount_in_cents = intval( floatval( $_REQUEST[ 'cardAmount' ] ) * 100 );
                $interval_count = trim( $_REQUEST[ 'planCount' ] );
                $interval_type = trim( $_REQUEST[ 'planInterval' ] );
                $this->plan_id = $helper->create_plan_id( $amount_in_cents, $interval_count, $interval_type );

                // See if this plan already exists.
                //
                try
                {
                    $plan = Stripe_Plan::retrieve( $this->plan_id );
                }
                catch( Exception $e )
                {
                    $plan = null;
                }

                if( $plan == null )
                {
                    // Create the plan
                    //
                    $args = array(
                        "id"                => $this->plan_id,
                        "amount"            => $amount_in_cents,
                        "currency"          => $this->settings->getCurrencySymbol(),
                        "interval"          => $interval_type,
                        "interval_count"    => $interval_count,
                        "name"              => $this->plan_id,
                    );
                    $plan = Stripe_Plan::create( $args );
                }
            }
            else
            {
                $plan = Stripe_Plan::retrieve( $this->plan_id );
            }
            $params = array(
                'plan' => $this->plan_id
            );

            // Apply the quantity.
            //
            $quantity = 1;
            if( isset( $this->response[ 'quantity' ] ) )
            {
                $quantity = intval( $this->response[ 'quantity' ] );
                if( $quantity < 1 )
                {
                    $quantity = 1;
                }
                $params[ 'quantity' ] = $quantity;
            }
            $this->amount = $quantity * $plan->amount;
            if( $plan->trial_period_days > 0 )
            {
                // Not being billed for the subscription yet.
                //
                $this->amount = 0;
            }
            $this->sub_total = $this->amount;

            // Apply the coupon.
            //
            if( !is_null( $this->coupon ) )
            {
                $params[ 'coupon' ] = $this->coupon->id;
                $this->discount     = intval( $this->amount * $this->coupon->percent_off / 100.0 );
                $this->sub_total -= $this->discount;
            }

            // Is there an initial fee on the first
            //	payment?
            //
            if( isset( $this->response[ 'init_fee' ] ) )
            {
                $this->initial_fee = floatval( $this->response[ 'init_fee' ] );
                $this->sub_total += $this->initial_fee;
                if( $this->initial_fee > 0 )
                {
                    $init_description = isset( $this->response[ 'init_description' ] ) ? $this->response[ 'init_description' ] : 'Initial Fee';
                    Stripe_InvoiceItem::create( array(
                                                    'customer'    => $this->customer->id,
                                                    'amount'      => $this->initial_fee,
                                                    'currency'    => $this->settings->getCurrencySymbol(),
                                                    'description' => $init_description
                                                ) );
                }
            }

            // Apply any configured tax rates
            //
            if( !isset( $this->response[ 'tax-exempt' ] ) )
            {
                $country        = isset( $this->response[ 'country' ] ) ? $this->response[ 'country' ] : null;
                $state          = isset( $this->response[ 'state' ] ) ? $this->response[ 'state' ] : null;
                $this->tax_rate = $this->settings->getTaxRate( $country, $state );
                $this->tax      = ceil( $this->sub_total * $this->tax_rate / 100.0 );
                if( $this->tax >= 0 )
                {
                    // Add an invoice item.
                    //
                    Stripe_InvoiceItem::create( array(
                                                    'customer'    => $this->customer->id,
                                                    'amount'      => $this->tax,
                                                    'currency'    => $this->settings->getCurrencySymbol(),
                                                    'description' => 'Tax'
                                                ) );
                }
            }
            $this->total = intval( $this->sub_total + $this->tax );

            // Add the subscription to the user.
            //
            $result = $this->customer->updateSubscription( $params );

            // Add to the response.
            //
            $this->response[ 'result' ] = $result;
            $this->response[ 'plan' ]   = $plan;
            $this->response[ 'id' ]     = $this->customer->id;
        }
    }
}
