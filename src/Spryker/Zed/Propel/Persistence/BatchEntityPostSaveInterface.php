<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Spryker\Zed\Propel\Persistence;

use Propel\Runtime\ActiveRecord\ActiveRecordInterface;

interface BatchEntityPostSaveInterface
{
    public function sharedPersist(ActiveRecordInterface $entity): void;

    public function recursiveCommit(): bool;

    public function batchPostSave(): void;
}
