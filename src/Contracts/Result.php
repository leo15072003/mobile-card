<?php


namespace X99Dev\MobileCard\Contracts;

interface Result
{
    /**
     * Get the unique order id for the transaction.
     *
     * @return string
     */
    public function getOrderId(): string;

    /**
     * Get the transaction status
     *
     * @return string
     */
    public function getStatus(): string;

    /**
     * Get the transaction message
     *
     * @return string
     */
    public function getMessage(): string;

    /**
     * Get the mobile card price
     *
     * @return int
     */
    public function getPrice(): int;

    /**
     * Get the revenue of the transaction
     *
     * @return float
     */
    public function getRevenue(): float;
}
