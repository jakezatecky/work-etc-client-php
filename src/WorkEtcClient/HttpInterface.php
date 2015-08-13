<?php
namespace WorkEtcClient;

/**
 * A simple HTTP client interface.
 */
interface HttpInterface
{

	/**
	 * Invoke a GET request.
	 *
	 * @param string $endpoint
	 * @param array  $parameters
	 *
	 * @return array
	 */
	public function get($endpoint, array $parameters = []);

	/**
	 * Invoke a POST request.
	 *
	 * @param string $endpoint
	 * @param array  $parameters
	 *
	 * @return array
	 */
	public function post($endpoint, array $parameters = []);

	/**
	 * Returns whether or not errors have occurred.
	 *
	 * @return bool
	 */
	public function hasErrors();

}
