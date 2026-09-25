<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Propel\Business\Model\PropelDatabase\Adapter\Sqlite;

use RuntimeException;
use Spryker\Shared\Config\Config;
use Spryker\Shared\Propel\PropelConstants;
use Spryker\Zed\Propel\Business\Model\PropelDatabase\Command\ExportDatabaseInterface;

class ExportSqliteDatabase implements ExportDatabaseInterface
{
    /**
     * @param string $backupPath
     *
     * @throws \RuntimeException
     */
    public function exportDatabase($backupPath): void
    {
        if (!copy((string)Config::get(PropelConstants::ZED_DB_DATABASE), $backupPath)) {
            throw new RuntimeException(sprintf('Could not copy the SQLite database to "%s".', $backupPath));
        }
    }
}
