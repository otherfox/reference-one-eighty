<?php

require_once 'pagination.php';

// Get the API key from the settings.
//
$settings = new DigLabs_Stripe_Helpers_Settings();
$secretKey = $settings->getSecretKey();
Stripe::setApiKey( $secretKey );

// Get the count / offset arguments.
//
$page = 0;
if( isset( $_REQUEST[ 'p' ] ) )
{
    $page = intval( $_REQUEST[ 'p' ] );
}
$count = 7;
$offset = $page * $count;

// Fetch the event data from Stripe
//
$total_events = 0;
$events = array();
try
{
    $args = array(
        'count'  => $count,
        'offset' => $offset
    );

    $all = Stripe_Transfer::all( $args );

    $events       = $all->data;
    $total_events = $all->count;
}
catch( Exception $e )
{
    echo "<div class='error'>Configure the Stripe API keys in the <strong>Setup</strong> tab.</div>";
}
$transfers = $all->data;

// Collect the data into a more usable container.
//
$rows = array();
foreach( $transfers as $transfer )
{
    if( !isset( $transfer->transactions ) )
    {
        continue;
    }
    $transactions   = $transfer->transactions->data;
    $expected_count = $transfer->transactions->count;
    if( count( $transactions ) != $expected_count )
    {
        $temp = $transfer->transactions->all( array( 'count' => $expected_count ) );
        $transactions = $temp->data;
    }

    $total_amounts = 0;
    $total_fees    = 0;
    foreach( $transactions as $transaction )
    {
        $total_amounts += $transaction->amount;
        $total_fees += $transaction->fee;

        $row = array(
            'transferred' => date( "M j, Y", $transfer->date ),
            'status'      => $transfer->status,
            'is_live'     => $transfer->livemode ? 'Yes' : 'No',
            'created'     => date( "M j, Y", $transaction->created ),
            'charge_id'   => $transaction->id,
            'amount'      => $transaction->amount / 100,
            'fee'         => $transaction->fee / 100,
            'description' => $transaction->description
        );

        $rows[ ] = $row;
    }
}

// Fetch additional data.
//
foreach($rows as $i=>$row)
{
    try
    {
        $charge = Stripe_Charge::retrieve($row['charge_id']);
        if(isset($charge->customer))
        {
            if(empty($charge->card->name))
            {
                $customer_id = $charge->customer;
                $customer = Stripe_Customer::retrieve( $customer_id );

                $description = $customer->description;
                $pairs = explode( '|', $description );
                if( count( $pairs ) > 1 )
                {
                    $fname = '';
                    $lname = '';
                    foreach( $pairs as $pair )
                    {
                        list($name, $value) = explode( ':=', $pair );
                        if( $name == 'fname' )
                        {
                            $fname = $value;
                        }
                        else if( $name == 'lname' )
                        {
                            $lname = $value;
                        }
                    }
                    $name = $fname . ' ' . $lname;
                    if( trim( $name ) != '' )
                    {
                        $rows[$i]['name'] = $name;
                    }
                    else
                    {
                        $rows[$i]['name'] = $customer->email;
                    }
                }
                else
                {
                    $rows[$i]['name'] = $customer->email;
                }
            }
            else
            {
                $rows[$i]['name'] = $charge->card->name;
            }
        }
        else
        {
            $rows[$i]['name'] = '--';
        }
    }
    catch (Exception $e)
    {
        // Not found. Probably deleted.
        //var_dump($e);
    }
}

if( isset( $_REQUEST[ 'f' ] ) )
{
    diglabs_export_to_file( $rows );
}

// Calculate the number of pages.
//
$tot_pages = ceil( $total_events / $count );

