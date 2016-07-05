<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'DigLabs_Auto_Loader' ) )
{
    class DigLabs_Auto_Loader
    {
        private $prefix;
        private $base_folder;
        private $folders = array();
        private $paths_looked = array();
        private $directs = array();

        public function __construct( $prefix, $base_folder )
        {
            $this->prefix = strtolower( $prefix );
            $this->base_folder = $base_folder;

            // Register the common classes.
            //
            $my_folder = dirname( __FILE__ ) . '/';
            $this->register_class( 'DigLabs_Logger', $my_folder . 'diglabs_logger.php' );
            $this->register_class( 'DigLabs_WordPress_Post_Helper', $my_folder . 'wppost-helper.php' );
            $this->register_class( 'DigLabs_Alternative_Plugin_Api', $my_folder . 'alt-api.php' );
            $this->register_class( 'DigLabs_Paginator_Info', $my_folder . 'paginator.php' );
        }

        public function register_class( $class_name, $path )
        {
            $key = strtolower( $class_name );
            $this->directs[ $key ] = $path;
        }

        public function register_folder( $search, $folder_name )
        {
            $this->folders[ $search ] = $folder_name;
        }

        public function resolve( $class_name )
        {
            if( class_exists( $class_name ) )
            {
                // Already done.
                //
                return;
            }

            $this->paths_looked = array();
            $original_class = $class_name;

            $class_name = ltrim( $class_name, '\\' );
            $class_name = strtolower( $class_name );

            // Check for the class in the registered classes.
            //
            if( isset( $this->directs[ $class_name ] ) )
            {
                $path = $this->directs[ $class_name ];
                if( is_readable( $path ) )
                {
                    require_once( $path );
                    return;
                }
            }

            if( strpos( $class_name, $this->prefix ) !== 0)
            {
                // Not this plugin's stuff, so exit early. This is important
                //  as this is a global registration hook and we don't want to
                //  add this extra logic globally.
                //
                return;
            }

            $class_name = str_replace( $this->prefix, '', $class_name );
            $class_name = str_replace( '_', '-', $class_name );

            $folder_name  = $this->base_folder;
            if ( $lastNsPos = strripos( $class_name, '\\' ) )
            {
                $namespace = substr( $class_name, 0, $lastNsPos );
                $class_name = substr( $class_name, $lastNsPos + 1 );
                $folder_name  .= str_replace( '\\', DIRECTORY_SEPARATOR, $namespace ) . DIRECTORY_SEPARATOR;
            }

            // Handle special cases.
            //
            // Example:
            //  $this->folders['admin'] = 'admin'
            //  $class_name = 'admin-setup-help'
            //
            //  $folder_name = '<root>/admin/'
            //  $file_name = 'setup-help.php'
            //
            foreach( $this->folders as $search => $sub_folder_name )
            {
                if( strpos( $class_name, $search ) === 0 )
                {
                    $folder_name .= $sub_folder_name . DIRECTORY_SEPARATOR;
                    $file_name = str_replace( $search . '-', '', $class_name ) . '.php';
                    $path = $folder_name . $file_name;
                    $this->paths_looked[] = $path;
                    if( is_readable( $path ) )
                    {
                        // Found our file....exit early.
                        //
                        require_once( $path );
                        return;
                    }
                }
            }

            // PSR0
            //
            $file_name = str_replace('-', DIRECTORY_SEPARATOR, $class_name) . '.php';
            $path = $folder_name . $file_name;
            $this->paths_looked[] = $path;
            if( is_readable( $path ) )
            {
                require_once( $path );
                return;
            }

            // Alternative.
            //
            $file_name = $class_name . '.php';
            $path = $folder_name . $file_name;
            $this->paths_looked[] = $path;
            if( is_readable( $path ) )
            {
                require_once( $path );
                return;
            }

            // Throw exception with info on where we looked.
            //
            $message = "<br /><br />Autoload Error. Looking for class '$original_class in the following places:<br /><br /><ul><li>";
            $message .= implode( "</li><li>", $this->paths_looked );
            $message .= "</li></ul><br />";
            throw new Exception( $message );
        }
    }
}
