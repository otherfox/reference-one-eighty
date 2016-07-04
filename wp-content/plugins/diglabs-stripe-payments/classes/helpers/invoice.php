<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if( !class_exists( 'DigLabs_Stripe_Helpers_Invoice' ) )
{
    class DigLabs_Stripe_Helpers_Invoice
    {
        public function add_item( $event, $amount, $description = null )
        {
            $settings = new DigLabs_Stripe_Helpers_Settings();
            $apiKey   = $settings->getSecretKey();
            $currency = $settings->getCurrencySymbol();

            $customer = $event->data->object->customer;
            $invoice  = $event->data->object->id;

            Stripe::setApiKey( $apiKey );

            return Stripe_InvoiceItem::create( array(
                                                   "customer"    => $customer,
                                                   "amount"      => $amount,
                                                   "currency"    => $currency,
                                                   "invoice"     => $invoice,
                                                   "description" => $description
                                               ) );
        }
    }
}
