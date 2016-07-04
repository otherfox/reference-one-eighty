<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if( !class_exists( 'DigLabs_Stripe_Admin_Menus' ) )
{
    define( 'DLSP_ADMIN_PAGE', 'diglabs_stripe_payments' );

    class DigLabs_Stripe_Admin_Menus
    {
        public function configure()
        {
            $base = DigLabs_Stripe_Payments::GlobalInstance();

            if( is_plugin_active( $base->file_name ) )
            {
                // Make sure the main menu exists
                //
                if( !$this->find_my_menu_item( 'diglabs' ) )
                {
                    add_menu_page(
                        "Dig Labs",
                        "Dig Labs",
                        "manage_options",
                        "diglabs",
                        array( $this, 'display_general_info' ),
                        'http://diglabs.com/images/beaker-icon.png'
                    );
                }

                // Add the submenu for this plugin
                //
                add_submenu_page(
                    'diglabs',
                    'Dig Labs',
                    'Stripe Payments',
                    'manage_options',
                    'diglabs_stripe_payments',
                    array( $this, 'display_my_admin' )
                );

                // Add the status submenu item
                //
                if( !$this->find_my_menu_item( 'diglabs_status' ) )
                {
                    add_submenu_page(
                        'diglabs',
                        'Dig Labs',
                        'Status',
                        'manage_options',
                        'diglabs_status',
                        array( $this, 'diglabs_status' )
                    );
                }
            }

        }

        public function display_general_info()
        {
            echo "<img src='http://diglabs.com/images/Dig-Labs.png' alt='logo' />";
            echo "<h3>Professional Website and Software Development</h3>";
            echo "<p>For more information about Dig Labs, visit our website <a href='http://diglabs.com'>http://diglabs.com</a>.</p>";
        }

        public function diglabs_status()
        {
            include 'admin-status.php';
        }

        public function display_my_admin()
        {
            include( 'admin_form.php' );
        }

        // Helper function to determine if a menu exists.
        //
        private function find_my_menu_item( $handle, $sub = false )
        {
            if( !is_admin() || ( defined( 'DOING_AJAX' ) && DOING_AJAX ) )
            {
                return false;
            }
            global $menu, $submenu;
            $check_menu = $sub ? $submenu : $menu;
            if( empty( $check_menu ) )
            {
                return false;
            }
            foreach( $check_menu as $k => $item )
            {
                if( $sub )
                {
                    foreach( $item as $sm )
                    {
                        if( $handle == $sm[ 2 ] )
                        {
                            return true;
                        }
                    }
                }
                else
                {
                    if( $handle == $item[ 2 ] )
                    {
                        return true;
                    }
                }
            }
            return false;
        }
    }

    add_action( 'admin_menu', array( new DigLabs_Stripe_Admin_Menus(), 'configure' ) );
}

add_action( 'admin_head', 'dlsp_dmin_register_head' );
function dlsp_dmin_register_head()
{
    $base       = DigLabs_Stripe_Payments::GlobalInstance();
    $plugin_url = $base->urls->plugin();

    wp_register_style( 'dlsp-common-style', $plugin_url . '/css/common.css', true );
    wp_enqueue_style( 'dlsp-common-style' );

    if( isset( $_REQUEST[ 'page' ] ) && strpos( $_REQUEST[ 'page' ], 'diglabs' ) !== false )
    {
        wp_register_style( 'dlsp-admin-style', $plugin_url . '/css/admin.css', true );
        wp_enqueue_style( 'dlsp-admin-style' );

        wp_register_script( 'dlsp-admin-js', $plugin_url . '/js/admin.js', array( 'jquery' ) );
        wp_enqueue_script( 'dlsp-admin-js' );
    }
}
