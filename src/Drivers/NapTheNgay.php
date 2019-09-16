<?php

namespace X99Dev\MobileCard\Drivers;

use X99Dev\MobileCard\Contracts\Result;
use X99Dev\MobileCard\MobileCard;

class NapTheNgay extends AbstractDriver
{
    /**
     * Get the configuration fields
     *
     * @return array
     */
    public function getConfigFields(): array
    {
        return [
            'merchant_id',
            'api_email',
            'secure_code',
        ];
    }

    /**
     * Get the charge URL for the driver.
     *
     * @return string
     */
    public function getChargeUrl(): string
    {
        return 'http://api.napthengay.com/v2/';
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
        // Map card types
        $mapTypes = [
            MobileCard::TYPE_VIETTEL => 1,
            MobileCard::TYPE_MOBIFONE => 2,
            MobileCard::TYPE_VINAPHONE => 3,
            MobileCard::TYPE_ZING => 4,
            MobileCard::TYPE_GATE => 5,
        ];

        $data = [
            'merchant_id' => $this->config['merchant_id'],
            'api_email' => $this->config['api_email'],
            'trans_id' => $card['order_id'],
            'card_id' => $mapTypes[$card['type']],
            'card_value' => $card['price'],
            'pin_field' => $card['pin'],
            'seri_field' => $card['serial'],
            'algo_mode' => 'hmac',
        ];

        $data['data_sign'] = hash_hmac('SHA1', implode('', $data), $this->config['secure_code']);

        return $data;
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
        $status = MobileCard::STATUS_ERROR;
        $mapStatus = [
            MobileCard::STATUS_WRONG => [106, 108, 111],
            MobileCard::STATUS_WAIT => [107],
            MobileCard::STATUS_MAINTENANCE => [102, 105, 110],
            MobileCard::STATUS_WRONG_PRICE => [109],
            MobileCard::STATUS_USED => [114],
            MobileCard::STATUS_SUCCESS => [100],
        ];

        foreach ($mapStatus as $localStatus => $gateStatus) {
            if (in_array($result['code'], $gateStatus)) {
                $status = $localStatus;
            }
        }

        return (new \X99Dev\MobileCard\Result)->setRaw($result)->map([
            'orderId' => $result['trans_id'],
            'status' => $status,
            'message' => $result['msg'],
            'price' => (int) $result['amount'],
            'revenue' => (int) $result['money_add'],
        ]);
    }
}
