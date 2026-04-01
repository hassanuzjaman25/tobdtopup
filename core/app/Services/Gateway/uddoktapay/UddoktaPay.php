<?php

namespace App\Services\Gateway\uddoktapay;

class UddoktaPay
{
    private $apiKey;
    private $apiBaseURL;

    public function __construct($apiKey, $apiBaseURL)
    {
        $this->apiKey = $apiKey;
        $this->apiBaseURL = $apiBaseURL;
    }

    private function normalizeBaseURL($apiBaseURL)
    {
        $baseURL = rtrim($apiBaseURL, '/');
        $apiSegmentPosition = strpos($baseURL, '/api');

        if ($apiSegmentPosition !== false) {
            $baseURL = substr($baseURL, 0, $apiSegmentPosition + 4); // Include '/api'
        }

        return $baseURL;
    }

    private function buildURL($endpoint)
    {
        return $this->apiBaseURL . '/' . $endpoint;
    }

    public function initPayment($requestData)
    {
        $apiUrl = 'https://' . $this->apiBaseURL . '/api/payment/create';
        $response = $this->sendRequest('POST', $apiUrl, $requestData);

        $this->validateApiResponse($response, 'Payment request failed');
        return $response['payment_url'];
    }

    public function verifyPayment($invoiceId)
    {
        $verifyUrl = 'https://' . $this->apiBaseURL . '/api/payment/verify';
        $requestData = ['transaction_id' => $invoiceId];
        return $this->sendRequest('POST', $verifyUrl, $requestData);
    }

    public function executePayment()
    {
        $rawInput = trim(file_get_contents('php://input'));
        $this->validateIpnResponse($rawInput);

        $data = json_decode($rawInput, true);
        $transaction_id = $data['transactionId'];

        return $this->verifyPayment($transaction_id);
    }

    private function sendRequest($method, $url, $data)
    {
        $headers = [
            'API-KEY: ' . $this->apiKey,
            'accept: application/json',
            'content-type: application/json'
        ];

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => $headers,
        ]);

        $response = curl_exec($curl);
        $error = curl_error($curl);
        curl_close($curl);

        if ($error) {
            throw new \Exception("cURL Error: $error");
        }

        return json_decode($response, true);
    }

    private function validateApiResponse($response, $errorMessage)
    {
        if (!isset($response['payment_url'])) {
            $message = isset($response['message']) ? $response['message'] : $errorMessage;
            throw new \Exception($message);
        }
    }

    private function validateIpnResponse($rawInput)
    {
        if (empty($rawInput)) {
            throw new \Exception("Invalid response from UddoktaPay API.");
        }
    }
}
