<?php

declare(strict_types=1);

namespace App\Infrastructure\Framwework\ParamConverter;

use App\UseCases\UseCase;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class InvalidParameterExceptionFactory
{
    /**
     * @template T of UseCase
     * @psalm-param class-string<T> $dtoClassName
     */
    public static function missingParameter(string $dtoClassName, array $data): BadRequestHttpException
    {
        $mandatoryFields = self::getMandatoryFields($dtoClassName, $data);

        return new BadRequestHttpException(sprintf(
            'Missing parameter%s: %s',
            count($mandatoryFields) > 1 ? 's' : '',
            implode(',', $mandatoryFields)
        ));
    }

    /**
     * @template T
     * @psalm-param class-string<T> $dtoClassName
     */
    private static function getMandatoryFields(string $dtoClassName, array $data, string $prefix = ''): array
    {
        if ($prefix) {
            $prefix .= '.';
        }
        $mandatoryFields = [];
        $r = new \ReflectionMethod($dtoClassName, '__construct');
        $params = $r->getParameters();
        foreach ($params as $param) {
            $paramName = strtolower(preg_replace('/[A-Z]/', '_\\0', lcfirst($param->getName())));
            $paramName = array_key_exists($paramName, $data) ? $paramName : $param->getName();

            if (!array_key_exists($paramName, $data) && !$param->isOptional()) {
                $mandatoryFields[] = [$prefix . $paramName];
            }

            $reflectionNamedType = $param->getType();
            if ($reflectionNamedType === null) {
                continue;
            }
            /** @var \ReflectionNamedType $reflectionNamedType */
            $className = $reflectionNamedType->getName();
            if (array_key_exists($paramName, $data) && class_exists($className)) {
                $mandatoryFields[] = self::getMandatoryFields(
                /** @var class-string $className */
                    $className,
                    $data[$param->getName()],
                    $prefix . $paramName
                );
            }
        }

        if (count($mandatoryFields) > 0) {
            return array_merge(...$mandatoryFields);
        }

        return [];
    }
}
