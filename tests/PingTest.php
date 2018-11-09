<?php

declare(strict_types=1);

namespace tests\Libero\DummyApi;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class PingTest extends KernelTestCase
{
    /**
     * @test
     */
    public function it_pings() : void
    {
        self::bootKernel();

        $response = self::$kernel->handle(Request::create('/ping'));

        $this->assertSame(Response::HTTP_OK, $response->getStatusCode());
        $this->assertSame('pong', $response->getContent());
    }
}
