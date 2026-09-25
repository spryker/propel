<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Propel\Business\Model\PropelDatabase\Adapter\Sqlite;

use PDO;
use Spryker\Shared\Config\Config;
use Spryker\Shared\Propel\PropelConstants;
use Spryker\Zed\Propel\Business\Model\PropelDatabase\Command\TableExistenceInterface;

class TableExistenceSqliteDatabase implements TableExistenceInterface
{
    public function tableExists(string $tableName): bool
    {
        $connection = new PDO('sqlite:' . Config::get(PropelConstants::ZED_DB_DATABASE));

        $statement = $connection->prepare("SELECT COUNT(*) FROM sqlite_master WHERE type = 'table' AND name = ?");
        $statement->execute([$tableName]);

        return (bool)$statement->fetchColumn();
    }
}
