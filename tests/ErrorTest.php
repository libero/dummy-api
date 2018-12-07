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

    /**
     * @test
     * @dataProvider languageProvider
     * @backupGlobals true
     */
    public function it_negotiates_locale(string $acceptLanguage, string $expected) : void
    {
        $_SERVER['DEFAULT_LOCALE'] = 'de';
        $_SERVER['POSSIBLE_LOCALES'] = 'en|es';

        self::bootKernel();

        $request = Request::create('/foo');
        $request->headers->set('Accept-Language', $acceptLanguage);

        $response = self::$kernel->handle($request);

        $this->assertSame(Response::HTTP_NOT_FOUND, $response->getStatusCode());
        $this->assertSame('application/problem+xml; charset=utf-8', $response->headers->get('Content-Type'));
        $this->assertSame($expected, $response->headers->get('Content-Language'));

        $dom = FluentDOM::load($response->getContent());

        $this->assertXpathMatch("/problem:problem[@xml:lang='${expected}']", $dom, ['problem' => 'urn:ietf:rfc:7807']);
    }

    public function languageProvider() : iterable
    {
        yield 'Any language' => ['*', 'en'];
        yield 'English' => ['en', 'en'];
        yield 'Spanish' => ['es', 'es'];
        yield 'French' => ['fr', 'de'];
        yield 'French or English' => ['fr, en;q=0.1', 'en'];
    }
}
