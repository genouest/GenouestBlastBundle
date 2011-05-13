<?php

namespace Genouest\Bundle\BlastBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BlastControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = $this->createClient();

        $crawler = $client->request('GET', '/');

        $this->assertTrue($crawler->filter('html:contains("Blast")')->count() > 0);
    }
}
