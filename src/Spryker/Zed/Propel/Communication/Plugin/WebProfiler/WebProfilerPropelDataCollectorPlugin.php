<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Propel\Communication\Plugin\WebProfiler;

use Spryker\Service\Container\ContainerInterface;
use Spryker\Shared\WebProfilerExtension\Dependency\Plugin\WebProfilerDataCollectorPluginInterface;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;
use Symfony\Component\HttpKernel\DataCollector\DataCollectorInterface;

/**
 * @method \Spryker\Zed\Propel\Communication\PropelCommunicationFactory getFactory()
 * @method \Spryker\Zed\Propel\PropelConfig getConfig()
 */
class WebProfilerPropelDataCollectorPlugin extends AbstractPlugin implements WebProfilerDataCollectorPluginInterface
{
    protected const string DATA_COLLECTOR_NAME = 'propel';

    protected const string DATA_TEMPLATE_NAME = '@Propel/Collector/propel';

    /**
     * {@inheritDoc}
     *
     * @api
     */
    public function getName(): string
    {
        return static::DATA_COLLECTOR_NAME;
    }

    /**
     * {@inheritDoc}
     *
     * @api
     */
    public function getTemplateName(): string
    {
        return static::DATA_TEMPLATE_NAME;
    }

    /**
     * {@inheritDoc}
     * - Returns a Propel data collector that captures all SQL queries executed during a request.
     *
     * @api
     */
    public function getDataCollector(ContainerInterface $container): DataCollectorInterface
    {
        return $this->getFactory()->createPropelDataCollector();
    }
}
