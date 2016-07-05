<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if( !class_exists( 'DigLabs_Stripe_Shortcodes_Stripe_Cart_Add_Item' ) )
{
    class DigLabs_Stripe_Shortcodes_Stripe_Cart_Add_Item extends DigLabs_Stripe_Shortcodes_Abstract_Base
    {
        private $tag = "stripe_cart_add_item";

        public function description()
        {
            return "Creates a button to allow a user to add an item to the shopping cart.";
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
                                        'id'    => null
                                     ), $atts ) );

            $this->include_scripts();

            return $this->render( $id );
        }

        private function include_scripts()
        {
            $base = DigLabs_Stripe_Payments::GlobalInstance();
            wp_enqueue_script('stripe_payment_plugin_cart', $base->urls->plugin() . '/js/cart.js', array('jquery'), '1.5.19', true);
        }

        private function render( $id )
        {
            if( $id == null )
            {
                return "Need to specify item ID. Here is an example: <code>[stripe_cart_add_item id='abc-4']</code>";
            }

            $plugin_url = DigLabs_Stripe_Payments::GlobalInstance()->urls->plugin();
            $spinner_url = $plugin_url . '/images/spinner.gif';
            $blog_url = get_site_url();

            $ajax_url = $blog_url . '/wp-admin/admin-ajax.php';

            return <<<HTML
<form method="post" class="diglabs-cart-item" action="$ajax_url">
<input type="hidden" name="quantity" value="1" />
<input type="hidden" name="id" value="$id" />
<input type="submit" name="addtocart" class="diglabs-btn-green" value="Add to Cart" title="Add to Cart">
<img src='$spinner_url' />
<span class="diglabs-cart-item-status"></span>
</form>
HTML;
        }
    }
}
