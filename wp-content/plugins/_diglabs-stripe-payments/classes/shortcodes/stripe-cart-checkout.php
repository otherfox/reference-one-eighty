<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if( !class_exists( 'DigLabs_Stripe_Shortcodes_Stripe_Cart_Checkout' ) )
{
    class DigLabs_Stripe_Shortcodes_Stripe_Cart_Checkout extends DigLabs_Stripe_Shortcodes_Abstract_Base
    {
        private $tag = "stripe_cart_checkout";

        public function description()
        {
            return "Creates a checkout form for the shopping cart.";
        }

        public function options()
        {
            return array();
        }

        public function tag()
        {
            return parent::ShortCodeWithPrefix( $this->tag );
        }

        public function output( $atts, $content = null )
        {
            extract( shortcode_atts( array(
                                        'test'      => false,
                                         "short"    => false,
                                         "medium"   => false,
                                         "state"    => 'AL',
                                         "country"  => 'US',
                                         "coupon"   => false,
                                         "text"     => "Checkout"
                                     ), $atts ) );

            $style = $this->get_billing_style( $short, $medium );

            $this->include_scripts();

            $is_pay = isset( $_REQUEST[ 'pay' ] );
            if( $is_pay )
            {
                return $this->render_pay( $test, $style, $state, $country, $coupon, $text);
            }

            return $this->render_cart();
        }

        private function include_scripts()
        {
            $base = DigLabs_Stripe_Payments::GlobalInstance();
            wp_enqueue_script('stripe_payment_plugin_cart', $base->urls->plugin() . '/js/cart.js', array('jquery'), '1.5.19', true);
        }

        private function render_cart()
        {
            $cart = new DigLabs_Stripe_Handlers_Cart();
            $items = $cart->get_items();
            if( count( $items ) == 0 )
            {
                return "Your cart is empty.";
            }

            $settings    = new DigLabs_Stripe_Helpers_Settings();
            $country_iso = $settings->getCountryIso();

            $country_helper = new DigLabs_Stripe_I18N_Country_Helper();

            $current_url = $this->get_current_url();

            $html = "<h3>Shopping Cart</h3>";
            $html .= "<form method='post' class='diglabs-checkout' action=''>";

            $table = "<table class='diglabs-cart-checkout'>";
            $table .= "<tr><th>Item</th><th style='text-align: right;'>Unit Price</th><th style='text-align: right;'>Quantity</th><th style='text-align: right;'>Item Total</th></tr>";
            $cart_total = 0;
            foreach( $items as $item )
            {
                $id = $item->id;
                $count = $item->count;
                $info = $item->info;
                $unit_cost = $item->unit_cost;
                $unit_cost_str = $country_helper->currency( $unit_cost, $country_iso );
                $total = intval( $count ) * floatval( $unit_cost );
                $cart_total += $total;
                $total_str = $country_helper->currency( $total , $country_iso );

                $table .= "<tr>";
                $table .= "<td>$info<input type='hidden' name='ids[]' value='$id' /></td>";
                $table .= "<td style='text-align: right;'>$unit_cost_str</td>";
                $table .= "<td style='text-align: right;'><input type='number' min=0 name='quantities[]' value='$count' /></td>";
                $table .= "<td style='text-align: right;'>$total_str</td>";
                $table .= "</tr>";
            }
            $cart_total_str = $country_helper->currency( $cart_total, $country_iso );
            $table .= "<tr><th colspan=3 style='text-align: right;'>Total</th><td style='text-align: right;'>$cart_total_str</td></tr>";
            $table .= "</table>";

            $html .= $table;

            $html .= "<div style='text-align: right;'>";
            $html .= "<input type='submit' class='diglabs-btn-blue' name='cmd-diglabs-cart-update' value='Update Cart' />&nbsp;&nbsp;";
            $html .= "<a href='$current_url?pay=now' class='diglabs-btn-green'>Checkout</a>";
            $html .= "</div>";
            $html .= "</form>";
            return $html;
        }

        private function render_pay( $test, $style, $state, $country, $coupon, $text )
        {
            $cart = new DigLabs_Stripe_Handlers_Cart();
            $items = $cart->get_items();
            if( count( $items ) == 0 )
            {
                return "Your cart is empty.";
            }
            $cart_total = 0;
            foreach( $items as $item )
            {
                $count = $item->count;
                $unit_cost = $item->unit_cost;
                $total = intval( $count ) * floatval( $unit_cost );
                $cart_total += $total;
            }

            $current_url = $this->get_current_url();
            $current_url = strtok( $current_url,'?' );

            $test_str = $test ? "test=true" : "";
            $style_str = $style . "=true";
            $state_str = "state='" . $state . "'";
            $country_str = "country='" . $country . "'";
            $coupon_str = $coupon ? "[stripe_form_coupon]" : "";

            $pay = <<<HTML
[stripe_form_begin $test_str]
<input type='hidden' name='diglabs-cart-checkout' value='yes' />
[stripe_form_standard_amount amount=$cart_total $style_str $state_str $country_str]
$coupon_str
HTML;
            $pay .= $this->render_form_end( $text, $current_url );
            $pay .= "[stripe_form_receipt]";
            $pay = do_shortcode( $pay );

            return $pay;
        }

        private function render_form_end( $text = "Submit", $current_url )
        {
            return <<<HTML
<div class="stripe-payment-form-row-submit">
<button class="stripe-payment-form-submit diglabs-btn-green" type="submit" class="button">$text</button>
<a class="diglabs-btn-blue" href='$current_url'>Return To Cart</a>
</div>
<div class="stripe-payment-form-row-progress">
<span class="stripe-payment-form-message"></span>
</div>
</form>
</div>
HTML;
        }

        private function get_current_url()
        {
            $url = 'http';
            if( isset( $_SERVER[ 'HTTPS' ] ) && $_SERVER[ 'HTTPS' ] == 'on' )
            {
                $url .= 's';
            }
            $url .= "://";
            if( $_SERVER["SERVER_PORT"] != "80" )
            {
                $url .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
            }
            else
            {
                $url .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
            }
            return $url;
        }
    }
}
