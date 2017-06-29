<?php

namespace WorkEtcClient;

class WorkEtcException extends \Exception
{
    /**
     * @var string
     */
    protected $defaultMessage = 'Unknown error.';

    /**
     * @param array $response
     */
    public function __construct(array $response)
    {
        $message = $this->buildMessage($response);

        parent::__construct($message, 0);
    }

    /**
     * Attempt to extract the core message and encode the rest.
     *
     * @param array $response
     *
     * @return string
     */
    protected function buildMessage(array $response): string
    {
        $message = isset($response['Message'])
            ? $response['Message']
            : $this->defaultMessage;

        $message .= '; response = ' . json_encode($response);

        return $message;
    }
}
