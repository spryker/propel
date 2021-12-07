<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerTest\Shared\Propel\Helper;

use Codeception\Module;
use Spryker\Zed\Propel\Business\Model\PropelGroupedSchemaFinder;
use Spryker\Zed\Propel\Business\Model\PropelGroupedSchemaFinderInterface;
use Spryker\Zed\Propel\Business\Model\PropelSchema;
use Spryker\Zed\Propel\Business\Model\PropelSchemaFinder;
use Spryker\Zed\Propel\Business\Model\PropelSchemaFinderInterface;
use Spryker\Zed\Propel\Business\Model\PropelSchemaInterface;
use Spryker\Zed\Propel\Business\PropelFacade;
use Spryker\Zed\Propel\Communication\Console\BuildModelConsole;
use SprykerTest\Shared\Testify\Helper\ConfigHelperTrait;
use SprykerTest\Zed\Testify\Helper\Business\BusinessHelperTrait;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

class PropelBuildModelHelper extends Module
{
    use ConfigHelperTrait, BusinessHelperTrait;

    /**
     * @var string
     */
    protected const CONFIG_SCHEMA_SOURCE_DIRECTORY_LIST = 'schemaSourceDirectoryList';

    /**
     * @var string
     */
    protected const CONFIG_SCHEMA_TARGET_DIRECTORY = 'schemaTargetDirectory';

    /**
     * @var string
     */
    protected const CONFIG_IS_ISOLATED_MODULE_TEST = 'isolated';

    /**
     * @var string
     */
    protected const PROPEL_MODULE_NAME = 'Propel';

    /**
     * @var string
     */
    protected const PROPEL_MODULE_NAMESPACE = 'Spryker\Zed\Propel';

    /**
     * @var string
     */
    protected const PROPEL_MODULE_NAMESPACE_BUSINESS = self::PROPEL_MODULE_NAMESPACE . '\Business';

    /**
     * @var string
     */
    protected const SCHEMA_FILE_PATTERN = '*.schema.xml';

    /**
     * @var string
     */
    protected const SCHEMA_TARGET_DIRECTORY_DEFAULT = '/src/Orm/Propel/Schema';

    /**
     * @var string
     */
    protected const SCHEMA_SOURCE_DIRECTORY_DEFAULT = 'src/Spryker/Zed/*/Persistence/Propel/Schema';

    /**
     * @var array
     */
    protected $config = [
        self::CONFIG_SCHEMA_SOURCE_DIRECTORY_LIST => [
            self::SCHEMA_SOURCE_DIRECTORY_DEFAULT,
        ],
        self::CONFIG_IS_ISOLATED_MODULE_TEST => false,
    ];

    /**
     * @return void
     */
    public function _initialize($settings = []): void
    {
        if ($this->config[static::CONFIG_IS_ISOLATED_MODULE_TEST]) {
            $this->createMockGetPropelSchemaPathPatterns();
            $this->getFacade()->cleanPropelSchemaDirectory();
            $this->copyPropelSchemasFromDefinedSchemaDirectoryList();
            $this->getFacade()->copySchemaFilesToTargetDirectory();
            $this->runCommand();
        }
    }

    protected function createMockGetPropelSchemaPathPatterns(): void
    {
        $this->getConfigHelper()->mockConfigMethod(
            'getPropelSchemaPathPatterns',
            $this->getPropelSchemaPathPatterns(),
            static::PROPEL_MODULE_NAME,
            static::PROPEL_MODULE_NAMESPACE
        );
    }

    /**
     * @return array<array-key, string>
     */
    protected function getPropelSchemaPathPatterns(): array
    {
        return array_map(function ($schemaPathPattern) {
            return APPLICATION_ROOT_DIR . '../../' . $schemaPathPattern;
        }, $this->config[static::CONFIG_SCHEMA_SOURCE_DIRECTORY_LIST]);
    }

    /**
     * @return void
     */
    protected function runCommand(): void
    {
        $application = new Application();
        $command = new BuildModelConsole();
        $output = new ConsoleOutput();
        $input = new ArrayInput([]);

        $application->addCommands([$command]);
        $application->setDefaultCommand(BuildModelConsole::COMMAND_NAME);
        $application->doRun($input, $output);
    }

