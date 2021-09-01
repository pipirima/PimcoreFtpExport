<?php

namespace Pipirima\PimcoreFtpExportBundle\Service;

use Pimcore\Log\Simple;
use Pipirima\PimcoreFtpExportBundle\PimcoreFtpExportBundle;

class Logger
{
    protected $debug;

    public function __construct(bool $debug = true)
    {
        $this->debug = $debug;
    }

    public function log(string $message)
    {
        if (!$this->debug) {
            return;
        }

        Simple::log(PimcoreFtpExportBundle::BUNDLE_CODE, $message);
    }
}
