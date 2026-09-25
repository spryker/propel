<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Propel\Business\Model\PropelDatabase\Adapter\Sqlite;

use PDO;
use Spryker\Shared\Config\Config;
use Spryker\Shared\Propel\PropelConstants;
use Spryker\Zed\Propel\Business\Model\PropelDatabase\Command\DropDatabaseTablesInterface;

class DropSqliteDatabaseTables implements DropDatabaseTablesInterface
{
    public function dropTables(): void
    {
        $connection = new PDO('sqlite:' . Config::get(PropelConstants::ZED_DB_DATABASE));

        // Only for this throwaway connection: with enforcement on, tables could not be dropped in
        // the arbitrary discovery order below. The runtime connection keeps the foreign_keys = ON
        // set in config_propel.php for MySQL/PostgreSQL parity.
        $connection->exec('PRAGMA foreign_keys = OFF');

        $statement = $connection->query("SELECT name FROM sqlite_master WHERE type = 'table' AND name NOT LIKE 'sqlite_%'");
        if ($statement === false) {
            return;
        }

        $tableNames = $statement->fetchAll(PDO::FETCH_COLUMN);

        foreach ($tableNames as $tableName) {
            $connection->exec(sprintf('DROP TABLE IF EXISTS "%s"', $tableName));
        }
    }
}
