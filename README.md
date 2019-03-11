# Aeon Gateway for WooCommerce

## Features

* Payment validation done through either `aeon-wallet-rpc` or the [aeonblocks.com blockchain explorer](https://aeonblocks.com/).
* Validates payments with `cron`, so does not require users to stay on the order confirmation page for their order to validate.
* Order status updates are done through AJAX instead of Javascript page reloads.
* Customers can pay with multiple transactions and are notified as soon as transactions hit the mempool.
* Configurable block confirmations, from `0` for zero confirm to `60` for high ticket purchases.
* Live price updates every minute; total amount due is locked in after the order is placed for a configurable amount of time (default 60 minutes) so the price does not change after order has been made.
* Hooks into emails, order confirmation page, customer order history page, and admin order details page.
* View all payments received to your wallet with links to the blockchain explorer and associated orders.
* Optionally display all prices on your store in terms of Aeon.
* Shortcodes! Display exchange rates in numerous currencies.

## Requirements

* Aeon wallet to receive payments - [GUI](https://github.com/aeonix/aeon-gui/releases) - [CLI](https://github.com/aeonix/aeon/releases) - [Paper](https://moneroaddress.org/)
* [BCMath](http://php.net/manual/en/book.bc.php) - A PHP extension used for arbitrary precision maths

## Installing the plugin

* Download the plugin from the [releases page](https://github.com/aeoncommunity/aeon-woocommerce-gateway/releases) or clone with `git clone https://github.com/aeoncommunity/aeon-woocommerce-gateway`
* Unzip or place the `aeon-woocommerce-gateway` folder in the `wp-content/plugins` directory.
* Activate "Aeon Woocommerce Gateway" in your WordPress admin dashboard.
* It is highly recommended that you use native cronjobs instead of WordPress's "Poor Man's Cron" by adding `define('DISABLE_WP_CRON', true);` into your `wp-config.php` file and adding `* * * * * wget -q -O - https://yourstore.com/wp-cron.php?doing_wp_cron >/dev/null 2>&1` to your crontab.

## Option 1: Use your wallet address and viewkey

This is the easiest way to start accepting Aeon on your website. You'll need:

* Your Aeon wallet address starting with `Wm`
* Your wallet's secret viewkey

Then simply select the `viewkey` option in the settings page and paste your address and viewkey. You're all set!

Note on privacy using the explorer method: when you validate transactions with your private viewkey, your viewkey is sent to (but not stored on) aeonblocks.com over HTTPS. This could potentially allow an attacker to see your incoming, but not outgoing, transactions. Even if this were to happen, your funds would still be safe and it would be impossible for somebody to steal your money. For maximum privacy, always use your own `aeon-wallet-rpc` instance.

## Option 2: Using `aeon-wallet-rpc`

The most secure way to accept Aeon on your website. You will need:

* Root or sudo access to your webserver
* Latest [Aeon-currency binaries](https://github.com/aeonix/aeon/releases)

After downloading (or compiling) the Aeon binaries on your server, install the [systemd unit files](https://github.com/aeoncommunity/aeon-woocommerce-gateway/assets/systemd-unit-files) or run `aeond` and `aeon-wallet-rpc` with `screen` or `tmux`. You can skip running `aeond` by using a remote node with `aeon-wallet-rpc` by adding `--daemon-address api.aeonminingpool.com:11181` to the `aeon-wallet-rpc.service` file.

Note on security: using this option, while the most secure, requires you to run the Aeon wallet RPC program on your server. The best practice for this is to use a view-only wallet as you only need to be able to view transactions and not send. If you choose to run the wallet as normal and not in view-only mode, you put your funds at risk of being hacked via open RPC port/IP with no password. 

## Configuration

* `Enable / Disable` - Turn on or off Aeon gateway. (Default: Disable)
* `Title` - Name of the payment gateway as displayed to the customer. (Default: Aeon Gateway)
* `Discount for using Aeon` - Percentage discount applied to orders for paying with Aeon. Can also be negative to apply a surcharge. (Default: 0)
* `Order valid time` - Number of seconds after order is placed that the transaction must be seen in the mempool. (Default: 3600 [1 hour])
* `Number of confirmations` - Number of confirmations the transaction must recieve before the order is marked as complete. Use `0` for nearly instant confirmation. (Default: 5)
* `Confirmation Type` - Confirm transactions with either your viewkey (via explorer), or by using `aeon-wallet-rpc`. (Default: viewkey)
* `Aeon Address` (if confirmation type is viewkey) - Your public Aeon address starting with Wm. (No default)
* `Secret Viewkey` (if confirmation type is viewkey) - Your *private* viewkey (No default)
* `Aeon wallet RPC Host/IP` (if confirmation type is `aeon-wallet-rpc`) - IP address where the wallet rpc is running. It is highly discouraged to run the wallet anywhere other than the local server! (Default: 127.0.0.1)
* `Aeon wallet RPC port` (if confirmation type is `aeon-wallet-rpc`) - Port the wallet rpc is bound to with the `--rpc-bind-port` argument. (Default 11182)
* `Testnet` - Check this to change the blockchain explorer links to the testnet explorer. (Default: unchecked)
* `SSL warnings` - Check this to silence SSL warnings. (Default: unchecked)
* `Show QR Code` - Show payment QR codes. (Default: unchecked)
* `Show Prices in Aeon` - Convert all prices on the frontend to Aeon. Experimental feature, only use if you do not accept any other payment option. (Default: unchecked)
* `Display Decimals` (if show prices in Aeon is enabled) - Number of decimals to round prices to on the frontend. The final order amount will not be rounded and will be displayed down to the nanoMonero. (Default: 12)

## Shortcodes

This plugin makes available two shortcodes that you can use in your theme.

#### Live price shortcode

This will display the price of Aeon in the selected currency. If no currency is provided, the store's default currency will be used.

```
[aeon-price]
[aeon-price currency="BTC"]
[aeon-price currency="USD"]
[aeon-price currency="CAD"]
[aeon-price currency="EUR"]
[aeon-price currency="GBP"]
```
Will display:
```
1 AEON = 0.2833 USD
1 AEON = 0.0000743 BTC
1 AEON = 0.2833 USD
1 AEON = 0.3737 CAD
1 AEON = 0.2488 EUR
1 AEON = 0.2139 GBP
```


#### Aeon accepted here badge

This will display a badge showing that you accept Aeon-currency.

`[aeon-accepted-here]`

![Aeon Accepted Here](/assets/images/aeon-accepted-here.png?raw=true "Aeon Accepted Here")

## Donations

AEON Community: WmtyvfjhjBVRgYSuQRZs2XgpDS2qhXma4aJLsXnZx6WA75J5KixvDi9F4wLh7h6Pg9QXnTrU3nFwJczDrmFRBeUg1kxyHvfdu

We are forked from Monero-Integrations. If you wish to donate to them as well for making this plug-in possible, visit https://github.com/monero-integrations/monerowp
