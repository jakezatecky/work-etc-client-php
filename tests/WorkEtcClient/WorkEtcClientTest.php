<?php

use Mockery as m;
use WorkEtcClient\WorkEtcClient;

class WorkEtcClientTest extends \PHPUnit_Framework_TestCase
{

	public function tearDown()
	{
		m::close();
	}

	public function testSimpleInvoke()
	{
		$interface = m::mock('WorkEtcClient\HttpInterface');

		$interface
			->shouldReceive('post')
			->with('/BuildSupplyDepots', [])
			->once()
			->andReturn('Not enough minerals.')
			->shouldReceive('hasErrors')
			->once()
			->andReturn(false);

		$client = new WorkEtcClient($interface);

		$this->assertEquals('Not enough minerals.', $client->invoke('BuildSupplyDepots'));
	}

	public function testParameterizedInvoke()
	{
		$interface = m::mock('WorkEtcClient\HttpInterface');

		$interface
			->shouldReceive('post')
			->with('/BuildSupplyDepots', ['minerals' => 100])
			->once()
			->andReturn('Sure thing.')
			->shouldReceive('hasErrors')
			->once()
			->andReturn(false);

		$client = new WorkEtcClient($interface);

		$this->assertEquals('Sure thing.', $client->invoke('BuildSupplyDepots', [
			'minerals' => 100
		]));
	}

	public function testInvokeWithWrapperResponse()
	{
		$interface = m::mock('WorkEtcClient\HttpInterface');

		$interface
			->shouldReceive('post')
			->with('/BuildSupplyDepots', [])
			->once()
			->andReturn(['d' => 'Not enough minerals.'])
			->shouldReceive('hasErrors')
			->once()
			->andReturn(false);

		$client = new WorkEtcClient($interface);

		$this->assertEquals('Not enough minerals.', $client->invoke('BuildSupplyDepots'));
	}

	/**
	 * @expectedException \WorkEtcClient\WorkEtcException
	 * @expectedExceptionMessage Not enough energy.; response = {"Message":"Not enough energy."}
	 */
	public function testKnownError()
	{
		$interface = m::mock('WorkEtcClient\HttpInterface');

		$interface
			->shouldReceive('post')
			->with('/FireYamatoCannon', [])
			->once()
			->andReturn(['Message' => 'Not enough energy.'])
			->shouldReceive('hasErrors')
			->once()
			->andReturn(true);

		$client = new WorkEtcClient($interface);

		$client->invoke('FireYamatoCannon');
	}

	/**
	 * @expectedException \WorkEtcClient\WorkEtcException
	 * @expectedExceptionMessage Unknown error.; response = "Additional supply depots required."
	 */
	public function testUnknownError()
	{
		$interface = m::mock('WorkEtcClient\HttpInterface');

		$interface
			->shouldReceive('post')
			->with('/TrainMarine', [])
			->once()
			->andReturn('Additional supply depots required.')
			->shouldReceive('hasErrors')
			->once()
			->andReturn(true);

		$client = new WorkEtcClient($interface);

		$client->invoke('TrainMarine');
	}

	public function testLogin()
	{
		$interface = m::mock('WorkEtcClient\HttpInterface');

		$interface
			->shouldReceive('post')
			->with('https://raynorsraiders.worketc.com/AuthenticateWebSafe', [
				'email' => 'jimmy',
				'pass'  => 'kerrigan',
			])
			->once()
			->andReturn(['SessionKey' => '12345'])
			->shouldReceive('hasErrors')
			->once()
			->andReturn(false);

		$client = new WorkEtcClient($interface);

		$client->login('raynorsraiders', 'jimmy', 'kerrigan');
	}

	public function testAuthenticatedInvoke()
	{
		$interface = m::mock('WorkEtcClient\HttpInterface');

		$interface
			->shouldReceive('post')
			->with('https://raynorsraiders.worketc.com/AuthenticateWebSafe', [
				'email' => 'jimmy',
				'pass'  => 'kerrigan',
			])
			->once()
			->andReturn(['SessionKey' => '12345'])
			->shouldReceive('hasErrors')
			->once()
			->andReturn(false)
			->shouldReceive('post')
			->with('https://raynorsraiders.worketc.com/QuitGame?VeetroSession=12345', [])
			->once()
			->andReturn('Quitting...')
			->shouldReceive('hasErrors')
			->once()
			->andReturn(false);

		$client = new WorkEtcClient($interface);

		$client->login('raynorsraiders', 'jimmy', 'kerrigan');

		$this->assertEquals('Quitting...', $client->invoke('QuitGame'));
	}

}
