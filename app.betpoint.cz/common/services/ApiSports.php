<?php

namespace common\services;

use DateTime;
use Yii;
use yii\httpclient\Client;

class ApiSports
{

	private $apiKey;
	private $sportAlias;

	public function __construct($sportAlias, $apiKey)
	{
		$this->apiKey = $apiKey;
		$this->sportAlias = $sportAlias;
	}

	/* 
	 * Creates a request to the sports API with the specified endpoint and parameters.
	 *
	 * This method constructs a request to the sports API using the Yii HTTP client. It sets the base URL,
	 * request format, URL parameters, and headers including the API key. If the response is successful,
	 * it returns the "response" data from the API. If there are errors or if the response is not OK,
	 * it logs an error and throws an exception with a generic error message.
	 *
	 * @param string $endpoint The API endpoint to call.
	 * @param array $params Optional parameters to include in the request.
	 * @return array The response data from the API.
	 * @throws \Exception If there is an error processing the request or if the response is not OK.
	 */
	public function createRequest($endpoint, $params = [])
	{
		$client = new Client([
			'baseUrl' => Yii::$app->params['sports'][$this->sportAlias]['api']['baseUrl'],
		]);
	
		$request = $client->createRequest()
			->setFormat(Client::FORMAT_JSON)
			->setUrl(array_merge([$endpoint], $params))
			->setHeaders([
				'x-rapidapi-key'  => $this->apiKey,
			]);

		$response = $request->send();
		if ($response->isOk && isset($response->getData()["response"]) && (isset($response->getData()["errors"]) && empty($response->getData()["errors"]) || !isset($response->getData()["errors"]))) {
				 
			return $response->getData();
		} else {
			 
			$genericErrorMessage = "An error occurred while processing the API request. More information in log files";
			Yii::error([
				"error" => $genericErrorMessage,
				"baseUrl" => Yii::$app->params['sports'][$this->sportAlias]['api']['baseUrl'],
				"endpoint" => $endpoint,
				"params" => $params,
				"statusCode" => $response->getStatusCode(),
				"response" => $response->getData(),
				"headers" => $response->getHeaders(),
			], 'apiSports');
			throw new \Exception($genericErrorMessage, $response->getStatusCode());
		}
	}
}