<?php

use Mockery as m;
use WorkEtcClient\HttpfulClient;

class HttpfulClientTest extends \PHPUnit_Framework_TestCase
{

	public function setUp()
	{
		$this->response = 'SCV ready!';

		$this->endpoint = 'http://example.com/build/terran/scv';

		$this->params = [
			'target'   => 'CommandCenter_1',
			'quantity' => 2,
		];

		$this->request = m::mock('alias:Httpful\Request');
		$this->request->body = $this->response;;
	}

	public function tearDown()
	{
		m::close();
	}

	public function testGet()
	{
		// Validate against the formed URL
		$url = 'http://example.com/build/terran/scv?target=CommandCenter_1&quantity=2';

		// Construct the statically-mocked nightmare
		$this->request->shouldReceive('get')->with($url)->once()
			->andReturn($this->request)->shouldReceive('expects')->with('application/json')->once()
			->andReturn($this->request)->shouldReceive('send')->once()
			->andReturn($this->request);

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
			->andReturn($this->request)->shouldReceive('expects')->with('application/json')->once()
			->andReturn($this->request)->shouldReceive('body')->with(json_encode($this->params))->once()
			->andReturn($this->request)->shouldReceive('send')->once()
			->andReturn($this->request);

		$client = new HttpfulClient;

		$this->assertEquals($this->response, $client->post(
			$this->endpoint,
			$this->params
		));
	}

}
