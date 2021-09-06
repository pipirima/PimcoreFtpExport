# PimcoreFtpExport
Automatically exports new assets from given directory into FTP server

## Description

This bundle allows defining arbitrary number of FTP exports of newly created Pimcore assets.
This is done by listening to Pimcore events.

## Installation

```shell
composer require pipirima/pimcore-ftp-export
```

Then either you can add the bundle into Pimcore kernel or call:

```shell
bin/console pimcore:bundle:enable PimcoreFtpExportBundle
```

## Usage

Add YAML configuration. See the example file with comments:

```shell
src/Resources/config/pimcore_ftp_export_example.yml
```

