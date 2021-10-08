<?php

namespace Spryker\Glue\Propel\Plugin;

use Propel\Runtime\Connection\ConnectionManagerMasterSlave;
use Propel\Runtime\Propel;
use Propel\Runtime\ServiceContainer\StandardServiceContainer;
use Spryker\Glue\Kernel\AbstractPlugin;
use Spryker\Service\Container\ContainerInterface;
use Spryker\Shared\ApplicationExtension\Dependency\Plugin\ApplicationPluginInterface;

/**
 * @method \Spryker\Zed\Propel\PropelConfig getConfig()
 */
class PropelApplicationPlugin extends AbstractPlugin implements ApplicationPluginInterface
{
    protected const DATA_SOURCE_NAME = 'zed';
    protected const LOAD_DATABASE_MAPS_NAME = 'loadDatabase.php';

    /**
     * {@inheritDoc}
     * - Initializes PropelOrm to be used within Zed.
     *
     * @api
     *
     * @param \Spryker\Service\Container\ContainerInterface $container
     *
     * @return \Spryker\Service\Container\ContainerInterface
     */
    public function provide(ContainerInterface $container): ContainerInterface
    {
        $manager = new ConnectionManagerMasterSlave();
        $manager->setName(static::DATA_SOURCE_NAME);
        $manager->setWriteConfiguration($this->getPropelWriteConfiguration());
        $manager->setReadConfiguration($this->getPropelReadConfiguration());

        $this->registerTableMaps();

        $serviceContainer = $this->getServiceContainer();
        $serviceContainer->setAdapterClass(static::DATA_SOURCE_NAME, $this->getConfig()->getCurrentDatabaseEngine());
        $serviceContainer->setConnectionManager(static::DATA_SOURCE_NAME, $manager);
        $serviceContainer->setDefaultDatasource(static::DATA_SOURCE_NAME);


        if ($this->getConfig()->isDebugEnabled() && $this->hasConnection()) {
            /** @var \Propel\Runtime\Connection\ConnectionWrapper $connection */
            $connection = Propel::getConnection();
            $connection->useDebug(true);
        }

        return $container;
    }

    /**
     * @return \Propel\Runtime\ServiceContainer\StandardServiceContainer
     */
    protected function getServiceContainer(): StandardServiceContainer
    {
        /** @var \Propel\Runtime\ServiceContainer\StandardServiceContainer $serviceContainer */
        $serviceContainer = Propel::getServiceContainer();

        return $serviceContainer;
    }

    /**
     * @return void
     */
    protected function registerTableMaps(): void
    {
        $loadDatabaseMapsPath = $this->getConfig()->getPropelConfig()['paths']['loaderScriptDir'] . DIRECTORY_SEPARATOR . static::LOAD_DATABASE_MAPS_NAME;

        if (file_exists($loadDatabaseMapsPath)) {
            require_once $loadDatabaseMapsPath;
        }
    }

    /**
     * @return bool
     */
    private function hasConnection(): bool
    {
        try {
            Propel::getConnection();

            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * @return array
     */
    private function getPropelWriteConfiguration(): array
    {
        $propelConfig = $this->getConfig()->getPropelConfig()['database']['connections']['default'];
        $propelConfig['user'] = $this->getConfig()->getUsername();
        $propelConfig['password'] = $this->getConfig()->getPassword();
        $propelConfig['dsn'] = $this->getConfig()->getPropelConfig()['database']['connections']['default']['dsn'];

        return $propelConfig;
    }

    /**
     * @return array|null
     */
    private function getPropelReadConfiguration(): ?array
    {
        $propelDefaultConnectionsConfig = $this->getConfig()->getPropelConfig()['database']['connections']['default'];

        return !empty($propelDefaultConnectionsConfig['slaves']) ? $propelDefaultConnectionsConfig['slaves'] : null;
    }
}
