<?php

namespace FintechFab\QiwiSdk\Test;

use Mockery;
use FintechFab\QiwiSdk\Curl;
use FintechFab\QiwiSdk\Gateway;

class ConnectorTest extends  \PHPUnit_Framework_TestCase
{

	/**
	 * @var Mockery\MockInterface|Curl
	 */
	private $mock;

	public function setUp()
	{
		parent::setUp();
		$this->mock = Mockery::mock('FintechFab\QiwiSdk\Curl');
	}


	public function testCreateBill()
	{

		$connector = new Gateway($this->mock);

		$bill = array(
			'user'     => 'tel:+7910000',
			'amount'   => 123.45,
			'ccy'      => 'RUB',
			'comment'  => 'SomeComment',
			'lifetime' => null,
			'prv_name' => Gateway::getConfig('provider.name'),
		);

		$args = array(
			123, 'PUT', $bill
		);

		$this->mock
			->shouldReceive('request')
			->withArgs($args)
			->andReturn((object)array(
				'response' => (object)array(
						'result_code' => 0,
						'bill'        => (object)array(
								'bill_id' => 123,
							),
					)
			));

		$isSuccess = $connector->createBill(123, '+7910000', 123.45, 'SomeComment');
		$this->assertTrue($isSuccess, $connector->getError());
	}

	public function testCreateBillFailFormat()
	{

		$connector = new Gateway($this->mock);

		$bill = array(
			'user'     => 'tel:+',
			'amount'   => 123.45,
			'ccy'      => 'RUB',
			'comment'  => null,
			'lifetime' => null,
			'prv_name' => Gateway::getConfig('provider.name'),
		);

		$args = array(
			123, 'PUT', $bill
		);

		$this->mock
			->shouldReceive('request')
			->withArgs($args)
			->andReturn((object)array(
				'response' => (object)array(
						'result_code' => 5,
					)
			));

		$isSuccess = $connector->createBill(123, '+', 123.45);
		$this->assertFalse($isSuccess);
		$this->assertEquals('Неверный формат параметров запроса', $connector->getError());
	}

	public function testCreateBillFailSum()
	{

		$connector = new Gateway($this->mock);

		$isSuccess = $connector->createBill(123, '+', 0);
		$this->assertFalse($isSuccess);
		$this->assertEquals('Сумма слишком мала', $connector->getError());
	}

	public function testGetBillStatus()
	{

		$connector = new Gateway($this->mock);

		$args = array(123);

		$this->mock
			->shouldReceive('request')
			->withArgs($args)
			->andReturn((object)array(
				'response' => (object)array(
						'result_code' => 0,
						'bill'        => (object)array(
								'status' => 'waiting',
							),
					)
			));

		$isSuccess = $connector->doRequestBillStatus(123);
		$this->assertTrue($isSuccess);
		$this->assertEquals('payable', $connector->getValueBillStatus());
	}

	public function testGetBillStatusFail()
	{

		$connector = new Gateway($this->mock);

		$args = array(123);

		$this->mock
			->shouldReceive('request')
			->withArgs($args)
			->andReturn((object)array(
				'response' => (object)array(
						'result_code' => 210,
						'bill'        => (object)array(
								'status' => 'waiting',
							),
					)
			));

		$isSuccess = $connector->doRequestBillStatus(123);
		$this->assertFalse($isSuccess);
		$this->assertEquals('Счет не найден', $connector->getError());
	}

	public function testCancelBill()
	{

		$connector = new Gateway($this->mock);

		$reject = array('status' => 'rejected');

		$args = array(
			123, 'PATCH', $reject
		);

		$this->mock
			->shouldReceive('request')
			->withArgs($args)
			->andReturn((object)array(
				'response' => (object)array(
						'result_code' => 0,
						'bill'        => (object)array(
								'bill_id' => 123,
							),
					)
			));

		$isSuccess = $connector->cancelBill(123);
		$this->assertTrue($isSuccess);
	}

	public function testCancelBillFail()
	{

		$connector = new Gateway($this->mock);

		$reject = array('status' => 'rejected');

		$args = array(
			123, 'PATCH', $reject
		);

		$this->mock
			->shouldReceive('request')
			->withArgs($args)
			->andReturn((object)array(
				'response' => (object)array(
						'result_code' => 150,
					)
			));

		$isSuccess = $connector->cancelBill(123);
		$this->assertFalse($isSuccess);
		$this->assertEquals('Ошибка авторизации', $connector->getError());
	}

	public function testPayReturn()
	{

		$connector = new Gateway($this->mock);

		$amount = array('amount' => 23);

		$args = array(
			123, 'PUT', $amount, 1
		);

		$this->mock
			->shouldReceive('request')
			->withArgs($args)
			->andReturn((object)array(
				'response' => (object)array(
						'result_code' => 0,
					)
			));

		$isSuccess = $connector->payReturn(123, 1, 23);
		$this->assertTrue($isSuccess);
	}

