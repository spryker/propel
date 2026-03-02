<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerTest\Zed\Propel\Business\Model;

use Codeception\Test\Unit;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Auto-generated group annotations
 *
 * @group SprykerTest
 * @group Zed
 * @group Propel
 * @group Business
 * @group Model
 * @group AbstractPropelSchemaTest
 * Add your own group annotations below this line
 */
abstract class AbstractPropelSchemaTest extends Unit
{
    public function setUp(): void
    {
        parent::setUp();

        mkdir($this->getFixtureDirectory());
        touch($this->getFixtureDirectory() . DIRECTORY_SEPARATOR . 'spy_foo.schema.xml');
        touch($this->getFixtureDirectory() . DIRECTORY_SEPARATOR . 'spy_bar.schema.xml');
    }

    public function tearDown(): void
    {
        $filesystem = new Filesystem();
        $filesystem->remove($this->getFixtureDirectory());
    }

    protected function getFixtureDirectory(): string
    {
        return __DIR__ . '/TempFixtures';
    }
}
