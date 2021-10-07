<?php

namespace Pipirima\PimcoreFtpExportBundle\Service;

use Pipirima\PimcoreFtpExportBundle\PimcoreFtpExportBundle;

/**
 * Class Config
 * @package Pipirima\PimcoreFtpExportBundle\Service
 */
class ConfigService
{
    protected array $config;

    public function __construct(?array $config = [])
    {
        $this->config = array($config);
    }

    /**
     * @return array
     */
    public function getConfig(): array
    {
        return $this->config;
    }
}
