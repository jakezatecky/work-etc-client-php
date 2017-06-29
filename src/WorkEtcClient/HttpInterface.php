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
     * @return array|string
     */
    public function get(string $endpoint, array $parameters = []);

    /**
     * Invoke a POST request.
     *
     * @param string $endpoint
     * @param array  $parameters
     *
     * @return array|string
     */
    public function post(string $endpoint, array $parameters = []);

    /**
     * Returns whether or not errors have occurred.
     *
     * @return bool
     */
    public function hasErrors(): bool;
}
