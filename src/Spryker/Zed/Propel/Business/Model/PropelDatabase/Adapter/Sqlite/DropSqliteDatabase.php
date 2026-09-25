<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Propel\Business\Model\PropelDatabase\Adapter\Sqlite;

use Spryker\Shared\Config\Config;
use Spryker\Shared\Propel\PropelConstants;
use Spryker\Zed\Propel\Business\Model\PropelDatabase\Command\DropDatabaseInterface;

class DropSqliteDatabase implements DropDatabaseInterface
{
    /**
     * @return void
     */
    public function dropDatabase()
    {
        $databaseFilePath = (string)Config::get(PropelConstants::ZED_DB_DATABASE);

        if (file_exists($databaseFilePath)) {
            unlink($databaseFilePath);
        }
    }
}
