<table class="striped" style="width:100%" cellspacing="0" cellpadding="5">
    <tr>
        <td>Exchange rate</td>
        <td>1 AEON = <?php echo $details['rate_formatted'].' '.$details['currency']; ?></td>
    </tr>
    <tr>
        <td>Total amount</td>
        <td><?php echo $details['amount_total_formatted']; ?> AEON</td>
    </tr>
    <tr>
        <td>Total paid</td>
        <td><?php echo $details['amount_paid_formatted']; ?> AEON</td>
    </tr>
    <tr>
        <td>Total due</td>
        <td><?php echo $details['amount_due_formatted']; ?> AEON</td>
    </tr>
    <tr>
        <td>Order age</td>
        <td><?php echo Aeon_Gateway::format_seconds_to_time($details['order_age']) ?> ago</td>
    </tr>
    <tr>
        <td>Order exipires</td>
        <td>
            <?php echo $details['order_expires'] ?>
        </td>
    </tr>
    <tr>
        <td>Status</td>
        <td>
            <?php
                 switch($details['status']) {
                 case 'confirmed':
                     echo '<span style="color:#006400">Confirmed</span>';
                     break;
                 case 'paid':
                     echo '<span style="color:#006400">Paid, waiting confirmation</span>';
                     break;
                 case 'partial':
                     echo '<span style="color:#ffae42">Partial payment made</span>';
                     break;
                 case 'unpaid':
                     echo '<span style="color:#ffae42">Pending payment</span>';
                     break;
                 case 'expired_partial':
                     echo '<span style="color:#dc143c">Expired, partial payment made</span>';
                     break;
                 case 'expired':
                     echo '<span style="color:#dc143c">Expired</span>';
                     break;
                 }
                 ?>
        </td>
    </tr>
    <tr>
        <td>Subaddress</td>
        <td><?php echo $details['payment_id'] ?></td>
    </tr>
</table>

<?php if(count($details['txs'])): ?>
<table class="striped" style="width:100%" cellspacing="0" cellpadding="5">
    <tr>
        <td>Transactions</td>
        <td>Height</td>
        <td>Amount</td>
    </tr>
    <?php foreach($details['txs'] as $tx): ?>
    <tr>
        <td>
            <a href="<?php echo AEON_GATEWAY_EXPLORER_URL.'/tx/'.$tx['txid']; ?>" target="_blank"><?php echo $tx['txid']; ?></a>
        </td>
        <td><?php echo $tx['height']; ?></td>
        <td><?php echo sprintf(AEON_GATEWAY_ATOMIC_UNITS_SPRINTF, $tx['amount'] / AEON_GATEWAY_ATOMIC_UNITS_POW); ?> AEON</td>
    </tr>
    <?php endforeach; ?>
</table>
<?php endif; ?>
