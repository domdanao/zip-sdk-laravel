<?php

namespace Domdanao\ZipSdkLaravel\Validators;

use Exception;
use Illuminate\Support\Facades\Validator;

class SourceValidator
{
    /**
     * Validate source data
     *
     * @param array $data
     * @return array
     * @throws Exception
     */
    public function validate(array $data): array
    {
        // Base validation rules
        $rules = [
            'type' => 'required|string|in:card,gcash,paymaya,wechat,alipay,unionpay,grabpay,instapay,qrph,bpi,unionbank,metrobank,bdo,pnb,rcbc',
            'customer_id' => 'nullable|string',
            'token' => 'nullable|string',
            'metadata' => 'nullable|array',
        ];

        // Add card validation rules if card is present
        if (isset($data['card'])) {
            $rules['card'] = 'required|array';
            $rules['card.name'] = 'required|string|max:255';
            $rules['card.number'] = 'required|string|min:13|max:19';
            $rules['card.exp_month'] = 'required|string|size:2';
            $rules['card.exp_year'] = 'required|string|size:4';
            $rules['card.cvc'] = 'required|string|min:3|max:4';
            $rules['card.address_line1'] = 'nullable|string|max:255';
            $rules['card.address_line2'] = 'nullable|string|max:255';
            $rules['card.address_city'] = 'nullable|string|max:255';
            $rules['card.address_state'] = 'nullable|string|max:255';
            $rules['card.address_country'] = 'nullable|string|max:2';
            $rules['card.address_zip'] = 'nullable|string|max:20';
        }

        // Add bank account validation rules if bank_account is present
        if (isset($data['bank_account'])) {
            $rules['bank_account'] = 'required|array';
            $rules['bank_account.reference_id'] = 'required|string';
            $rules['bank_account.bank_type'] = 'required|string';
            $rules['bank_account.bank_code'] = 'required|string';
            $rules['bank_account.account_name'] = 'nullable|string';
            $rules['bank_account.account_number'] = 'nullable|string';
            $rules['bank_account.account_type'] = 'nullable|string';
            $rules['bank_account.expires_at'] = 'nullable|string';
            $rules['bank_account.metadata'] = 'nullable|array';
        }

        // Add owner validation rules if owner is present
        if (isset($data['owner'])) {
            $rules['owner'] = 'required|array';
            
            // Billing address validation
            if (isset($data['owner']['billing'])) {
                $rules['owner.billing'] = 'required|array';
                $rules['owner.billing.name'] = 'required|string|max:255';
                $rules['owner.billing.phone_number'] = 'required|string|max:20';
                $rules['owner.billing.email'] = 'required|email|max:255';
                $rules['owner.billing.line1'] = 'required|string|max:255';
                $rules['owner.billing.line2'] = 'nullable|string|max:255';
                $rules['owner.billing.city'] = 'required|string|max:255';
                $rules['owner.billing.state'] = 'required|string|max:255';
                $rules['owner.billing.country'] = 'required|string|max:2';
                $rules['owner.billing.zip_code'] = 'required|string|max:20';
            }
            
            // Shipping address validation
            if (isset($data['owner']['shipping'])) {
                $rules['owner.shipping'] = 'required|array';
                $rules['owner.shipping.name'] = 'required|string|max:255';
                $rules['owner.shipping.phone_number'] = 'required|string|max:20';
                $rules['owner.shipping.email'] = 'required|email|max:255';
                $rules['owner.shipping.line1'] = 'required|string|max:255';
                $rules['owner.shipping.line2'] = 'nullable|string|max:255';
                $rules['owner.shipping.city'] = 'required|string|max:255';
                $rules['owner.shipping.state'] = 'required|string|max:255';
                $rules['owner.shipping.country'] = 'required|string|max:2';
                $rules['owner.shipping.zip_code'] = 'required|string|max:20';
            }
        }

        // Add redirect validation rules if redirect is present
        if (isset($data['redirect'])) {
            $rules['redirect'] = 'required|array';
            $rules['redirect.success'] = 'required|url';
            $rules['redirect.fail'] = 'required|url';
            $rules['redirect.notify'] = 'nullable|url';
        }

        // Ensure at least one of token, card, or bank_account is provided
        if (!isset($data['token']) && !isset($data['card']) && !isset($data['bank_account'])) {
            throw new Exception('Source validation failed: At least one of token, card, or bank_account must be provided');
        }

        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            throw new Exception('Source validation failed: ' . $validator->errors()->first());
        }

        return $validator->validated();
    }
}
