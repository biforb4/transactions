<?php

declare(strict_types=1);

namespace App\Tests\Infrastructure\Api;

use App\Infrastructure\Api\PageRouteGenerator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\RouterInterface;

class PageRouteGeneratorTest extends TestCase
{

    public function testShouldSetPage()
    {
        $this->expectNotToPerformAssertions();
        //given
        $requestStack = new RequestStack();
        $requestStack->push(new Request([], [], ['_route' => 'test', '_route_params' => []]));
        $router = $this->createMock(RouterInterface::class);
        $router->method('generate')
            ->with('test', $this->logicalAnd($this->arrayHasKey('page')), 0)
            ->willReturn('string');

        //when && then
        $sut = new PageRouteGenerator($requestStack, $router);
        $sut(2);
    }
}
