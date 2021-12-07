<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerTest\Shared\Propel\Helper;

use Codeception\Module;
use Spryker\Zed\Propel\Business\PropelFacade;
use Spryker\Zed\Propel\Communication\Console\BuildModelConsole;
use Spryker\Zed\Propel\Communication\Console\MigrateConsole;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

class PropelMigrateHelper extends Module
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
        $command = new MigrateConsole();
        $output = new ConsoleOutput();
        $input = new ArrayInput([]);

        $application->addCommands([$command]);
        $application->setDefaultCommand(MigrateConsole::COMMAND_NAME);
        $application->doRun($input, $output);
    }
}
