<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'DigLabs_Stripe_Helpers_Urls' ) )
{
    class DigLabs_Stripe_Helpers_Urls
    {
        private $plugin_url;

        public function plugin()
        {
            if ( $this->plugin_url )
            {
                return $this->plugin_url;
            }
            $base = DigLabs_Stripe_Payments::GlobalInstance();
            return $this->plugin_url = untrailingslashit( plugins_url( '/',  $base->paths->plugin() ) ) . '/' . $base->file_name_without_ext;
        }
    }
}