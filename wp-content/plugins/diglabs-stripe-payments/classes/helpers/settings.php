<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'DigLabs_Stripe_Helpers_Settings' ) )
{
    class DigLabs_Stripe_Helpers_Settings
    {
        const LIVE_PUBLIC_KEY = "stripe_payment_live_public_key";
        const LIVE_SECRET_KEY = "stripe_payment_live_secret_key";
        const TEST_PUBLIC_KEY = "stripe_payment_test_public_key";
        const TEST_SECRET_KEY = "stripe_payment_test_secret_key";
        const IS_LIVE_KEYS    = "stripe_payment_is_live_keys";
        const IS_AUTO_PLAN    = "stripe_payment_is_auto_plan";
        const TAX_DATA		  = "stripe_payment_tax_data";
        const CURRENCY_SYMBOL = "stripe_payment_currency_symbol";
        const COUNTRY_ISO     = "stripe_payment_country_iso";
        const WEBHOOK_URL	  = "stripe_payment_webhook_url";
        const DOWNLOAD_KEY    = "stripe_payment_download_key";


        public $isLive;
        public $isAutoPlan;
        public $livePublicKey;
        public $liveSecretKey;
        public $testPublicKey;
        public $testSecretKey;
        public $currencySymbol;
        public $countryIso;
        public $taxData;
        public $webHookUrl;
        public $downloadKey;

        private $wpPostHelper;

        function __construct()
        {
            $this->fetchAll();

            $this->wpPostHelper = new DigLabs_WordPress_Post_Helper();
        }

        public function setTaxData($data)
        {
            $this->setAndFetch(self::TAX_DATA, $data);
        }

        public function getTaxData()
        {
            if(is_array($this->taxData))
            {
                return $this->taxData;
            }
            return array();
        }

        public function getTaxRate($country=null, $state=null)
        {
            if(is_null($country) || strlen(trim($country))==0)
            {
                return 0.0;
            }
            if(is_null($state) || strlen(trim($state))==0)
            {
                return 0.0;
            }
            if(!is_array($this->taxData))
            {
                return 0.0;
            }
            if(isset( $this->taxData[$country] ) )
            {
                if( isset( $this->taxData[$country][$state] ) )
                {
                    return $this->taxData[$country][$state];
                }
                else if (isset( $this->taxData[$country]['*'] ) )
                {
                    return $this->taxData[$country]['*'];
                }
            }
            return 0.0;
        }

        public function setIsLive($val)
        {
            $this->setAndFetch(self::IS_LIVE_KEYS, $val);
        }

        public function setIsAutoPlan($val)
        {
            $this->setAndFetch(self::IS_AUTO_PLAN, $val);
        }

        public function setLivePublicKey($val)
        {
            $this->setAndFetch(self::LIVE_PUBLIC_KEY, $val);
        }

        public function setLiveSecretKey($val)
        {
            $this->setAndFetch(self::LIVE_SECRET_KEY, $val);
        }

        public function setTestPublicKey($val)
        {
            $this->setAndFetch(self::TEST_PUBLIC_KEY, $val);
        }

        public function setTestSecretKey($val)
        {
            $this->setAndFetch(self::TEST_SECRET_KEY, $val);
        }

        public function setCurrencySymbol($val)
        {
            $this->setAndFetch(self::CURRENCY_SYMBOL, $val);
        }

        public function getCurrencySymbol()
        {
            return $this->currencySymbol;
        }

        public function setCountryIso($val)
        {
            $this->setAndFetch(self::COUNTRY_ISO, $val);
        }

        public function getCountryIso()
        {
            return $this->countryIso;
        }

        public function getPaymentUrl()
        {
            $url = home_url();
            // ensure SSL
            $type = strtolower( substr($url, 0, 5) );
            if($type != 'https')
            {
                $url = 'https'.substr($url, 4);
            }
            return $url;
        }

        public function setWebHookUrl($val)
        {
            if( !isset( $val ) || $val == '' )
            {
                // Don't allow an empty webhook as all URLs will
                //  match and the site will effectively be down.
                //
                $val = 'payment-webhook';
            }
            if( $val != $this->webHookUrl )
            {
                $this->updatePage($this->webHookUrl, $val);
            }
            $this->setAndFetch(self::WEBHOOK_URL, $val);
        }

        public function getWebHookUrl()
        {
            if( empty( $this->webHookUrl ) )
            {
                return false;
            }
            $page = get_page_by_path( $this->webHookUrl );
            $permalink = get_permalink($page->ID);
            return $permalink;
        }

        public function getPublicKey()
        {
            if($this->isLive)
            {
                return $this->livePublicKey;
            }
            return $this->testPublicKey;
        }

        public function getSecretKey()
        {
            if($this->isLive)
            {
                return $this->liveSecretKey;
            }
            return $this->testSecretKey;
        }

        public function getDownloadKey()
        {
            return $this->downloadKey;
        }

        public function setDownloadKey( $key )
        {
            $this->setAndFetch( self::DOWNLOAD_KEY, $key );
        }

        function isValid()
        {
            $error = "";
            if( strlen($this->getPublicKey())==0)
            {
                $error .= "<li>Public key is not set.</li>";
            }
            if( strlen($this->getSecretKey())==0)
            {
                $error .= "<li>Secret key is not set.</li>";
            }
            if( strlen($this->currencySymbol)==0)
            {
                $error .= "<li>Secret key is not set.</li>";
            }
            if(strlen($error)>0) {
                $error = "<div class='stripe-payment-config-errors'><p>Fix the following configuration errors before using the form.</p><ul>".$error."</ul></div>";
            }

            return $error;
        }


        private function setAndFetch($key, $val)
        {
            update_option($key, $val);
            $this->fetchAll();
        }

        private function fetchAll()
        {
            $isLiveKeys 			= get_option(self::IS_LIVE_KEYS);
            $this->isLive 			= strlen($isLiveKeys)==0 ? false : true;
            $isAutoPlan             = get_option(self::IS_AUTO_PLAN);
            $this->isAutoPlan       = strlen($isAutoPlan)==0 ? false : true;
            $this->livePublicKey 	= get_option(self::LIVE_PUBLIC_KEY);
            $this->liveSecretKey 	= get_option(self::LIVE_SECRET_KEY);
            $this->testPublicKey 	= get_option(self::TEST_PUBLIC_KEY);
            $this->testSecretKey 	= get_option(self::TEST_SECRET_KEY);
            $this->currencySymbol 	= get_option(self::CURRENCY_SYMBOL);
            $this->webHookUrl 		= get_option(self::WEBHOOK_URL);
            $this->taxData 			= get_option(self::TAX_DATA);
            $this->countryIso       = get_option(self::COUNTRY_ISO);
            $this->downloadKey      = get_option(self::DOWNLOAD_KEY);
        }

        private function updatePage($old, $new)
        {
            $this->wpPostHelper->delete_page($old);
            $this->wpPostHelper->update_page($new, $new);
        }

    }
}
