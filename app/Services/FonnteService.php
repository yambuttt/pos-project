<?php

namespace App\Services;

use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class FonnteService
{
    protected bool $enabled;
    protected ?string $token;
    protected string $baseUrl;
    protected string $countryCode;
    protected int $defaultDelay;
    protected bool $defaultTyping;
    protected ?string $defaultGroupTarget;

    public function __construct()
    {
        $this->enabled = (bool) config('services.fonnte.enabled', false);
        $this->token = config('services.fonnte.token');
        $this->baseUrl = rtrim((string) config('services.fonnte.base_url', 'https://api.fonnte.com'), '/');
        $this->countryCode = (string) config('services.fonnte.country_code', '62');
        $this->defaultDelay = (int) config('services.fonnte.default_delay', 0);
        $this->defaultTyping = (bool) config('services.fonnte.default_typing', false);
        $this->defaultGroupTarget = config('services.fonnte.default_group_target');
    }

    public function isEnabled(): bool
    {
        return $this->enabled && filled($this->token);
    }

    public function sendMessage(string $target, string $message, array $extra = []): array
    {
        return $this->send([
            'target' => $target,
            'message' => $message,
        ] + $extra);
    }

    public function sendToPhone(string $phone, string $message, array $extra = []): array
    {
        return $this->send([
            'target' => $this->normalizePhone($phone),
            'message' => $message,
            'countryCode' => $extra['countryCode'] ?? $this->countryCode,
        ] + $extra);
    }

    public function sendToGroup(string $groupId, string $message, array $extra = []): array
    {
        return $this->send([
            'target' => $groupId,
            'message' => $message,
        ] + $extra);
    }

    public function sendToDefaultGroup(string $message, array $extra = []): array
    {
        if (blank($this->defaultGroupTarget)) {
            return [
                'ok' => false,
                'status' => null,
                'data' => null,
                'raw' => null,
                'message' => 'FONNTE_DEFAULT_GROUP_TARGET belum diatur.',
            ];
        }

        return $this->sendToGroup($this->defaultGroupTarget, $message, $extra);
    }

    public function sendWithUrl(string $target, string $message, string $url, ?string $filename = null, array $extra = []): array
    {
        $payload = [
            'target' => $target,
            'message' => $message,
            'url' => $url,
        ];

        if (filled($filename)) {
            $payload['filename'] = $filename;
        }

        return $this->send($payload + $extra);
    }

    public function getWhatsappGroups(): array
    {
        if (!$this->isEnabled()) {
            return [
                'ok' => false,
                'status' => null,
                'data' => [],
                'raw' => null,
                'message' => 'Fonnte belum aktif atau token belum diisi.',
            ];
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => $this->token,
            ])->acceptJson()->post($this->baseUrl . '/get-whatsapp-group');

            return [
                'ok' => $response->successful(),
                'status' => $response->status(),
                'data' => $response->json('data', []),
                'raw' => $response->body(),
                'message' => $response->successful()
                    ? 'Berhasil mengambil daftar grup.'
                    : 'Gagal mengambil daftar grup.',
            ];
        } catch (RequestException $e) {
            return [
                'ok' => false,
                'status' => optional($e->response)->status(),
                'data' => [],
                'raw' => optional($e->response)->body(),
                'message' => $e->getMessage(),
            ];
        } catch (\Throwable $e) {
            return [
                'ok' => false,
                'status' => null,
                'data' => [],
                'raw' => null,
                'message' => $e->getMessage(),
            ];
        }
    }

    protected function send(array $payload): array
    {
        if (!$this->isEnabled()) {
            return [
                'ok' => false,
                'status' => null,
                'data' => null,
                'raw' => null,
                'message' => 'Fonnte belum aktif atau token belum diisi.',
            ];
        }

        $payload = $this->applyDefaults($payload);

        try {
            $response = Http::withHeaders([
                'Authorization' => $this->token,
            ])->asForm()->acceptJson()->post($this->baseUrl . '/send', $payload);

            return [
                'ok' => $response->successful(),
                'status' => $response->status(),
                'data' => $response->json(),
                'raw' => $response->body(),
                'message' => $response->successful()
                    ? 'Pesan berhasil dikirim.'
                    : 'Gagal mengirim pesan ke Fonnte.',
            ];
        } catch (RequestException $e) {
            return [
                'ok' => false,
                'status' => optional($e->response)->status(),
                'data' => optional($e->response)->json(),
                'raw' => optional($e->response)->body(),
                'message' => $e->getMessage(),
            ];
        } catch (\Throwable $e) {
            return [
                'ok' => false,
                'status' => null,
                'data' => null,
                'raw' => null,
                'message' => $e->getMessage(),
            ];
        }
    }

    protected function applyDefaults(array $payload): array
    {
        if (!array_key_exists('typing', $payload)) {
            $payload['typing'] = $this->defaultTyping;
        }

        if (!array_key_exists('delay', $payload)) {
            $payload['delay'] = $this->defaultDelay;
        }

        if (
            !array_key_exists('countryCode', $payload)
            && isset($payload['target'])
            && $this->looksLikePhoneTarget((string) $payload['target'])
        ) {
            $payload['countryCode'] = $this->countryCode;
        }

        return $payload;
    }

    protected function looksLikePhoneTarget(string $target): bool
    {
        if (Str::contains($target, '@g.us')) {
            return false;
        }

        if (Str::contains($target, ',')) {
            return false;
        }

        return (bool) preg_match('/^[0-9+\-\s()]+$/', $target);
    }

    protected function normalizePhone(string $phone): string
    {
        $phone = preg_replace('/\D+/', '', $phone ?? '');

        if (blank($phone)) {
            return '';
        }

        if (Str::startsWith($phone, '0')) {
            return substr($phone, 1);
        }

        if (Str::startsWith($phone, '62')) {
            return substr($phone, 2);
        }

        return $phone;
    }
}