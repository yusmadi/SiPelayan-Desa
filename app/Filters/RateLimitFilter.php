<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

/**
 * RateLimitFilter — Pembatasan request berbasis Redis/Cache (Sliding Window)
 *
 * Argumen: 'limit:window'
 * Contoh:  'ratelimit:60:60'  → max 60 request per 60 detik
 *          'ratelimit:5:300'  → max 5 request per 5 menit (untuk login)
 */
class RateLimitFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        [$limit, $window] = $this->parseArguments($arguments);

        $ip       = $request->getIPAddress();
        $userId   = session('user_id') ?? 'guest';
        $key      = 'ratelimit_' . md5($ip . '_' . $userId . '_' . $request->getUri()->getPath());

        $cache    = \Config\Services::cache();
        $current  = (int) ($cache->get($key) ?? 0);

        if ($current >= $limit) {
            log_message('info', "[RateLimit] Limit exceeded: IP={$ip}, Key={$key}");

            if ($request->isAJAX()) {
                return service('response')
                    ->setStatusCode(429)
                    ->setHeader('Retry-After', (string)$window)
                    ->setJSON([
                        'status'  => 'error',
                        'message' => 'Terlalu banyak permintaan. Coba lagi dalam beberapa saat.',
                    ]);
            }

            return redirect()->back()->with('error', "Terlalu banyak permintaan. Silakan tunggu sekitar {$window} detik sebelum mencoba kembali.");
        }

        // Increment dengan sliding window
        if ($current === 0) {
            $cache->save($key, 1, $window);
        } else {
            $cache->increment($key);
        }

        return null;
    }

    private function parseArguments(?array $arguments): array
    {
        if (empty($arguments)) {
            return [60, 60]; // Default: 60 req/menit
        }

        $parts = explode(':', $arguments[0]);
        return [
            (int) ($parts[0] ?? 60),
            (int) ($parts[1] ?? 60),
        ];
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        return null;
    }
}
