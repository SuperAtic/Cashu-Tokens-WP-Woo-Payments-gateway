<?php

class WC_Cashu_Gateway extends WC_Payment_Gateway {

    public function __construct() {
        $this->id = 'cashu';
        $this->method_title = __('CASHU Anonymous Payments', 'woocommerce');
        $this->method_description = __('Accept payments using CASHU tokens.', 'woocommerce');
        $this->supports = array('products');

        // Define user set form fields
        $this->init_form_fields();

        // Load the settings
        $this->init_settings();

        // Define user set variables
        $this->title = $this->get_option('title');

        // Save settings
        add_action('woocommerce_update_options_payment_gateways_' . $this->id, array($this, 'process_admin_options'));
    }

    public function init_form_fields() {
        $this->form_fields = array(
            'enabled' => array(
                'title' => __('Enable/Disable', 'woocommerce'),
                'type' => 'checkbox',
                'label' => __('Enable CASHU Payment', 'woocommerce'),
                'default' => 'yes'
            ),
            'title' => array(
                'title' => __('Title', 'woocommerce'),
                'type' => 'text',
                'description' => __('This controls the title which the user sees during checkout.', 'woocommerce'),
                'default' => __('CASHU Anonymous Payments', 'woocommerce'),
                'desc_tip' => true,
            ),
            // Add other fields as needed for connecting to the mint
        );
    }

    public function process_payment($order_id) {
        global $woocommerce;
        $order = new WC_Order($order_id);

        $token = $_POST['cashu_token'];

        // Validate the token and get the amount of satoshis
        $satoshis = CashuJS::getSatoshisFromToken($token);  // Replace with actual method from cashu-js

        if ($satoshis == $order->get_total()) {
            $order->payment_complete();
            $order->reduce_order_stock();
            $woocommerce->cart->empty_cart();
            return array(
                'result' => 'success',
                'redirect' => $this->get_return_url($order)
            );
        } else {
            wc_add_notice(__('Payment error: Invalid token or incorrect amount.', 'woocommerce'), 'error');
            return;
        }
    }
}


public function process_payment($order_id) {
    global $woocommerce;
    $order = new WC_Order($order_id);

    $token = $_POST['cashu_token'];

    // Use the cashu-js library to get the amount of satoshis from the token
    $wallet = new Wallet();
    $isValid = $wallet->checkSpendable($token); // This method checks if the token is spendable

    if ($isValid) {
        $satoshis = $wallet->sumProofs($token); // This method sums the proofs (tokens) to get the total amount

        if ($satoshis == $order->get_total()) {
            $order->payment_complete();
            $order->reduce_order_stock();
            $woocommerce->cart->empty_cart();
            return array(
                'result' => 'success',
                'redirect' => $this->get_return_url($order)
            );
        } else {
            wc_add_notice(__('Payment error: Invalid token or incorrect amount.', 'woocommerce'), 'error');
            return;
        }
    } else {
        wc_add_notice(__('Payment error: Invalid token.', 'woocommerce'), 'error');
        return;
    }
}

public function process_payment($order_id) {
    global $woocommerce;
    $order = new WC_Order($order_id);

    $token = $_POST['cashu_token'];

    // Call the Node.js server to get the amount of satoshis from the token
    $response = wp_remote_post('http://localhost:3000/sumProofs', [
        'body' => json_encode(['token' => $token]),
        'headers' => ['Content-Type' => 'application/json']
    ]);

    $body = wp_remote_retrieve_body($response);
    $data = json_decode($body, true);

    $satoshis = $data['satoshis'];

    if ($satoshis == $order->get_total()) {
        $order->payment_complete();
        $order->reduce_order_stock();
        $woocommerce->cart->empty_cart();
        return array(
            'result' => 'success',
            'redirect' => $this->get_return_url($order)
        );
    } else {
        wc_add_notice(__('Payment error: Invalid token or incorrect amount.', 'woocommerce'), 'error');
        return;
    }
}
