<?php foreach($errors as $error): ?>
<div class="error"><p><strong>Aeon Gateway Error</strong>: <?php echo $error; ?></p></div>
<?php endforeach; ?>

<h1>Aeon Gateway Settings</h1>

<?php if($confirm_type === 'aeon-wallet-rpc'): ?>
<div style="border:1px solid #ddd;padding:5px 10px;">
    <?php
         echo 'Wallet height: ' . $balance['height'] . '</br>';
         echo 'Your balance is: ' . $balance['balance'] . '</br>';
         echo 'Unlocked balance: ' . $balance['unlocked_balance'] . '</br>';
         ?>
</div>
<?php endif; ?>

<table class="form-table">
    <?php echo $settings_html ?>
</table>

<h4><a href="https://github.com/monero-integrations/monerowp">Learn more about using the Aeon payment gateway</a></h4>

<script>
function aeonUpdateFields() {
    var confirmType = jQuery("#woocommerce_aeon_gateway_confirm_type").val();
    if(confirmType == "aeon-wallet-rpc") {
        jQuery("#woocommerce_aeon_gateway_aeon_address").closest("tr").hide();
        jQuery("#woocommerce_aeon_gateway_viewkey").closest("tr").hide();
        jQuery("#woocommerce_aeon_gateway_daemon_host").closest("tr").show();
        jQuery("#woocommerce_aeon_gateway_daemon_port").closest("tr").show();
    } else {
        jQuery("#woocommerce_aeon_gateway_aeon_address").closest("tr").show();
        jQuery("#woocommerce_aeon_gateway_viewkey").closest("tr").show();
        jQuery("#woocommerce_aeon_gateway_daemon_host").closest("tr").hide();
        jQuery("#woocommerce_aeon_gateway_daemon_port").closest("tr").hide();
    }
    var useAeonPrices = jQuery("#woocommerce_aeon_gateway_use_aeon_price").is(":checked");
    if(useAeonPrices) {
        jQuery("#woocommerce_aeon_gateway_use_aeon_price_decimals").closest("tr").show();
    } else {
        jQuery("#woocommerce_aeon_gateway_use_aeon_price_decimals").closest("tr").hide();
    }
}
aeonUpdateFields();
jQuery("#woocommerce_aeon_gateway_confirm_type").change(aeonUpdateFields);
jQuery("#woocommerce_aeon_gateway_use_aeon_price").change(aeonUpdateFields);
</script>

<style>
#woocommerce_aeon_gateway_aeon_address,
#woocommerce_aeon_gateway_viewkey {
    width: 100%;
}
</style>