// Render pagination on top of the form.
//
diglabs_kriesi_pagination($page, $tot_pages, 2);
$tab = $_REQUEST['tab'];
?>

    <table class="widefat fixed">
        <thead>
        <tr>
            <th id="columnname" class="manage-column column-columnname" scope="col">Xfer Date</th>
            <th id="columnname" class="manage-column column-columnname" scope="col">Create Date</th>
            <th id="columnname" class="manage-column column-columnname" scope="col">Id</th>
            <th id="columnname" class="manage-column column-columnname" scope="col">Person</th>
            <th id="columnname" class="manage-column column-columnname" scope="col">Is Live</th>
            <th id="columnname" class="manage-column column-columnname" scope="col">Amount</th>
            <th id="columnname" class="manage-column column-columnname" scope="col">Fee</th>
            <th id="columnname" class="manage-column column-columnname" scope="col">Xfer</th>
        </tr>
        </thead>

        <tfoot>
        <tr>
            <th id="columnname" class="manage-column column-columnname" scope="col">Xfer Date</th>
            <th id="columnname" class="manage-column column-columnname" scope="col">Create Date</th>
            <th id="columnname" class="manage-column column-columnname" scope="col">Id</th>
            <th id="columnname" class="manage-column column-columnname" scope="col">Person</th>
            <th id="columnname" class="manage-column column-columnname" scope="col">Is Live</th>
            <th id="columnname" class="manage-column column-columnname" scope="col">Amount</th>
            <th id="columnname" class="manage-column column-columnname" scope="col">Fee</th>
            <th id="columnname" class="manage-column column-columnname" scope="col">Xfer</th>
        </tr>
        </tfoot>

        <tbody>
        <?php
        $row = 0;
        $baseUrl = '?page=' . DLSP_ADMIN_PAGE . "&tab=" . $tab;
        if(isset($_REQUEST['filter']))
        {
            $baseUrl .= "filter=" . $_REQUEST['filter'];
        }
        foreach($rows as $i=>$row)
        {
            $tr_class = ( $i%2 == 0 ) ? "class='alternate'" : "";
            $tdStart = "<td class='column-columnname' style='border-bottom:1px dotted #f2f2f2;'>";
            $tdEnd = "</td>";
            echo "<tr $tr_class style='border-bottom:1px dashed #f2f2f2;'>";
            echo $tdStart . $row['transferred'] . $tdEnd;
            echo $tdStart . $row['created'] . $tdEnd;
            echo $tdStart . $row['charge_id'] . $tdEnd;
            echo $tdStart . $row['name'] . $tdEnd;
            echo $tdStart . $row['is_live'] . $tdEnd;
            echo $tdStart . number_format($row['amount'], 2) . $tdEnd;
            echo $tdStart . number_format($row['fee'], 2) . $tdEnd;
            echo $tdStart . number_format($row['amount'] - $row['fee'], 2) . $tdEnd;
            echo "</tr>";
            echo "<tr $tr_class style=''>";
            echo "<td></td>";
            echo "<td colspan=7>";
            $json = json_decode( $row['description'] );
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
                echo $row['description'];
            }
            echo "</td>";
            echo "</tr>";
        }
        ?>

        </tbody>

    </table>

<?php

// Render the export link.
//
echo "<a style='float:right;' href='?page=" . DLSP_ADMIN_PAGE . "&tab=$tab&p=$page&f=1'>Export to tab separated variable file.</a>";

// Render pagination on bottom of the form
//
diglabs_kriesi_pagination($page, $tot_pages, 2);



// Function to export the results to a file.
//
function diglabs_export_to_file( $rows )
{
    ob_end_clean();

    $fileName = 'items.txt';
    header( 'Cache-Control: must-revalidate, post-check=0, pre-check=0' );
    header( 'Content-Description: File Transfer' );
    header( 'Content-type: text/plain' );
    header( 'Content-Disposition: attachment; filename=' . $fileName );
    header( 'Expires: 0' );
    header( 'Pragma: public' );

    $fh = @fopen( 'php://output', 'w' );

    $header      = array(
        "Xfer Date",
        "Create Date",
        "Id",
        "Person",
        "Is Live",
        "Amount",
        "Fee",
        "Xfer",
        "Description"
    );
    $header_line = '#' . join( "\t", $header ) . "\n";
    fwrite( $fh, $header_line );

    foreach( $rows as $row )
    {
        $line_data = array(
            $row[ 'transferred' ],
            $row[ 'created' ],
            $row[ 'charge_id' ],
            $row[ 'name' ],
            $row[ 'is_live' ],
            number_format( $row[ 'amount' ], 2 ),
            number_format( $row[ 'fee' ], 2 ),
            number_format( $row[ 'amount' ] - $row[ 'fee' ], 2 ),
            $row[ 'description' ]
        );
        $line      = join( "\t", $line_data ) . "\n";
        fwrite( $fh, $line );
    }

    fclose( $fh );
    exit();
}


?>