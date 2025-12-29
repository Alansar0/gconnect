<?php
namespace App\Services\RouterApi;

use Illuminate\Support\Str;

class MockMikroTikService implements RouterApiInterface
{
    public function connect(array $connection): bool
    {
        return true;
    }

    public function createVoucher(array $data): array
    {
        return [
            'username' => 'V' . strtoupper(Str::random(6)),
            'password' => Str::random(8),
            'raw'      => 'mocked'
        ];
    }

    public function getProfiles(): array
    {
        return [
            ['name' => '1 Hour Plan', 'mikrotik' => '1h-5M', 'time' => 60],
            ['name' => '1 Day Plan',  'mikrotik' => '1d-5M', 'time' => 1440],
        ];
    }

    public function switchToWan(string $wanPort): bool
    {
        return true; // always success in mock
    }
}

