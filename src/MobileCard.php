<?php

namespace X99Dev\MobileCard;

use X99Dev\MobileCard\Contracts\Factory;

/**
 * @method static void setDefaultDriver(string $driver = '', array $config = [])
 * @method static Factory driver(string $driver = '', array $config = [])
 * @method static \X99Dev\MobileCard\Contracts\Result charge(array $card)
 * @method static \X99Dev\MobileCard\Contracts\Result callback(array $data)
 */
class MobileCard
{
    /**
     * Card types
     */
    public const TYPE_VIETTEL = 'VTT';
    public const TYPE_VINAPHONE = 'VNP';
    public const TYPE_MOBIFONE = 'VMS';
    public const TYPE_ZING = 'ZING';
    public const TYPE_GATE = 'GATE';
    public const TYPE_GARENA = 'GARENA';

    /**
     * Transaction status
     */
    public const STATUS_MAKE_TRANSACTION = 'make_transaction';
    public const STATUS_WRONG = 'wrong';
    public const STATUS_ERROR = 'error';
    public const STATUS_WAIT = 'wait';
    public const STATUS_MAINTENANCE = 'maintenance';
    public const STATUS_WRONG_PRICE = 'wrong_price';
    public const STATUS_USED = 'used';
    public const STATUS_SUCCESS = 'success';

    protected static $instance;

    public static function __callStatic($name, $arguments)
    {
        if (static::$instance === null) {
            static::$instance = new Manager;
        }

        return static::$instance->$name(...$arguments);
    }
}
