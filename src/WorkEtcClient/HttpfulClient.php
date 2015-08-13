<?php
namespace WorkEtcClient;

use Httpful\Request;

class HttpfulClient implements HttpInterface
{

	/**
	 * Invoke a GET request.
	 *
	 * @param string $endpoint
	 * @param array  $parameters
	 *
	 * @return array
	 */
	public function get($endpoint, array $parameters)
	{
		$response = Request::get($endpoint . $this->buildQuery($parameters))
			->expects('application/json')
			->send();

		return $response->body;
	}

	/**
	 * Invoke a POST request.
	 *
	 * @param string $endpoint
	 * @param array  $parameters
	 *
	 * @return array
	 */
	public function post($endpoint, array $parameters)
	{
		$response = Request::post($endpoint)
			->expects('application/json')
			->body(json_encode($parameters))
			->send();

		return $response->body;
	}

	/**
	 * Builds the query string for the URL.
	 *
	 * @param array $data
	 *
	 * @return string
	 */
	protected function buildQuery(array $data = [])
	{
		if (count($data) === 0) {
			return null;
		}

		return '?' . http_build_query($data);
	}

}
