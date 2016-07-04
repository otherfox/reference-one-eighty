<?php

require_once 'pagination.php';

// Get the API key from the settings.
//
$settings = new DigLabs_Stripe_Helpers_Settings();
$secretKey = $settings->getSecretKey();
Stripe::setApiKey( $secretKey );

// Get the customer id (if any)
//
$custId = null;
if( isset( $_REQUEST[ 'c' ] ) )
{
    $custId = $_REQUEST[ 'c' ];
}

if( !is_null( $custId ) && !empty( $custId ) )
{
    // Handle any post backs
    //
    if( isset( $_POST[ 'add-payment' ] ) )
    {
        try
        {
            $args   = array(
                "amount"      => intval( floatval( $_POST[ 'amount' ] ) * 100 ),
                "currency"    => "usd",
                "customer"    => $custId,
                "description" => $_POST[ 'description' ]
            );
            $result = Stripe_Charge::create( $args );
            echo "<div class='updated'><p><strong>Payment added.</strong></p></div>";
        }
        catch( Exception $e )
        {
            $json    = json_decode( $e->http_body );
            $message = $json->error->message;
            echo "<div class='error'><p><strong>Error: </strong>{$message}</p></div>";
        }
    }

    if( isset( $_POST[ 'add-plan' ] ) )
    {
        $plan = $_POST[ 'plan' ];

        try
        {
            $customer = Stripe_Customer::retrieve( $custId );

            if( $plan == 'Cancel' )
            {
                // Cancel plan if there is one.
                //
                if( !is_null( $customer->subscription ) )
                {
                    $result = $customer->cancelSubscription();
                    echo "<div class='updated'><p><strong>Subscription cancelled.</strong></p></div>";
                }
            }
            else
            {
                $args   = array(
                    "plan" => $_POST[ 'plan' ]
                );
                $result = $customer->updateSubscription( $args );
                echo "<div class='updated'><p><strong>Subscription updated.</strong></p></div>";
            }
        }
        catch( Exception $e )
        {
            $json    = json_decode( $e->http_body );
            $message = $json->error->message;
            echo "<div class='error'><p><strong>Error: </strong>{$message}</p></div>";
        }
    }

    if( isset( $_POST[ 'refund' ] ) )
    {
        try
        {
            $payid  = $_POST[ 'payid' ];
            $ch     = Stripe_Charge::retrieve( $payid );
            $result = $ch->refund();
            echo "<div class='updated'><p><strong>Payment refunded.</strong></p></div>";
        }
        catch( Exception $e )
        {
            $json    = json_decode( $e->http_body );
            $message = $json->error->message;
            echo "<div class='error'><p><strong>Error: </strong>{$message}</p></div>";
        }
    }

    if( isset( $_POST[ 'add-coupon' ] ) )
    {
        try
        {
            $coupon = $_POST[ 'coupon' ];

            $customer = Stripe_Customer::retrieve( $custId );

            if( $coupon == 'None' )
            {
                // Remove the discount from the customer.
                //
                echo "<div class='error'><p><strong>Error: </strong>Operation not supported.</p></div>";
            }
            else
            {
                // Apply the coupon
                //
                $customer->coupon = $coupon;
                $customer->save();
                echo "<div class='updated'><p><strong>Coupon updated.</strong></p></div>";
            }
        }
        catch( Exception $e )
        {
            $json    = json_decode( $e->http_body );
            $message = $json->error->message;
            echo "<div class='error'><p><strong>Error: </strong>{$message}</p></div>";
        }
    }

    if( isset( $_POST[ 'delete-cust' ] ) )
    {
        try
        {
            $customer = Stripe_Customer::retrieve( $custId );
            $customer->delete();
            $custId = null;
            echo "<div class='updated'><p><strong>Customer deleted.</strong></p></div>";
        }
        catch( Exception $e )
        {
            $json    = json_decode( $e->http_body );
            $message = $json->error->message;
            echo "<div class='error'><p><strong>Error: </strong>{$message}</p></div>";
        }
    }
}

$count = 10;
$users = array();
$plans = array();
$coupons = array();

