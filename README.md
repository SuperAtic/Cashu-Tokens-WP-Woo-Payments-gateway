# WooCommerce CASHU Gateway Plugin

![CASHU Logo](assets/plugin-logo.png)

This plugin integrates CASHU as a payment gateway into any WooCommerce-powered WordPress site. It allows users to make payments using CASHU tokens.

## Table of Contents

- [Installation](#installation)
- [Configuration](#configuration)
- [Usage](#usage)
- [Integration with Node.js](#integration-with-nodejs)
- [Credits](#credits)
- [License](#license)

## Installation

1. Download the plugin zip file.
2. Go to **WordPress Admin > Plugins > Add New**.
3. Click **Upload Plugin** and choose the downloaded zip file.
4. Activate the plugin.

## Configuration

1. Navigate to **WooCommerce > Settings > Payments**.
2. Click on **CASHU Anonymous Payments** to configure the payment gateway.
3. Enter the required details to connect to your mint.

## Usage

1. During checkout, select **CASHU Anonymous Payments** as the payment method.
2. Enter your CASHU token.
3. If the token is valid and corresponds to the cart total, you can proceed with the payment.

## Integration with Node.js

This plugin communicates with a Node.js server to interact with the \`cashu-js\` library. Ensure the Node.js server is running when the WordPress site is live. For detailed setup instructions, refer to the [Node.js server setup guide](path_to_nodejs_setup_guide.md).

## Credits

- [cashu-js library](https://github.com/cashubtc/cashu-js)
- [WooCommerce](https://woocommerce.com/)
- [WordPress](https://wordpress.org/)

## License

This project is licensed under the MIT License. See the [LICENSE](LICENSE) file for details.
