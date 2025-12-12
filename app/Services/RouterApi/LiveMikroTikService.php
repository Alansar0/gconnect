<?php
namespace App\Services\RouterApi;

use RouterOS\Client;
use RouterOS\Query;
use Exception;

// class LiveMikroTikService implements RouterApiInterface {
//     protected $client;

//     public function connect(array $connection): bool {
//         try {
//             $this->client = new Client([
//                 'host' => $connection['host'],
//                 'user' => $connection['username'],
//                 'pass' => $connection['password'],
//                 'port' => $connection['port'] ?? 8728,
//             ]);
//             return true;
//         } catch (Exception $e) {
//             return false;
//         }
//     }

//     public function createVoucher(array $data): array {
//         try {
//             $query = (new Query('/ip/hotspot/user/add'))
//                 ->equal('name', $data['username'])
//                 ->equal('password', $data['password'])
//                 ->equal('profile', $data['profile']);
//             $this->client->query($query)->read();
//             return [
//                 'username' => $data['username'],
//                 'password' => $data['password'],
//                 'raw' => 'live',
//             ];
//         } catch (Exception $e) {
//             return ['error' => $e->getMessage()];
//         }
//     }

//     public function getProfiles(): array {
//         try {
//             $response = $this->client->query(new Query('/ip/hotspot/user/profile/print'))->read();
//             return collect($response)->map(function ($profile) {
//                 return [
//                     'name' => $profile['name'] ?? 'N/A',
//                     'mikrotik' => $profile['name'] ?? '',
//                     'time' => $profile['on-login'] ?? '',
//                 ];
//             })->toArray();
//         } catch (Exception $e) {
//             return [['error' => $e->getMessage()]];
//         }
//     }
// }
class LiveMikroTikService implements RouterApiInterface
{
    protected Client $client;

    public function connect(array $connection): bool
    {
        try {
            $this->client = new Client([
                'host' => $connection['host'],
                'user' => $connection['username'],
                'pass' => $connection['password'],
                'port' => $connection['port'] ?? 8728,
                'timeout' => 5,
            ]);
            return true;
        } catch (Exception $e) {
            report($e);
            return false;
        }
    }


    public function createVoucher(array $data): array
    {
        try {
            $query = (new Query('/ip/hotspot/user/add'))
                ->equal('name', $data['username'])
                ->equal('password', $data['password'])
                ->equal('profile', $data['profile']);

            $this->client->query($query)->read();

            return [
                'username' => $data['username'],
                'password' => $data['password'],
                'raw'      => 'created'
            ];

        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }


    public function getProfiles(): array
    {
        try {
            $query = new Query('/ip/hotspot/user/profile/print');
            $profiles = $this->client->query($query)->read();

            return array_map(function ($item) {
                return [
                    'name' => $item['name'],
                    'mikrotik' => $item['name'],
                    'raw' => $item,
                ];
            }, $profiles);

        } catch (Exception $e) {
            return [];
        }
    }


    public function switchToWan(string $wanPort): bool
    {
        try {
            $wan1 = $this->client->query(
                (new Query('/ip/route/print'))->equal('comment', 'WAN1-DEFAULT-ROUTE')
            )->read();

            $wan2 = $this->client->query(
                (new Query('/ip/route/print'))->equal('comment', 'WAN2-DEFAULT-ROUTE')
            )->read();

            if (!$wan1 || !$wan2) {
                return false;
            }

            if ($wanPort === 'ether2') { // WAN2 active
                $this->client->query(
                    (new Query('/ip/route/set'))->equal('.id', $wan2[0]['.id'])->equal('distance', 1)
                )->read();

                $this->client->query(
                    (new Query('/ip/route/set'))->equal('.id', $wan1[0]['.id'])->equal('distance', 2)
                )->read();

            } else { // WAN1 active
                $this->client->query(
                    (new Query('/ip/route/set'))->equal('.id', $wan1[0]['.id'])->equal('distance', 1)
                )->read();

                $this->client->query(
                    (new Query('/ip/route/set'))->equal('.id', $wan2[0]['.id'])->equal('distance', 2)
                )->read();
            }

            return true;
        } catch (Exception $e) {
            report($e);
            return false;
        }
    }
}
