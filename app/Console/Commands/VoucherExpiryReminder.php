<?
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Notifications\AdminAnnouncement;
use Illuminate\Support\Facades\Notification;
use App\Models\Voucher;

class VoucherExpiryReminder extends Command
{
    protected $signature = 'voucher:expiry-reminder';
    protected $description = 'Send voucher expiry reminders';

    public function handle()
    {
        $vouchers = Voucher::where('status', 'active')
            ->where('expires_at', '>', now())
            ->get();

        foreach ($vouchers as $voucher) {

            $minutesLeft = $voucher->minutesLeft();

            // 1️⃣ 90 minutes reminder
            if ($minutesLeft <= 90 && !$voucher->reminded_90) {
                $this->notify($voucher, 'Your voucher will expire in 1 hour 30 minutes. Please buy a new voucher to avoid internet interruption.');
                $voucher->update(['reminded_90' => true]);
            }

            // 2️⃣ 40 minutes reminder
            if ($minutesLeft <= 40 && !$voucher->reminded_40) {
                $this->notify($voucher, 'Your voucher will expire in 40 minutes. Kindly recharge.');
                $voucher->update(['reminded_40' => true]);
            }

            // 3️⃣ 20 minutes reminder
            if ($minutesLeft <= 20 && !$voucher->reminded_20) {
                $this->notify($voucher, 'Final reminder: Your voucher expires in 20 minutes.');
                $voucher->update(['reminded_20' => true]);
            }
        }
    }

        protected function notify($voucher, string $message)
        {
            if (!$voucher->user) {
                return;
            }

            $url = route('voucher.buy'); // or dashboard / renew page

            Notification::send(
                $voucher->user,
                new AdminAnnouncement($message, $url)
            );
        }

}
