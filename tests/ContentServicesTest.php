<?php

declare(strict_types=1);

namespace tests\Libero\DummyApi;

use FluentDOM;
use PHPUnit\Xpath\Assert as XpathAssertions;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use function ob_get_clean;
use function ob_start;

/**
 * @backupGlobals enabled
 */
final class ContentServicesTest extends KernelTestCase
{
    private const ARTICLES_PATH = __DIR__.'/fixtures/articles';

    use XpathAssertions;

    /**
     * @before
     */
    public function setUpFixtures() : void
    {
        $_SERVER['ARTICLES_PATH'] = self::ARTICLES_PATH;
    }

    /**
     * @test
     */
    public function it_pings() : void
    {
        self::bootKernel();

        $response = self::$kernel->handle(Request::create('/articles/ping'));

        $this->assertSame(Response::HTTP_OK, $response->getStatusCode());
        $this->assertSame('pong', $response->getContent());
    }

    /**
     * @test
     */
    public function it_lists_items() : void
    {
        self::bootKernel();

        $response = self::$kernel->handle(Request::create('/articles/items'));

        $this->assertSame(Response::HTTP_OK, $response->getStatusCode());
        $this->assertSame('application/xml; charset=utf-8', $response->headers->get('Content-Type'));

        $dom = FluentDOM::load($response->getContent());

        $this->assertXpathCount(2, '/libero:item-list/libero:item-ref', $dom, ['libero' => 'http://libero.pub']);
    }

    /**
     * @test
     */
    public function it_gets_an_item_version() : void
    {
        self::bootKernel();

        $response = $this->handle(Request::create('/articles/items/item1/versions/latest'), $content);

        $this->assertSame(Response::HTTP_OK, $response->getStatusCode());
        $this->assertSame('application/xml; charset=utf-8', $response->headers->get('Content-Type'));
        $this->assertXmlStringEqualsXmlFile(self::ARTICLES_PATH.'/item1/2.xml', $content);
    }

    /**
     * @test
     */
    public function it_gets_an_item() : void
    {
        self::bootKernel();

        $response = $this->handle(Request::create('/articles/items/item1/versions/1'), $content);

        $this->assertSame(Response::HTTP_OK, $response->getStatusCode());
        $this->assertSame('application/xml; charset=utf-8', $response->headers->get('Content-Type'));
        $this->assertXmlStringEqualsXmlFile(self::ARTICLES_PATH.'/item1/1.xml', $content);
    }

    /**
     * @test
     * @dataProvider pathTypeProvider
     */
    public function it_requires_xml(string $path) : void
    {
        self::bootKernel();

        $request = Request::create($path);
        $request->headers->set('Accept', 'application/json');

        $response = $this->handle($request, $content);

        $this->assertSame(Response::HTTP_NOT_ACCEPTABLE, $response->getStatusCode());
        $this->assertSame('application/problem+xml; charset=utf-8', $response->headers->get('Content-Type'));
    }

    public function pathTypeProvider() : iterable
    {
        yield 'list' => ['/articles/items'];
        yield 'item' => ['/articles/items/item1/versions/1'];
    }

    private function handle(Request $request, &$content) : Response
    {
        ob_start();
        $response = self::$kernel->handle($request);
        $content = ob_get_clean();

        return $response;
    }
}
