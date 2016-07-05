<?php

if( !class_exists( 'DigLabs_Alternative_Plugin_Api' ) )
{
    class DigLabs_Alternative_Plugin_Api
    {
        private $api_url;
        private $plugin_folder;
        private $plugin_file;
        private $data;

        public function __construct( $api_url, $plugin_folder, $plugin_file, $data = array() )
        {
            $this->api_url       = $api_url;
            $this->plugin_folder = $plugin_folder;
            $this->plugin_file   = $plugin_file;
            $this->data          = $data;
        }

        public function check( $transient )
        {
            // Check if the transient contains the 'checked' information.
            //	If no, just return its value without updating it.
            //
            if( empty( $transient->checked ) )
            {
                return $transient;
            }

            // POST data to send to your API
            //
            $key  = $this->plugin_folder . '/' . $this->plugin_file;
            $args = array(
                'action'      => 'update-check',
                'plugin_name' => $this->plugin_folder,
                'version'     => $transient->checked[ $key ],
            );
            if( is_array( $this->data ) && count( $this->data ) > 0 )
            {
                $args = array_merge( $args, $this->data );
            }

            // Send request checking for an update
            //
            $response = $this->request( $args );

            // If response is false, don't alter the transient
            //
            if( false !== $response )
            {
                $transient->response[ $key ] = $response;
            }
            else
            {
                unset( $transient->response[ $key ] );
            }

            return $transient;
        }

        public function info( $false, $action, $args )
        {
            // Check if this plugin's API is about this plugin
            //
            if( !is_null( $args ) && isset( $args->slug ) && $args->slug != $this->plugin_folder )
            {
                return $false;
            }

            // POST data to send to your API
            //
            $args = array(
                'action'      => 'plugin-information',
                'plugin_name' => $this->plugin_folder,
                'data'        => base64_encode( json_encode( $this->data ) )
            );
            if( is_array( $this->data ) && count( $this->data ) > 0 )
            {
                $args = array_merge( $args, $this->data );
            }

            // Send request for detailed information
            //
            $response = $this->request( $args );

            return $response;
        }

        private function request( $args )
        {
            // Send request
            //
            $request = wp_remote_post( $this->api_url, array( 'body' => $args ) );

            // Make sure the request was successful
            //
            if( is_wp_error( $request ) or wp_remote_retrieve_response_code( $request ) != 200 )
            {
                // Request failed
                //
                return false;
            }

            // Read server response, which should be an object.
            //
            $body = wp_remote_retrieve_body( $request );
            if( strlen( $body ) > 0 )
            {
                $response = unserialize( $body );
                if( is_object( $response ) )
                {
                    return $response;
                }
            }
            return false;
        }
    }
}
