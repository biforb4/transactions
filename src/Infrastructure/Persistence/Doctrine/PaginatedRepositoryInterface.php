<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine;

/**
 * @template T
 */
interface PaginatedRepositoryInterface
{
    /** @return int<0, max> */
    public function count(): int;

    /**
     * @return list<T>
     */
    public function getSlice($offset, $length): array;

}
