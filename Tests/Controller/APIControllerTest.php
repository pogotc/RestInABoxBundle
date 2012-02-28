<?php

namespace Pogotc\RestInABoxBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class APIControllerTest extends WebTestCase
{
	
	protected $testKernel;
	protected $container;
	protected $entityManager;
	
	protected $entityName;
	
	public function setUp(){
		$this->entityName = 'user';
		
		// Boot the AppKernel in the test environment and with the debug.
        $this->testKernel = new \AppKernel('test', true);
        $this->testKernel->boot();

        // Store the container and the entity manager in test case properties
        $this->container = $this->testKernel->getContainer();
        $this->entityManager = $this->container->get('doctrine')->getEntityManager();
		
		$connection = $this->entityManager->getConnection();
		$connection->executeUpdate("TRUNCATE ".$this->entityName);
	}
	
    public function testCreate()
    {
		$client = static::createClient();
		
		//Start by making sure the user table is clear
		$client->request("GET", '/api/'.$this->entityName);
		$response = $client->getResponse()->getContent();
		$response = (array) json_decode($response);
		$this->assertTrue(is_array($response));
		$this->assertEquals(0, count($response));
		
        //Create a new user
        $client->request('POST', '/api/'.$this->entityName.'/create', array('name' => 'Armando Paredes'));
		$response = $client->getResponse()->getContent();
		$response = (array) json_decode($response);
		$this->assertTrue(is_array($response));
		$this->assertTrue(array_key_exists("id", $response));
		$this->assertEquals(1, $response['id']);
		
		//Check he's returned from by our main list
		$client->request("GET", '/api/'.$this->entityName);
		$response = $client->getResponse()->getContent();
		$response = (array) json_decode($response);

		$this->assertTrue(is_array($response));
		$this->assertEquals(1, count($response));
		$user = (array)$response[0];
		$this->assertEquals(1, $user['id']);
		$this->assertEquals('Armando Paredes', $user['name']);
		
		//Add another user
		$client->request('POST', '/api/'.$this->entityName.'/create', array('name' => 'Jimmy Crabs'));
		$response = $client->getResponse()->getContent();
		$response = (array) json_decode($response);
		$this->assertTrue(is_array($response));
		$this->assertTrue(array_key_exists("id", $response));
		$this->assertEquals(2, $response['id']);
		
		
		//Check both users are returned
		$client->request("GET", '/api/'.$this->entityName);
		$response = $client->getResponse()->getContent();
		$response = (array) json_decode($response);
		$this->assertTrue(is_array($response));
		$this->assertEquals(2, count($response));
		
		//Update Jimmy Crabs
		$client->request('PUT', '/api/'.$this->entityName.'/2', array('name' => 'James Crabtree'));
		$response = $client->getResponse()->getContent();
		$response = (array) json_decode($response);
		$this->assertEquals(2, $response['id']);
		$this->assertEquals('James Crabtree', $response['name']);
		
		//View Armando's record
		$client->request('GET', '/api/'.$this->entityName.'/1');
		$response = $client->getResponse()->getContent();
		$response = (array) json_decode($response);
		$this->assertTrue(array_key_exists("id", $response));
		$this->assertEquals(1, $response['id']);
		$this->assertEquals('Armando Paredes', $response['name']);
		
		//And delete Mr Crabtree
		$client->request('DELETE', '/api/'.$this->entityName.'/2');
		$response = $client->getResponse()->getContent();
		
		//Make sure we only have one user in total
		//Check he's returned from by our main list
		$client->request("GET", '/api/'.$this->entityName);
		$response = $client->getResponse()->getContent();
		$response = (array) json_decode($response);
		$this->assertTrue(is_array($response));
		$this->assertEquals(1, count($response));
    }
}
