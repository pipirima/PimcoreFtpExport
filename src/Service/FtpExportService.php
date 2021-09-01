<?php

namespace Pipirima\PimcoreFtpExportBundle\Service;

use FtpClient\FtpClient;
use FtpClient\FtpException;
use Pimcore\Model\Asset;

class FtpExportService
{
    private const WS_FTP_SERVER = 'ftp_export_server';
    private const WS_FTP_USER = 'ftp_export_user';
    private const WS_FTP_PASS = 'ftp_export_pass';
    private const WS_FTP_LOCAL_FOLDER = 'ftp_export_local_folder';
    private const WS_FTP_REMOTE_FILE = 'ftp_export_filename';

    private const REMOTE_TEMP_EXT = 'tra';
    private const REMOTE_TARGET_EXT = 'csv';

    private FtpClient $ftpClient;

    private ConfigService $configService;

    private Logger $logger;

    public function __construct(FtpClient $ftpClient, ConfigService $configService, Logger $logger)
    {
        $this->ftpClient = $ftpClient;
        $this->configService = $configService;
        $this->logger = $logger;
    }

    /**
     * @param Asset $asset
     * @throws FtpException
     */
    public function processFtpExport(Asset $asset)
    {
        $this->logger->log("asset path: " . $asset->getFullPath());
        $this->logger->log("config: " . print_r($this->configService->getConfig()));
        return;

        if ($asset instanceof Asset\Folder) {
            return;
        }

        $localFolder = $this->websiteSettingService->getAssetValue(self::WS_FTP_LOCAL_FOLDER);
        if (!$localFolder instanceof Asset\Folder) {
            return;
        }

        if ($localFolder->getId() != $asset->getParentId()) {
            return;
        }

        $ftpServer = $this->websiteSettingService->getStringValue(self::WS_FTP_SERVER, '');
        $ftpUser = $this->websiteSettingService->getStringValue(self::WS_FTP_USER, '');
        $ftpPass = $this->websiteSettingService->getStringValue(self::WS_FTP_PASS, '');
        $filename = $this->websiteSettingService->getStringValue(self::WS_FTP_REMOTE_FILE, '');

        if (!$ftpServer || !$ftpUser || !$ftpPass || !$filename) {
            return;
        }

        $this->ftpClient->connect($ftpServer);
        $this->ftpClient->login($ftpUser, $ftpPass);
        $this->ftpClient->pasv(true);
        $tempFilename = $filename . '.' . self::REMOTE_TEMP_EXT;
        $targetFilename = $filename . '.' . self::REMOTE_TARGET_EXT;
        $assetPath = $asset->getFileSystemPath();
        $this->ftpClient->put($tempFilename, $assetPath, FTP_ASCII);
        $this->ftpClient->delete($targetFilename);
        $this->ftpClient->rename($tempFilename, $targetFilename);
    }
}