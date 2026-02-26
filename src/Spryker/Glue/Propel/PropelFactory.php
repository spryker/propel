<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Glue\Propel;

use Spryker\Glue\Kernel\AbstractFactory;
use Spryker\Shared\Propel\DataCollector\PropelDataCollector;
use Spryker\Shared\Propel\Logger\PropelInMemoryLogger;
use Spryker\Shared\Propel\Logger\PropelInMemoryLoggerInterface;
use Symfony\Component\HttpKernel\DataCollector\DataCollectorInterface;

class PropelFactory extends AbstractFactory
{
    public function createPropelDataCollector(): DataCollectorInterface
    {
        return new PropelDataCollector(
            $this->createPropelInMemoryLogger(),
        );
    }

    public function createPropelInMemoryLogger(): PropelInMemoryLoggerInterface
    {
        return new PropelInMemoryLogger();
    }
}
