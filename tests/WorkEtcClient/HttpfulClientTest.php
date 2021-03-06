<?php

use Mockery as m;
use PHPUnit\Framework\TestCase;
use WorkEtcClient\HttpfulClient;

class GoodResponse
{
    public $body = 'SCV ready!';

    public function hasErrors()
    {
        return false;
    }
}

class BadResponse
{
    public $body = 'Not enough minerals.';

    public function hasErrors()
    {
        return true;
    }
}


class HttpfulClientTest extends TestCase
{
    private $response;
    private $endpoint;
    private $params;
    private $request;
    private $goodResponse;
    private $badResponse;

    public function setUp(): void
    {
        $this->response = 'SCV ready!';

        $this->endpoint = 'http://example.com/build/terran/scv';

        $this->params = [
            'target'   => 'CommandCenter_1',
            'quantity' => 2,
        ];

        $this->request = m::mock('alias:Httpful\Request');

        $this->goodResponse = new GoodResponse();
        $this->badResponse = new BadResponse();
    }

    public function tearDown(): void
    {
        m::close();
    }

    public function testGet()
    {
        // Validate against the formed URL
        $url = 'http://example.com/build/terran/scv?target=CommandCenter_1&quantity=2';

        // Construct the statically-mocked nightmare
        $this->request->shouldReceive('get')->with($url)->once()
            ->andReturn($this->request)->shouldReceive('expectsType')->with('application/json')->once()
            ->andReturn($this->request)->shouldReceive('send')->once()
            ->andReturn($this->goodResponse);

        $client = new HttpfulClient;

        $this->assertEquals($this->response, $client->get(
            $this->endpoint,
            $this->params
        ));
    }

    public function testPost()
    {
        // Construct the statically-mocked nightmare
        $this->request->shouldReceive('post')->with($this->endpoint)->once()
            ->andReturn($this->request)->shouldReceive('expectsType')->with('application/json')->once()
            ->andReturn($this->request)->shouldReceive('body')->with(json_encode($this->params))->once()
            ->andReturn($this->request)->shouldReceive('send')->once()
            ->andReturn($this->goodResponse);

        $client = new HttpfulClient;

        $this->assertEquals($this->response, $client->post(
            $this->endpoint,
            $this->params
        ));
    }

    public function testHasErrors()
    {
        // Construct the statically-mocked nightmare
        $this->request->shouldReceive('get')
            ->andReturn($this->request)->shouldReceive('expectsType')
            ->andReturn($this->request)->shouldReceive('send')
            ->andReturn($this->badResponse);

        $client = new HttpfulClient;

        $client->get('');

        $this->assertTrue($client->hasErrors());
    }

    public function testEmptyArray()
    {
        // Construct the statically-mocked nightmare
        $this->request->shouldReceive('post')
            ->andReturn($this->request)->shouldReceive('expectsType')
            ->andReturn($this->request)->shouldReceive('body')->with('{}')->once()
            ->andReturn($this->request)->shouldReceive('send')
            ->andReturn($this->badResponse);

        $client = new HttpfulClient;

        $client->post('');

        $this->assertTrue($client->hasErrors());
    }
}
