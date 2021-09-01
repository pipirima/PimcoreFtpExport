<?php

namespace Pipirima\PimcoreFtpExportBundle\EventHandler;

use Pimcore\Event\Model\AssetEvent;
use Pimcore\Event\Model\ElementEventInterface;
use Pipirima\PimcoreFtpExportBundle\Service\FtpExportService;

class AssetEventHandler
{
    protected FtpExportService $ftpExportService;

    public function __construct(FtpExportService $ftpExportService)
    {
        $this->ftpExportService = $ftpExportService;
    }

    /**
     * @param ElementEventInterface $event
     * @throws \FtpClient\FtpException
     */
    public function onAssetPostAdd(ElementEventInterface $event)
    {
        /** AssetEvent $event */
        if (!$event instanceof AssetEvent) {
            return;
        }

        $asset = $event->getAsset();
        $this->ftpExportService->processFtpExport($asset);
    }
}
