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
        if ($asset instanceof Asset\Folder) {
            return;
        }
        $config = $this->configService->getConfig();
        $this->logger->log("asset path: " . $asset->getFullPath());
        $this->logger->log("config: " . print_r($config, true));

        foreach ($config as $item) {
            $this->processFtpExportItem($asset, $item);
        }
    }

    /**
     * @param Asset $asset
     * @param array $config
     * @throws FtpException
     */
    public function processFtpExportItem(Asset $asset, array $config)
    {
        $this->logger->log(" === ITEM ===");
        // asset
        $assetFilename = $asset->getFilename();
        $assetPath = $asset->getPath();
        $this->logger->log("assetFilename: " . $assetFilename . ", assetPath: " . $assetPath);

        if ($asset instanceof Asset\Folder) {
            $this->logger->log(" - asset is a folder - skipping");
            return;
        }

        // config
        $this->logger->log("config: " . print_r($config, true));
        if (isset($config['active']) && !$config['active']) {
            $this->logger->log(" - inactive - skipping");
            return;
        }

        $exportName = $config['name'] ?? "";
        $this->logger->log(" - name: " . $exportName);

        $localFolder = $config['local_folder'] ?? false;
        if (false === $localFolder) {
            $this->logger->log(" - local_folder - NOT SET - skipping");
            return;
        }

        if ($assetPath !== $localFolder) {
            $this->logger->log(" - NOT EQUAL: local_folder: '$localFolder', asset path: '$assetPath' - skipping");
            return;
        }

        $remoteFilename = $config['remote_filename'] ?? $assetFilename;
        $this->logger->log(" - remote filename: $remoteFilename");

        if (!isset($config['ftp'])) {
            $this->logger->log(" - NO 'ftp' settings - skipping");
            return;
        }

        $ftp = $config['ftp'];
        if (!isset($ftp['server'])) {
            $this->logger->log(" - NO ftp 'server' settings - skipping");
            return;
        }
        if (!isset($ftp['login'])) {
            $this->logger->log(" - NO ftp 'login' settings - skipping");
            return;
        }
        if (!isset($ftp['password'])) {
            $this->logger->log(" - NO ftp 'password' settings - skipping");
            return;
        }

        $ftpServer = $ftp['server'];
        $ftpLogin = $ftp['login'];
        $ftpPassword = $ftp['password'];

        if (!$ftpServer || !$ftpLogin || !$ftpPassword || !$remoteFilename) {
            $this->logger->log(" - NO DATA: ftpServer: $ftpServer, ftpLogin: $ftpLogin, ftpPasswortd: $ftpPassword, remoteFilename: $remoteFilename - skipping");
            return;
        }

        $this->ftpClient->connect($ftpServer);
        $this->ftpClient->login($ftpLogin, $ftpPassword);
        if (isset($ftp['passive'])) {
            $this->logger->log(" - passive is set to: " . ($ftp['passive'] ? 'true' : 'false'));
            $this->ftpClient->pasv($ftp['passive']);
        } else {
            $this->logger->log(" - no passive/active set");
        }

        $mode = FTP_ASCII;
        if (isset($ftp['mode'])) {
            $mode = $ftp['mode'] == 'binary' ? FTP_BINARY : FTP_ASCII;
        }
        $this->logger->log(" - mode is set to: $mode (" . intval(FTP_BINARY) . "-binary, " . intval(FTP_ASCII) . " - ascii");

        $remoteTempFilename = $remoteFilename . '.' . self::REMOTE_TEMP_EXT;

        $localTempFilename = tempnam("/tmp", "abc");
        $handle = fopen($localTempFilename, "w");
        $data = $asset->getData();
        $this->logger->log("asset data sizeof: " . sizeof($data));
        fwrite($handle, $data);
        fclose($handle);

        $this->logger->log("Files: ");
        $this->logger->log(" - local tmp file: " . $localTempFilename);
        $this->logger->log(" - remote tmp file: " . $remoteTempFilename);
        $this->logger->log(" - remote: " . $remoteFilename);

        $this->logger->log("Sending: $localTempFilename to $remoteTempFilename");
        $this->ftpClient->put($remoteTempFilename, $localTempFilename, $mode);
        $this->logger->log("Deleting: $remoteFilename");
        $this->ftpClient->delete($remoteFilename);
        $this->logger->log("Renaming: $remoteTempFilename to $remoteFilename");
        $this->ftpClient->rename($remoteTempFilename, $remoteFilename);

        $this->logger->log("Deleting: $localTempFilename");
        unlink($localTempFilename);
    }
}
