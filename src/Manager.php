<?php

namespace X99Dev\MobileCard;

use InvalidArgumentException;
use X99Dev\MobileCard\Contracts\Factory;

class Manager
{
    /**
     * The default driver.
     *
     * @var array
     */
    protected $default;

    /**
     * The array of created "drivers".
     *
     * @var array
     */
    protected $drivers = [];

    /**
     * Set the default driver.
     *
     * @param  string  $driver
     * @param  array  $config
     */
    public function setDefaultDriver(string $driver = '', array $config = []): void
    {
        $this->default = compact('driver', 'config');
    }

    /**
     * Dynamically call the default driver instance.
     *
     * @param  string  $method
     * @param  array  $parameters
     *
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        return $this->driver()->$method(...$parameters);
    }

    /**
     * Create an instance of the specified driver.
     *
     * @param  string  $driver
     * @param  array  $config
     *
     * @return Factory
     */
    public function driver(string $driver = '', array $config = []): Factory
    {
        $driver = $driver ?: $this->default['driver'];
        $config = $config ?: $this->default['config'];

        if (empty($driver)) {
            throw new InvalidArgumentException('No mobile card driver was specified.');
        }

        if (!class_exists($driver) || !is_subclass_of($driver, Factory::class)) {
            throw new InvalidArgumentException("Driver [$driver] not supported.");
        }

        if (!isset($this->drivers[$driver])) {
            if (!is_array($config)) {
                $config = [];
            }

            $this->drivers[$driver] = new $driver($config);
        }

        return $this->drivers[$driver];
    }
}
