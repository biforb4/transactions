<?php

declare(strict_types=1);

namespace App\Tests\Infrastructure\Api;

use App\Infrastructure\Api\PaginatorFactory;
use App\Infrastructure\Persistence\Doctrine\PaginatedRepositoryInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class PaginatorFactoryTest extends TestCase
{

    private RequestStack $requestStack;
    private PaginatedRepositoryInterface|MockObject $repository;

    protected function setUp(): void
    {
        $this->requestStack = new RequestStack();
        $this->repository = $this->createMock(PaginatedRepositoryInterface::class);
    }

    public function testShouldCreatePaginatorWithDefaultValues(): void
    {
        //given
        $this->requestStack->push(new Request());
        $this->repository->method('count')->willReturn(1);
        $this->repository->method('getSlice')->willReturn([]);

        //when
        $sut = new PaginatorFactory($this->requestStack, $this->repository);
        $result = $sut->create();

        //then
        $this->assertSame(1, $result->getCurrentPage());
        $this->assertSame(10, $result->getMaxPerPage());
    }

    public function testShouldCreatePaginatorWithValuesFromTheRequest(): void
    {
        //given
        $this->requestStack->push(new Request(['perPage' => 100, 'page' => 2]));
        $this->repository->method('count')->willReturn(300);
        $this->repository->method('getSlice')->willReturn([]);

        //when
        $sut = new PaginatorFactory($this->requestStack, $this->repository);
        $result = $sut->create();

        //then
        $this->assertSame(2, $result->getCurrentPage());
        $this->assertSame(100, $result->getMaxPerPage());
    }
}