try
{
    // Get all the stripe plans & coupons.
    //
    $plans   = Stripe_Plan::all();
    $coupons = Stripe_Coupon::all();

    // Get the count / offset arguements.
    //
    $page = 0;
    if( isset( $_REQUEST[ 'p' ] ) )
    {
        $page = intval( $_REQUEST[ 'p' ] );
    }
    $offset = $page * $count;

    // Fetch the user data from Stripe
    //
    $args  = array(
        'count'  => $count,
        'offset' => $offset,
        'expand' => array( 'data.default_card' )
    );
    $all   = Stripe_Customer::all( $args );
    $users = $all->data;
}
catch( Exception $e )
{
    echo "<div class='error'>Configure the Stripe API keys in the <strong>Setup</strong> tab.</div>";
}

// Calculate the number of pages.
//
$total_users = $all->count;
$tot_pages = ceil( $total_users / $count );

// Render pagination on top of the form.
//
diglabs_kriesi_pagination( $page, $tot_pages, 2 );
$tab = 'customers';
if( isset( $_REQUEST[ 'tab' ] ) )
{
    $tab = $_REQUEST[ 'tab' ];
}
?>

    <table class="widefat fixed">
        <thead>
        <tr>
            <th id="columnname" class="manage-column column-columnname" scope="col">Id</th>
            <th id="columnname" class="manage-column column-columnname" scope="col">Timestamp</th>
            <th id="columnname" class="manage-column column-columnname" scope="col">Email</th>
            <th id="columnname" class="manage-column column-columnname" scope="col">Is Live</th>
            <th id="columnname" class="manage-column column-columnname" scope="col">Card Name</th>
            <th id="columnname" class="manage-column column-columnname" scope="col">Last 4</th>
            <th id="columnname" class="manage-column column-columnname" scope="col">Expiration</th>
        </tr>
        </thead>

        <tfoot>
        <tr>
            <th id="columnname" class="manage-column column-columnname" scope="col">Id</th>
            <th id="columnname" class="manage-column column-columnname" scope="col">Timestamp</th>
            <th id="columnname" class="manage-column column-columnname" scope="col">Email</th>
            <th id="columnname" class="manage-column column-columnname" scope="col">Is Live</th>
            <th id="columnname" class="manage-column column-columnname" scope="col">Card Name</th>
            <th id="columnname" class="manage-column column-columnname" scope="col">Last 4</th>
            <th id="columnname" class="manage-column column-columnname" scope="col">Expiration</th>
        </tr>
        </tfoot>

        <tbody>
        <?php foreach( $users as $index => $user )
        { ?>
            <tr <?php if( $index % 2 == 0 )
            {
                echo "class='alternate'";
            } ?> >
                <td class="column-columnname"><a
                        href='<?php echo "?page=" . DLSP_ADMIN_PAGE . "&tab=$tab&p=$page&c=$user->id"; ?>'><?php echo $user->id; ?></a>
                </td>
                <td class="column-columnname"><?php echo date( 'F j Y g:i a', $user->created ); ?></td>
                <td class="column-columnname"><?php echo $user->email; ?></td>
                <td class="column-columnname"><?php echo $user->livemode ? 'true' : 'false'; ?></td>
                <td class="column-columnname"><?php echo $user->default_card->name; ?></td>
                <td class="column-columnname"><?php echo $user->default_card->last4; ?></td>
                <td class="column-columnname"><?php echo $user->default_card->exp_month . '/' . $user->default_card->exp_year; ?></td>
            </tr>
        <?php } ?>
        </tbody>

    </table>
<?php
// Render pagination on bottom of the form
//
diglabs_kriesi_pagination( $page, $tot_pages, 2 );

