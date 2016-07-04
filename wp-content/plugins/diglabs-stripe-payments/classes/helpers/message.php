<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'DigLabs_Stripe_Helpers_Message' ) )
{
    class DigLabs_Stripe_Helpers_Message
    {
        private $errors = array();
        private $messages = array();

        public function add( $data, $is_error = false )
        {
            $html = is_array( $data ) ? "<h4>{$data['title']}</h4><div>{$data['body']}</div>" : $data;
            if( $is_error )
            {
                $this->errors[] = $html;
            }
            else
            {
                $this->messages[] = $html;
            }
        }

        public function add_info( $title = '', $body = '')
        {
            $html = "<h4>$title</h4><div>$body</div>";
            $this->messages[] = $html;
        }

        public function add_error( $title = '', $body = '')
        {
            $html = "<h4>$title</h4><div>$body</div>";
            $this->errors[] = $html;
        }

        public function count()
        {
            return $this->count_messages() + $this->count_errors();
        }
        public function count_messages()
        {
            return count( $this->messages );
        }
        public function count_errors()
        {
            return count( $this->errors );
        }

        public function render()
        {
            foreach( $this->errors as $error )
            {
                echo $this->render_message( $error, 'diglabs-error' );
            }
            foreach( $this->messages as $message )
            {
                echo $this->render_message( $message );
            }
        }

        private function render_message( $html, $class = '' )
        {
            $msg = "<div class='updated diglabs-message $class '>";
            $msg .= "<img class='diglabs-icon' src='http://diglabs.com/images/beaker-icon.png' />";
            $msg .= $html;
            $msg .= "</div>";
            return $msg;
        }
    }
}