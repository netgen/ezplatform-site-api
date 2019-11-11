<?php

declare(strict_types=1);

namespace Netgen\Bundle\EzPlatformSiteApiBundle\View\Redirect;

final class RedirectConfiguration
{
    /**
     * @var string
     */
    private $target;

    /**
     * @var array
     */
    private $targetParameters = [];

    /**
     * @var bool
     */
    private $permanent;

    /**
     * @var bool
     */
    private $absolute;

    public function __construct(string $target, array $targetParameters, bool $permanent, bool $absolute)
    {
        $this->target = $target;
        $this->targetParameters = $targetParameters;
        $this->permanent = $permanent;
        $this->absolute = $absolute;
    }

    public static function fromConfigurationArray(array $config): RedirectConfiguration
    {
        $target = $config['target'];
        $targetParameters = $config['target_parameters'];
        $permanent = $config['permanent'];
        $absolute = $config['absolute'];

        return new RedirectConfiguration($target, $targetParameters, $permanent, $absolute);
    }

    /**
     * @return string
     */
    public function getTarget(): string
    {
        return $this->target;
    }

    /**
     * @return array
     */
    public function getTargetParameters(): array
    {
        return $this->targetParameters;
    }

    /**
     * @return bool
     */
    public function isPermanent(): bool
    {
        return $this->permanent;
    }

    /**
     * @return bool
     */
    public function isAbsolute(): bool
    {
        return $this->absolute;
    }
}
