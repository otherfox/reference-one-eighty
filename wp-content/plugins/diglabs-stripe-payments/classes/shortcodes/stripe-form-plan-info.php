<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if( !class_exists( 'DigLabs_Stripe_Shortcodes_Stripe_Form_Plan_Info' ) )
{
    class DigLabs_Stripe_Shortcodes_Stripe_Form_Plan_Info extends DigLabs_Stripe_Shortcodes_Abstract_Base
    {
        private $tag = "stripe_form_plan_info";

        public function description()
        {
            return 'Creates a section in the form to collect a recurring payment.';
        }

        public function options()
        {
            return array(
                'plan'          => array(
                    'type'          => 'string',
                    'description'   => 'The <code>ID</code> of a plan that exists in your Stripe.com account. If this attribute is not provided, the payment form will be generated as a single payment with the amount not specified.',
                    'is_required'   => true,
                    'example'       => 'plan="monthly_49"'
                )
            );
        }

        public function tag()
        {
            return parent::ShortCodeWithPrefix( $this->tag );
        }

        public function output( $atts, $content = null )
        {
            extract( shortcode_atts( array(
                                         "plan" => null
                                     ), $atts ) );

            if( $plan == null && isset( $_REQUEST[ 'plan' ] ) )
            {
                $plan = $_REQUEST[ 'plan' ];
            }

            if( $plan == null )
            {
                $stripe_form_amount = new DigLabs_Stripe_Shortcodes_Stripe_Form_Amount();
                return $stripe_form_amount->output( $atts, $content );
            }

            return $this->render_plan_info( $plan );
        }

        public function render_plan_info( $plan = null )
        {
            $plans = explode(',', $plan);
            if( count( $plans ) == 1 && strtolower( $plans[0] ) != 'other' )
            {
                return $this->render_single_plan_info( $plans[0] );
            }
            return $this->render_multiple_plan_info( $plans );
            $planInfo       = Stripe_Plan::retrieve( $plan );
        }

        public function render_single_plan_info( $plan )
        {
            $planInfo       = Stripe_Plan::retrieve( $plan );
            $amount         = $planInfo->amount;
            $planName       = $planInfo->name;
            $interval_count = $planInfo->interval_count;
            $interval       = $interval_count . ' ' . $planInfo->interval;
            if( $interval_count > 1 )
            {
                $interval .= 's';
            }
            $amountShown = $amount == null ? '' : number_format( $amount / 100, 2 );

            $settings    = new DigLabs_Stripe_Helpers_Settings();
            $country_iso = $settings->getCountryIso();

            $country_helper = new DigLabs_Stripe_I18N_Country_Helper();
            $country        = $country_helper->country( $country_iso );
            $currency       = $country->currency_name;

            return <<<HTML
<h3 class="stripe-payment-form-section">Plan Information</h3>
<input class="plan" type="hidden" name="plan" value="$plan" />
<input class="amount" type="hidden" name="amount" value="$amount" />
<input class="interval" type="hidden" name="interval" value="$interval" />
<div class="stripe-payment-form-row">
<label>Plan Name</label>
<input type="text" size="20" name="planName" disabled="disabled" class="planName disabled required" value="$planName" />
<span class="stripe-payment-form-error"></span>
</div>
<div class="stripe-payment-form-row">
<label>$currency</label>
<input type="text" size="20" name="cardAmount" disabled="disabled" class="cardAmount disabled amount required" value="$amountShown" />
<span class="stripe-payment-form-error"></span>
</div>
<div class="stripe-payment-form-row">
<label>Every</label>
<input type="text" size="20" name="planInterval" disabled="disabled" class="planName disabled required" value="$interval" />
<span class="stripe-payment-form-error"></span>
</div>
HTML;
        }

        public function render_multiple_plan_info( $plans )
        {
            $settings       = new DigLabs_Stripe_Helpers_Settings();
            $country_iso    = $settings->getCountryIso();
            $country_helper = new DigLabs_Stripe_I18N_Country_Helper();
            $country        = $country_helper->country( $country_iso );
            $currency       = $country->currency_name;

            // Build the HTML;
            //
            $html = <<<HTML
<div class="stripe-payment-plans">
<h3 class="stripe-payment-form-section">Plan Information</h3>
<div class="stripe-payment-form-row">
HTML;
            $disabled = '';
            if( count( $plans ) > 1 )
            {
                $disabled = 'disabled="disabled"';
                $html .= <<<HTML
<label>Plan Options</label>
<select class="diglabs-plan" name="plan">
HTML;

            foreach( $plans as $plan_id )
            {
                $option = "<option ";
                if( strtolower( $plan_id ) == "other" )
                {
                    $option .= "value='other' ";
                    $option .= "data-amount='-1'";
                    $option .= ">";
                    $option .= $plan_id;
                }
                else
                {
                    $stripe_plan_id = trim( $plan_id );
                    $plan = Stripe_Plan::retrieve( $stripe_plan_id );
                    $option .= "value='" . $plan->id . "' ";
                    $option .= "data-amount='". $plan->amount . "' ";
                    $option .= "data-count='" . $plan->interval_count . "' ";
                    $option .= "data-interval='" . $plan->interval ."'";
                    $option .= ">";
                    $option .= $plan->name;
                }
                $option .= "</option>";
                $html .= $option;
            }

            $html .= <<<HTML
</select>
HTML;
            }
            else
            {
                $html .= <<<HTML
<input name="plan" type="hidden" value="other" />
HTML;
            }
            $html .= <<<HTML
<input class="amount" type="hidden" name="amount" />
<input class="interval" type="hidden" name="interval" />
</div>
<div class="stripe-payment-form-row">
<label>$currency</label>
<input type="text" size="20" name="cardAmount" $disabled class="cardAmount amountShown disabled required" />
<span class="stripe-payment-form-error"></span>
</div>
<div class="stripe-payment-form-row">
<label>Every</label>
<select name="planCount" $disabled class="planCount disabled required stripe-payment-form-small">
    <option value="1">1</option>
    <option value="2">2</option>
    <option value="3">3</option>
    <option value="4">4</option>
    <option value="5">5</option>
    <option value="6">6</option>
    <option value="7">7</option>
    <option value="8">8</option>
    <option value="9">9</option>
    <option value="10">10</option>
    <option value="11">11</option>
    <option value="12">12</option>
</select>
<select name="planInterval" $disabled class="planInterval disabled stripe-payment-form-medium">
    <option value="week">Week(s)</option>
    <option value="month">Month(s)</option>
    <option value="year">Year</option>
</select>
<span class="stripe-payment-form-error"></span>
</div>
</div>
<script type="text/javascript">
(function($){
    $(document).ready(function(){
        $(".diglabs-plan").change(function(){
            var form = $(this).closest('form');
            updatePlanInfo( form );
        });
        $('.planInterval').change(function(){
            var interval = $(this).val();
            var div = $(this).closest('div.stripe-payment-plans');
            var planCount = $('.planCount', div);
            var end = 12;
            if(interval=="year"){
                end = 1;
            }
            planCount.html('');
            for(var i=1;i<=end;i++){
                planCount.append('<option value="' + i + '">' + i + '</option>');
            }
        });
        updatePlanInfo();
    });
    function updatePlanInfo( form ){
        if(form==undefined){
            form = $('.diglabs-plan').closest('form');
        }
        var option = $('.diglabs-plan option:selected', form);
        var div = option.closest('div.stripe-payment-plans');
        var amount = parseInt(option.data('amount'));

        var elAmount = $('.amount', div);
        var elInterval = $('.interval', div);
        var elCardAmount = $('.cardAmount', div);
        var elPlanCount = $('.planCount', div);
        var elPlanInterval = $('.planInterval', div);

        var count, interval;
        if(amount>0){
            // Configured plan
            count = option.data('count');
            interval = option.data('interval');
            elCardAmount.attr('disabled', 'disabled').addClass('disabled');
            elPlanCount.attr('disabled', 'disabled').addClass('disabled');
            elPlanInterval.attr('disabled', 'disabled').addClass('disabled');
        }else{
            // Custom plan
            amount = 1000
            count = 1;
            interval = 'month';
            elCardAmount.removeAttr('disabled').removeClass('disabled');
            elPlanCount.removeAttr('disabled').removeClass('disabled');
            elPlanInterval.removeAttr('disabled').removeClass('disabled');
        }
        var intervalText = count + ' ' + interval;
        if(count > 1){
            intervalText += 's';
        }
        var dollarAmount = amount/100;

        elAmount.val(amount);
        elInterval.val(intervalText);
        elCardAmount.val(dollarAmount);
        elPlanCount.val(count);
        elPlanInterval.val(interval);
    }
})(jQuery);
</script>
HTML;
            return $html;
        }
    }
}
