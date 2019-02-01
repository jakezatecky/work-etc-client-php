<?php

use Mockery as m;
use PHPUnit\Framework\TestCase;
use WorkEtcClient\WorkEtcClient;

class WorkEtcClientTest extends TestCase
{
    public function tearDown(): void
    {
        m::close();
    }

    public function testSimpleInvoke()
    {
        $interface = m::mock('WorkEtcClient\HttpInterface');
        $interface
            ->shouldReceive('post')
            ->with('/json/BuildSupplyDepots', [])
            ->once()
            ->andReturn(['Not enough minerals.'])
            ->shouldReceive('hasErrors')
            ->once()
            ->andReturn(false);

        $client = new WorkEtcClient($interface);

        $this->assertEquals(['Not enough minerals.'], $client->invoke('BuildSupplyDepots'));
    }

    public function testParameterizedInvoke()
    {
        $interface = m::mock('WorkEtcClient\HttpInterface');
        $interface
            ->shouldReceive('post')
            ->with('/json/BuildSupplyDepots', ['minerals' => 100])
            ->once()
            ->andReturn(['Sure thing.'])
            ->shouldReceive('hasErrors')
            ->once()
            ->andReturn(false);
        $client = new WorkEtcClient($interface);

        $this->assertEquals(['Sure thing.'], $client->invoke('BuildSupplyDepots', [
            'minerals' => 100
        ]));
    }

    public function testInvokeWithWrapperResponse()
    {
        $interface = m::mock('WorkEtcClient\HttpInterface');
        $interface
            ->shouldReceive('post')
            ->with('/json/BuildSupplyDepots', [])
            ->once()
            ->andReturn(['d' => ['Not enough minerals.']])
            ->shouldReceive('hasErrors')
            ->once()
            ->andReturn(false);
        $client = new WorkEtcClient($interface);

        $this->assertEquals(['Not enough minerals.'], $client->invoke('BuildSupplyDepots'));
    }

    public function testKnownError()
    {
        $interface = m::mock('WorkEtcClient\HttpInterface');
        $interface
            ->shouldReceive('post')
            ->with('/json/FireYamatoCannon', [])
            ->once()
            ->andReturn(['Message' => 'Not enough energy.'])
            ->shouldReceive('hasErrors')
            ->once()
            ->andReturn(true);
        $client = new WorkEtcClient($interface);

        $this->expectException('\WorkEtcClient\WorkEtcException');
        $this->expectExceptionMessage('Not enough energy.; response = {"Message":"Not enough energy."}');
        $client->invoke('FireYamatoCannon');
    }

    public function testUnknownError()
    {
        $interface = m::mock('WorkEtcClient\HttpInterface');
        $interface
            ->shouldReceive('post')
            ->with('/json/TrainMarine', [])
            ->once()
            ->andReturn(['Additional supply depots required.'])
            ->shouldReceive('hasErrors')
            ->once()
            ->andReturn(true);
        $client = new WorkEtcClient($interface);

        $this->expectException('\WorkEtcClient\WorkEtcException');
        $this->expectExceptionMessage('Unknown error.; response = ["Additional supply depots required."]');
        $client->invoke('TrainMarine');
    }

    public function testLogin()
    {
        $interface = m::mock('WorkEtcClient\HttpInterface');
        $interface
            ->shouldReceive('post')
            ->with('https://raynorsraiders.worketc.com/json/AuthenticateWebSafe', [
                'email' => 'jimmy',
                'pass' => 'kerrigan',
            ])
            ->once()
            ->andReturn(['SessionKey' => '12345'])
            ->shouldReceive('hasErrors')
            ->once()
            ->andReturn(false);
        $client = new WorkEtcClient($interface);

        // No exception should be thrown
        $client->login('raynorsraiders', 'jimmy', 'kerrigan');
        $this->assertTrue(true);
    }

    public function testAuthenticatedInvoke()
    {
        $interface = m::mock('WorkEtcClient\HttpInterface');
        $interface
            ->shouldReceive('post')
            ->with('https://raynorsraiders.worketc.com/json/AuthenticateWebSafe', [
                'email' => 'jimmy',
                'pass' => 'kerrigan',
            ])
            ->once()
            ->andReturn(['SessionKey' => '12345'])
            ->shouldReceive('hasErrors')
            ->once()
            ->andReturn(false)
            ->shouldReceive('post')
            ->with('https://raynorsraiders.worketc.com/json/QuitGame?VeetroSession=12345', [])
            ->once()
            ->andReturn(['Quitting...'])
            ->shouldReceive('hasErrors')
            ->once()
            ->andReturn(false);
        $client = new WorkEtcClient($interface);

        $client->login('raynorsraiders', 'jimmy', 'kerrigan');
        $this->assertEquals(['Quitting...'], $client->invoke('QuitGame'));
    }
}
