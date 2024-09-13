<?php

declare(strict_types=1);

namespace ProDevTools\QueryLogger\Console\Command;

use Magento\Framework\App\DeploymentConfig\Writer;
use Magento\Framework\Config\File\ConfigFilePool;
use Magento\Framework\DB\Logger\LoggerProxy;
use Magento\Framework\Exception\FileSystemException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Magento\Developer\Console\Command\QueryLogEnableCommand as CoreQueryLogEnableCommand;

class QueryLogEnableCommand extends CoreQueryLogEnableCommand
{
    public const INPUT_ARG_TABLE_FILTERS = 'include-table-filters';

    public const PARAM_TABLE_FILTERS = 'table_filters';

    /**
     * Constructor.
     *
     * @param Writer $deployConfigWriter
     * @param string|null $name
     */
    public function __construct(
        private readonly Writer $deployConfigWriter,
        ?string $name = null
    ) {
        parent::__construct($deployConfigWriter, $name);
    }

    /**
     * @inheritdoc
     */
    protected function configure(): void
    {
        parent::configure();

        // Adding a new input option for table filters
        $this->addOption(
            self::INPUT_ARG_TABLE_FILTERS,
            null,
            InputOption::VALUE_OPTIONAL,
            'Include Table Filters. [comma-separated: dev:query-log:enable --include-table-filters=table1,table2]',
            ""
        );
    }

    /**
     * Executes the command.
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // Retrieve table filters option from input
        $tableFilters = $input->getOption(self::INPUT_ARG_TABLE_FILTERS);

        // Prepare the data to be saved in the configuration
        $configGroup = [
            LoggerProxy::CONF_GROUP_NAME => [
                self::PARAM_TABLE_FILTERS => $tableFilters
            ]
        ];

        // Attempt to save the configuration
        try {
            $this->deployConfigWriter->saveConfig([ConfigFilePool::APP_ENV => $configGroup]);
        } catch (FileSystemException $e) {
            // Log error and return a failure code
            $output->writeln("<error>Failed to save configuration: {$e->getMessage()}</error>");
            return 1;
        }

        // Proceed with the parent class's execute logic
        return parent::execute($input, $output);
    }
}
