<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'DigLabs_Stripe_I18N_Country_Helper' ) )
{
    class DigLabs_Stripe_I18N_Country_Helper
    {
        public $json_file;
        private $countries = array();
        private $supported_countries = array();

        public function __construct( $json_file = null )
        {
            if( is_null( $json_file ) )
            {
                $json_file = dirname( __FILE__ ) . '/countries.json';
            }
            $this->json_file = $json_file;

            $this->parse_file();
        }

        public function parse_file()
        {
            $this->countries = array();

            $json = file_get_contents( $this->json_file );
            $json = preg_replace('/^\xEF\xBB\xBF/', '', $json);
            $array = json_decode( $json );

            foreach( $array as $obj )
            {
                $country = new DigLabs_Stripe_I18N_Country();
                $country->country_iso_2char     = $obj->country_iso_2char;
                $country->country_iso_3char     = $obj->country_iso_3char;
                $country->country_iso_number    = $obj->country_iso_number;
                $country->country_name          = $obj->country_name;
                $country->currency_iso_3char    = $obj->currency_iso_3char;
                $country->currency_iso_number   = $obj->currency_iso_number;
                $country->currency_name         = $obj->currency_name;
                $country->states                = $obj->states;
                $country->state_name            = 'State/Province';

                $this->countries[ $country->country_iso_2char ] = $country;
            }
        }

        public function countries()
        {
            return $this->countries;
        }

        public function country( $iso2 )
        {
            if( isset( $this->countries[ $iso2 ] ) )
            {
                return $this->countries[ $iso2 ];
            }
            return null;
        }

        public function get_state_abbreviation($country_code, $state_name )
        {
            if( isset( $this->countries[ $country_code ] ) )
            {
                $country = $this->countries[ $country_code ];
                $states = $country->states;
                foreach( $states as $abbr => $name )
                {
                    if( $name == $state_name )
                    {
                        return $abbr;
                    }
                }
            }
            return null;
        }

        public function supported_sites()
        {
            if( count( $this->supported_countries ) == 0 )
            {
                if( ini_get( 'allow_url_fopen' ) == true )
                {
                    try
                    {
                        // Try and fetch the live version from the site.
                        //
                        $url = "http://diglabs.com/api/stripe/countries.json";
                        $json = file_get_contents( $url );
                        if( $json != FALSE )
                        {
                            $this->supported_countries = json_decode( $json );
                        }
                    }
                    catch (Exception $e)
                    {
                        // Force the hard-coded country list...
                        //
                        $this->supported_countries = null;
                    }
                }
                if( is_null( $this->supported_countries ) || count( $this->supported_countries ) == 0 )
                {
                    // Use a hard-coded version.
                    //
                    $this->supported_countries = array(
                        "US", "CA", "GB", "IE", "FR", "NL"
                    );
                }
            }

            return $this->supported_countries;
        }

        public function users_location()
        {
            $ip = "54.235.81.42"; //$_SERVER['REMOTE_ADDR'];
            $json = file_get_contents("http://api.easyjquery.com/ips/?ip=".$ip."&full=true");
            $json = json_decode($json,true);

            $country_code = $json['Country'];
            $country_name = $json['CountryName'];
            $state_name = $json['RegionName'];
            $state_code = $this->get_state_abbreviation( $country_code, $state_name );
            return array(
                'country_code'      => $country_code,
                'country_name'      => $country_name,
                'state_code'        => $state_code,
                'state_name'        => $state_name
                );
        }

        public function currency( $amount = 0, $country_code )
        {
            $bc = $country_code;
            $currency_before = '';
            $currency_after = '';

            if( $bc == 'GB' || $bc == 'IE' || $bc == 'CY' ) $currency_before = '&pound;';
            if( $bc == 'AT' || $bc == 'BE' || $bc == 'FI' || $bc == 'FR' ||
                $bc == 'DE' || $bc == 'GR' || $bc == 'GP' || $bc == 'IT' ||
                $bc == 'LU' || $bc == 'NL' || $bc == 'PT' || $bc == 'SI' ||
                $bc == 'ES') $currency_before = '&euro;';
            if( $bc == 'BR' ) $currency_before = 'R$';
            if( $bc == 'CN' || $bc == 'JP' ) $currency_before = '&yen;';
            if( $bc == 'CR' ) $currency_before = '&cent;';
            if( $bc == 'HR' ) $currency_after = ' kn';
            if( $bc == 'CZ' ) $currency_after = ' kc';
            if( $bc == 'DK' ) $currency_before = 'DKK ';
            if( $bc == 'EE' ) $currency_after = ' EEK';
            if( $bc == 'HK' ) $currency_before = 'HK$';
            if( $bc == 'HU' ) $currency_after = ' Ft';
            if( $bc == 'IS' || $bc == 'SE' ) $currency_after = ' kr';
            if( $bc == 'IN' ) $currency_before = 'Rs. ';
            if( $bc == 'ID' ) $currency_before = 'Rp. ';
            if( $bc == 'IL' ) $currency_after = ' NIS';
            if( $bc == 'LV' ) $currency_before = 'Ls ';
            if( $bc == 'LT' ) $currency_after = ' Lt';
            if( $bc == 'MY' ) $currency_before = 'RM';
            if( $bc == 'MT' ) $currency_before = 'Lm';
            if( $bc == 'NO' ) $currency_before = 'kr ';
            if( $bc == 'PH' ) $currency_before = 'PHP';
            if( $bc == 'PL' ) $currency_after = ' z';
            if( $bc == 'RO' ) $currency_after = ' lei';
            if( $bc == 'RU' ) $currency_before = 'RUB';
            if( $bc == 'SK' ) $currency_after = ' Sk';
            if( $bc == 'ZA' ) $currency_before = 'R ';
            if( $bc == 'KR' ) $currency_before = 'W';
            if( $bc == 'CH' ) $currency_before = 'SFr. ';
            if( $bc == 'SY' ) $currency_after = ' SYP';
            if( $bc == 'TH' ) $currency_after = ' Bt';
            if( $bc == 'TT' ) $currency_before = 'TT$';
            if( $bc == 'TR' ) $currency_after = ' TL';
            if( $bc == 'AE' ) $currency_before = 'Dhs. ';
            if( $bc == 'VE' ) $currency_before = 'Bs. ';

            if( $currency_before == '' && $currency_after == '' ) $currency_before = '$';

            return $currency_before . number_format( $amount, 2 ) . $currency_after;
        }
    }
}