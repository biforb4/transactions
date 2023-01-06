<?php

declare(strict_types=1);

namespace App\Infrastructure\Framwework\ParamConverter;

use App\UseCases\UseCase;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Exception\MissingConstructorArgumentsException;
use Symfony\Component\Serializer\SerializerInterface;

class UseCaseParamConverter implements ParamConverterInterface
{
    public function __construct(private SerializerInterface $serializer)
    {
    }

    /**
     * @inheritDoc
     */
    public function apply(Request $request, ParamConverter $configuration): bool
    {
        $content = $this->decodeBodyContent($request);
        $data = array_merge($request->query->all(), $request->attributes->all(), $request->request->all(), $content);
        $data = $this->filterBooleans($data);
        $data = $this->convertNumeric($data);
        $data['ipAddress'] = $request->getClientIp();
        if (isset($data['brand']) && $request->attributes->has('brand')) {
            $data['brand'] = $request->attributes->get('brand');
        }

        /** @psalm-var class-string<UseCase> $dtoClassName */
        $dtoClassName = $configuration->getClass();
        try {
            /** @psalm-suppress UndefinedInterfaceMethod */
            $dto = $this->serializer->denormalize($data, $dtoClassName, 'json', ['disable_type_enforcement' => true]);
        } catch (MissingConstructorArgumentsException $e) {
            throw InvalidParameterExceptionFactory::missingParameter($dtoClassName, $data);
        }
        $request->attributes->set($configuration->getName(), $dto);

        return true;
    }

    /**
     * @inheritDoc
     */
    public function supports(ParamConverter $configuration): bool
    {
        $class = $configuration->getClass();
        return is_string($class) && is_subclass_of($class, UseCase::class);
    }

    private function decodeBodyContent(Request $request): array
    {
        $content = [];
        $format = $request->getFormat($request->headers->get('Content-Type'));
        if ($request->getContent() === '') {
            return [];
        }
        if ($format === 'json' && in_array($request->getMethod(), ['POST', 'PUT', 'PATCH', 'DELETE'], true)) {
            /** @psalm-suppress UndefinedInterfaceMethod */
            $content = $this->serializer->decode($request->getContent(), $format);
        }
        return $content;
    }

    private function filterBooleans(array $data): array
    {
        foreach ($data as $key => $value) {
            if ($value === 'false' || $value === 'true') {
                $data[$key] = filter_var($value, FILTER_VALIDATE_BOOLEAN);
            }
        }
        return $data;
    }

    private function convertNumeric(array $data): array
    {
        foreach ($data as $key => $value) {
            if (is_numeric($value) && is_string($value)) {
                /** @psalm-var numeric-string $value */
                $data[$key] = str_contains($value, '.') ? (float)$value : (int)$value;
            }
        }
        return $data;
    }
}
