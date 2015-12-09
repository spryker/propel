<?php

/**
 * (c) Spryker Systems GmbH copyright protected
 */

namespace SprykerEngine\Zed\Propel\Communication\Console;

use SprykerEngine\Shared\Config;
use SprykerFeature\Shared\Application\ApplicationConfig;
use SprykerFeature\Zed\Console\Business\Model\Console;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ConvertConfigConsole extends Console
{

    const COMMAND_NAME = 'propel:config:convert';

    /**
     * @return void
     */
    protected function configure()
    {
        $this->setName(self::COMMAND_NAME);
        $this->setDescription('Write Propel2 configuration');

        parent::configure();
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->info('Write propel config');

        $config = [
            'propel' => Config::get(ApplicationConfig::PROPEL),
        ];

        $dsn = Config::get(ApplicationConfig::ZED_DB_ENGINE) . ':host=' . Config::get(ApplicationConfig::ZED_DB_HOST)
            . ';dbname=' . Config::get(ApplicationConfig::ZED_DB_DATABASE);

        $config['propel']['database']['connections']['default']['dsn'] = $dsn;
        $config['propel']['database']['connections']['default']['user'] = Config::get(ApplicationConfig::ZED_DB_USERNAME);
        $config['propel']['database']['connections']['default']['password'] = Config::get(ApplicationConfig::ZED_DB_PASSWORD);

        $config['propel']['database']['connections']['zed'] = $config['propel']['database']['connections']['default'];

        $json = json_encode($config, JSON_PRETTY_PRINT);

        $fileName = $config['propel']['paths']['phpConfDir']
            . DIRECTORY_SEPARATOR
            . 'propel.json';

        if (!is_dir(dirname($fileName))) {
            mkdir(dirname($fileName), 0777, true);
        }

        file_put_contents($fileName, $json);
    }

}
