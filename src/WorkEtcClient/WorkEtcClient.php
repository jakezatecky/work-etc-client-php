<?php

namespace WorkEtcClient;

/**
 * A thin HTTP client for WORK[etc]'s API.
 */
class WorkEtcClient
{
    /**
     * @var \WorkEtcClient\HttpInterface
     */
    protected $httpClient;

    /**
     * @var string
     */
    protected $domain = null;

    /**
     * @var string
     */
    protected $url = null;

    /**
     * @var string
     */
    protected $sessionKey = null;

    /**
     * @param \WorkEtcClient\HttpInterface
     */
    public function __construct(HttpInterface $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    /**
     * Connect to WORK[etc] and return the connection.
     *
     * @param string $domain
     * @param string $email
     * @param string $password
     *
     * @return \WorkEtcClient\WorkEtcClient
     */
    public static function connect(string $domain, string $email, string $password): WorkEtcClient
    {
        $client = new static(new HttpfulClient);

        $client->login($domain, $email, $password);

        return $client;
    }

    /**
     * Authenticate into WORK[etc].
     *
     * @param string $domain
     * @param string $email
     * @param string $password
     *
     * @return void
     *
     * @throws \WorkEtcClient\WorkEtcException
     */
    public function login(string $domain, string $email, string $password)
    {
        $this->registerDomain($domain);

        $result = $this->invoke('AuthenticateWebSafe', [
            'email' => $email,
            'pass' => $password,
        ]);

        $this->sessionKey = $result['SessionKey'];
    }

    /**
     * @param string $domain
     *
     * @return void
     */
    protected function registerDomain(string $domain)
    {
        $this->domain = $domain;

        $this->url = 'https://' . $domain . '.worketc.com';
    }

    /**
     * Invoke the given method and return the result.
     *
     * @param string $endpoint
     * @param array  $params
     *
     * @return array|string
     *
     * @throws \WorkEtcClient\WorkEtcException
     */
    public function invoke(string $endpoint, array $params = [])
    {
        $url = $this->buildUrl($endpoint);

        $response = $this->httpClient->post($url, $params);

        $this->checkErrors($response);

        // Account for WORK[etc]'s random addition of a top-level associative
        // array
        $response = isset($response['d']) ? $response['d'] : $response;

        return $response;
    }

    /**
     * Build the URL from the registered domain and the endpoint.
     *
     * @param string $endpoint
     *
     * @return string
     */
    protected function buildUrl(string $endpoint): string
    {
        if ($this->sessionKey !== null) {
            $endpoint = $endpoint . '?VeetroSession=' . $this->sessionKey;
        }

        return $this->url . '/json/' . $endpoint;
    }

    /**
     * Check if the WORK[etc] response had errors and attempts to parse them if
     * so.
     *
     * @param array $response
     *
     * @return void
     *
     * @throws \WorkEtcClient\WorkEtcException if the HTTP response code is 400 or above.
     */
    protected function checkErrors(array $response)
    {
        if ($this->httpClient->hasErrors() === false) {
            return;
        }

        throw new WorkEtcException($response);
    }
}
