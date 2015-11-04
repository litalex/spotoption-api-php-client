<?php

namespace Algomonster\SpotOption;

class BasicTest extends PHPUnit_Framework_TestCase
{
	pulic function testCreateClient(){
		$client = new SpotOptionApiClient();
		$this->assertInstanceOf('Client', $client);
	}
}