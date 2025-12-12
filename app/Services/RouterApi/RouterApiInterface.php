<?php

namespace App\Services\RouterApi;
interface RouterApiInterface {
    public function connect(array $connection): bool;
    public function createVoucher(array $data): array; // returns ['username'=>..., 'password'=>..., 'raw'=>...]
    public function getProfiles(): array;
    
    public function switchToWan(string $wanPort): bool;
}
