<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if( !class_exists( 'DigLabs_Stripe_Webhook_Stripe_Email_Parser' ) )
{

    /* 	--------------------------------------

        The following parser result and interface abstract away the payment gateway.

        -------------------------------------- */
    class DigLabs_Stripe_Webhook_Stripe_Email_Parser_Result
    {
        public $subject;
        public $body;
        public $to_address;

        public $email;
        public $product;
        public $subscription;
        public $description;
        public $meta;

        public $charge_id;
        public $amount;
        public $tax = 0.0;
        public $name;
        public $card_type;
        public $card_last4;

        public $plans = array();

        public $lines = array();
    }

    interface DigLabs_Stripe_Webhook_Stripe_Email_Parser_Interface
    {
        public function parse_event($event);
    }

    /* 	--------------------------------------

        A Stripe implementation of the email parser.

        -------------------------------------- */
    class DigLabs_Stripe_Webhook_Stripe_Email_Parser implements DigLabs_Stripe_Webhook_Stripe_Email_Parser_Interface
    {
        public function parse_event($event, $testing=false)
        {
            $result = new DigLabs_Stripe_Webhook_Stripe_Email_Parser_Result();

            $type = $event->type;

            if($type=='charge.succeeded')
            {
                $result->subject    = "Payment Received";
                $result->body       = 'Thank you for your payment. This email is your receipt and includes important information. If you feel this transaction is in error, please respond to this email with the details.';
                $result->amount 	= $event->data->object->amount/100;
            }
            else if($type=='invoice.payment_failed')
            {
                $result->subject    = "Payment Failed";
                $result->body       = 'We recently tried to bill your credit card for a recurring payment. That attempt failed. Could it be that you have a new credit card? Please contact us so we can update our records. We appreciate your continued support. Thanks!';
                $result->amount 	= $event->data->object->amount/100;
            }
            else if($type=='charge.refunded')
            {
                $result->subject    = "Payment Refunded";
                $result->body       = 'A refund has been made to your credit card. This email is your receipt and includes important information. If you feel this transaction is in error, please respond to this email with the details.';
                $result->amount 	= $event->data->object->amount_refunded/100;
            }


            // Charge information.
            //
            $result->charge_id 	    = $event->data->object->id;

            $card                   = $event->data->object->card;
            $result->name 		    = $card->name;
            $result->card_type 	    = $card->type;
            $result->card_last4 	= $card->last4;

            if(!is_null($event->data->object->invoice))
            {
                // This is an invoice
                //
                try
                {
                    $invoice = Stripe_Invoice::retrieve($event->data->object->invoice);

                    // Collect the line items from the invoice.
                    //
                    $lines = $invoice->lines;
                    foreach( $lines->data as $line )
                    {
                        $line_amount = $line->amount / 100;
                        $line_description = '';
                        if( isset( $line->description ) )
                        {
                            $line_description = $line->description;
                        }
                        else if( isset( $line->plan ) )
                        {
                            $interval = $line->plan->interval_count . ' ' . $line->plan->interval;
                            if( $line->plan->interval_count > 1 )
                            {
                                $interval .= 's';
                            }
                            $line_description = 'Subscription (' . $interval . ')';

                            $plan = array(
                                'id'		=> $line->plan->id,
                                'name'      => $line->plan->name,
                                'interval' 	=> $interval
                            );
                            $result->plans[] = $plan;
                        }
                        $result->lines[ $line_description ] = $line_amount;
                    }

                    if( count( $result->plans ) == 0 )
                    {
                        // Need to look else where for the subscription.
                        //
                        if( isset( $lines->subscriptions ) )
                        {
                            foreach( $lines->subscriptions as $subscription )
                            {
                                $interval = $subscription->plan->interval_count . ' ' . $subscription->plan->interval;
                                if( $subscription->plan->interval_count > 1 )
                                {
                                    $interval .= 's';
                                }
                                $plan = array(
                                    'id'		=> $subscription->plan->id,
                                    'name'      => $subscription->plan->name,
                                    'interval' 	=> $interval
                                );
                                $result->plans[] = $plan;
                            }
                        }
                    }
                }
                catch (Exception $e)
                {
                    if(!$testing)
                    {
                        return false;
                    }
                }
            }

            $helper = new DigLabs_Stripe_Helpers_Stripe();
            if( !is_null( $event->data->object->metadata ) )
            {
                $json_string = $helper->convert_from_strip_metadata( $event->data->object->metadata );
                if( strlen( $json_string ) > 0 )
                {
                    $result->meta = json_decode( $json_string );
                }
            }

            if(!is_null($event->data->object->customer))
            {
                try
                {
                    $customer = Stripe_Customer::retrieve($event->data->object->customer);
                    $event->customer = $customer;
                    $result->to_address = $customer->email;

                    if( is_null( $result->meta ) )
                    {
                        $json_string = $helper->convert_from_strip_metadata( $customer->metadata );
                        if( strlen( $json_string ) > 0 )
                        {
                            $result->meta = json_decode( $json_string );
                        }
                    }

                }
                catch (Exception $e)
                {
                    if(!$testing)
                    {
                        return false;
                    }
                }
            }

            $meta = $result->meta;
            if(isset($meta->email))
            {
                $result->email = $meta->email;
            }
            if(isset($meta->product))
            {
                $result->product = $meta->product;
            }
            if(isset($meta->subscription))
            {
                $result->subscription = $meta->subscription;
            }

            return $result;
        }
    }
}