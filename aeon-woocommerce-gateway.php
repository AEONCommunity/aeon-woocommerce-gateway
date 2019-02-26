<?php
/*
Plugin Name: Aeon Woocommerce Gateway
Plugin URI: https://github.com/monero-integrations/monerowp
Description: Extends WooCommerce by adding a Aeon Gateway
Version: 3.0.0
Tested up to: 4.9.8
Author: mosu-forge, SerHack
Author URI: https://monerointegrations.com/
Ported by: 420coupe
*/
// This code isn't for Dark Net Markets, please report them to Authority!

defined( 'ABSPATH' ) || exit;

// Constants, you can edit these if you fork this repo
define('AEON_GATEWAY_MAINNET_EXPLORER_URL', 'http://aeonblocks.com');
define('AEON_GATEWAY_TESTNET_EXPLORER_URL', 'http://testnet.aeon.lol');
define('AEON_GATEWAY_ADDRESS_PREFIX', 0xB2);
define('AEON_GATEWAY_ADDRESS_PREFIX_INTEGRATED', 0x2733);
define('AEON_GATEWAY_ATOMIC_UNITS', 12);
define('AEON_GATEWAY_ATOMIC_UNIT_THRESHOLD', 10); // Amount under in atomic units payment is valid
define('AEON_GATEWAY_DIFFICULTY_TARGET', 240);

// Do not edit these constants
define('AEON_GATEWAY_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('AEON_GATEWAY_PLUGIN_URL', plugin_dir_url(__FILE__));
define('AEON_GATEWAY_ATOMIC_UNITS_POW', pow(10, AEON_GATEWAY_ATOMIC_UNITS));
define('AEON_GATEWAY_ATOMIC_UNITS_SPRINTF', '%.'.AEON_GATEWAY_ATOMIC_UNITS.'f');

// Include our Gateway Class and register Payment Gateway with WooCommerce
add_action('plugins_loaded', 'aeon_init', 1);
function aeon_init() {

    // If the class doesn't exist (== WooCommerce isn't installed), return NULL
    if (!class_exists('WC_Payment_Gateway')) return;

    // If we made it this far, then include our Gateway Class
    require_once('include/class-aeon-gateway.php');

    // Create a new instance of the gateway so we have static variables set up
    new Aeon_Gateway($add_action=false);

    // Include our Admin interface class
    require_once('include/admin/class-aeon-admin-interface.php');

    add_filter('woocommerce_payment_gateways', 'aeon_gateway');
    function aeon_gateway($methods) {
        $methods[] = 'Aeon_Gateway';
        return $methods;
    }

    add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'aeon_payment');
    function aeon_payment($links) {
        $plugin_links = array(
            '<a href="'.admin_url('admin.php?page=aeon_gateway_settings').'">'.__('Settings', 'aeon_gateway').'</a>'
        );
        return array_merge($plugin_links, $links);
    }

    add_filter('cron_schedules', 'aeon_cron_add_one_minute');
    function aeon_cron_add_one_minute($schedules) {
        $schedules['one_minute'] = array(
            'interval' => 60,
            'display' => __('Once every minute', 'aeon_gateway')
        );
        return $schedules;
    }

    add_action('wp', 'aeon_activate_cron');
    function aeon_activate_cron() {
        if(!wp_next_scheduled('aeon_update_event')) {
            wp_schedule_event(time(), 'one_minute', 'aeon_update_event');
        }
    }

    add_action('aeon_update_event', 'aeon_update_event');
    function aeon_update_event() {
        Aeon_Gateway::do_update_event();
    }

    add_action('woocommerce_thankyou_'.Aeon_Gateway::get_id(), 'aeon_order_confirm_page');
    add_action('woocommerce_order_details_after_order_table', 'aeon_order_page');
    add_action('woocommerce_email_after_order_table', 'aeon_order_email');

    function aeon_order_confirm_page($order_id) {
        Aeon_Gateway::customer_order_page($order_id);
    }
    function aeon_order_page($order) {
        if(!is_wc_endpoint_url('order-received'))
            Aeon_Gateway::customer_order_page($order);
    }
    function aeon_order_email($order) {
        Aeon_Gateway::customer_order_email($order);
    }

    add_action('wc_ajax_aeon_gateway_payment_details', 'aeon_get_payment_details_ajax');
    function aeon_get_payment_details_ajax() {
        Aeon_Gateway::get_payment_details_ajax();
    }

    add_filter('woocommerce_currencies', 'aeon_add_currency');
    function aeon_add_currency($currencies) {
        $currencies['Aeon'] = __('Aeon', 'aeon_gateway');
        return $currencies;
    }

    add_filter('woocommerce_currency_symbol', 'aeon_add_currency_symbol', 10, 2);
    function aeon_add_currency_symbol($currency_symbol, $currency) {
        switch ($currency) {
        case 'Aeon':
            $currency_symbol = 'AEON';
            break;
        }
        return $currency_symbol;
    }

    if(Aeon_Gateway::use_aeon_price()) {

        // This filter will replace all prices with amount in Aeon (live rates)
        add_filter('wc_price', 'aeon_live_price_format', 10, 3);
        function aeon_live_price_format($price_html, $price_float, $args) {
            if(!isset($args['currency']) || !$args['currency']) {
                global $woocommerce;
                $currency = strtoupper(get_woocommerce_currency());
            } else {
                $currency = strtoupper($args['currency']);
            }
            return Aeon_Gateway::convert_wc_price($price_float, $currency);
        }

        // These filters will replace the live rate with the exchange rate locked in for the order
        // We must be careful to hit all the hooks for price displays associated with an order,
        // else the exchange rate can change dynamically (which it should for an order)
        add_filter('woocommerce_order_formatted_line_subtotal', 'aeon_order_item_price_format', 10, 3);
        function aeon_order_item_price_format($price_html, $item, $order) {
            return Aeon_Gateway::convert_wc_price_order($price_html, $order);
        }

        add_filter('woocommerce_get_formatted_order_total', 'aeon_order_total_price_format', 10, 2);
        function aeon_order_total_price_format($price_html, $order) {
            return Aeon_Gateway::convert_wc_price_order($price_html, $order);
        }

        add_filter('woocommerce_get_order_item_totals', 'aeon_order_totals_price_format', 10, 3);
        function aeon_order_totals_price_format($total_rows, $order, $tax_display) {
            foreach($total_rows as &$row) {
                $price_html = $row['value'];
                $row['value'] = Aeon_Gateway::convert_wc_price_order($price_html, $order);
            }
            return $total_rows;
        }

    }

    add_action('wp_enqueue_scripts', 'aeon_enqueue_scripts');
    function aeon_enqueue_scripts() {
        if(Aeon_Gateway::use_aeon_price())
            wp_dequeue_script('wc-cart-fragments');
        if(Aeon_Gateway::use_qr_code())
            wp_enqueue_script('aeon-qr-code', AEON_GATEWAY_PLUGIN_URL.'assets/js/qrcode.min.js');

        wp_enqueue_script('aeon-clipboard-js', AEON_GATEWAY_PLUGIN_URL.'assets/js/clipboard.min.js');
        wp_enqueue_script('aeon-gateway', AEON_GATEWAY_PLUGIN_URL.'assets/js/aeon-gateway-order-page.js');
        wp_enqueue_style('aeon-gateway', AEON_GATEWAY_PLUGIN_URL.'assets/css/aeon-gateway-order-page.css');
    }

    // [aeon-price currency="USD"]
    // currency: BTC, GBP, etc
    // if no none, then default store currency
    function aeon_price_func( $atts ) {
        global  $woocommerce;
        $a = shortcode_atts( array(
            'currency' => get_woocommerce_currency()
        ), $atts );

        $currency = strtoupper($a['currency']);
        $rate = Aeon_Gateway::get_live_rate($currency);
        if($currency == 'BTC')
            $rate_formatted = sprintf('%.8f', $rate / 1e8);
        else
            $rate_formatted = sprintf('%.5f', $rate / 1e8);

        return "<span class=\"aeon-price\">1 AEON = $rate_formatted $currency</span>";
    }
    add_shortcode('aeon-price', 'aeon_price_func');


    // [aeon-accepted-here]
    function aeon_accepted_func() {
        return '<img src="'.AEON_GATEWAY_PLUGIN_URL.'assets/images/aeon-accepted-here.png" />';
    }
    add_shortcode('aeon-accepted-here', 'aeon_accepted_func');

}

