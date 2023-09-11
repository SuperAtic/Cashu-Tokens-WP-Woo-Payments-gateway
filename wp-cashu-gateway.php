<?php

/**
 * Plugin Name: WooCommerce CASHU Gateway
 * Description: Adds CASHU as a payment gateway in WooCommerce.
 * Version: 0.2.0
 * Author: CASHU Team
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Include the main gateway class
include_once 'includes/class-wc-cashu-gateway.php';

// Add the gateway to WooCommerce
function add_cashu_gateway($methods) {
    $methods[] = 'WC_Cashu_Gateway';
    return $methods;
}
add_filter('woocommerce_payment_gateways', 'add_cashu_gateway');

// Enqueue scripts
function enqueue_cashu_scripts() {
    wp_enqueue_script('cashu-js', plugin_dir_url(__FILE__) . 'assets/js/cashu.js', array('jquery'), '1.0.0', true);
    wp_enqueue_script('cashu-gateway', plugin_dir_url(__FILE__) . 'assets/js/frontend.js', array('jquery', 'cashu-js'), '1.0.0', true);
}
add_action('wp_enqueue_scripts', 'enqueue_cashu_scripts');

// AJAX handler for token validation
function validate_cashu_token() {
    $token = sanitize_text_field($_POST['token']);

    // Use the cashu-js library to validate the token
    $isValid = CashuJS::validateToken($token);  // Replace with actual method from cashu-js

    if ($isValid) {
        wp_send_json_success();
    } else {
        wp_send_json_error(array('message' => 'Invalid token.'));
    }
}

// AJAX handler for token validation
function validate_cashu_token() {
    $token = sanitize_text_field($_POST['token']);

    // Use the cashu-js library to validate the token
    $wallet = new Wallet(); // Assuming the Wallet class is available in PHP
    $isValid = $wallet->checkSpendable($token); // This method checks if the token is spendable

    if ($isValid) {
        wp_send_json_success();
    } else {
        wp_send_json_error(array('message' => 'Invalid token.'));
    }
}

add_action('wp_ajax_validate_cashu_token', 'validate_cashu_token');
add_action('wp_ajax_nopriv_validate_cashu_token', 'validate_cashu_token');
