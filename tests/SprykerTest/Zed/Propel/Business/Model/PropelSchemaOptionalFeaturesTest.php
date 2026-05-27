<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerTest\Zed\Propel\Business\Model;

use Codeception\Test\Unit;
use ReflectionClass;
use RuntimeException;
use Spryker\Shared\Kernel\ClassResolver\AbstractClassResolver as SharedAbstractClassResolver;
use Spryker\Shared\Kernel\ClassResolver\Config\SharedConfigNotFoundException;
use Spryker\Shared\Kernel\ClassResolver\Config\SharedConfigResolver;
use Spryker\Zed\Kernel\ClassResolver\AbstractClassResolver as ZedAbstractClassResolver;
use Spryker\Zed\Kernel\ClassResolver\Config\BundleConfigNotFoundException;
use Spryker\Zed\Kernel\ClassResolver\Config\BundleConfigResolver;
use Spryker\Zed\Propel\Business\Model\PropelGroupedSchemaFinder;
use Spryker\Zed\Propel\Business\Model\PropelSchema;
use Spryker\Zed\Propel\Business\Model\PropelSchemaFinder;
use Spryker\Zed\Propel\Business\Model\PropelSchemaMerger;
use Spryker\Zed\Propel\Business\Model\PropelSchemaMergerInterface;
use Spryker\Zed\Propel\Business\Model\PropelSchemaWriter;
use Spryker\Zed\Propel\Business\SchemaElementFilter\PropelSchemaElementFilter;
use Spryker\Zed\Propel\Dependency\Service\PropelToUtilTextServiceBridge;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\SplFileInfo;

/**
 * Auto-generated group annotations
 *
 * @group SprykerTest
 * @group Zed
 * @group Propel
 * @group Business
 * @group Model
 * @group PropelSchemaOptionalFeaturesTest
 * Add your own group annotations below this line
 */
class PropelSchemaOptionalFeaturesTest extends Unit
{
    protected const string MODULE_NAME = 'TestModule';

    protected const string FEATURE_NAME = 'TestFeature';

    /**
     * @var \SprykerTest\Zed\Propel\PropelBusinessTester
     */
    protected $tester;

    protected string $tempDir;

    protected string $schemaFilePath;

    protected function setUp(): void
    {
        parent::setUp();

        // PropelSchemaFinder uses depth < 2, so the optional file must be at depth 1 from the search root.
        // Using the full path so that SplFileInfo::getRealPath() contains /Zed/Module/Persistence/Propel/Schema/Feature/
        // which is what the regex in getGroupedSchemasWithOptionalFeatures() matches against.
        $this->tempDir = sprintf(
            '%s/propel_optional_test_%s/Zed/%s/Persistence/Propel/Schema',
            sys_get_temp_dir(),
            uniqid(),
            static::MODULE_NAME,
        );

        $optionalDir = $this->tempDir . '/' . static::FEATURE_NAME;
        mkdir($optionalDir, 0777, true);

        $this->schemaFilePath = $optionalDir . '/test_module.schema.xml';
        file_put_contents($this->schemaFilePath, '<?xml version="1.0"?><database/>');
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        (new Filesystem())->remove(dirname($this->tempDir, 4));
    }

    public function testOptionalSchemaIsKeptWhenOnlyZedConfigExistsAndFeatureIsEnabled(): void
    {
        $schemas = $this->invokeFilter(
            bundleConfigResolver: $this->createBundleConfigResolverReturning($this->createConfigWithMethod(true)),
            sharedConfigResolver: $this->createSharedConfigResolverThrowing(),
        );

        $this->assertCount(1, $schemas);
    }

    public function testOptionalSchemaIsRemovedWhenOnlyZedConfigExistsAndFeatureIsDisabled(): void
    {
        $schemas = $this->invokeFilter(
            bundleConfigResolver: $this->createBundleConfigResolverReturning($this->createConfigWithMethod(false)),
            sharedConfigResolver: $this->createSharedConfigResolverThrowing(),
        );

        $this->assertCount(0, $schemas);
    }

    public function testOptionalSchemaIsKeptWhenOnlySharedConfigExistsAndFeatureIsEnabled(): void
    {
        $schemas = $this->invokeFilter(
            bundleConfigResolver: $this->createBundleConfigResolverThrowing(),
            sharedConfigResolver: $this->createSharedConfigResolverReturning($this->createConfigWithMethod(true)),
        );

        $this->assertCount(1, $schemas);
    }

