<?php
/**
 * Plugin Name: Dig Labs - Stripe Payments
 * Plugin URI: http://diglabs.com/stripe/
 * Description: This plugin allows the Stripe payment system to be easily integrated into WordPress.
 * Version: 2.3.12
 * Author: Dig Labs
 * Author URI: http://diglabs.com/
 * Requires at least: 3.1
 * Tested up to: 3.7.1
 *
 * Text Domain: diglabs
 * Domain Path: /i18n/languages/
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'DigLabs_Stripe_Payments' ) )
{
    class DigLabs_Stripe_Payments
    {
        private static $name_instance = 'diglabsstripepayments';
        public static function GlobalInstance()
        {
            if( isset( $GLOBALS[ DigLabs_Stripe_Payments::$name_instance ] ) )
            {
                return $GLOBALS[ DigLabs_Stripe_Payments::$name_instance ];
            }
            return null;
        }

        private static $logger_name = 'diglabsstripepayments_logger';
        public static function log()
        {
            return $GLOBALS[ DigLabs_Stripe_Payments::$logger_name ];
        }

        // Message helper functions for easy access.
        //
        public static function Info( $title, $body )
        {
            $base = DigLabs_Stripe_Payments::GlobalInstance();
            $messages = $base->messages;
            $messages->add( array(
                                'title' => $title,
                                'body'  => $body
                            ), false);
        }
        public static function Error( $title, $body )
        {
            $base = DigLabs_Stripe_Payments::GlobalInstance();
            $messages = $base->messages;
            $messages->add( array(
                                'title' => $title,
                                'body'  => $body
                            ), true);
        }

        public $file_name;
        public $file_name_without_ext;
        public $name;
        public $home_page;
        public $description;
        public $version;

        public $logger;
        public $paths;
        public $urls;
        public $messages;

        private $autoloader;
        private $payment_begin_callbacks = array();
        private $payment_end_callbacks   = array();
        private $cart_item_callbacks = array();

        public function __construct()
        {
            $GLOBALS[ DigLabs_Stripe_Payments::$name_instance ] = $this;
            $this->get_my_info();

            // Need to load this helper since it is used by the autoloader.
            //
            require_once( dirname( __FILE__ ) . '/classes/helpers/paths.php' );
            $this->paths = new DigLabs_Stripe_Helpers_Paths();

            // Setup auto-loading
            //
            $this->setup_auto_loading();

            // Load additional helpers.
            //
            $this->urls = new DigLabs_Stripe_Helpers_Urls();
            $this->messages = new DigLabs_Stripe_Helpers_Message();

            // Create the global logger instance.
            //
            $this->create_logger();

            // Update API
            //
            $this->update_api();

            // Load the stripe API.
            //
            $this->load_stripe_api_library();

            // Register activation hooks.
            //
            register_activation_hook( __FILE__, array( new DigLabs_Stripe_Admin_Updater(), 'update' ) );

            // Other WordPress actions and hooks
            //
            add_action( 'wp_ajax_stripe_plugin_process_card', array( new DigLabs_Stripe_Handlers_Ajax(), 'process' ), 1 );
            add_action( 'wp_ajax_nopriv_stripe_plugin_process_card', array( new DigLabs_Stripe_Handlers_Ajax(), 'process' ), 1 );
            add_action( 'wp_head', array( $this, 'add_page_header_code' ), 0 );
            add_filter( 'template_include', array( $this, 'custom_template' ) );
            add_action( 'in_admin_footer', array( $this, 'show_messages' ) );

            add_action( 'widgets_init', array( $this, 'register_widgets' ) );
            add_action( 'init', array( new DigLabs_Stripe_Handlers_Cart(), 'process' ), 0 );
            add_action( 'plugins_loaded', array( $this, 'download_file' ) );
            add_action( 'wp_ajax_stripe_plugin_cart_get', array( new DigLabs_Stripe_Handlers_Cart(), 'get' ), 1 );
            add_action( 'wp_ajax_nopriv_stripe_plugin_cart_get', array( new DigLabs_Stripe_Handlers_Cart(), 'get' ), 1 );
            add_action( 'wp_ajax_stripe_plugin_cart_add', array( new DigLabs_Stripe_Handlers_Cart(), 'add' ), 1 );
            add_action( 'wp_ajax_nopriv_stripe_plugin_cart_add', array( new DigLabs_Stripe_Handlers_Cart(), 'add' ), 1 );
            add_action( 'wp_ajax_stripe_plugin_cart_add', array( new DigLabs_Stripe_Handlers_Cart(), 'remove' ), 1 );
            add_action( 'wp_ajax_nopriv_stripe_plugin_cart_add', array( new DigLabs_Stripe_Handlers_Cart(), 'remove' ), 1 );

            // Register short codes.
            //
            $this->register_shortcodes();

            if( is_admin() )
            {
                $this->admin();
            }
        }

        private function get_my_info()
        {
            $this->file_name             = plugin_basename( __FILE__ );
            $this->file_name_without_ext = dirname( $this->file_name );

            if( !function_exists( 'get_plugins' ) )
            {
                require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
            }
            $plugin_folder = get_plugins( '/' . plugin_basename( dirname( __FILE__ ) ) );
            $plugin_file   = basename( ( __FILE__ ) );

            $my_data           = $plugin_folder[ $plugin_file ];
            $this->name        = $my_data[ 'Name' ];
            $this->version     = $my_data[ 'Version' ];
            $this->description = $my_data[ 'Description' ];
            $this->home_page   = $my_data[ 'PluginURI' ];
        }

        private function create_logger()
        {
            // Create a global logging instance that can be throughout this plugin
            //
            $log_folder = $this->paths->logs();
            $this->logger = new DigLabs_Logger( $log_folder, DigLabs_Logger::DEBUG );
            $GLOBALS[ DigLabs_Stripe_Payments::$logger_name ] = $this->logger;
        }

        public function autoload_resolve( $class )
        {
            $this->autoloader->resolve( $class );
        }

        private function setup_auto_loading()
        {
            if( function_exists( 'spl_autoload_register' ) )
            {
                // Supports auto-loading.
                //
                require_once $this->paths->plugin() . '/classes/common/auto-loader.php';
                if ( function_exists( "__autoload" ) )
                {
                    spl_autoload_register( "__autoload" );
                }
                $base_folder = $this->paths->plugin() . '/classes/';
                $class_prefix = 'DigLabs_Stripe_';
                $this->autoloader = new DigLabs_Auto_Loader( $class_prefix, $base_folder );
                $this->autoloader->register_folder( 'i18n', 'i18n' );
                $this->autoloader->register_folder( 'admin', 'admin' );
                $this->autoloader->register_folder( 'shortcodes', 'shortcodes' );
                $this->autoloader->register_folder( 'helpers', 'helpers' );
                $this->autoloader->register_folder( 'ui', 'ui' );
                $this->autoloader->register_folder( 'webhook', 'webhook' );
                $this->autoloader->register_class( 'DigLabs_Stripe_Admin_Menus', $this->paths->plugin() . '/admin/admin.php' );

                spl_autoload_register( array( $this, 'autoload_resolve' ) );
            }
        }

        public function load_stripe_api_library()
        {
            if( !class_exists( 'Stripe' ) )
            {
                require_once $this->paths->plugin() . '/stripe-php-1.8.0/lib/Stripe.php';
            }
            else
            {
                // Someone else loaded the library. This may cause issues. Better,
                //  log the location and version.
                //
                $reflection = new ReflectionClass( 'Stripe' );
                $version    = Stripe::VERSION;
                $this->logger->info( 'Found another instance of the Stripe class. version=' . $version . ' @ ' . $reflection->getFileName() );
            }
        }

        public function show_messages()
        {
            if( $this->messages->count() > 0 )
            {
                $this->messages->render();
            }
        }

        public function get_shortcodes()
        {
            return array(
                'DigLabs_Stripe_Shortcodes_Stripe_Checkout_Button',
                'DigLabs_Stripe_Shortcodes_Stripe_Form_Account',
                'DigLabs_Stripe_Shortcodes_Stripe_Form_Amount',
                'DigLabs_Stripe_Shortcodes_Stripe_Form_Begin',
                'DigLabs_Stripe_Shortcodes_Stripe_Form_Billing_Info',
                'DigLabs_Stripe_Shortcodes_Stripe_Form_Coupon',
                'DigLabs_Stripe_Shortcodes_Stripe_Form_Data',
                'DigLabs_Stripe_Shortcodes_Stripe_Form_End',
                'DigLabs_Stripe_Shortcodes_Stripe_Form_No_Amount',
                'DigLabs_Stripe_Shortcodes_Stripe_Form_Payment_Info',
                'DigLabs_Stripe_Shortcodes_Stripe_Form_Plan_Info',
                'DigLabs_Stripe_Shortcodes_Stripe_Form_Plan_Initial',
                'DigLabs_Stripe_Shortcodes_Stripe_Form_Receipt',
                'DigLabs_Stripe_Shortcodes_Stripe_Form_Redirect',
                'DigLabs_Stripe_Shortcodes_Stripe_Form_Section_Header',
                'DigLabs_Stripe_Shortcodes_Stripe_Form_Section_Row',
                'DigLabs_Stripe_Shortcodes_Stripe_Form_Standard_Amount',
                'DigLabs_Stripe_Shortcodes_Stripe_Form_Standard_Plan',
                'DigLabs_Stripe_Shortcodes_Stripe_Cart_Add_Item',
                'DigLabs_Stripe_Shortcodes_Stripe_Cart_Checkout',
            );
        }

        public function render_manual()
        {
            echo "<h2>WordPress Shortcodes</h2>";
            $classes = $this->get_shortcodes();
            foreach( $classes as $class )
            {
                $short_code = new $class;
                echo $short_code->manual();
            }
        }

        private function register_shortcodes()
        {
            $classes = $this->get_shortcodes();
            foreach( $classes as $class )
            {
                $shortcode = new $class;
                $shortcode->register();
            }
        }

        public function register_widgets()
        {
            register_widget( 'DigLabs_Stripe_UI_Cart_Widget' );
        }

        private function admin()
        {
            // Force updates for those who drop in the update via FTP.
            //
            $updater = new DigLabs_Stripe_Admin_Updater();
            $updater->update();

            require_once $this->paths->plugin() . '/admin/admin.php';
        }

        public function add_page_header_code()
        {
            if( function_exists( 'wp_enqueue_script' ) )
            {
                // Include our CSS styles.
                //
                $plugin_url = $this->urls->plugin();
                wp_register_style( 'dlsp-common-style', $plugin_url . '/css/common.css', true );
                wp_enqueue_style( 'dlsp-common-style' );

                wp_register_style( 'dlsp-stripe-style', $plugin_url . '/css/stripe.css', true );
                wp_enqueue_style( 'dlsp-stripe-style' );

                // add our scripts and their dependencies
                wp_enqueue_script( 'jquery' );
            }
        }

        public function custom_template()
        {
            global $template;

            $proto         = ( isset( $_SERVER[ 'HTTPS' ] ) && $_SERVER[ 'HTTPS' ] == 'on' ) ? 'https://' : 'http://';
            $the_url       = $proto . $_SERVER[ 'SERVER_NAME' ] . $_SERVER[ 'REQUEST_URI' ];
            $the_url_lc    = untrailingslashit( strtolower( $the_url ) );
            $request_parts = parse_url( $the_url_lc );
            $request_path  = isset( $request_parts[ "path" ] ) ? $request_parts[ "path" ] : "";
            $request_query = isset( $request_parts[ "query" ] ) ? $request_parts[ "query" ] : "";

            $settings     = new DigLabs_Stripe_Helpers_Settings();
            $web_hook_url = untrailingslashit( strtolower( $settings->getWebHookUrl() ) );

            if( $web_hook_url != false )
            {
                $hook_parts = parse_url( $web_hook_url );
                $hook_path  = isset( $hook_parts[ "path" ] ) ? $hook_parts[ "path" ] : "";
                $hook_query = isset( $hook_parts[ "query" ] ) ? $hook_parts[ "query" ] : "";

                $site_url   = site_url( null, 'https' );
                $site_parts = parse_url( $site_url );
                $site_path  = isset( $site_parts[ "path" ] ) ? $site_parts[ "path" ] : "";

                $request_path = str_replace( $site_path, "", $request_path );
                $hook_path    = str_replace( $site_path, "", $hook_path );

                if( $request_path == $hook_path && $request_query == $hook_query )
                {
                    $template = $this->paths->plugin() . '/classes/webhook/webhook-template.php';
                    return $template;
                }
            }

            return $template;
        }

        public function download_file()
        {
            if(is_admin()
                && isset( $_REQUEST['page'] ) && $_REQUEST['page']=='diglabs_stripe_payments'
                && isset( $_REQUEST['tab'] ) &&  $_REQUEST['tab']=='charges'
                && isset( $_REQUEST['f'] ) )
            {
                // This is a download. Short-circuit the default templating stuff.
                //
                require_once( dirname(__FILE__) . '/admin/charges-form.php');
            }
        }

        public function register_payment_begin_callback( $callback )
        {
            $this->payment_begin_callbacks[] = $callback;
        }

        public function get_payment_begin_callbacks()
        {
            return $this->payment_begin_callbacks;
        }

        public function register_payment_end_callback( $callback )
        {
            $this->payment_end_callbacks[] = $callback;
        }

        public function get_payment_end_callbacks()
        {
            return $this->payment_end_callbacks;
        }

        public function register_cart_item_callback( $callback )
        {
            $this->cart_item_callbacks[] = $callback;
        }

        public function get_cart_item_callbacks()
        {
            return $this->cart_item_callbacks;
        }

        private function update_api()
        {
            // Add the ability for the plugin to detect available updates.
            //
            $api_url       = 'http://diglabs.com/api/plugin/';
            $plugin_folder = 'diglabs-stripe-payments';
            $plugin_file   = 'diglabs-stripe-payments.php';

            $settings = new DigLabs_Stripe_Helpers_Settings();


            $data = array(
                'id'    =>   $settings->downloadKey,
                'url'   =>   base64_encode( site_url() )
            );

            $dl_alt_api = new DigLabs_Alternative_Plugin_Api( $api_url, $plugin_folder, $plugin_file, $data );
            add_filter( 'pre_set_site_transient_update_plugins', array( &$dl_alt_api, 'check' ) );
            add_filter( 'plugins_api', array( &$dl_alt_api, 'info' ), 10, 3 );
        }
    }

    // Initialize the plugin.
    //
    new DigLabs_Stripe_Payments();

    // Keep these global functions to not break support for older versions.
    //
    function stripe_register_payment_begin_callback( $callback )
    {
        DigLabs_Stripe_Payments::GlobalInstance()->register_payment_begin_callback( $callback );
    }

    function stripe_register_payment_end_callback( $callback )
    {
        DigLabs_Stripe_Payments::GlobalInstance()->register_payment_end_callback( $callback );
    }
}