    /**
     * @return \Spryker\Zed\Propel\Business\PropelFacade
     */
    private function getFacade(): PropelFacade
    {
        return $this->getBusinessHelper()->getFacade(
            static::PROPEL_MODULE_NAME,
            static::PROPEL_MODULE_NAMESPACE_BUSINESS,
        );
    }

    /**
     * This will copy all propel schema files from defined schema directories in the codeception.yml file
     * of the module under test.
     *
     * @return void
     * @example codeception.yml
     *
     * ```
     * env:
     *   isolated: # (environment name which will be used on CLI with `vendor/bin/codecept run --env isolated`)
     *     modules:
     *       config:
     *         \SprykerTest\Shared\Propel\Helper\PropelInstallHelper:
     *           schemaDirectories:
     *             - # path to schema files, relative to the module under test.
     * ```
     *
     */
    private function copyPropelSchemasFromDefinedSchemaDirectoryList(): void
    {
        $finder = $this->createPropelSchemaFinder($this->config[static::CONFIG_SCHEMA_SOURCE_DIRECTORY_LIST]);

        if ($finder->count() > 0) {
            $schemaTargetDirectory = $this->getSchemaTargetDirectory();
            $filesystem = new Filesystem();

            /** @var \Symfony\Component\Finder\SplFileInfo $file */
            foreach ($finder as $file) {
                $path = $schemaTargetDirectory . DIRECTORY_SEPARATOR . $file->getFilename();
                $filesystem->dumpFile($path, $file->getContents());
            }
        }
    }

    /**
     * @param array $schemaSourceDirectoryList
     *
     * @return \Symfony\Component\Finder\Finder
     */
    private function createPropelSchemaFinder(array $schemaSourceDirectoryList): Finder
    {
        $schemaSourceDirectoryList = array_map(function (string $schemaSourceDirectory) {
            if (defined('MODULE_UNDER_TEST_ROOT_DIR') && MODULE_UNDER_TEST_ROOT_DIR !== null) {
                return rtrim(MODULE_UNDER_TEST_ROOT_DIR . $schemaSourceDirectory, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
            }

            return rtrim(APPLICATION_ROOT_DIR, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $schemaSourceDirectory . DIRECTORY_SEPARATOR;
        }, $schemaSourceDirectoryList);


        $finder = new Finder();

        $finder->files()->in($schemaSourceDirectoryList)->name(static::SCHEMA_FILE_PATTERN);

        return $finder;
    }

    /**
     * Getting path to where the files from the bundle to test should be copied to ("virtual project").
     *
     * @return string
     */
    private function getSchemaTargetDirectory(): string
    {
        $schemaTargetDirectory = $this->getSchemaTargetDirectoryDefault();

        if ($this->hasSchemaTargetDirectoryConfigured()) {
            $schemaTargetDirectory = $this->config[static::CONFIG_SCHEMA_TARGET_DIRECTORY];
        }

        $this->createSchemaTargetDirectoryIfNotExists($schemaTargetDirectory);

        return $schemaTargetDirectory;
    }

    /**
     * Getting default path to where the files from bundle to test should be copied ("virtual project").
     *
     * @return string
     */
    private function getSchemaTargetDirectoryDefault(): string
    {
        return rtrim(APPLICATION_ROOT_DIR, '/') . static::SCHEMA_TARGET_DIRECTORY_DEFAULT;
    }

    /**
     * Checking whether default path is overwritten by configuration value.
     *
     * @return bool
     */
    private function hasSchemaTargetDirectoryConfigured(): bool
    {
        return isset($this->config[static::CONFIG_SCHEMA_TARGET_DIRECTORY]);
    }

    /**
     * Checking whether target schema directory exits otehrwise creating it.
     *
     * @param string $schemaTargetDirectory
     *
     * @return void
     */
    private function createSchemaTargetDirectoryIfNotExists(string $schemaTargetDirectory): void
    {
        if (!is_dir($schemaTargetDirectory)) {
            mkdir($schemaTargetDirectory, 0775, true);
        }
    }
}
