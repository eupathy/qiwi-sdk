Qiwi PHP SDK
===============

PHP SDK for Visa Qiwi Wallet (REST Protocol)

# Requirements

- php >=5.3.0
- php5-curl

# Installation

## Composer

    {
        "require": {
            "fintech-fab/qiwi-sdk": "dev-master"
        },
    }

# Simple usage

```PHP

use FintechFab\QiwiSdk\Curl;
use FintechFab\QiwiSdk\Gateway;


$config = array(
	'gateUrl'  => 'url-to-qiwi-gate',
    'provider' => array(
	        'id'       => 'your-qiwi-gate-id',
	        'password' => 'your-qiwi-gate-password',
	    ),
);

Gateway::setConfig($config);

```