	public function testPayReturnFail()
	{

		$connector = new Gateway($this->mock);

		$amount = array('amount' => 23);

		$args = array(
			123, 'PUT', $amount, 1
		);

		$this->mock
			->shouldReceive('request')
			->withArgs($args)
			->andReturn((object)array(
				'response' => (object)array(
						'result_code' => 215,
					)
			));

		$isSuccess = $connector->payReturn(123, 1, 23);
		$this->assertFalse($isSuccess);
		$this->assertEquals('Счет с таким bill_id уже существует', $connector->getError());
	}

	public function testPayReturnFailSum()
	{

		$connector = new Gateway($this->mock);

		$isSuccess = $connector->payReturn(123, 1, 0);
		$this->assertFalse($isSuccess);
		$this->assertEquals('Сумма слишком мала', $connector->getError());
	}

	public function testGetPayReturnStatus()
	{

		$connector = new Gateway($this->mock);

		$args = array(
			123, 'GET', null, 1
		);

		$this->mock
			->shouldReceive('request')
			->withArgs($args)
			->andReturn((object)array(
				'response' => (object)array(
						'result_code' => 0,
						'refund'      => (object)array(
								'status' => 'processing',
							),
					)
			));

		$isSuccess = $connector->doRequestReturnStatus(123, 1);
		$this->assertTrue($isSuccess);
		$this->assertEquals('onReturn', $connector->getValuePayReturnStatus());
	}

	public function testGetPayReturnStatusFail()
	{

		$connector = new Gateway($this->mock);

		$args = array(
			123, 'GET', null, 1
		);

		$this->mock
			->shouldReceive('request')
			->withArgs($args)
			->andReturn((object)array(
				'response' => (object)array(
						'result_code' => 210,
					)
			));

		$isSuccess = $connector->doRequestReturnStatus(123, 1);
		$this->assertFalse($isSuccess);
		$this->assertEquals('Счет не найден', $connector->getError());
	}

	/**
	 * @dataProvider callbacks
	 */
	public function testCallback($post, $error, $xml, $message = null)
	{

		$connector = new Gateway($this->mock);

		$result = $connector->doParseCallback($post);

		if ($error) {
			$this->assertFalse($result, print_r($post, true));

		} else {

			$this->assertTrue($result, print_r($post, true));
			$this->assertEquals($post['bill_id'], $connector->getCallbackOrderId());
			$this->assertEquals($post['amount'], $connector->getCallbackAmount());
			$this->assertEquals($post['status'], $connector->getValueBillStatus());
			$this->assertEquals($message, $connector->getError());
		}

		$this->assertEquals($xml, $connector->getCallbackResponse());

	}

	/**
	 * @return array
	 *
	 * @dataProvider
	 */
	public static function callbacks()
	{
		return array(

			__LINE__ => array(
				'post'  => array(
					'bill_id'  => 12345,
					'status'   => 'paid',
					'error'    => 0,
					'amount'   => 123.45,
					'user'     => 'tel:+79000000000',
					'prv_name' => 'Provider Name',
					'ccy'      => 'RUB',
					'comment'  => 'Comment',
					'command'  => 'bill',
				),
				'error' => false,
				'xml'   => '<?xml version="1.0"?><result><result_code>0</result_code></result>',
			),

			__LINE__ => array(
				'post'  => array(
					'bill_id' => 12345,
					'status'  => 'paid',
					'error'   => 0,
					'amount'  => 123.45,
					'user'    => 'tel:+79000000000',
					'ccy'     => 'RUB',
					'command' => 'bill',
				),
				'error' => false,
				'xml'   => '<?xml version="1.0"?><result><result_code>0</result_code></result>',
			),

			__LINE__ => array(
				'post'  => array(
					'bill_id'  => 12345,
					'status'   => 'paid',
					'error'    => 0,
					'amount'   => 123.45,
					'prv_name' => 'Provider Name',
					'ccy'      => 'RUB',
					'comment'  => 'Comment',
					'command'  => 'bill',
				),
				'error' => true,
				'xml'   => '<?xml version="1.0"?><result><result_code>5</result_code></result>',
			),

			__LINE__ => array(
				'post'    => array(
					'bill_id'  => 12345,
					'status'   => 'paid',
					'error'    => 300,
					'amount'   => 123.45,
					'user'     => 'tel:+79000000000',
					'prv_name' => 'Provider Name',
					'ccy'      => 'RUB',
					'comment'  => 'Comment',
					'command'  => 'bill',
				),
				'error'   => false,
				'xml'     => '<?xml version="1.0"?><result><result_code>0</result_code></result>',
				'message' => 'Неизвестная ошибка',
			),

		);
	}

}