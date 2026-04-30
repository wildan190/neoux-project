<?php

namespace Modules\Procurement\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MidtransIrisService
{
    protected $baseUrl;
    protected $apiKey;

    public function __construct()
    {
        $isProduction = config('services.midtrans.is_production');
        $this->baseUrl = $isProduction 
            ? 'https://app.midtrans.com/iris/api/v1' 
            : 'https://app.sandbox.midtrans.com/iris/api/v1';
            
        $this->apiKey = config('services.midtrans.iris_api_key');
    }

    /**
     * Create a payout to a vendor's bank account.
     * 
     * @param string $referenceNo Unique reference for this payout (e.g. PO number + timestamp)
     * @param float $amount Amount to disburse
     * @param string $bankCode Bank code (e.g. 'bca', 'mandiri')
     * @param string $accountNumber Beneficiary account number
     * @param string $accountName Beneficiary account name
     * @param string $email Beneficiary email for notification
     * @return array|bool Returns response data on success, false on failure
     */
    public function createPayout($referenceNo, $amount, $bankCode, $accountNumber, $accountName, $email)
    {
        try {
            $payload = [
                'payouts' => [
                    [
                        'beneficiary_name' => $accountName,
                        'beneficiary_account' => $accountNumber,
                        'beneficiary_bank' => $bankCode,
                        'beneficiary_email' => $email,
                        'amount' => strval($amount),
                        'notes' => preg_replace('/[^A-Za-z0-9 ]/', '', 'Disbursement for ' . $referenceNo),
                    ]
                ]
            ];

            $response = Http::withHeaders([
                'Authorization' => 'Basic ' . base64_encode($this->apiKey . ':'),
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])->post($this->baseUrl . '/payouts', $payload);

            if ($response->successful()) {
                Log::info('Midtrans IRIS Payout Success', ['response' => $response->json()]);
                return $response->json();
            }

            Log::error('Midtrans IRIS Payout Failed', [
                'status' => $response->status(),
                'body' => $response->json(),
                'payload' => $payload
            ]);

            return false;
            
        } catch (\Exception $e) {
            Log::error('Midtrans IRIS Service Exception: ' . $e->getMessage());
            return false;
        }
    }
}
