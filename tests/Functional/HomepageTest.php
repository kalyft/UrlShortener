<?php

namespace Tests\Functional;

class HomepageTest extends BaseTestCase
{
    /**
     * Test that the index route returns a rendered response containing the text 'SlimFramework' but not a greeting
     */
    public function testGetHomepageWithoutName()
    {
        $response = $this->runApp('GET', '/');

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertContains('SlimFramework', (string)$response->getBody());
        $this->assertNotContains('Hello', (string)$response->getBody());
    }

    /**
     * Test that the index route with optional name argument returns a rendered greeting
     */
    public function testGetHomepageWithGreeting()
    {
        $response = $this->runApp('POST', '/shorten');

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertContains('Hello shorten!', (string)$response->getBody());
    }

	public function testGoogle()
    {
		$_POST = Array (
			'lurl' => 'https://www.facebook.com',
			'provider' => 'google'
		);
        $response = $this->runApp('POST', '/shorten', $_POST);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertContains('Short URL', (string)$response->getBody());
    }
	
	public function testBitly()
    {
		$_POST = Array (
			'lurl' => 'https://www.facebook.com',
			'provider' => 'bitgiabit'
		);
		
        $response = $this->runApp('POST', '/shorten', $_POST);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertContains('Short URL', (string)$response->getBody());
    }
}