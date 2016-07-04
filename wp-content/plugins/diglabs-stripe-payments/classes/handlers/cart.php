<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'DigLabs_Stripe_Handlers_Cart' ) )
{
    class DigLabs_Stripe_Cart_Item
    {
        public $id;
        public $count;
        public $unit_cost;
        public $info;

        public function __construct()
        {
            $this->id = -1;
            $this->count = 0;
            $this->unit_cost = 0;
            $this->info = '';
        }
    }

    class DigLabs_Stripe_Handlers_Cart
    {
        private static $session_key = 'DigLabs_Stripe_Payments_Cart';

        public function __construct()
        {
            if (session_id() == '')
            {
                session_start();
            }
        }

        // Process any post requests
        public function process()
        {
            if($_SERVER['REQUEST_METHOD'] === 'POST' )
            {
                // Handle HTTP POST from cart forms
                //
                if( isset( $_POST['cmd-diglabs-cart-update'] ) &&
                    !empty( $_POST['cmd-diglabs-cart-update'] ) )
                {
                    $this->update_cart();
                }
            }
        }

        // Get the cart items
        public function get_items()
        {
            if( isset( $_SESSION[ DigLabs_Stripe_Handlers_Cart::$session_key ] ) )
            {
                $this->log_message("Found session data.");
                return unserialize( $_SESSION[ DigLabs_Stripe_Handlers_Cart::$session_key ] );
            }
            $this->log_message("No session data found.");
            return array();
        }

        // Clear the cart
        public function clear()
        {
            unset( $_SESSION[ DigLabs_Stripe_Handlers_Cart::$session_key ] );
        }

        // Returns cart data
        public function get()
        {
            // Create the response array
            //
            $response = array(
                'success'   => true,
                'message'   => '',
                'items'     => $this->get_items()
            );

            echo json_encode( $response );
            die();
        }

        // Adds item(s) to the cart and returns the cart data
        public function add()
        {
            // Create the response array
            //
            $response = array(
                'success' => false,
                'message' => ''
            );

            if($_SERVER['REQUEST_METHOD'] === 'POST' && isset( $_POST[ 'id' ] ) )
            {
                $item_id = $_POST[ 'id' ];
                if( $this->add_item( $item_id ) )
                {
                    $response['success'] = true;
                }
                else
                {
                    $response['message'] = 'Unable to add item.';
                }
            }

            $response[ 'items' ] = $this->get_items();

            echo json_encode( $response );
            die();
        }

        // Removes items(s) from the card and returns the cart data
        public function remove()
        {
            // Create the response array
            //
            $response = array(
                'success' => false,
                'message' => ''
            );

            if($_SERVER['REQUEST_METHOD'] === 'POST' && isset( $_POST[ 'id' ] ) )
            {
                $item_id = $_POST[ 'id' ];
                if( $this->remove_item( $item_id ) )
                {
                    $response['success'] = true;
                }
            }

            $response[ 'items' ] = $this->get_items();

            echo json_encode( $response );
            die();
        }

        private function update_cart()
        {
            $ids = $_POST[ 'ids' ];
            $quantities = $_POST[ 'quantities' ];

            $items = $this->get_items();
            $result = array();
            foreach( $items as $item )
            {
                $item_id = $item->id;
                $index = array_search( $item_id, $ids );
                $item->count = $quantities[ $index ];
                if( $item->count > 0 )
                {
                    $result[] = $item;
                }
            }

            $this->update_items( $result );
        }

        private function add_item( $item_id, $count = 1 )
        {
            $items = $this->get_items();

            // If the item is already in the list...increment and exit early.
            //
            foreach( $items as $item )
            {
                if( $item->id == $item_id )
                {
                    $item->count += $count;

                    $this->update_items( $items );

                    return true;
                }
            }

            // Look up the item using the externally provided callback.
            //
            $item_data = $this->get_item_data( $item_id );
            if( $item_data != null )
            {
                $cart_item = new DigLabs_Stripe_Cart_Item();
                $cart_item->id = $item_id;
                $cart_item->count = $count;
                $cart_item->info = $item_data[ 'info' ];
                $cart_item->unit_cost = $item_data[ 'cost' ];

                $items[] = $cart_item;

                $this->update_items( $items );

                return true;
            }

            return false;
        }

        private function remove_item( $item_id, $count = 1 )
        {
            $items = $this->get_items();

            // Remove item with this ID from the list.
            //
            $found = false;
            $result = array();
            foreach( $items as $item )
            {
                if( $item->id == $item_id )
                {
                    $item->count -= $count;
                    $found = false;
                }
                if( $item->count > 0 )
                {
                    $result[] = $item;
                }
            }

            $this->update_items( $items );

            return $found;
        }

        private function update_items( $items )
        {
            $_SESSION[ DigLabs_Stripe_Handlers_Cart::$session_key ] = serialize( $items );
        }

        private function log_message( $msg )
        {
            $log = DigLabs_Stripe_Payments::log();
            $log->debug( "CART: " . $msg );
        }

        private function log_error( $error )
        {
            $log = DigLabs_Stripe_Payments::log();
            $log->error( "CART: " . $error );
        }

        private function get_item_data( $item_id )
        {
            // The first one to return the data wins.
            //
            $cart_item_callbacks = DigLabs_Stripe_Payments::GlobalInstance()->get_cart_item_callbacks();
            foreach( $cart_item_callbacks as $callback )
            {
                $function = $this->get_function( $callback );
                $this->log_message('CART ITEM CALLBACK - ' . $callback . ' @ ' . $function->getFileName() );
                $params = array( &$item_id );
                $result = call_user_func_array( $callback, $params );
                if( $result != null && is_array( $result ) && isset( $result[ 'info' ] ) && isset( $result[ 'cost' ] ) )
                {
                    return $result;
                }
            }

            return null;
        }

        private function get_function( $callback )
        {
            if( is_array( $callback ) )
            {
                // must be a class method
                list( $class, $method ) = $callback;
                return new ReflectionMethod( $class, $method );
            }

            // class::method syntax
            if( is_string( $callback ) && strpos( $callback, "::" ) !== false )
            {
                list( $class, $method ) = explode( "::", $callback );
                return new ReflectionMethod( $class, $method );
            }

            // assume it's a function
            return new ReflectionFunction( $callback );
        }
    }
}
