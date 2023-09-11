<?php
// Define a shortcode to display Cashu payment information
function cashu_payment_info_shortcode() {
    // You can generate and return the shortcode content here
    return 'This is some Cashu payment information.';
}

// Register the shortcode with WordPress
add_shortcode('cashu_info', 'cashu_payment_info_shortcode');