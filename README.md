# ProDevTools Magento2 Query Logger Module

## Overview

The **ProDevTools Query Logger** module for Magento 2 enhances the default query logging functionality by adding the ability to filter logged queries based on table names. This module allows you to have more control over which database queries are logged, making it easier to monitor and troubleshoot specific areas of your application.

## Features

- **Extended Query Logging**: Enhances default query logging capabilities.
- **Table Filtering**: Filters logged queries based on specified table names.
- **Custom Console Command**: Adds a new command to enable query logging with table filters.

## Installation

### 1. Install via `app/code` Directory

1. Clone the repository:

    ```bash
    git clone https://github.com/AlexTs556/magento2-prodevtools-query-logger.git
    ```

2. Copy the module to your Magento installation:

    ```bash
    cp -R magento2-prodevtools-query-logger/ <Magento_Root>/app/code/ProDevTools/QueryLogger/
    ```

3. Enable the module:

    ```bash
    php bin/magento setup:upgrade
    php bin/magento setup:di:compile
    ```

### 2. Install via Composer

1. Add the repository to your `composer.json`:

    ```bash
    composer require prodevtools/magento2-query-logger
    ```

2. Run the following Composer command to install the module:

    ```bash
    composer require prodevtools/magento2-query-logger
    ```

3. Enable the module:

    ```bash
    php bin/magento setup:upgrade
    php bin/magento setup:di:compile
    ```

## Usage

### Enabling Query Logging

To enable query logging with table filters, use the custom console command provided by this module:

```bash
php bin/magento dev:query-log:enable --include-table-filters=table1,table2
```
Replace table1,table2 with a comma-separated list of table names you want to include in the query log.

## Support

If you encounter any issues or need support, please open an issue on the [GitHub repository](https://github.com/AlexTs556/magento2-prodevtools-query-logger/issues).
