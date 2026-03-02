<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerTest\Zed\Propel\Business\Model\Schema\Validator;

use Codeception\Test\Unit;
use Spryker\Service\UtilText\UtilTextService;
use Spryker\Zed\Propel\Business\Model\PropelGroupedSchemaFinder;
use Spryker\Zed\Propel\Business\Model\PropelGroupedSchemaFinderInterface;
use Spryker\Zed\Propel\Business\Model\PropelSchemaFinder;
use Spryker\Zed\Propel\Business\Model\PropelSchemaFinderInterface;
use Spryker\Zed\Propel\Business\Model\Schema\Validator\PropelSchemaValidator;
use Spryker\Zed\Propel\Dependency\Service\PropelToUtilTextServiceBridge;

/**
 * Auto-generated group annotations
 *
 * @group SprykerTest
 * @group Zed
 * @group Propel
 * @group Business
 * @group Model
 * @group Schema
 * @group Validator
 * @group PropelSchemaValidatorTest
 * Add your own group annotations below this line
 */
class PropelSchemaValidatorTest extends Unit
{
    public function testValidateReturnsTransferWithoutErrorsWhenNoAttributeValueChangeIsDetected(): void
    {
        $groupedSchemaFinder = $this->getGroupedSchemaFinder($this->getSchemaFinderForValidCase());
        $propelSchemaValidator = new PropelSchemaValidator($groupedSchemaFinder, $this->getUtilTextService());

        $schemaValidationTransfer = $propelSchemaValidator->validate();

        $this->assertTrue($schemaValidationTransfer->getIsSuccess());
    }

    public function testValidateReturnsTransferWithErrorsWhenAttributeValueChangeIsDetected(): void
    {
        $groupedSchemaFinder = $this->getGroupedSchemaFinder($this->getSchemaFinderForInvalidCase());
        $propelSchemaValidator = new PropelSchemaValidator($groupedSchemaFinder, $this->getUtilTextService());
        $schemaValidationTransfer = $propelSchemaValidator->validate();

        $this->assertFalse($schemaValidationTransfer->getIsSuccess());
    }

    public function testValidateReturnsTransferWithoutErrorsWhenAttributeValueChangeIsDetectedButWhitelisted(): void
    {
        $groupedSchemaFinder = $this->getGroupedSchemaFinder($this->getSchemaFinderForInvalidCase());
        $propelSchemaValidator = new PropelSchemaValidator($groupedSchemaFinder, $this->getUtilTextService(), ['foo_bar.schema.xml' => ['type']]);
        $schemaValidationTransfer = $propelSchemaValidator->validate();

        $this->assertTrue($schemaValidationTransfer->getIsSuccess());
    }

    protected function getGroupedSchemaFinder(PropelSchemaFinderInterface $innerFinder): PropelGroupedSchemaFinderInterface
    {
        return new PropelGroupedSchemaFinder($innerFinder);
    }

    protected function getSchemaFinderForValidCase(): PropelSchemaFinderInterface
    {
        $schemaFinder = new PropelSchemaFinder([
            __DIR__ . '/Fixtures/Valid/*/',
        ]);

        return $schemaFinder;
    }

    protected function getSchemaFinderForInvalidCase(): PropelSchemaFinderInterface
    {
        $schemaFinder = new PropelSchemaFinder([
            __DIR__ . '/Fixtures/Invalid/*/',
        ]);

        return $schemaFinder;
    }

    protected function getUtilTextService(): PropelToUtilTextServiceBridge
    {
        return new PropelToUtilTextServiceBridge(new UtilTextService());
    }
}
