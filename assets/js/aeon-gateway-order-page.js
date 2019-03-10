/*
 * Copyright (c) 2018, Ryo Currency Project
 * Copyright (c) 2019, Nonsensical-Debauchery-Inc
*/
function aeon_showNotification(message, type='success') {
    var toast = jQuery('<div class="' + type + '"><span>' + message + '</span></div>');
    jQuery('#aeon_toast').append(toast);
    toast.animate({ "right": "12px" }, "fast");
    setInterval(function() {
        toast.animate({ "right": "-400px" }, "fast", function() {
            toast.remove();
        });
    }, 2500)
}
function aeon_showQR(show=true) {
    jQuery('#aeon_qr_code_container').toggle(show);
}
function aeon_fetchDetails() {
    var data = {
        '_': jQuery.now(),
        'order_id': aeon_details.order_id
    };
    jQuery.get(aeon_ajax_url, data, function(response) {
        if (typeof response.error !== 'undefined') {
            console.log(response.error);
        } else {
            aeon_details = response;
            aeon_updateDetails();
        }
    });
}

function aeon_updateDetails() {

    var details = aeon_details;

    jQuery('#aeon_payment_messages').children().hide();
    switch(details.status) {
        case 'unpaid':
            jQuery('.aeon_payment_unpaid').show();
            jQuery('.aeon_payment_expire_time').html(details.order_expires);
            break;
        case 'partial':
            jQuery('.aeon_payment_partial').show();
            jQuery('.aeon_payment_expire_time').html(details.order_expires);
            break;
        case 'paid':
            jQuery('.aeon_payment_paid').show();
            jQuery('.aeon_confirm_time').html(details.time_to_confirm);
            jQuery('.button-row button').prop("disabled",true);
            break;
        case 'confirmed':
            jQuery('.aeon_payment_confirmed').show();
            jQuery('.button-row button').prop("disabled",true);
            break;
        case 'expired':
            jQuery('.aeon_payment_expired').show();
            jQuery('.button-row button').prop("disabled",true);
            break;
        case 'expired_partial':
            jQuery('.aeon_payment_expired_partial').show();
            jQuery('.button-row button').prop("disabled",true);
            break;
    }

    jQuery('#aeon_exchange_rate').html('1 AEON = '+details.rate_formatted+' '+details.currency);
    jQuery('#aeon_total_amount').html(details.amount_total_formatted);
    jQuery('#aeon_total_paid').html(details.amount_paid_formatted);
    jQuery('#aeon_total_due').html(details.amount_due_formatted);

    jQuery('#aeon_integrated_address').html(details.integrated_address);

    if(aeon_show_qr) {
        var qr = jQuery('#aeon_qr_code').html('');
        new QRCode(qr.get(0), details.qrcode_uri);
    }

    if(details.txs.length) {
        jQuery('#aeon_tx_table').show();
        jQuery('#aeon_tx_none').hide();
        jQuery('#aeon_tx_table tbody').html('');
        for(var i=0; i < details.txs.length; i++) {
            var tx = details.txs[i];
            var height = tx.height == 0 ? 'N/A' : tx.height;
            var row = ''+
                '<tr>'+
                '<td style="word-break: break-all">'+
                '<a href="'+aeon_explorer_url+'/tx/'+tx.txid+'" target="_blank">'+tx.txid+'</a>'+
                '</td>'+
                '<td>'+height+'</td>'+
                '<td>'+tx.amount_formatted+' Aeon</td>'+
                '</tr>';

            jQuery('#aeon_tx_table tbody').append(row);
        }
    } else {
        jQuery('#aeon_tx_table').hide();
        jQuery('#aeon_tx_none').show();
    }

    // Show state change notifications
    var new_txs = details.txs;
    var old_txs = aeon_order_state.txs;
    if(new_txs.length != old_txs.length) {
        for(var i = 0; i < new_txs.length; i++) {
            var is_new_tx = true;
            for(var j = 0; j < old_txs.length; j++) {
                if(new_txs[i].txid == old_txs[j].txid && new_txs[i].amount == old_txs[j].amount) {
                    is_new_tx = false;
                    break;
                }
            }
            if(is_new_tx) {
                aeon_showNotification('Transaction received for '+new_txs[i].amount_formatted+' Aeon');
            }
        }
    }

    if(details.status != aeon_order_state.status) {
        switch(details.status) {
            case 'paid':
                aeon_showNotification('Your order has been paid in full');
                break;
            case 'confirmed':
                aeon_showNotification('Your order has been confirmed');
                break;
            case 'expired':
            case 'expired_partial':
                aeon_showNotification('Your order has expired', 'error');
                break;
        }
    }

    aeon_order_state = {
        status: aeon_details.status,
        txs: aeon_details.txs
    };

}
jQuery(document).ready(function($) {
    if (typeof aeon_details !== 'undefined') {
        aeon_order_state = {
            status: aeon_details.status,
            txs: aeon_details.txs
        };
        setInterval(aeon_fetchDetails, 30000);
        aeon_updateDetails();
        new ClipboardJS('.clipboard').on('success', function(e) {
            e.clearSelection();
            if(e.trigger.disabled) return;
            switch(e.trigger.getAttribute('data-clipboard-target')) {
                case '#aeon_integrated_address':
                    aeon_showNotification('Copied destination address!');
                    break;
                case '#aeon_total_due':
                    aeon_showNotification('Copied total amount due!');
                    break;
            }
            e.clearSelection();
        });
    }
});