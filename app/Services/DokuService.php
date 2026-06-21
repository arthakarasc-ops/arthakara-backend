<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class DokuService
{
    private const SIGNATURE_PREFIX = 'HMACSHA256=';

    protected string $clientId;
    protected string $sharedKey;
    protected string $apiUrl;

    public function __construct()
    {
        $this->clientId  = config('doku.client_id') ?? '';
        $this->sharedKey = config('doku.shared_key') ?? '';
        $this->apiUrl    = config('doku.api_url') ?? 'https://api-sandbox.doku.com';
    }

    /**
     * Generate signature header for Doku API requests.
     *
     * Format signature component (per Doku docs):
     *   Client-Id:<clientId>\n
     *   Request-Id:<requestId>\n
     *   Request-Timestamp:<timestamp>\n
     *   Request-Target:<targetPath>\n
     *   Digest:<base64(sha256(body))>
     *
     * Signed with HMAC-SHA256 using shared key.
     *
     * @param string $targetPath API endpoint path (e.g., /checkout/v1/payment)
     * @param string $requestId  Unique request ID
     * @param string $timestamp  ISO8601 timestamp (UTC)
     * @param string $jsonBody   Raw JSON body string (minified)
     * @return string Signature header value prefixed with "HMACSHA256="
     */
    public function generateSignature(string $targetPath, string $requestId, string $timestamp, string $jsonBody): string
    {
        $digest           = base64_encode(hash('sha256', $jsonBody, true));
        $signatureComponent = $this->buildSignatureComponent($this->clientId, $requestId, $timestamp, $targetPath, $digest);
        $signature        = base64_encode(hash_hmac('sha256', $signatureComponent, $this->sharedKey, true));

        return self::SIGNATURE_PREFIX . $signature;
    }

    /**
     * Create Checkout URL via Doku API.
     *
     * Sends raw JSON body (not re-encoded by Laravel HTTP client)
     * to ensure the signature digest matches the actual body sent.
     *
     * @param array $payload Checkout payload
     * @return array|null Returns response array on success, null on failure
     */
    public function createCheckout(array $payload): ?array
    {
        if (empty($this->clientId) || empty($this->sharedKey)) {
            Log::error('Doku credentials are empty. Please check DOKU_CLIENT_ID and DOKU_SHARED_KEY in your .env.');
            return null;
        }

        $targetPath = '/checkout/v1/payment';
        $requestId  = (string) Str::uuid();
        $timestamp  = gmdate('Y-m-d\TH:i:s\Z');

        // Minified JSON body — signature is calculated against this exact string
        $jsonBody  = json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        $signature = $this->generateSignature($targetPath, $requestId, $timestamp, $jsonBody);

        Log::info('Doku Checkout Request:', [
            'url'        => $this->apiUrl . $targetPath,
            'request_id' => $requestId,
            'timestamp'  => $timestamp,
        ]);

        try {
            // Send raw JSON body (withBody) to ensure byte-exact match with signature digest
            $response = Http::withHeaders([
                'Client-Id'         => $this->clientId,
                'Request-Id'        => $requestId,
                'Request-Timestamp' => $timestamp,
                'Signature'         => $signature,
                'Content-Type'      => 'application/json',
            ])->withBody($jsonBody, 'application/json')
              ->post($this->apiUrl . $targetPath);

            if ($response->successful()) {
                Log::info('Doku Checkout Response:', [
                    'status' => $response->status(),
                    'body'   => $response->json(),
                ]);
                return $response->json();
            }

            Log::error('Doku Checkout API failed:', [
                'status'   => $response->status(),
                'response' => $response->body(),
            ]);
            return null;
        } catch (\Exception $e) {
            Log::error('Doku Checkout exception: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Verify the signature of incoming Doku callback/notification request.
     *
     * Re-calculates the expected signature from the raw body and headers,
     * then compares it against the received Signature header.
     *
     * @param string $rawBody           Raw content of request body
     * @param string $receivedSignature Signature header value
     * @param string $receivedClientId  Client-Id header value
     * @param string $receivedRequestId Request-Id header value
     * @param string $receivedTimestamp Request-Timestamp header value
     * @param string $targetPath        Path where callback was received (e.g. /api/doku-callback)
     * @return bool
     */
    public function verifyCallbackSignature(
        string $rawBody,
        string $receivedSignature,
        string $receivedClientId,
        string $receivedRequestId,
        string $receivedTimestamp,
        string $targetPath
    ): bool {
        // Validate Client-Id matches our configured one
        if ($receivedClientId !== $this->clientId) {
            Log::warning('Doku Callback: Client-Id mismatch.', [
                'received'   => $receivedClientId,
                'configured' => $this->clientId,
            ]);
            return false;
        }

        $digest             = base64_encode(hash('sha256', $rawBody, true));
        $signatureComponent = $this->buildSignatureComponent($receivedClientId, $receivedRequestId, $receivedTimestamp, $targetPath, $digest);
        $calculatedValue    = base64_encode(hash_hmac('sha256', $signatureComponent, $this->sharedKey, true));
        $expectedSignature  = self::SIGNATURE_PREFIX . $calculatedValue;

        // Use hash_equals for timing-safe comparison (prevents timing attacks)
        if (!hash_equals($expectedSignature, trim($receivedSignature))) {
            Log::warning('Doku Callback: Signature verification failed.', [
                'received' => trim($receivedSignature),
                'expected' => $expectedSignature,
            ]);
            return false;
        }

        return true;
    }

    /**
     * Bangun string komponen signature sesuai format Doku.
     */
    private function buildSignatureComponent(
        string $clientId,
        string $requestId,
        string $timestamp,
        string $targetPath,
        string $digest
    ): string {
        return "Client-Id:{$clientId}\n"
             . "Request-Id:{$requestId}\n"
             . "Request-Timestamp:{$timestamp}\n"
             . "Request-Target:{$targetPath}\n"
             . "Digest:{$digest}";
    }
}
