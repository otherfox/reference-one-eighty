<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'DigLabs_Stripe_Helpers_Paths' ) )
{
    class DigLabs_Stripe_Helpers_Paths
    {
        private $plugin_path;
        private $logs_path;

        public function plugin()
        {
            if ( $this->plugin_path )
            {
                return $this->plugin_path;
            }
            return $this->plugin_path = untrailingslashit( realpath( dirname( __FILE__ ) . '/../..' ) );
        }

        public function logs()
        {
            if ( $this->logs_path )
            {
                return $this->logs_path;
            }
            $upload_dir =  wp_upload_dir();
            return $this->logs_path = $upload_dir['basedir'] . '/diglabs/stripe-payments/logs';
        }
    }
}