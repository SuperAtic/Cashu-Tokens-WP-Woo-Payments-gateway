<?php
/**
 * Plugin Name: Cashu Woocommerce Payment Gateway
 * Plugin URI: https://VEINTIUNO.world/m/digitales/Cashu-Woo-Payment-Gateway
 * Description: Cashu is Ecash for Bitcoin, this plugin is a WordPress integration that enable ecash generated on the <a href="https://cashu.space"><code>CASHU</code></a> protocol to be used in WooCommerce as payment during the cjheckout process. Compatible with <em>Calle's certified wallets</em> like <a href="https://cashu.me" target="_blank">CashuMe</a>, <a href="https://alpha.nutstash.app/" target="_blank">NustStash</a> web wallets and <a href="https://github.com/SuperAtic/NutStash-Browser-Extension" target="_blank">browser extensions</a> and the mobile application <a href="https://github.com/cashubtc/eNuts" target="_blank">eNuts<a/>.
 * Version: 0.1.0
 * Author: SuperAtic inc.
 * Author URI: https://SuperAtic.com
 */

// Define constants for plugin paths and URLs.
define('CASHU_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('CASHU_PLUGIN_URL', plugin_dir_url(__FILE__));

// Include necessary files.
require_once CASHU_PLUGIN_DIR . 'includes/cashu-admin.php';
require_once CASHU_PLUGIN_DIR . 'includes/cashu-functions.php';
require_once CASHU_PLUGIN_DIR . 'includes/cashu-shortcodes.php';

// Enqueue plugin assets.
function cashu_enqueue_assets() {
    // Enqueue the JavaScript file
    wp_enqueue_script('cashu-scripts', CASHU_PLUGIN_URL . 'assets/js/cashu-scripts.js', array('jquery'), '1.0', true);
    
    // Enqueue the CSS file
    wp_enqueue_style('cashu-styles', CASHU_PLUGIN_URL . 'assets/css/cashu-styles.css');
}

add_action('wp_enqueue_scripts', 'cashu_enqueue_assets');

// Register activation hook
register_activation_hook(__FILE__, 'cashu_plugin_activation');

// Activation function
function cashu_plugin_activation() {
    // Check if WooCommerce is active
    if (!is_plugin_active('woocommerce/woocommerce.php')) {
        // Deactivate the plugin
        deactivate_plugins(plugin_basename(__FILE__));

        // Redirect to plugins page with a notice
        wp_redirect(admin_url('plugins.php?deactivate=true&plugin_status=all&paged=1&s='));
        exit;
    }
    
    // Add any activation tasks here
    // For example, you can create necessary database tables or initialize default settings
}

// Inside the WC_Gateway_Cashu class definition

// Process the payment
public function process_payment($order_id) {
    // Get the Cashu token submitted by the user
    $cashu_token = isset($_POST['cashu_token']) ? sanitize_text_field($_POST['cashu_token']) : '';

    // Validate the Cashu token with the Cashu API (implement this part)
    $is_valid_token = cashu_validate_token($cashu_token);

    if ($is_valid_token) {
        // Payment was successful, update order status to 'completed'
        $order = wc_get_order($order_id);
        $order->payment_complete();
        $order->add_order_note('Payment was successful.');

        // Redirect to the thank you page
        return array(
            'result' => 'success',
            'redirect' => $order->get_checkout_order_received_url(),
        );
    } else {
        // Payment failed, update order status to 'failed'
        $order = wc_get_order($order_id);
        $order->update_status('failed', 'Payment failed.');

        // Redirect to the checkout page with an error message
        wc_add_notice('Invalid Cashu token. Please try again.', 'error');
        return array(
            'result' => 'fail',
            'redirect' => wc_get_checkout_url(),
        );
    }
}

// Initialize payment gateway settings
public function init_form_fields() {
    $this->form_fields = array(
        'enabled' => array(
            'title' => 'Enable/Disable',
            'label' => 'Enable Cashu Payments',
            'type' => 'checkbox',
            'default' => 'no',
        ),
        'title' => array(
            'title' => 'Title',
            'type' => 'text',
            'description' => 'This controls the title that the user sees during checkout.',
            'default' => 'Cashu',
            'desc_tip' => true,
        ),
        'description' => array(
            'title' => 'Description',
            'type' => 'textarea',
            'description' => 'Payment method description that the customer will see on your checkout.',
            'default' => 'Pay securely using Cashu.',
        ),
        // Add more payment gateway settings fields here as needed
    );
}

// Display payment form on the checkout page
public function payment_fields() {
    // Display any form fields or instructions here
    echo '<div id="cashu_payment_form">';
    echo '<h3>' . __('Cashu Payment', 'your-text-domain') . '</h3>';
    echo '<p>' . __('Please enter your Cashu token below:', 'your-text-domain') . '</p>';
    echo '<input type="text" id="cashu-token" name="cashu_token" required>';
    echo '</div>';
}

// Add the Cashu payment gateway to WooCommerce
function add_cashu_gateway($methods) {
    $methods[] = 'WC_Gateway_Cashu';
    return $methods;
}

add_filter('woocommerce_payment_gateways', 'add_cashu_gateway');
