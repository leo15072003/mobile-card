<?php

namespace X99Dev\MobileCard\Contracts;

interface Factory
{
    /**
     * Make a transaction
     *
     * @param  array  $card
     *
     * @return Result
     */
    public function charge(array $card): Result;

    /**
     * Handler the callback
     *
     * @param  array  $data
     *
     * @return Result
     */
    public function callback(array $data): Result;
}
