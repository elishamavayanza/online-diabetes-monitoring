<?php

namespace App\Observability;

use Doctrine\DBAL\Driver as DriverInterface;
use Doctrine\DBAL\Driver\Connection;
use Doctrine\DBAL\Driver\Middleware as DriverMiddleware;
use Doctrine\DBAL\Driver\Middleware\AbstractConnectionMiddleware;
use Doctrine\DBAL\Driver\Middleware\AbstractDriverMiddleware;
use Doctrine\DBAL\Driver\Middleware\AbstractStatementMiddleware;
use Doctrine\DBAL\Driver\Result;
use Doctrine\DBAL\Driver\Statement;
use Psr\Log\LoggerInterface;

/**
 * Middleware DBAL qui journalise les requêtes SQL « lentes » (au-delà d'un
 * seuil en millisecondes) sur le canal monolog « performance ».
 *
 * Utile pour détecter les requêtes coûteuses sur la vraie base sans tracer
 * toutes les requêtes (pas de surcharge en production).
 */
final class SlowQueryMiddleware implements DriverMiddleware
{
    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly float $thresholdMs = 150.0
    ) {
    }

    public function wrap(DriverInterface $driver): DriverInterface
    {
        return new class($driver, $this->logger, $this->thresholdMs) extends AbstractDriverMiddleware {
            public function __construct(
                DriverInterface $driver,
                private readonly LoggerInterface $logger,
                private readonly float $thresholdMs
            ) {
                parent::__construct($driver);
            }

            public function connect(array $params): Connection
            {
                return new class(
                    parent::connect($params),
                    $this->logger,
                    $this->thresholdMs
                ) extends AbstractConnectionMiddleware {
                    public function __construct(
                        Connection $connection,
                        private readonly LoggerInterface $logger,
                        private readonly float $thresholdMs
                    ) {
                        parent::__construct($connection);
                    }

                    public function prepare(string $sql): Statement
                    {
                        return new class(
                            parent::prepare($sql),
                            $this->logger,
                            $this->thresholdMs,
                            $sql
                        ) extends AbstractStatementMiddleware {
                            public function __construct(
                                Statement $statement,
                                private readonly LoggerInterface $logger,
                                private readonly float $thresholdMs,
                                private readonly string $sql
                            ) {
                                parent::__construct($statement);
                            }

                            public function execute(): Result
                            {
                                $start = microtime(true);
                                $result = parent::execute();
                                $this->logIfSlow($start);

                                return $result;
                            }

                            private function logIfSlow(float $start): void
                            {
                                $durationMs = (microtime(true) - $start) * 1000.0;
                                if ($durationMs >= $this->thresholdMs) {
                                    $this->logger->warning(
                                        'SQL lente ({duration_ms} ms) : {sql}',
                                        ['duration_ms' => round($durationMs, 1), 'sql' => $this->sql]
                                    );
                                }
                            }
                        };
                    }

                    public function query(string $sql): Result
                    {
                        $start = microtime(true);
                        try {
                            return parent::query($sql);
                        } finally {
                            $durationMs = (microtime(true) - $start) * 1000.0;
                            if ($durationMs >= $this->thresholdMs) {
                                $this->logger->warning(
                                    'SQL lente ({duration_ms} ms) : {sql}',
                                    ['duration_ms' => round($durationMs, 1), 'sql' => $sql]
                                );
                            }
                        }
                    }
                };
            }
        };
    }
}