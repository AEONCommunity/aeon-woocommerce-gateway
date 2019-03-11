<?php foreach($errors as $error): ?>
<div class="error"><p><strong>Aeon Gateway Error</strong>: <?php echo $error; ?></p></div>
<?php endforeach; ?>

<h1>Aeon Gateway Settings</h1>

<?php if($confirm_type === 'aeon-wallet-rpc'): 
                #this could be more versatile.
                #a rewrite will come soon
                #fetch the coin value data
                $coinData = wp_remote_get('https://coincodex.com/api/coincodex/get_coin/aeon' );
                #decode the coin value data
                $usd3 = json_decode($coinData['body'], true);
                #grab balance data
                $balance = Aeon_Gateway::admin_balance_info();
                #assign balance data to variables for math
                $totalBalance = $balance['balance'];
                $unlocked = $balance['unlocked_balance'];
                #convert coin value data last price to number at two decimal places.
                #used at current usd value in dashboard
                $aeonUSDCoinCodex = number_format($usd3['last_price_usd'],2,'.','');
                #do math for locked and unlocked balances
                #both are used in dashboard
                $usdLockedBalanceValue = number_format((float)$aeonUSDCoinCodex*(float)$totalBalance,2,'.','');
                $usdUnlockedBalanceValue = number_format((float)$aeonUSDCoinCodex*(float)$unlocked,2,'.','');
                #perhaps we could move css elsewhere in the future. works here for now
                #could also be done more nicely. not optimized or mobile view.
                #$balance_info = ?>
                    <style>
                        .stats_box_aeon { 
                        padding:3px 0px 3px 3px;
                        background:#2c89a0ee;
                        width: 32%;
                        display:inline-block;
                        border: 1px solid #00000033;
                        color: #fff;
                        }
                        .stats_box_heading {
                          font-size: 16px;
                          font-weight: bold;
                          text-transform: uppercase;
                        }
                        .stats_box_info { 
                        font-size: 12px;
                        }
                        .aeon_stats_container { 
                        display: block;
                        }
                    </style>
                    <div class="aeon_stats_container">
                    <div class="stats_box_aeon">
                        <h2 class="stats_box_heading">Wallet Height</h2>
                        <span class="stats_box_info"><?php echo $balance['height']; ?></span>
                    </div>
                    <div class="stats_box_aeon">
                        <h2 class="stats_box_heading">Wallet Balance</h2>
                        <span class="stats_box_info"><?php echo $balance['balance']; ?></span>
                    </div>
                    <div class="stats_box_aeon">
                        <h2 class="stats_box_heading">Unlocked Balance</h2>
                        <span class="stats_box_info"><?php echo $balance['unlocked_balance']; ?></span>
                    </div>
                    <div class="stats_box_aeon">
                        <h2 class="stats_box_heading">AEON USD</h2>
                        <span class="stats_box_info"><?php echo '$' . $aeonUSDCoinCodex; ?></span>
                    </div>
                    <div class="stats_box_aeon">
                        <h2 class="stats_box_heading">Balance usd</h2>
                        <span class="stats_box_info"><?php echo '$' . $usdLockedBalanceValue; ?></span>
                    </div>
                    <div class="stats_box_aeon">
                        <h2 class="stats_box_heading">Unlocked Balance USD</h2>
                        <span class="stats_box_info"><?php echo '$' . $usdUnlockedBalanceValue; ?></span>
                    </div>
                </div>

<?php endif; ?>

<table class="form-table">
    <?php echo $settings_html ?>
</table>

<h4><a href="https://github.com/aeoncommunity/aeon-woocommerce-gateway" target="_blank">Learn more about using the Aeon payment gateway</a></h4>

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