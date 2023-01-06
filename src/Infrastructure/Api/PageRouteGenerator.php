<?php

declare(strict_types=1);

namespace App\Infrastructure\Api;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

class PageRouteGenerator
{
    private RouterInterface $router;
    private Request $request;

    public function __construct(RequestStack $requestStack, RouterInterface $router)
    {
        $this->router = $router;
        $request = $requestStack->getCurrentRequest();
        if ($request === null) {
            throw new \LogicException('Routes can be generated only in the context of request');
        }
        $this->request = $request;
    }

    public function __invoke(int $page): string
    {
        $route = $this->request->attributes->get('_route');
        $inputParams = $this->request->attributes->get('_route_params');
        $newParams = array_merge($inputParams, $this->request->query->all());
        $newParams['page'] = $page;
        return $this->router->generate($route, $newParams, UrlGeneratorInterface::ABSOLUTE_URL);
    }
}
