<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'DigLabs_Stripe_Admin_Updater' ) )
{
    class DigLabs_Stripe_Admin_Updater
    {
        private $country_helper;
        private $settings;

        public function __construct()
        {
            $this->country_helper = new DigLabs_Stripe_I18N_Country_Helper();
            $this->settings = new DigLabs_Stripe_Helpers_Settings();
        }

        public function update()
        {
            $this->update_taxes();
            $this->update_country_iso();
        }

        private function update_taxes()
        {
            $tax_data = $this->settings->getTaxData();
            if( !is_null( $tax_data ) && is_array( $tax_data ) )
            {
                $need_to_convert = false;
                foreach( $tax_data as $value )
                {
                    if( !is_array( $value ) )
                    {
                        // This is the old format where only the US was supported.
                        //
                        $need_to_convert = true;
                    }
                }
                if( $need_to_convert )
                {
                    $new_tax_data = array(
                        'US' => $tax_data
                    );
                    $this->settings->setTaxData( $new_tax_data );
                }
            }
        }

        private function update_country_iso()
        {
            $country_iso = $this->settings->getCountryIso();
            if( !$country_iso )
            {
                $currency_symbol = strtoupper( $this->settings->getCurrencySymbol() );
                if( $currency_symbol == 'USD' )
                {
                    $country = $this->country_helper->country('US');
                    $this->settings->setCountryIso( $country->country_iso_2char );
                }
                else
                {
                    $countries = $this->country_helper->countries();
                    foreach( $countries as $iso => $country )
                    {
                        if( $country->currency_iso_3char == $currency_symbol )
                        {
                            $this->settings->setCountryIso( $iso );
                        }
                    }
                }
            }
        }
    }
}
