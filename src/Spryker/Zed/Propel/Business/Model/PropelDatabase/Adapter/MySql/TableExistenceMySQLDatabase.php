<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Propel\Business\Model\PropelDatabase\Adapter\MySql;

use Propel\Runtime\Propel;
use Spryker\Zed\Propel\Business\Model\PropelDatabase\Command\TableExistenceInterface;
use Spryker\Zed\Propel\PropelConfig;

class TableExistenceMySQLDatabase implements TableExistenceInterface
{
    /**
     * @var \Spryker\Zed\Propel\PropelConfig
     */
    protected $propelConfig;

    public function __construct(PropelConfig $propelConfig)
    {
        $this->propelConfig = $propelConfig;
    }

    public function tableExists(string $tableName): bool
    {
        $connection = Propel::getConnection();
        $query = 'SELECT 1 FROM information_schema.tables WHERE table_name = ? AND table_schema = ?;';

        /** @var \PDOStatement $statement */
        $statement = $connection->prepare($query);
        $statement->execute([
            $tableName,
            $this->propelConfig->getCurrentZedDatabaseName(),
        ]);
        $result = $statement->fetch();

        return (bool)$result;
    }
}
