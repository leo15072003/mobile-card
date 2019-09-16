<?php

namespace X99Dev\MobileCard\Drivers;

use X99Dev\MobileCard\Contracts\Result;
use X99Dev\MobileCard\MobileCard;

class TrumThe247 extends AbstractDriver
{
    /**
     * {@inheritDoc}
     */
    public function getConfigFields(): array
    {
        return [
            'api_key',
            'api_secret',
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function getChargeUrl(): string
    {
        return 'https://trumthe247.com/restapi/charge';
    }

    /**
     * {@inheritDoc}
     */
    public function getChargeFields(array $card): array
    {
        return [
            'card' => $card['type'],
            'amount' => $card['price'],
            'serial' => $card['serial'],
            'pin' => $card['pin'],
            'api_key' => $this->config['api_key'],
            'api_secret' => $this->config['api_secret'],
            'content' => $card['order_id'],
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function callback(array $data): Result
    {
        return $this->mapResultToObject($data);
    }

    /**
     * {@inheritDoc}
     */
    protected function mapResultToObject(array $result, array $card = []): Result
    {
        // Map transaction status
        $status = MobileCard::STATUS_ERROR;
        $mapStatus = [
            MobileCard::STATUS_WRONG => ['-1007', '-1008', '-1009', '-1010'],
            MobileCard::STATUS_MAINTENANCE => ['-1001', '-1002', '-1003', '-1004', '-1005'],
            MobileCard::STATUS_WRONG_PRICE => ['-1015'],
            MobileCard::STATUS_USED => ['-1006'],
        ];

        foreach ($mapStatus as $localStatus => $gateStatus) {
            if (in_array($result['status'], $gateStatus)) {
                $status = $localStatus;
            }
        }

        if ($result['status'] == 1) {
            if (!empty($result['card_data'])) {
                $status = MobileCard::STATUS_SUCCESS;
            } else {
                $status = MobileCard::STATUS_WAIT;
            }
        }

        return (new \X99Dev\MobileCard\Result)->setRaw($result)->map([
            'orderId' => $result['content'] ?? $card['order_id'],
            'status' => $status,
            'message' => $result['desc'],
            'price' => $result['card_data']['amount'] ?? 0,
            'revenue' => $result['card_data']['real_amount'] ?? 0,
        ]);
    }
}
