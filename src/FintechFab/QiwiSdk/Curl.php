<?php
namespace FintechFab\QiwiSdk;


use stdClass;

class Curl
{
	private $url;
	public $curlError;

	/**
	 * Определяет URL для curl запроса
	 *
	 * @param $orderId
	 * @param $payReturnId
	 *
	 */
	private function  setUrl($orderId, $payReturnId = null)
	{
		$this->url = Gateway::getConfig('gateUrl') . Gateway::getConfig('provider.id')
			. '/bills/' . $orderId;
		if ($payReturnId != null) {
			$this->url .= '/refund/' . $payReturnId;
		}

	}

	/**
	 * Получает параметры для запроса и возвращает объект с ответом от сервера
	 * или с ошибками curl
	 *
	 * @param int    $order_id
	 * @param string $method
	 * @param null   $query
	 * @param null   $payReturnId
	 *
	 * @return stdClass
	 */
	public function request($order_id, $method = 'GET', $query = null, $payReturnId = null)
	{
		$this->setUrl($order_id, $payReturnId);

		$headers = array(
			"Accept: text/json",
			"Content-Type: application/x-www-form-urlencoded; charset=utf-8",
		);

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $this->url);
		curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		curl_setopt(
			$ch,
			CURLOPT_USERPWD,
			Gateway::getConfig('provider.id') . ':'
			. Gateway::getConfig('provider.password')
		);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
		if ($query != null) {
			curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($query));
		}

		$httpResponse = curl_exec($ch);
		$httpError = curl_error($ch);
		$info = curl_getinfo($ch);
		$response = @json_decode($httpResponse);

		if (!$response || !$httpResponse || $httpError) {

			$this->curlError = (object)array(
				'code'     => $info['http_code'],
				'error'    => $httpError,
				'response' => $httpResponse,
			);

			return ($this->curlError);
		}

		return $response;
	}
} 