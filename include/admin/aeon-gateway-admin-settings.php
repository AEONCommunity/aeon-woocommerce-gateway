<?php

defined( 'ABSPATH' ) || exit;

return array(
    'enabled' => array(
        'title' => __('Enable / Disable', 'aeon_gateway'),
        'label' => __('Enable this payment gateway', 'aeon_gateway'),
        'type' => 'checkbox',
        'default' => 'no'
    ),
    'title' => array(
        'title' => __('Title', 'aeon_gateway'),
        'type' => 'text',
        'desc_tip' => __('Payment title the customer will see during the checkout process.', 'aeon_gateway'),
        'default' => __('Aeon Gateway', 'aeon_gateway')
    ),
    'description' => array(
        'title' => __('Description', 'aeon_gateway'),
        'type' => 'textarea',
        'desc_tip' => __('Payment description the customer will see during the checkout process.', 'aeon_gateway'),
        'default' => __('Pay securely using Aeon. You will be provided payment details after checkout.', 'aeon_gateway')
    ),
    'discount' => array(
        'title' => __('Discount for using Aeon', 'aeon_gateway'),
        'desc_tip' => __('Provide a discount to your customers for making a private payment with Aeon', 'aeon_gateway'),
        'description' => __('Enter a percentage discount (i.e. 5 for 5%) or leave this empty if you do not wish to provide a discount', 'aeon_gateway'),
        'type' => __('number'),
        'default' => '0'
    ),
    'valid_time' => array(
        'title' => __('Order valid time', 'aeon_gateway'),
        'desc_tip' => __('Amount of time order is valid before expiring', 'aeon_gateway'),
        'description' => __('Enter the number of seconds that the funds must be received in after order is placed. 3600 seconds = 1 hour', 'aeon_gateway'),
        'type' => __('number'),
        'default' => '3600'
    ),
    'confirms' => array(
        'title' => __('Number of confirmations', 'aeon_gateway'),
        'desc_tip' => __('Number of confirms a transaction must have to be valid', 'aeon_gateway'),
        'description' => __('Enter the number of confirms that transactions must have. Enter 0 to zero-confim. Each confirm will take approximately four minutes', 'aeon_gateway'),
        'type' => __('number'),
        'default' => '5'
    ),
    'confirm_type' => array(
        'title' => __('Confirmation Type', 'aeon_gateway'),
        'desc_tip' => __('Select the method for confirming transactions', 'aeon_gateway'),
        'description' => __('Select the method for confirming transactions', 'aeon_gateway'),
        'type' => 'select',
        'options' => array(
            'viewkey'        => __('viewkey', 'aeon_gateway'),
            'aeon-wallet-rpc' => __('aeon-wallet-rpc', 'aeon_gateway')
        ),
        'default' => 'viewkey'
    ),
    'aeon_address' => array(
        'title' => __('Aeon Address', 'aeon_gateway'),
        'label' => __('Useful for people that have not a daemon online'),
        'type' => 'text',
        'desc_tip' => __('Aeon Wallet Address (AeonL)', 'aeon_gateway')
    ),
    'viewkey' => array(
        'title' => __('Secret Viewkey', 'aeon_gateway'),
        'label' => __('Secret Viewkey'),
        'type' => 'text',
        'desc_tip' => __('Your secret Viewkey', 'aeon_gateway')
    ),
    'daemon_host' => array(
        'title' => __('Aeon wallet RPC Host/IP', 'aeon_gateway'),
        'type' => 'text',
        'desc_tip' => __('This is the Daemon Host/IP to authorize the payment with', 'aeon_gateway'),
        'default' => '127.0.0.1',
    ),
    'daemon_port' => array(
        'title' => __('Aeon wallet RPC port', 'aeon_gateway'),
        'type' => __('number'),
        'desc_tip' => __('This is the Wallet RPC port to authorize the payment with', 'aeon_gateway'),
        'default' => '11180',
    ),
    'testnet' => array(
        'title' => __(' Testnet', 'aeon_gateway'),
        'label' => __(' Check this if you are using testnet ', 'aeon_gateway'),
        'type' => 'checkbox',
        'description' => __('Advanced usage only', 'aeon_gateway'),
        'default' => 'no'
    ),
    'onion_service' => array(
        'title' => __(' SSL warnings ', 'aeon_gateway'),
        'label' => __(' Check to Silence SSL warnings', 'aeon_gateway'),
        'type' => 'checkbox',
        'description' => __('Check this box if you are running on an Onion Service (Suppress SSL errors)', 'aeon_gateway'),
        'default' => 'no'
    ),
    'show_qr' => array(
        'title' => __('Show QR Code', 'aeon_gateway'),
        'label' => __('Show QR Code', 'aeon_gateway'),
        'type' => 'checkbox',
        'description' => __('Enable this to show a QR code after checkout with payment details.'),
        'default' => 'no'
    ),
    'use_aeon_price' => array(
        'title' => __('Show Prices in Aeon', 'aeon_gateway'),
        'label' => __('Show Prices in Aeon', 'aeon_gateway'),
        'type' => 'checkbox',
        'description' => __('Enable this to convert ALL prices on the frontend to Aeon (experimental)'),
        'default' => 'no'
    ),
    'use_aeon_price_decimals' => array(
        'title' => __('Display Decimals', 'aeon_gateway'),
        'type' => __('number'),
        'description' => __('Number of decimal places to display on frontend. Upon checkout exact price will be displayed.'),
        'default' => 12,
    ),
);
