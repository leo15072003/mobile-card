<?php

namespace X99Dev\MobileCard;

use ArrayAccess;

class Result implements ArrayAccess, Contracts\Result
{
    /**
     * The unique order id for the transaction.
     *
     * @var string
     */
    public $orderId;

    /**
     * The transaction status
     *
     * @var string
     */
    public $status;

    /**
     * The transaction message
     *
     * @var string
     */
    public $message;

    /**
     * The mobile card price
     *
     * @var int
     */
    public $price;

    /**
     * The revenue of the transaction
     *
     * @var float
     */
    public $revenue;

    /**
     * The result's raw attributes.
     *
     * @var array
     */
    public $result;

    /**
     * {@inheritDoc}
     */
    public function getOrderId(): string
    {
        return $this->orderId;
    }

    /**
     * {@inheritDoc}
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * {@inheritDoc}
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * {@inheritDoc}
     */
    public function getPrice(): int
    {
        return $this->price;
    }

    /**
     * {@inheritDoc}
     */
    public function getRevenue(): float
    {
        return $this->revenue;
    }

    /**
     * Get the raw result array.
     *
     * @return array
     */
    public function getRaw(): array
    {
        return $this->result;
    }

    /**
     * Set the raw user array from the provider.
     *
     * @param  array  $user
     * @return $this
     */
    public function setRaw(array $user): self
    {
        $this->result = $user;
        return $this;
    }

    /**
     * Map the given array onto the result's properties.
     *
     * @param  array  $attributes
     * @return $this
     */
    public function map(array $attributes): self
    {
        foreach ($attributes as $key => $value) {
            $this->{$key} = $value;
        }

        return $this;
    }

    /**
     * Determine if the given raw result attribute exists.
     *
     * @param  string  $offset
     * @return bool
     */
    public function offsetExists($offset): bool
    {
        return array_key_exists($offset, $this->result);
    }

    /**
     * Get the given key from the raw result.
     *
     * @param  string  $offset
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->result[$offset];
    }

    /**
     * Set the given attribute on the raw result array.
     *
     * @param  string  $offset
     * @param  mixed  $value
     * @return void
     */
    public function offsetSet($offset, $value): void
    {
        $this->result[$offset] = $value;
    }

    /**
     * Unset the given value from the raw result array.
     *
     * @param  string  $offset
     * @return void
     */
    public function offsetUnset($offset): void
    {
        unset($this->result[$offset]);
    }
}
