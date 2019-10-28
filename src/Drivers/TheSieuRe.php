<?php

namespace X99Dev\MobileCard\Drivers;

use X99Dev\MobileCard\Contracts\Result;
use X99Dev\MobileCard\MobileCard;

class TheSieuRe extends AbstractDriver
{
    protected $mapTypes = [
        MobileCard::TYPE_VIETTEL => 'VIETTEL',
        MobileCard::TYPE_MOBIFONE => 'MOBIFONE',
        MobileCard::TYPE_VINAPHONE => 'VINAPHONE',
    ];

    /**
     * Get the configuration fields
     *
     * @return array
     */
    public function getConfigFields(): array
    {
        return [
            'partner_id',
            'partner_key',
        ];
    }

    /**
     * Get the charge URL for the driver.
     *
     * @return string
     */
    public function getChargeUrl(): string
    {
        return 'http://api.thesieure.com/chargingws/v2';
    }

    /**
     * Get the POST parameters for the charge request.
     *
     * @param  array  $card
     *
     * @return array
     */
    public function getChargeFields(array $card): array
    {
        $data = [
            'telco' => $this->mapTypes[$card['type']],
            'code' => $card['pin'],
            'serial' => $card['serial'],
            'amount' => $card['price'],
            'command' => 'charging',
            'partner_id' => $this->config['partner_id'],
            'request_id' => $card['order_id'],
        ];

        $data['sign'] = $this->createSign($this->config['partner_key'], $data);

        return $data;
    }

    /**
     * Create sign for API
     *
     * @param $partnerKey
     * @param $params
     *
     * @return string
     */
    protected function createSign($partnerKey, $params): string
    {
        $data = [];
        $data['request_id'] = $params['request_id'];
        $data['code'] = $params['code'];
        $data['partner_id'] = $params['partner_id'];
        $data['serial'] = $params['serial'];
        $data['telco'] = $params['telco'];
        $data['command'] = $params['command'];
        ksort($data);
        $sign = $partnerKey;
        foreach ($data as $item) {
            $sign .= $item;
        }

        return md5($sign);
    }

    /**
     * {@inheritdoc}
     */
    public function callback(array $data): Result
    {
        return $this->mapResultToObject($data);
    }

    /**
     * Map the raw result array to a Result instance.
     *
     * @param  array  $result
     * @param  array  $card
     * @return Result
     */
    protected function mapResultToObject(array $result, array $card = []): Result
    {
        // Map transaction status
        switch ($result['status']) {
            case 99:
                $status = MobileCard::STATUS_WAIT;
                break;
            case 1:
                $status = MobileCard::STATUS_SUCCESS;
                break;
            case 2:
                $status = MobileCard::STATUS_WRONG_PRICE;
                break;
            case 3:
                $status = MobileCard::STATUS_WRONG;
                break;
            case 4:
                $status = MobileCard::STATUS_MAINTENANCE;
                break;
            default:
                $status = MobileCard::STATUS_ERROR;
                break;
        }

        return (new \X99Dev\MobileCard\Result)->setRaw($result)->map([
            'orderId' => $result['request_id'] ?? $card['order_id'],
            'status' => $status,
            'message' => $result['message'],
            'price' => $result['value'] ?? 0,
            'revenue' => $result['amount'] ?? 0,
        ]);
    }
}
