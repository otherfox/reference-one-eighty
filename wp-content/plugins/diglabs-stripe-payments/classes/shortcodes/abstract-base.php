<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'DigLabs_Stripe_Shortcodes_Abstract_Base' ) )
{
    abstract class DigLabs_Stripe_Shortcodes_Abstract_Base
    {
        public function ShortCodeWithPrefix( $name )
        {
            return '' . $name;
        }

        public abstract function description();
        public abstract function options();

        public abstract function tag();
        public abstract function output( $atts, $content = null);

        public function manual()
        {
            $short_code = $this->tag();
            $description = $this->description();
            $my_options = $this->options();
            if( !is_array( $my_options ) )
            {
                $my_options = array();
            }

            $options = "None";
            if( count( $my_options ) > 0 )
            {
                $options = "<dl>";
                foreach( $my_options as $option_name=>$info)
                {
                    $status = $info['is_required'] ? 'yes' : 'no';
                    $example = "Example: <code>" . $info['example'] . "</code>";
                    $options .= "<dt style='overflow:auto;'>$option_name <span style='float:right;'>Type: <em>" . $info['type'] . "</em> | Required: <em>" . $status . "</em></span></dt>";
                    $options .= "<dd>" . $info['description'] . "<br />$example</dd>";
                }
                $options .= "</dl>";
            }

            return <<<HTML
<div class='diglabs-info'>
    <h3>&#91;$short_code&#93;</h3>
    <p>$description</p>
    <p>
        <h4>Options</h4>
        $options
    </p>
</div>
HTML;
        }

        public function register()
        {
            add_shortcode( $this->tag(), array( $this, 'output' ) );
        }

        public function get_billing_style( $short, $medium )
        {
            $style = "long";
            if( is_string( $medium ) )
            {
                $medium = strtolower( $medium );
                $medium = $medium === 'true';
                if( $medium )
                {
                    $style = "medium";
                }
            }
            if( is_string( $short ) )
            {
                $short = strtolower( $short );
                $short = $short === 'true';
                if( $short )
                {
                    $style = "short";
                }
            }
            return $style;
        }
    }
}