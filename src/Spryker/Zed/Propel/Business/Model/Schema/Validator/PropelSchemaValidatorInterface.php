<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Propel\Business\Model\Schema\Validator;

use Generated\Shared\Transfer\SchemaValidationTransfer;

interface PropelSchemaValidatorInterface
{
    public function validate(): SchemaValidationTransfer;
}
