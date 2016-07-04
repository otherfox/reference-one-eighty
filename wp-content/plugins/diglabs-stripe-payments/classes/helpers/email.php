<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'DigLabs_Stripe_Helpers_Email' ) )
{
    class DigLabs_Stripe_Helpers_Email
    {
        public function createBody( $title, $msg, $data, $footer = '' )
        {
            $table_style  = 'width: 80%; margin: 20px auto; border: 1px solid #666;';
            $header_style = 'padding: 25px 10px 5px; font-weight: bold; font-size: 16px; text-transform: uppercase; background-color: #f2f2f2;';
            $left_style   = 'text-align: right; white-space: nowrap; font-weight: bold;';
            $right_style  = 'width: 99%;';
            $body         = <<<MSG
    <html>
        <body>
            <div style="padding:10px;background-color:#f2f2f2;">
                <div style="padding:10px;border:1px solid #eee;background-color:#fff;">
                    <h2>$title</h2>
                    <div style="margin:10px;">
                        $msg
                    </div>
                    <table rules="all" style='$table_style' cellpadding="10">
                        <!--table rows-->
                    </table>
                    $footer
                </div>
            </div>
        </body>
    </html>
MSG;

            $rows = "";
            if( !is_null( $data ) )
            {
                foreach( $data as $key => $val )
                {
                    if( $val == "section" )
                    {
                        $rows .= "<tr><td style='$header_style' colspan=2>$key</td></tr>";
                    }
                    else
                    {
                        $rows .= "<tr><td style='$left_style'>" . $key . "</td><td style='$right_style''>" . $val . "</td></tr>";
                    }
                }
            }
            $body = str_replace( "<!--table rows-->", $rows, $body );

            return $body;
        }

        public function sendReceipt( $to, $subject, $title, $msg, $data )
        {
            $body = '';
            if( function_exists( 'stripe_email_body' ) )
            {
                $body = stripe_email_body( $data );
            }
            else
            {
                $footer = '';
                if( function_exists( 'stripe_email_footer' ) )
                {
                    $footer = stripe_email_footer( $data );
                }
                $body = $this->createBody( $title, $msg, $data, $footer );
            }

            $this->sendEmail( $to, $subject, $body );
        }

        public function sendEmail( $to, $subject, $body, $headers = "" )
        {
            $log    = DigLabs_Stripe_Payments::log();
            $prelog = "EMAIL: ";

            if( function_exists( 'stripe_email_before_send' ) )
            {
                if( !stripe_email_before_send( $to, $subject, $body, $headers ) )
                {
                    // email was cancelled
                    return;
                }
            }
            add_filter( 'wp_mail_content_type', create_function( '', 'return "text/html";' ) );
            if( wp_mail( $to, $subject, $body, $headers ) )
            {
                // Sent successfully.
                //
                $log->info( $prelog . "Email successfully. TO: " . $to . ", SUBJECT: " . $subject );
            }
            else
            {
                global $phpmailer;
                $log->error( $prelog . "Failed to send email! TO: " . $to . ", SUBJECT: " . $subject );
                if( isset( $phpmailer ) )
                {
                    $log->error( $prelog . "----> " . $phpmailer->ErrorInfo );
                }
            }
        }
    }
}