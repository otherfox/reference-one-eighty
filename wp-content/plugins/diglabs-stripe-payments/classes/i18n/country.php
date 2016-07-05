<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'DigLabs_Stripe_I18N_Country' ) )
{
    class DigLabs_Stripe_I18N_Country
    {
        public $country_iso_2char;
        public $country_iso_3char;
        public $country_iso_number;
        public $country_name;
        public $currency_iso_3char;
        public $currency_iso_number;
        public $currency_name;
        public $state_name;
        public $states = array();
    }
}
