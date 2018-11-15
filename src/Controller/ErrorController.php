<?php

declare(strict_types=1);

namespace Libero\DummyApi\Controller;

use RuntimeException;
use Symfony\Component\HttpFoundation\Response;

final class ErrorController
{
    public function __invoke() : Response
    {
        throw new RuntimeException('Some exception', 0, new RuntimeException('Some other exception'));
    }
}
