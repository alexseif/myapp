<?php

namespace AppBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class WorkWeekControllerTest extends WebTestCase
{
    public function testDisplayworkweek()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/displayWorkWeek');
    }
}
