<?php

declare(strict_types=1);

namespace Netgen\Bundle\EzPlatformSiteApiBundle\Converter;

use Netgen\EzPlatformSiteApi\API\LoadService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;

abstract class SiteParamConverter implements ParamConverterInterface
{
    /**
     * @var \Netgen\EzPlatformSiteApi\API\LoadService
     */
    protected $loadService;

    public function __construct(LoadService $loadService)
    {
        $this->loadService = $loadService;
    }

    public function supports(ParamConverter $configuration): bool
    {
        return \is_a($configuration->getClass(), $this->getSupportedClass(), true);
    }

    public function apply(Request $request, ParamConverter $configuration): bool
    {
        if (!$request->attributes->has($this->getPropertyName())) {
            return false;
        }
        $valueObjectId = $request->attributes->getInt($this->getPropertyName());
        if (!$valueObjectId && $configuration->isOptional()) {
            return false;
        }

        $request->attributes->set($configuration->getName(), $this->loadValueObject($valueObjectId));

        return true;
    }

    abstract protected function getSupportedClass(): string;

    /**
     * @return string property name used in the method of the controller needing param conversion
     */
    abstract protected function getPropertyName(): string;

    abstract protected function loadValueObject(int $id);
}
