<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerTest\Shared\Propel\Helper;

use Codeception\Lib\ModuleContainer;
use Codeception\Module;
use Codeception\TestInterface;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\Map\RelationMap;
use Spryker\Zed\Propel\Business\PropelFacade;
use Spryker\Zed\Propel\Communication\Console\BuildModelConsole;
use Spryker\Zed\Propel\Communication\Console\BuildSqlConsole;
use Spryker\Zed\Propel\Communication\Console\ConvertConfigConsole;
use Spryker\Zed\Propel\Communication\Console\CreateDatabaseConsole;
use Spryker\Zed\Propel\Communication\Console\DiffConsole;
use Spryker\Zed\Propel\Communication\Console\InsertSqlConsole;
use Spryker\Zed\Propel\Communication\Console\MigrateConsole;
use Spryker\Zed\Propel\Communication\Console\MigrationCheckConsole;
use Spryker\Zed\Propel\Communication\Console\PostgresqlCompatibilityConsole;
use Spryker\Zed\Propel\Communication\Console\PropelInstallConsole;
use Spryker\Zed\Propel\Communication\Console\SchemaCopyConsole;
use SprykerTest\Zed\Testify\Helper\Business\DependencyProviderHelper;
use SprykerTest\Zed\Testify\Helper\Business\DependencyProviderHelperTrait;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\ConsoleOutput;
use \Spryker\Zed\Console\ConsoleDependencyProvider;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

class PropelDiffHelper extends Module
{
    /**
     * @var string
     */
    protected const CONFIG_IS_ISOLATED_MODULE_TEST = 'isolated';

    /**
     * @return void
     */
    public function _beforeSuite($settings = []): void
    {
        if ($this->config[static::CONFIG_IS_ISOLATED_MODULE_TEST]) {
            $this->runCommand();
        }
    }

    /**
     * @return void
     */
    protected function runCommand(): void
    {
        $application = new Application();
        $command = new DiffConsole();
        $output = new ConsoleOutput();
        $input = new ArrayInput([]);

        $application->addCommands([$command]);
        $application->setDefaultCommand(DiffConsole::COMMAND_NAME);
        $application->doRun($input, $output);
    }
}
