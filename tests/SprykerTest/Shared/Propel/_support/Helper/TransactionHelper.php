<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerTest\Shared\Propel\Helper;

use Codeception\Module;
use Codeception\TestInterface;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Propel;
use ReflectionClass;
use ReflectionMethod;
use Silex\Application;
use Spryker\Service\Container\Container;
use Spryker\Zed\Propel\Communication\Plugin\Application\PropelApplicationPlugin;
use Spryker\Zed\Propel\Communication\Plugin\ServiceProvider\PropelServiceProvider;
use Throwable;

class TransactionHelper extends Module
{
    public function _initialize(): void
    {
        Propel::disableInstancePooling();

        if (class_exists(PropelApplicationPlugin::class)) {
            $propelApplicationPlugin = new PropelApplicationPlugin();
            $propelApplicationPlugin->provide(new Container());

            return;
        }

        $this->addBackwardCompatibleServiceProvider();
    }

    /**
     * @deprecated Will be removed in favor of {@link \Spryker\Zed\Propel\Communication\Plugin\Application\PropelApplicationPlugin}.
     *
     * @return void
     */
    protected function addBackwardCompatibleServiceProvider(): void
    {
        $propelServiceProvider = new PropelServiceProvider();
        $propelServiceProvider->boot(new Application());
    }

    public function _before(TestInterface $test): void
    {
        parent::_before($test);

        try {
            $reflectionMethod = new ReflectionMethod($test->getTestCase(), $test->getName());
            $docBlock = $reflectionMethod->getDocComment();

            if (strpos($docBlock, '@disableTransaction') !== false) {
                return;
            }
        } catch (Throwable $throwable) {
        }

        Propel::getWriteConnection('zed')->beginTransaction();
    }

    public function _after(TestInterface $test): void
    {
        parent::_after($test);

        $connection = Propel::getWriteConnection('zed');

        if (!$connection->inTransaction()) {
            // DDL statements (CREATE TABLE / DROP TABLE) in MySQL/MariaDB cause an implicit COMMIT,
            // ending the PDO transaction without Propel's nestedTransactionCount tracking it.
            // Reset the counter so the next test can start a fresh transaction.
            $this->resetNestedTransactionCount($connection);

            return;
        }

        $connection->forceRollBack();
    }

    protected function resetNestedTransactionCount(ConnectionInterface $connection): void
    {
        $reflection = new ReflectionClass($connection);
        $property = $reflection->getProperty('nestedTransactionCount');
        $property->setValue($connection, 0);
    }

    public function _afterSuite(): void
    {
        Propel::closeConnections();
    }
}
