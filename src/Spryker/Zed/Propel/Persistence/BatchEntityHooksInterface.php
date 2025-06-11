<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Spryker\Zed\Propel\Persistence;

interface BatchEntityHooksInterface
{
    /**
     * @return void
     */
    public function batchPreSaveHook(): void;

    /**
     * @return void
     */
    public function batchPostSaveHook(): void;
}
