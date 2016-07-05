<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'DigLabs_Stripe_UI_Cart_Widget' ) )
{
    class DigLabs_Stripe_UI_Cart_Widget extends WP_Widget
    {
        function __construct()
        {
            parent::__construct(
                'DigLabs_Stripe_UI_Cart_Widget',
                'Dig Labs Stripe Cart',
                array( 'description' => 'Displays a simple checkout cart.' )
            );
        }

        function form( $instance )
        {
            if ( isset( $instance[ 'title' ] ) )
            {
                $title = $instance[ 'title' ];
            }
            else
            {
                $title = 'Cart Items';
            }
            if( isset( $instance[ 'url' ] ) )
            {
                $url = $instance[ 'url' ];
            }
            else
            {
                $url = '';
            }
            ?>
            <p>
                <label for="<?php echo $this->get_field_name( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
                <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
            </p>
            <p>
                <label for="<?php echo $this->get_field_name( 'url' ); ?>"><?php _e( 'Checkout URL:' ); ?></label>
                <input class="widefat" id="<?php echo $this->get_field_id( 'url' ); ?>" name="<?php echo $this->get_field_name( 'url' ); ?>" type="text" value="<?php echo esc_attr( $url ); ?>" />
            </p>
            <?php
        }

        function update( $new_instance, $old_instance )
        {
            $instance            = $old_instance;
            $instance[ 'title' ] = strip_tags( $new_instance[ 'title' ] );
            $instance[ 'url' ]   = strip_tags( $new_instance[ 'url' ] );

            return $instance;
        }

        function widget( $args, $instance )
        {
            extract( $args );

            // Before widget markup
            //
            echo $before_widget;

            // Title
            //
            $title = apply_filters( 'widget_title', $instance[ 'title' ] );
            if( !empty( $title ) )
            {
                echo $before_title . $title . $after_title;
            }

            // Content
            //
            $cart = new DigLabs_Stripe_Handlers_Cart();
            $items = $cart->get_items();
            $settings    = new DigLabs_Stripe_Helpers_Settings();
            $country_iso = $settings->getCountryIso();

            $country_helper = new DigLabs_Stripe_I18N_Country_Helper();
            $zero_cost_str  = $country_helper->currency( 0, $country_iso );

            $html = "<div class='diglabs-cart-widget' data-items='" . json_encode($items) . "' data-zero='$zero_cost_str''>";
            $html .= "<ul><!-- rendered in javascript --></ul>";
            $html .= "<p class='diglabs-cart-widget-summary'>Total = <span class='diglabs-cart-widget-total'><span></span></p>";

            // Url
            //
            $url = $instance[ 'url' ];
            if( !empty( $url ) )
            {
                $html .= "<p class='diglabs-cart-widget-summary'><a href='$url'>Check Out</a></p>";
            }
            $html .= "</div>";

            echo $html;

            // After widget markup
            echo $after_widget;
        }
    }
}