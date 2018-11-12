<?php

declare(strict_types=1);

namespace tests\Libero\DummyApi;

use FluentDOM;
use PHPUnit\Xpath\Assert as XpathAssertions;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class ErrorTest extends KernelTestCase
{
    use XpathAssertions;

    /**
     * @test
     */
    public function it_returns_api_problems() : void
    {
        self::bootKernel();

        $response = self::$kernel->handle(Request::create('/foo'));

        $this->assertSame(Response::HTTP_NOT_FOUND, $response->getStatusCode());
        $this->assertSame('application/problem+xml; charset=utf-8', $response->headers->get('Content-Type'));

        $dom = FluentDOM::load($response->getContent());

        $this->assertXpathMatch('/problem:problem/problem:title', $dom, ['problem' => 'urn:ietf:rfc:7807']);
    }
}
