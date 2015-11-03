<?php

namespace Algomonster\SpotOptionApi;

class BasicTest extends PHPUnit_Framework_TestCase
{
	pulic function testCreateClient(){
		$client = new SpotOptionApiClient();
		$this->assertInstanceOf('SpotOptionApiClient', $client);
	}
}