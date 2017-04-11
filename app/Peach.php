<?php

namespace App;

use GuzzleHttp\Client as Guzzle;

/**
* Peach
*/
class Peach
{
	private $client;

	public function __construct(Guzzle $client)
	{
		$this->client = $client;
	}

	public function autorize($number, $holder, $exp_month, $exp_year, $cvv, $url = null)
	{
		$response = $this->client->post('https://test.oppwa.com/v1/payments', [
			'form_params' => [
				'authentication.userId' => config('services.peach.user_id'),
				'authentication.password' => config('services.peach.password'),
				'authentication.entityId' => config('services.peach.entityId'),
				'amount' => number_format(1, 2),
		    	'currency' => 'ZAR',
		    	'paymentBrand' => $this->getCardType($number),
		    	'paymentType' => 'PA',
				'card.number' => $number,
		    	'card.holder' => $holder,
		    	'card.expiryMonth' => $exp_month,
		    	'card.expiryYear' => $exp_year,
		    	'card.cvv' => $cvv,
		    	'createRegistration' => true,
		    	'recurringType' => 'INITIAL',
		    	'shopperResultUrl' => config('app.url') . $url ?? '/return'
	    	]
		]);

		return json_decode($response->getBody(), true);
	}

	public function fetchPayment($id)
	{
		try {
			$response = $this->client->get("https://test.oppwa.com/v1/payments/{$id}", [
			'query' => [
				'authentication.userId' => config('services.peach.user_id'),
				'authentication.password' => config('services.peach.password'),
				'authentication.entityId' => config('services.peach.entityId'),
			]
		]);

		$data = json_decode($response->getBody(), true);

		return new Payment($data);

		} catch (\GuzzleHttp\Exception\ClientException $e) {
	    	return json_decode($e->getResponse()->getBody()->getContents(), true);
	    }
	}

	public function charge($token, $amount, $reference, $description = null)
	{
		try {
			$response = $this->client->post("https://test.oppwa.com/v1/registrations/{$token}/payments", [
			'query' => [
				'authentication.userId' => config('services.peach.user_id'),
				'authentication.password' => config('services.peach.password'),
				'authentication.entityId' => config('services.peach.recurringId'),
				'amount' => number_format($amount, 2, '.', ''),
				'currency' => 'ZAR',
				'paymentType' => 'DB',
				'recurringType' => 'REPEATED',
                'merchantInvoiceId' => str_limit($reference, 255),
                'descriptor' => $description ?? str_limit($description, 127)
			]
		]);

		$data = json_decode($response->getBody(), true);

		return $data;

		} catch (\GuzzleHttp\Exception\ClientException $e) {
	    	return json_decode($e->getResponse()->getBody()->getContents(), true);
	    }
	}

	public function deleteToken($token)
	{
		try {
			$response = $this->client->delete("https://test.oppwa.com/v1/registrations/{$token}", [
			'query' => [
				'authentication.userId' => config('services.peach.user_id'),
				'authentication.password' => config('services.peach.password'),
				'authentication.entityId' => config('services.peach.entityId'),
			]
		]);

		$data = json_decode($response->getBody(), true);

		return $data;

		} catch (\GuzzleHttp\Exception\ClientException $e) {
	    	return json_decode($e->getResponse()->getBody()->getContents(), true);
	    }
	}

	protected function getCardType($number) {
	    $re = [
	        "visa"       => "/^4[0-9]{12}(?:[0-9]{3})?$/",
	        "mastercard" => "/^5[1-5][0-9]{14}$/"
	    ];

	    if (preg_match($re['visa'],$number)) {
	        return 'VISA';
	    }

	    else if (preg_match($re['mastercard'],$number)) {
	        return 'MASTER';
	    }
	 }
}