    public function testOptionalSchemaIsRemovedWhenOnlySharedConfigExistsAndFeatureIsDisabled(): void
    {
        $schemas = $this->invokeFilter(
            bundleConfigResolver: $this->createBundleConfigResolverThrowing(),
            sharedConfigResolver: $this->createSharedConfigResolverReturning($this->createConfigWithMethod(false)),
        );

        $this->assertCount(0, $schemas);
    }

    public function testThrowsWhenBothConfigResolversReturnNothing(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessageMatches('/Config class for module/');

        $this->invokeFilter(
            bundleConfigResolver: $this->createBundleConfigResolverThrowing(),
            sharedConfigResolver: $this->createSharedConfigResolverThrowing(),
        );
    }

    public function testThrowsWhenBothConfigsExistButNeitherHasTheFeatureMethod(): void
    {
        $configWithoutMethod = new class {
        };

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessageMatches('/Config method/');

        $this->invokeFilter(
            bundleConfigResolver: $this->createBundleConfigResolverReturning($configWithoutMethod),
            sharedConfigResolver: $this->createSharedConfigResolverReturning($configWithoutMethod),
        );
    }

    /**
     * @return array<\Symfony\Component\Finder\SplFileInfo>
     */
    protected function invokeFilter(
        ZedAbstractClassResolver $bundleConfigResolver,
        SharedAbstractClassResolver $sharedConfigResolver,
    ): array {
        $schema = $this->createPropelSchema($bundleConfigResolver, $sharedConfigResolver);

        $fileInfo = new SplFileInfo($this->schemaFilePath, static::FEATURE_NAME, static::FEATURE_NAME . '/test_module.schema.xml');

        $method = (new ReflectionClass($schema))->getMethod('getGroupedSchemasWithOptionalFeatures');
        $method->setAccessible(true);

        return $method->invoke($schema, [$fileInfo]);
    }

    protected function createConfigWithMethod(bool $featureEnabled): object
    {
        return new class ($featureEnabled) {
            public function __construct(private bool $enabled)
            {
            }

            public function isTestFeatureEnabled(): bool
            {
                return $this->enabled;
            }
        };
    }

    protected function createBundleConfigResolverReturning(object $config): ZedAbstractClassResolver
    {
        $mock = $this->createMock(BundleConfigResolver::class);
        $mock->method('resolve')->willReturn($config);

        return $mock;
    }

    protected function createBundleConfigResolverThrowing(): ZedAbstractClassResolver
    {
        $mock = $this->createMock(BundleConfigResolver::class);
        $mock->method('resolve')->willThrowException($this->createMock(BundleConfigNotFoundException::class));

        return $mock;
    }

    protected function createSharedConfigResolverReturning(object $config): SharedAbstractClassResolver
    {
        $mock = $this->createMock(SharedConfigResolver::class);
        $mock->method('resolve')->willReturn($config);

        return $mock;
    }

    protected function createSharedConfigResolverThrowing(): SharedAbstractClassResolver
    {
        $mock = $this->createMock(SharedConfigResolver::class);
        $mock->method('resolve')->willThrowException($this->createMock(SharedConfigNotFoundException::class));

        return $mock;
    }

    protected function createPropelSchema(
        ZedAbstractClassResolver $bundleConfigResolver,
        SharedAbstractClassResolver $sharedConfigResolver,
    ): PropelSchema {
        $finder = new PropelSchemaFinder([$this->tempDir]);
        $groupedFinder = new PropelGroupedSchemaFinder($finder);
        $writer = new PropelSchemaWriter(new Filesystem(), sys_get_temp_dir());

        return new PropelSchema($groupedFinder, $writer, $this->createPropelSchemaMerger(), $bundleConfigResolver, $sharedConfigResolver);
    }

    protected function createPropelSchemaMerger(): PropelSchemaMergerInterface
    {
        return new PropelSchemaMerger(
            new PropelToUtilTextServiceBridge(
                $this->tester->getLocator()->utilText()->service(),
            ),
            new PropelSchemaElementFilter([]),
        );
    }
}
