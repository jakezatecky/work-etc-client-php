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
     * @return array|string
     *
     * @throws \Httpful\Exception\ConnectionErrorException
     */
    public function get(string $endpoint, array $parameters = [])
    {
        $request = Request::get($endpoint . $this->buildQuery($parameters))
            ->expects('application/json');

        return $this->send($request);
    }

    /**
     * Invoke a POST request.
     *
     * @param string $endpoint
     * @param array  $parameters
     *
     * @return array|string
     *
     * @throws \Httpful\Exception\ConnectionErrorException
     */
    public function post(string $endpoint, array $parameters = [])
    {
        $request = Request::post($endpoint)
            ->expects('application/json')
            ->body($this->jsonEncode($parameters));

        return $this->send($request);
    }

    /**
     * Encode the given array to JSON. If the array would not translate to an
     * object, force it.
     *
     * WORK[etc] expects an object, even if that object would be empty.
     *
     * @param array $array
     *
     * @return string
     */
    protected function jsonEncode(array $array): string
    {
        if (empty($array)) {
            return '{}';
        }

        return json_encode($array);
    }

    /**
     * Send the request and check for errors.
     *
     * @param \Httpful\Request $request
     *
     * @return array|string
     *
     * @throws \Httpful\Exception\ConnectionErrorException
     */
    protected function send(Request $request)
    {
        $response = $request->send();

        $this->hasErrors = $response->hasErrors();

        return json_decode(json_encode($response->body), true);
    }

    /**
     * Builds the query string for the URL.
     *
     * @param array $data
     *
     * @return string
     */
    protected function buildQuery(array $data = []): string
    {
        if (count($data) === 0) {
            return '';
        }

        return '?' . http_build_query($data);
    }

    /**
     * Returns whether or not errors have occurred.
     *
     * @return bool
     */
    public function hasErrors(): bool
    {
        return $this->hasErrors;
    }
}
