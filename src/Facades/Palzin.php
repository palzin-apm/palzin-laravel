<?php

namespace Palzin\Laravel\Facades;


use Illuminate\Support\Facades\Facade;
use Palzin\Models\Error;
use Palzin\Models\Segment;
use Palzin\Models\Transaction;

/**
 * @method Transaction startTransaction($name)
 * @method Transaction currentTransaction()
 * @method static bool needTransaction()
 * @method static bool hasTransaction()
 * @method static bool canAddSegments()
 * @method static bool isRecording()
 * @method static \Palzin\Palzin startRecording()
 * @method static \Palzin\Palzin stopRecording()
 * @method Segment startSegment($type, $label)
 * @method mixed addSegment($callback, $type, $label, $throw = false)
 * @method Error reportException(\Throwable $exception, $handled = true)
 * @method static void flush()
 * @method static void beforeFlush(callable $callback)
 */
class Palzin extends Facade
{
    /**
     * @inheritDoc
     */
    protected static function getFacadeAccessor()
    {
        return 'palzin';
    }
}
