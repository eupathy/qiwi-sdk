Qiwi PHP SDK
===============

PHP SDK для работы с сервером QIWI через REST протокол

# Требования

- php >=5.3.0
- php5-curl

# Установка

## Composer

    {
        "require": {
            "fintech-fab/qiwi-sdk": "dev-master"
        },
    }

# Использование

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
