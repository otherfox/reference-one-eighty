<?php

require_once 'pagination.php';

// Get the API key from the settings.
//
$settings = new DigLabs_Stripe_Helpers_Settings();
$secretKey = $settings->getSecretKey();
Stripe::setApiKey( $secretKey );

// Get helper to show amount in proper currency.
//
$country_helper = new DigLabs_Stripe_I18N_Country_Helper();
$country_code = $settings->getCountryIso();

// Get the count / offset arguements.
//
$page = 0;
if(isset($_REQUEST['p']))
{
	$page = intval( $_REQUEST[ 'p' ] );
}
$count = 50;
$offset = $page * $count;

// Fetch the event data from Stripe
//
$total_events = 0;
$events = array();
$filter = '';
try 
{
	$args = array(
			'count'		=> $count,
			'offset'	=> $offset
		);

	// Add any filter
	//
	if(isset($_REQUEST['filter']) && strlen(trim($_REQUEST['filter']))>0)
	{
		$filter = trim($_REQUEST['filter']);
		$args['type'] = $filter;
	}

	$all = Stripe_Event::all( $args );

	$events = $all->data;
	$total_events = $all->count;

} 
catch (Exception $e) 
{
	echo "<div class='error'>Configure the Stripe API keys in the <strong>Setup</strong> tab.</div>";
}


// Calculate the number of pages.
//
$tot_pages = ceil( $total_events / $count );
?>

<p>
	<form action='' method='GET'>
		<?php echo "<input type='hidden' name='page' value='". DLSP_ADMIN_PAGE . "' />"; ?>
		<input type='hidden' name='tab' value='events' />
		<?php if($page>0) { echo "<input type='hidden' name='p' value='$page' />"; } ?>
		<?php echo "Filter: <input name='filter' type='text' placeHolder='e.g. transfer.paid' value='" . $filter . "'/>"; ?>
		<button type='submit'>Filter</button>
	</form>
</p>

<?php
// Render pagination on top of the form.
//
diglabs_kriesi_pagination($page, $tot_pages, 2);
$tab = $_REQUEST['tab'];
?>

<table class="widefat fixed">
	<thead>
		<tr>
			<th id="columnname" class="manage-column column-columnname" scope="col">Id</th>
			<th id="columnname" class="manage-column column-columnname" scope="col">Timestamp</th>
			<th id="columnname" class="manage-column column-columnname" scope="col">Type</th>
			<th id="columnname" class="manage-column column-columnname" scope="col">Amount</th>
			<th id="columnname" class="manage-column column-columnname" scope="col">Is Live</th>
			<th id="columnname" class="manage-column column-columnname" scope="col">Pending</th>
		</tr>
	</thead>

	<tfoot>
		<tr>
			<th id="columnname" class="manage-column column-columnname" scope="col">Id</th>
			<th id="columnname" class="manage-column column-columnname" scope="col">Timestamp</th>
			<th id="columnname" class="manage-column column-columnname" scope="col">Type</th>
			<th id="columnname" class="manage-column column-columnname" scope="col">Amount</th>
			<th id="columnname" class="manage-column column-columnname" scope="col">Is Live</th>
			<th id="columnname" class="manage-column column-columnname" scope="col">Pending</th>
		</tr>
	</tfoot>

	<tbody>
		<?php foreach( $events as $index => $event ) { ?>
		<tr <?php if($index%2==0){echo "class='alternate'";} ?> >
			<td class="column-columnname"><?php echo $event->id; ?></a></td>
			<td class="column-columnname"><?php echo date( 'F j Y g:i a', $event->created ); ?></td>
			<td class="column-columnname"><?php echo $event->type; ?></td>
			<td class="column-columnname"><?php echo $country_helper->currency( $event->data->object->amount/100, $country_code ); ?></td>
			<td class="column-columnname"><?php echo $event->livemode ? 'Yes' : 'No'; ?></td>
			<td class="column-columnname"><?php echo $event->pending_webhooks; ?></td>
		</tr>
		<?php } ?>
	</tbody>

</table>
<?php
// Render pagination on bottom of the form
//
diglabs_kriesi_pagination($page, $tot_pages, 2);
?>