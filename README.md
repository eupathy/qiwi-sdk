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

$curl = new Curl();

// выставить счет
$gate = new Gateway($curl);
$billCreated = $gate->createBill(
	1234,		// номер заказа (счета) в вашей системе
	'+71234567890', // номер кошелька киви (моб. тел. плательщика)
	123.45,		// сумма счета
	'Комментарий!',	// комментрий к счету
	60*60*24	// на сутки
);

// проверить статус по номеру заказа (счета)
$gate = new Gateway($curl);
$statusChecked = $gate->doRequestBillStatus(1234);
if($statusChecked){
	$status = $gate->getValueBillStatus();
	switch($status){
		case 'payable': // ожидает оплаты
		case 'paid': // оплачен
		case 'canceled': // отменен
		case 'expired': // отменен, просрочен
	}
}


// отменить счет по номеру заказа (счета)
$gate = new Gateway($curl);
$billCanceled = $gate->cancelBill(1234);


// обработать callback от сервера
$gate = new Gateway($curl);
$correctCallback = $gate->doParseCallback();
if($correctCallback){
	$orderId = $gate->getCallbackOrderId();
	$amount = $gate->getCallbackAmount();
	$statusAfterCallback = $gate->getValueBillStatus();
	switch($status){
		case 'paid': // оплачен
		case 'canceled': // отменен
		case 'expired': // отменен, просрочен
	}
}

// обработка ошибок
$gate = new Gateway($curl);
$billCreated = $gate->createBill(/*...*/);
if(!$billCreated){
	$errorMessage = $gate->getError();
}

```
