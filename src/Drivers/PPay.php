<?php

namespace X99Dev\MobileCard\Drivers;

use InvalidArgumentException;
use X99Dev\MobileCard\Contracts\Result;
use X99Dev\MobileCard\MobileCard;

class PPay extends TheSieuRe
{
    protected $mapTypes = [
        MobileCard::TYPE_VIETTEL => 'VIETTEL',
        MobileCard::TYPE_MOBIFONE => 'MOBIFONE',
        MobileCard::TYPE_VINAPHONE => 'VINAPHONE',
        MobileCard::TYPE_GATE => 'GATE',
        MobileCard::TYPE_ZING => 'ZING',
        MobileCard::TYPE_GARENA => 'GARENA',
    ];

    /**
     * Get the charge URL for the driver.
     *
     * @return string
     */
    public function getChargeUrl(): string
    {
        return 'https://ppay.vn/chargingws/v2';
    }

    /**
     * {@inheritdoc}
     */
    public function callback(array $data): Result
    {
        // Validate callback
        $callbackSign = md5($this->config['partner_key'].$data['code'].$data['serial']);

        if ($data['callback_sign'] != $callbackSign) {
            throw new InvalidArgumentException('Sign not match.');
        }

        return $this->mapResultToObject($data);
    }
}