register_deactivation_hook(__FILE__, 'aeon_deactivate');
function aeon_deactivate() {
    $timestamp = wp_next_scheduled('aeon_update_event');
    wp_unschedule_event($timestamp, 'aeon_update_event');
}

register_activation_hook(__FILE__, 'aeon_install');
function aeon_install() {
    global $wpdb;
    require_once( ABSPATH . '/wp-admin/includes/upgrade.php' );
    $charset_collate = $wpdb->get_charset_collate();

    $table_name = $wpdb->prefix . "aeon_gateway_quotes";
    if($wpdb->get_var("show tables like '$table_name'") != $table_name) {
        $sql = "CREATE TABLE $table_name (
               order_id BIGINT(20) UNSIGNED NOT NULL,
               payment_id VARCHAR(94) DEFAULT '' NOT NULL,
               currency VARCHAR(6) DEFAULT '' NOT NULL,
               rate BIGINT UNSIGNED DEFAULT 0 NOT NULL,
               amount BIGINT UNSIGNED DEFAULT 0 NOT NULL,
               paid TINYINT NOT NULL DEFAULT 0,
               confirmed TINYINT NOT NULL DEFAULT 0,
               pending TINYINT NOT NULL DEFAULT 1,
               created TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
               PRIMARY KEY (order_id)
               ) $charset_collate;";
        dbDelta($sql);
    }

    $table_name = $wpdb->prefix . "aeon_gateway_quotes_txids";
    if($wpdb->get_var("show tables like '$table_name'") != $table_name) {
        $sql = "CREATE TABLE $table_name (
               id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
               payment_id VARCHAR(94) DEFAULT '' NOT NULL,
               txid VARCHAR(64) DEFAULT '' NOT NULL,
               amount BIGINT UNSIGNED DEFAULT 0 NOT NULL,
               height MEDIUMINT UNSIGNED NOT NULL DEFAULT 0,
               PRIMARY KEY (id),
               UNIQUE KEY (payment_id, txid, amount)
               ) $charset_collate;";
        dbDelta($sql);
    }

    $table_name = $wpdb->prefix . "aeon_gateway_live_rates";
    if($wpdb->get_var("show tables like '$table_name'") != $table_name) {
        $sql = "CREATE TABLE $table_name (
               currency VARCHAR(6) DEFAULT '' NOT NULL,
               rate BIGINT UNSIGNED DEFAULT 0 NOT NULL,
               updated TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
               PRIMARY KEY (currency)
               ) $charset_collate;";
        dbDelta($sql);
    }
}
