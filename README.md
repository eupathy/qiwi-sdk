Qiwi PHP SDK
===============

Демо-сервер: http://fintech-fab.ru/qiwi/gate/about

PHP SDK для работы с сервером QIWI через REST протокол.
Используйте этот пакет чтобы обращаться к серверу QIWI.
Подробная инструкция по использованию демо-сервера находится в разработке.
Протестировать работу можно с пакетами эмуляции сервера QIWI и интернет магазина:

- QIWI-gate: https://github.com/fintech-fab/qiwi-gate
- QIWI-shop: https://github.com/fintech-fab/qiwi-shop

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
	        'id'       => 'your-qiwi-gate-id',          //логин в системе QIWI
	        'password' => 'your-qiwi-gate-password',    //пароль в системе QIWI
	        'key'      => 'your-qiwi-gate-key',         //ключ для подписи в QIWI
	    ),
);

Gateway::setConfig($config);

```
