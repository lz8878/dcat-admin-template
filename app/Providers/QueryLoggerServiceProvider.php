<?php

namespace App\Providers;

use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Database\Events\TransactionBeginning;
use Illuminate\Database\Events\TransactionCommitted;
use Illuminate\Database\Events\TransactionRolledBack;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;

class QueryLoggerServiceProvider extends ServiceProvider
{
    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        if (! config('app.debug')) {
            return;
        }

        $this->app['events']->listen([
            QueryExecuted::class,
            TransactionBeginning::class,
            TransactionCommitted::class,
            TransactionRolledBack::class,
        ], function ($event) {
            Log::debug(
                match (true) {
                    $event instanceof TransactionBeginning => 'begin transaction',
                    $event instanceof TransactionCommitted => 'commit transaction',
                    $event instanceof TransactionRolledBack => 'rollback transaction',
                    default => $this->prepareSql($event),
                }
            );
        });
    }

    /**
     * @param  \Illuminate\Database\Events\QueryExecuted  $query
     * @return string
     */
    protected function prepareSql(QueryExecuted $query): string
    {
        $sql = str_replace(['%', '?'], ['%%', '%s'], $query->sql);

        $bindings = $query->connection->prepareBindings($query->bindings);

        if (count($bindings)) {
            $sql = vsprintf($sql, array_map([$query->connection->getPdo(), 'quote'], $bindings));
        }

        return sprintf('[%s] %s', $this->formatDuration($query->time), $sql);
    }

    /**
     * @param  float  $milliseconds
     * @return string
     */
    protected function formatDuration($milliseconds): string
    {
        return match (true) {
            $milliseconds >= 1000 => round($milliseconds / 1000, 2).'s',
            $milliseconds < 0.01 => round($milliseconds * 1000).'μs',
            default => $milliseconds.'ms',
        };
    }
}
