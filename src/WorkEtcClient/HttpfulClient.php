<?php
namespace WorkEtcClient;

use Httpful\Request;

class HttpfulClient implements HttpInterface
{

	/**
	 * @var bool
	 */
	protected $hasErrors = false;

	/**
	 * Invoke a GET request.
	 *
	 * @param string $endpoint
	 * @param array  $parameters
	 *
	 * @return array
	 */
	public function get($endpoint, array $parameters = [])
	{
		$request = Request::get($endpoint . $this->buildQuery($parameters))
			->expects('application/json');

		return $this->send($request)->body;
	}

	/**
	 * Invoke a POST request.
	 *
	 * @param string $endpoint
	 * @param array  $parameters
	 *
	 * @return array
	 */
	public function post($endpoint, array $parameters = [])
	{
		$request = Request::post($endpoint)
			->expects('application/json')
			->body(json_encode($parameters));

		return $this->send($request)->body;
	}

	/**
	 * Send the request and check for errors.
	 *
	 * @param \Httpful\Request $request
	 *
	 * @return \Httpful\Response
	 */
	protected function send(Request $request)
	{
		$response = $request->send();

		$this->hasErrors = $response->hasErrors();

		return $response;
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

	/**
	 * Returns whether or not errors have occurred.
	 *
	 * @return bool
	 */
	public function hasErrors()
	{
		return $this->hasErrors;
	}

}
