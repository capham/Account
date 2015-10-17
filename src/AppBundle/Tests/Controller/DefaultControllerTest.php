<?php
namespace AppBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/account');
        $this->assertEquals(403, $client->getResponse()->getStatusCode());
    }

    public function test1Index()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/account?accountID=1&AuthID=TASK24H-TEST');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
}