// Render the information for the selected customer (if any)
//
if( !is_null( $custId ) && !empty( $custId ) )
{
    // Fetch the customer from Stripe
    //
    $cust     = Stripe_Customer::retrieve( array( 'id' => $custId, 'expand' => array( 'default_card' ) ) );
    $card     = $cust->default_card;
    $payments = Stripe_Charge::all( array( 'customer' => $custId ) );
    $sub      = $cust->subscription;
    $discount = $cust->discount;

    // Gravatar stuff
    //
    $md5          = md5( $cust->email );
    $gravatar_src = "http://www.gravatar.com/avatar/$md5?s=80";

	?>
<div style="clear:both;"></div>
<hr />
<div class="area">
	<div class="section">
		<div class="gravatar">
			<img src="<?php echo $gravatar_src; ?>" />
			<?php if( !$cust->livemode ) { echo '<span class="mode">Test</span>'; } ?>
		</div>
		<div class="title"><?php echo $cust->email; ?></div>
		<div class="id"><?php echo $cust->id; ?></div>
	</div>
	<div class="section">
		<h4>Customer Details</h4>
		<div class="container">
			<dl>
				<dt>ID:</dt>
				<dd><?php echo $cust->id; ?></dd>
				<dt>Created:</dt>
				<dd><?php echo date( 'F j Y g:i a', $cust->created ); ?></dd>
				<dt>Email:</dt>
				<dd><?php echo $cust->email; ?></dd>
				<dt>Description:</dt>
				<dd>
                <?php
                    $json = json_decode( $cust->description );
                    // json_last_error is PHP >= 5.3.0
                    if( !is_null( $json ) && (is_array( $json ) || is_object( $json ) ) )
                    {
                        $temp = array();
                        foreach( $json as $key=>$value )
                        {
                            $temp[] = "$key: <strong>$value</strong>";
                        }
                        echo implode(", ", $temp );
                    }
                    else
                    {
                        echo $cust->description;
                    }
                ?>
                </dd>
			</dl>
		</div>
	</div>
	<div class="section">
		<h4>Active Card</h4>
		<div class="container">
			<dl>
				<dt>Name:</dt>
				<dd><?php echo $card->name; ?></dd>
				<dt>Number:</dt>
				<dd>**** **** **** <?php echo $card->last4; ?></dd>
				<dt>Fingerprint:</dt>
				<dd><?php echo $card->fingerprint; ?></dd>
				<dt>Expires:</dt>
				<dd><?php echo $card->exp_month; ?> / <?php echo $card->exp_year; ?></dd>
				<dt>Type:</dt>
				<dd><?php echo $card->type; ?></dd>
			</dl>
		</div>
	</div>
	<div class="section">
		<h4>Payments<span class='add'>Create Payment</span></h4>
		<div class="form">
			<form method="post" action="" class="confirm">
				<dl>
					<dt><label for="amount">Amount:</label></dt>
					<dd><input maxlength="45" size="25" name="amount" type="text" /></dd>
					<dt><label for="description">Description:</label></dt>
					<dd><input maxlength="45" size="25" name="description" type="text" /></dd>
				</dl>
				<p><input class="button-primary" name="add-payment" type="submit" value="Create Payment" /></p>
			</form>
		</div>
		<div class="container">
			<ul>
			<?php foreach( $payments->data as $payment ) {
				echo "<li>";
				echo "<span class='payid'>".$payment->id."</span>";
				echo "<span class='amount'>$ ".number_format( $payment->amount/100, 2 )."</span>";
				echo "<span class='date'>".date( 'F j Y g:i a', $payment->created )."</span>";
                $json = json_decode( $payment->description );
                // json_last_error is PHP >= 5.3.0
                if( !is_null( $json ) && (is_array( $json ) || is_object( $json ) ) )
                {
                    echo "<span class='desc' style='display:block;margin-top:10px;line-height: 22px;'>";
                    $temp = array();
                    foreach( $json as $key=>$value )
                    {
                        $temp[] = "$key: <strong>$value</strong>";
                    }
                    echo implode(", ", $temp );
                    echo "</span>";
                }
                else
                {
                    echo "<span class='desc'>";
                    echo $payment->description;
                    echo "</span>";
                }
				if($payment->refunded) {
				echo "<span class='refund'>refunded</span>";
				} else {
				?>
<span class="refund">
	<form method="post" action="" class="confirm">
		<input type="hidden" name="payid" value="<?php echo $payment->id; ?>" />
		<input class="button-primary" name="refund" type="submit" value="refund" />
	</form>
</span>
				<?php }
				echo "</li>";
			} ?>
			</ul>
		</div>
	</div>
	<div class="section">
		<h4>Subscriptions<span class='add'><?php echo is_null($sub) ? "Add" : "Update"; ?> Subscription</span></h4>
		<div class="form">
			<form method="post" action="" class="confirm">
				<dl>
					<dt>Current Plan:</dt>
					<dd><?php echo is_null($sub) ? "None" : $sub->plan->name; ?></dd>
					<dt><label for="plan">New Plan:</label></dt>
					<dd>
						<select name="plan">
							<option value="Cancel">Cancel or None</option>
						<?php foreach( $plans->data as $plan ) {
							echo "<option value='$plan->id'>$plan->name ( $".number_format($plan->amount/100, 2)." per $plan->interval )</option>";
						} ?>
						</select>
					</dd>
				</dl>
				<p><input class="button-primary" name="add-plan" type="submit" value="Create Payment" /></p>
			</form>
		</div>
		<div class="container">
		<?php if( !is_null( $sub ) ) { ?>
			<dl>
				<dt>Status:</dt>
				<dd><?php echo $sub->status; ?></dd>
				<dt>Plan:</dt>
				<dd><?php echo $sub->plan->name; ?></dd>
				<dt>Interval:</dt>
				<dd><?php echo $sub->plan->interval; ?></dd>
				<dt>Amount:</dt>
				<dd>$ <?php echo number_format( $sub->plan->amount/100, 2); ?></dd>
			</dl>
		<?php } else {
			echo "<span>No current subscription.</span>";
		} ?>
		</div>
	</div>
	<div class="section">
		<h4>Discount<span class='add'><?php echo is_null($discount) ? "Add" : "Update"; ?> Coupon</span></h4>
		<div class="form">
			<form method="post" action="" class="confirm">
				<dl>
					<dt>Current Discount:</dt>
					<dd><?php echo is_null($discount) ? "None" : $discount->coupon->id." ( ".$discount->coupon->percent_off."% off )"; ?></dd>
					<dt><label for="coupon">New Coupon:</label></dt>
					<dd>
						<select name="coupon">
						<?php foreach( $coupons->data as $coupon ) {
							echo "<option value='$coupon->id'>$coupon->id ( ".$coupon->percent_off."% off )</option>";
						} ?>
						</select>
					</dd>
				</dl>
				<p><input class="button-primary" name="add-coupon" type="submit" value="Update Discount" /></p>
			</form>
		</div>
		<div class="container">
		<?php if( !is_null( $discount ) ) { ?>
			<dl>
				<dt>Coupon:</dt>
				<dd><?php echo $discount->coupon->id; ?></dd>
				<dt>Percent Off:</dt>
				<dd><?php echo $discount->coupon->percent_off; ?></dd>
				<dt>Redeem By:</dt>
				<dd><?php echo date( 'F j Y g:i a', $discount->coupon->redeem_by ); ?></dd>
				<dt>Duration:</dt>
				<dd><?php echo $discount->coupon->duration; ?></dd>
				<dt>Redeemed:</dt>
				<dd><?php echo $discount->coupon->times_redeemed." of ".$discount->coupon->max_redemptions; ?></dd>
				<dt>Is Live:</dt>
				<dd><?php echo $discount->coupon->livemode ? 'Yes' : 'No'; ?></dd>
			</dl>
		<?php } else {
			echo "<span>No current coupon.</span>";
		} ?>
		</div>
	</div>
	<div class="section">
		<form method="post" action="" class="confirm">
			<p><input class="button-primary" name="delete-cust" type="submit" value="Delete Customer" /></p>
		</form>
	</div>
</div>

<script type="text/javascript">
	(function($) {
		$(document).ready(function(){

			$('.add').click(function(){

				var section = $(this).closest('.section');
				$('div.form', section).slideToggle('slow');

			});

			$('form.confirm').submit(function(e){

				if(confirm("Are you sure?")!=true) {
					e.preventDefault();
				}
			});
		});
	})(jQuery);
</script>
	<?php
}

?>