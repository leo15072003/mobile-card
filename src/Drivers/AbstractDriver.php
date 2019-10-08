<?php

namespace X99Dev\MobileCard\Drivers;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;
use X99Dev\MobileCard\Contracts\Factory;
use X99Dev\MobileCard\Contracts\Result;

abstract class AbstractDriver implements Factory
{
    /**
     * The driver configurations
     *
     * @var array
     */
    public $config;

    public function __construct(array $config)
    {
        $this->config = $this->validatedConfig($config);
    }

    /**
     * Get the validated driver configurations
     *
     * @param  array  $config
     * @return array
     */
    public function validatedConfig(array $config): array
    {
        foreach ($this->getConfigFields() as $field) {
            if (!isset($config[$field])) {
                throw new InvalidArgumentException("Missing required config field [$field].");
            }
        }

        return $config;
    }

    /**
     * Get the configuration fields
     *
     * @return array
     */
    abstract public function getConfigFields(): array;

    /**
     * {@inheritdoc}
     */
    public function charge(array $card): Result
    {
        // Validate card
        foreach (['order_id', 'type', 'price', 'serial', 'pin'] as $field) {
            if (empty($card[$field])) {
                throw new InvalidArgumentException("Missing required field [$field].");
            }
        }

        // Make a http request
        $client = new Client([
            'http_errors' => false,
            'verify' => false,
            'timeout' => 60,
            'headers' => [
                'Accept' => 'application/json',
            ],
        ]);

        $response = $client->post($this->getChargeUrl(), array_merge($this->config['guzzle'] ?? [], [
            'form_params' => $fields = $this->getChargeFields($card),
        ]));

        // Handle result
        $result = json_decode($raw = str_replace("\xEF\xBB\xBF", '', (string) $response->getBody()), true);

        if (empty($result)) {
            if (class_exists(Log::class)) {
                Log::error('Transaction error', [
                    'driver' => static::class,
                    'fields' => $fields,
                    'result' => $raw,
                ]);
            }

            $result = [];
        }

        return $this->mapResultToObject($result, $card);
    }

    /**
     * Get the charge URL for the driver.
     *
     * @return string
     */
    abstract public function getChargeUrl(): string;

    /**
     * Get the POST parameters for the charge request.
     *
     * @param  array  $card
     *
     * @return array
     *
     */
    abstract public function getChargeFields(array $card): array;

    /**
     * Map the raw result array to a Result instance.
     *
     * @param  array  $result
     * @param  array  $card
     * @return Result
     */
    abstract protected function mapResultToObject(array $result, array $card = []): Result;

    /**
     * {@inheritdoc}
     */
    abstract public function callback(array $data): Result;
}
