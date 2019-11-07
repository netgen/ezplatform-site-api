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
    private $permanent = false;

    /**
     * RedirectConfiguration constructor.
     *
     * @param string $target
     * @param array $targetParameters
     * @param bool $permanent
     */
    public function __construct(string $target, array $targetParameters, bool $permanent)
    {
        $this->target = $target;
        $this->targetParameters = $targetParameters;
        $this->permanent = $permanent;
    }

    public static function fromConfigurationArray($config): RedirectConfiguration
    {
        $target = $config['target'];
        $targetParameters = $config['target_parameters'];
        $permanent = $config['permanent'];

        return new RedirectConfiguration($target, $targetParameters, $permanent);
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
}
