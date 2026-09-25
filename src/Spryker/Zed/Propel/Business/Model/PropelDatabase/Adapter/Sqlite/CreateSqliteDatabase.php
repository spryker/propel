<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Propel\Business\Model\PropelDatabase\Adapter\Sqlite;

use PDO;
use RuntimeException;
use Spryker\Shared\Config\Config;
use Spryker\Shared\Propel\PropelConstants;
use Spryker\Zed\Propel\Business\Model\PropelDatabase\Command\CreateDatabaseInterface;

class CreateSqliteDatabase implements CreateDatabaseInterface
{
    protected const int DIRECTORY_PERMISSIONS = 0775;

    /**
     * @throws \RuntimeException
     *
     * @return void
     */
    public function createIfNotExists()
    {
        $databaseFilePath = (string)Config::get(PropelConstants::ZED_DB_DATABASE);

        $directory = dirname($databaseFilePath);
        // The second is_dir() covers a concurrent creator: parallel suites share one database path.
        if (!is_dir($directory) && !mkdir($directory, static::DIRECTORY_PERMISSIONS, true) && !is_dir($directory)) {
            throw new RuntimeException(sprintf('Could not create the SQLite database directory "%s".', $directory));
        }

        // Opening the connection creates the database file when it does not exist yet.
        new PDO('sqlite:' . $databaseFilePath);
    }
}
