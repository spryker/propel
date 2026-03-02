<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerTest\Zed\Propel\Business;

use Codeception\Test\Unit;
use Spryker\Zed\Propel\Business\Exception\UnSupportedDatabaseEngineException;
use Spryker\Zed\Propel\PropelConfig;

/**
 * Auto-generated group annotations
 *
 * @group SprykerTest
 * @group Zed
 * @group Propel
 * @group Business
 * @group PropelConfigTest
 * Add your own group annotations below this line
 */
class PropelConfigTest extends Unit
{
    private function getConfig(): PropelConfig
    {
        return new PropelConfig();
    }

    public function testGetGeneratedDirectoryShouldReturnPathToGeneratedFiles(): void
    {
        $this->assertTrue(is_dir($this->getConfig()->getGeneratedDirectory()));
    }

    public function getPropelConfig(): void
    {
        $this->assertIsArray($this->getConfig()->getPropelConfig());
    }

    public function testGetSchemaDirectoryShouldReturnPathToSchemas(): void
    {
        $this->assertTrue(is_dir($this->getConfig()->getSchemaDirectory()));
    }

    public function testGetPropelSchemaPathPatterShouldReturnArrayWithPatternsToSchemaDirectories(): void
    {
        $pathPatterns = $this->getConfig()->getPropelSchemaPathPatterns();
        $this->assertTrue(is_array($pathPatterns));
    }

    public function testGetLogPath(): void
    {
        $this->assertIsString($this->getConfig()->getLogPath());
    }

    public function testGetCurrentDatabaseEngine(): void
    {
        $this->assertIsString($this->getConfig()->getCurrentDatabaseEngine());
    }

    public function testGetCurrentDatabaseEngineName(): void
    {
        $this->assertIsString($this->getConfig()->getCurrentDatabaseEngineName());
    }

    public function testGetCurrentDatabaseEngineNameThrowsException(): void
    {
        $propelConfigMock = $this->getPropelConfigMock();

        $this->expectException(UnSupportedDatabaseEngineException::class);
        $propelConfigMock->getCurrentDatabaseEngineName();
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject|\Spryker\Zed\Propel\PropelConfig
     */
    protected function getPropelConfigMock(): PropelConfig
    {
        $propelConfigMock = $this->getMockBuilder(PropelConfig::class)->onlyMethods(['getCurrentDatabaseEngine'])->getMock();
        $propelConfigMock->expects($this->once())->method('getCurrentDatabaseEngine')->willReturn('Un supported database engine');

        return $propelConfigMock;
    }
}
