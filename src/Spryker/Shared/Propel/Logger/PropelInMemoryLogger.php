<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Shared\Propel\Logger;

class PropelInMemoryLogger implements PropelInMemoryLoggerInterface
{
    /**
     * @var string
     */
    protected const KEY_SQL = 'sql';

    /**
     * @var string
     */
    protected const KEY_TIMESTAMP = 'timestamp';

    /**
     * @var array<int, array<string, mixed>>
     */
    protected static array $logs = [];

    /**
     * @var string|null
     */
    protected static ?string $currentSegmentKey = null;

    /**
     * @var array<string, array<int, array<string, mixed>>>
     */
    protected static array $segmentedLogs = [];

    /**
     * @param \Stringable|string $message
     * @param array<string, mixed> $context
     *
     * @return void
     */
    public function info($message, array $context = []): void
    {
        $logEntry = [
            static::KEY_SQL => (string)$message,
            static::KEY_TIMESTAMP => microtime(true),
        ];

        if (static::$currentSegmentKey !== null) {
            static::$segmentedLogs[static::$currentSegmentKey][] = $logEntry;

            return;
        }

        static::$logs[] = $logEntry;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function getLogs(): array
    {
        return static::$logs;
    }

    /**
     * @return void
     */
    public function resetLogs(): void
    {
        static::$logs = [];
    }

    /**
     * @param string $key
     *
     * @return void
     */
    public static function startSegment(string $key): void
    {
        static::$currentSegmentKey = $key;

        if (!isset(static::$segmentedLogs[$key])) {
            static::$segmentedLogs[$key] = [];
        }
    }

    /**
     * @return void
     */
    public static function endSegment(): void
    {
        static::$currentSegmentKey = null;
    }

    /**
     * @return array<string, array<int, array<string, mixed>>>
     */
    public function getSegmentedLogs(): array
    {
        return static::$segmentedLogs;
    }

    /**
     * @return void
     */
    public function resetSegmentedLogs(): void
    {
        static::$segmentedLogs = [];
        static::$currentSegmentKey = null;
    }

    /**
     * @param \Stringable|string $message
     * @param array<string, mixed> $context
     *
     * @return void
     */
    public function emergency($message, array $context = []): void
    {
        // do nothing
    }

    /**
     * @param \Stringable|string $message
     * @param array<string, mixed> $context
     *
     * @return void
     */
    public function alert($message, array $context = []): void
    {
        // do nothing
    }

    /**
     * @param \Stringable|string $message
     * @param array<string, mixed> $context
     *
     * @return void
     */
    public function critical($message, array $context = []): void
    {
        // do nothing
    }

    /**
     * @param \Stringable|string $message
     * @param array<string, mixed> $context
     *
     * @return void
     */
    public function error($message, array $context = []): void
    {
        // do nothing
    }

    /**
     * @param \Stringable|string $message
     * @param array<string, mixed> $context
     *
     * @return void
     */
    public function warning($message, array $context = []): void
    {
        // do nothing
    }

    /**
     * @param \Stringable|string $message
     * @param array<string, mixed> $context
     *
     * @return void
     */
    public function notice($message, array $context = []): void
    {
        // do nothing
    }

    /**
     * @param \Stringable|string $message
     * @param array<string, mixed> $context
     *
     * @return void
     */
    public function debug($message, array $context = []): void
    {
        // do nothing
    }

    /**
     * @param mixed $level
     * @param \Stringable|string $message
     * @param array<string, mixed> $context
     *
     * @return void
     */
    public function log($level, $message, array $context = []): void
    {
        // do nothing
    }
}
