<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Shared\Propel\DataCollector;

use Spryker\Shared\Propel\Logger\PropelInMemoryLoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;
use Throwable;

class PropelDataCollector extends DataCollector
{
    protected const string COLLECTOR_NAME = 'propel';

    protected const string KEY_QUERIES = 'queries';

    protected const string KEY_QUERY_COUNT = 'queryCount';

    protected const string KEY_UNIQUE_QUERY_COUNT = 'uniqueQueryCount';

    protected const string KEY_SQL = 'sql';

    protected const string KEY_COUNT = 'count';

    protected const string KEY_TIMESTAMPS = 'timestamps';

    protected const string KEY_SEGMENTED_QUERIES = 'segmentedQueries';

    public function __construct(protected PropelInMemoryLoggerInterface $propelLogger)
    {
    }

    public function collect(Request $request, Response $response, ?Throwable $exception = null): void
    {
        $logs = $this->propelLogger->getLogs();
        $grouped = $this->groupQueries($logs);

        $this->data[static::KEY_QUERIES] = $this->sortByCountDescending($grouped);
        $this->data[static::KEY_QUERY_COUNT] = count($logs);
        $this->data[static::KEY_UNIQUE_QUERY_COUNT] = count($grouped);

        $segmentedLogs = $this->propelLogger->getSegmentedLogs();
        $this->data[static::KEY_SEGMENTED_QUERIES] = $this->processSegmentedLogs($segmentedLogs);
    }

    public function getName(): string
    {
        return static::COLLECTOR_NAME;
    }

    public function reset(): void
    {
        $this->data = [];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function getQueries(): array
    {
        return $this->data[static::KEY_QUERIES] ?? [];
    }

    public function getQueryCount(): int
    {
        return $this->data[static::KEY_QUERY_COUNT] ?? 0;
    }

    public function getUniqueQueryCount(): int
    {
        return $this->data[static::KEY_UNIQUE_QUERY_COUNT] ?? 0;
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    public function getSegmentedQueries(): array
    {
        return $this->data[static::KEY_SEGMENTED_QUERIES] ?? [];
    }

    public function getTotalQueryCount(): int
    {
        $mainCount = $this->getQueryCount();
        $segmentedCount = 0;

        foreach ($this->getSegmentedQueries() as $segmentData) {
            $segmentedCount += $segmentData[static::KEY_QUERY_COUNT] ?? 0;
        }

        return $mainCount + $segmentedCount;
    }

    public function getTotalUniqueQueryCount(): int
    {
        $allQueries = [];

        foreach ($this->getQueries() as $query) {
            $hash = md5($query[static::KEY_SQL]);
            $allQueries[$hash] = true;
        }

        foreach ($this->getSegmentedQueries() as $segmentData) {
            foreach ($segmentData[static::KEY_QUERIES] ?? [] as $query) {
                $hash = md5($query[static::KEY_SQL]);
                $allQueries[$hash] = true;
            }
        }

        return count($allQueries);
    }

    /**
     * @param array<int, array<string, mixed>> $logs
     *
     * @return array<string, array<string, mixed>>
     */
    protected function groupQueries(array $logs): array
    {
        $grouped = [];

        foreach ($logs as $log) {
            $sql = $log[static::KEY_SQL];
            $hash = md5($sql);

            if (!isset($grouped[$hash])) {
                $grouped[$hash] = [
                    static::KEY_SQL => $sql,
                    static::KEY_COUNT => 0,
                    static::KEY_TIMESTAMPS => [],
                ];
            }

            $grouped[$hash][static::KEY_COUNT]++;
            $grouped[$hash][static::KEY_TIMESTAMPS][] = $log['timestamp'];
        }

        return $grouped;
    }

    /**
     * @param array<string, array<string, mixed>> $grouped
     *
     * @return array<int, array<string, mixed>>
     */
    protected function sortByCountDescending(array $grouped): array
    {
        $queries = array_values($grouped);

        usort($queries, function (array $a, array $b): int {
            return $b[static::KEY_COUNT] <=> $a[static::KEY_COUNT];
        });

        return $queries;
    }

    /**
     * @param array<string, array<int, array<string, mixed>>> $segmentedLogs
     *
     * @return array<string, array<string, mixed>>
     */
    protected function processSegmentedLogs(array $segmentedLogs): array
    {
        $processed = [];

        foreach ($segmentedLogs as $key => $logs) {
            $grouped = $this->groupQueries($logs);

            $processed[$key] = [
                static::KEY_QUERIES => $this->sortByCountDescending($grouped),
                static::KEY_QUERY_COUNT => count($logs),
                static::KEY_UNIQUE_QUERY_COUNT => count($grouped),
            ];
        }

        return $processed;
    }
}
