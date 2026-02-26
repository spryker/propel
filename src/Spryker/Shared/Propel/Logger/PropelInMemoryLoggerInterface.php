<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Shared\Propel\Logger;

use Psr\Log\LoggerInterface;

interface PropelInMemoryLoggerInterface extends LoggerInterface
{
    /**
     * Specification:
     * - Returns all captured SQL log entries.
     * - Each entry contains the SQL string and execution timestamp.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getLogs(): array;

    /**
     * Specification:
     * - Resets the captured SQL log entries.
     */
    public function resetLogs(): void;

    /**
     * Specification:
     * - Returns all captured segmented SQL log entries.
     * - Segmented logs are organized by segment key.
     * - Each segment contains an array of log entries with SQL string and execution timestamp.
     *
     * @return array<string, array<int, array<string, mixed>>>
     */
    public function getSegmentedLogs(): array;

    /**
     * Specification:
     * - Resets the captured segmented SQL log entries.
     * - Clears the current segment key.
     */
    public function resetSegmentedLogs(): void;
}
