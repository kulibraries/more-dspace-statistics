<?php

namespace ScholarWorks\StatsBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AuthorsControllerControllerTest extends WebTestCase
{
    public function testAuthors()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/authors/');
    }

}
