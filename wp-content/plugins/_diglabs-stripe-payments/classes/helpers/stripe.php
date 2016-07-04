<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'DigLabs_Stripe_Helpers_Stripe' ) )
{
    class DigLabs_Stripe_Helpers_Stripe
    {
        private $base_name = 'diglabs';


        public function convert_to_stripe_metadata( $data )
        {
            $chunks = str_split( $data, 200 );
            $result = array();
            foreach( $chunks as $i => $chunk )
            {
                $name = $this->base_name . $i;
                $result[ $name ] = $chunk;
            }

            return $result;
        }

        public function convert_from_strip_metadata( $metadata )
        {
            $index = 0;
            $diglabs = array();
            $done = false;
            while( !$done )
            {
                $name = $this->base_name . $index;
                if( isset( $metadata[$name] ) )
                {
                    $diglabs[] = $metadata[$name];
                }
                else
                {
                    $done = true;
                }
                $index++;
            }

            $result = implode( $diglabs );

            return $result;
        }

        public function create_plan_id( $amount_in_cents, $interval_count, $interval_type )
        {
            $name = $this->base_name . '_' . $amount_in_cents . '_' . $interval_count . '_' . $interval_type;
            $name = strtolower( $name );
            return $name;
        }
    }
}