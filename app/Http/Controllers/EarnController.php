<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserReward;
use Illuminate\Http\Response;
use App\Models\AdminReward;
use App\Models\Wallet;



class EarnController extends Controller
{
    public function index()
        {
            $wallet = \App\Models\Wallet::firstOrCreate(['user_id' => auth()->id()]);

            
            $cashback = $wallet->cashback_balance ?? 0;
            $vouchers = $wallet->voucher_balance ?? 0;

            return view('earn.index', compact('cashback', 'vouchers'));

        }



    public function morningAzkar()
        {

            $adhkar = include resource_path('views/components/azkar/M_adhkar-data.blade.php');

            $type = 'morning';

            return view('earn.morningAzkar', compact('adhkar', 'type'));
        }


    public function eveningAzkar()
        {
           $adhkar = include resource_path('views/components/azkar/E_adhkar-data.blade.php');
           $type = 'evening';

            return view('earn.eveningAzkar', compact('adhkar', 'type'));


        }

        public function claim(Request $request)
    {
        $request->validate([
            'type' => 'required|in:morning,evening'
        ]);

        $user = auth()->user();

        $reward = AdminReward::where('for', $request->type)->first();

        if (!$reward) {
            return response()->json([
                'status' => 'error',
                'message' => 'Reward not configured'
            ], 404);
        }

        // Prevent double claim (daily optional)
        $already = UserReward::where('user_id', $user->id)
            ->where('type', $request->type)
            ->whereDate('created_at', now())
            ->exists();

        if ($already) {
            return response()->json([
                'status' => 'error',
                'message' => 'Reward already claimed'
            ], 403);
        }

        $wallet = Wallet::firstOrCreate(['user_id' => $user->id]);

        // Apply cashback using admin voucher rate
        $wallet->addCashback(
            (float) $reward->cashback_amount,
            (float) $reward->voucher_rate
        );

        UserReward::create([
            'user_id' => $user->id,
            'amount'  => $reward->cashback_amount,
            'type'    => $request->type,
            'source'  => 'azkar'
        ]);

        return response()->json([
            'status' => 'success',
            'amount' => $reward->cashback_amount
        ]);
    }

    public function makaranta()
        {

            return view('earn.makaranta.index');
        }

        public function friday($shafi = 1)
            {
                $adhkar = include resource_path('views/components/friday/salawat.blade.php');

                $quran = collect(include resource_path('views/components/friday/suratukahf.blade.php'));
                $page = $quran->firstWhere('page', $shafi);

                if (!$page) {
                    abort(404);
                }

                return view('earn.friday', compact('adhkar', 'page'));
            }

    /**
     * Show the darasi view with audio files.
     *
     * @return \Illuminate\View\View
     */
    public function darasi($course = 'sharrindajjal')
        {
            // Audio directory
            $dir = public_path('audios/' . $course);
            $files = [];

            if (is_dir($dir)) {
                $files = array_values(
                    array_filter(
                        scandir($dir),
                        fn($entry) => $entry !== '.' && $entry !== '..' && !is_dir($dir . '/' . $entry)
                    )
                );
                natsort($files);
                $files = array_values($files);
            }

            $displayName = ucwords(str_replace(['_', '-'], ' ', $course));

            return view(
                'earn.makaranta.darasi',
                [
                    'files' => $files,
                    'course' => $course,
                    'displayName' => $displayName
                ]
            );

        }


            //
        /**
         * Show audio player for a specific file.
         *
         * @param string $course The course folder name
         * @param string $file The audio file name
         * @return \Illuminate\View\View
         */

        public function sauraro($course, $file)
            {
                // 🔹 Build audio file path
                $relPath = 'audios/' . $course . '/' . $file;
                $fullPath = public_path($relPath);

                if (!file_exists($fullPath)) {
                    abort(404, 'Audio file not found.');
                }

                // 🔹 Display names
                $displayName = ucwords(str_replace(['_', '-'], ' ', $course));
                $displayFile = ucwords(str_replace(['_', '-'], ' ', pathinfo($file, PATHINFO_FILENAME)));

                // 🔹 Load quiz from config/sauraro/
                $quizConfigPath = config_path("sauraro/quiz_data_{$course}.php");
                $quizzes = [];

                if (file_exists($quizConfigPath)) {
                    $quizConfig = include($quizConfigPath);
                    // Match the audio file name, e.g., "001.mp3"
                    $fileQuizzes = collect($quizConfig)->firstWhere('file', $file);
                    $quizzes = $fileQuizzes ? $fileQuizzes['questions'] : [];
                } else {
                    \Log::warning("Quiz config not found for Sauraro course", [
                        'course' => $course,
                        'path' => $quizConfigPath
                    ]);
                }
                  // ✅ Get reward from DB
                $reward = AdminReward::firstWhere('for', 'sauraro');

                session(['current_course' => $course]);
                            // 🔹 Save course in session (for quiz reward logic)
                            session(['current_course' => $course]);

                            return view('earn.makaranta.sauraro', [
                                'course' => $course,
                                'file' => $file,
                                'path' => $relPath,
                                'displayName' => $displayName,
                                'displayFile' => $displayFile,
                                'quizzes' => $quizzes,
                                'reward' => $reward, // <-- pass to Blade

                            ]);
                        }



        /**
         * Display the reading view for a specific course and page
         *
         * @param int $pageId
         * @return \Illuminate\View\View
         */
            public function karanta($pageId)
                {
                    // Get the current course from the session or default to kurakurai100
                    $course = session('current_course', 'kurakurai100');

                    // Load course-specific content file
                    $contentFile = "components/karanta/{$course}.blade.php";
                    if (!file_exists(resource_path("views/{$contentFile}"))) {
                        abort(404, 'Course content not found.');
                    }

                    $lessonPages = collect(include resource_path("views/{$contentFile}"));
                    $page = $lessonPages->where('page', $pageId)->first();

                    // Handle missing pages gracefully
                    if (!$page) {
                        abort(404, 'Page not found.');
                    }

                    // Load course-specific quiz data
                    $quizConfig = "karanta.quiz_data_{$course}";
                    $allQuizzes = collect(config($quizConfig, []));
                    $pageQuizzes = $allQuizzes->firstWhere('page_id', (int)$pageId);
                    $quizzes = $pageQuizzes ? $pageQuizzes['questions'] : [];

                    // Get display name for the course
                    $displayName = ucwords(str_replace(['_', '-'], ' ', $course));

                    return view('earn.makaranta.karanta', compact('page', 'quizzes', 'course', 'displayName'));
                }


           

            public function submitQuiz(Request $request, $pageId)
{
    $course = session('current_course', 'kurakurai100');
    $type = $request->input('type', 'karanta'); // 'karanta' or 'sauraro'

    try {
        $quizConfigPath = config_path("{$type}/quiz_data_{$course}.php");
        if (!file_exists($quizConfigPath)) {
            return response()->json(['status' => 'error', 'message' => 'Quiz config not found.'], 404);
        }

        $quizConfig = include($quizConfigPath);
        $pageQuizzes = collect($quizConfig)->firstWhere(
            $type === 'karanta' ? 'page_id' : 'file',
            $type === 'karanta' ? (int) $pageId : $pageId
        );

        $quizzes = $pageQuizzes ? $pageQuizzes['questions'] : [];

        if (empty($quizzes)) {
            return response()->json(['status' => 'error', 'message' => 'No quizzes available.'], 400);
        }

        $submitted = collect($request->all())
            ->filter(fn($v, $k) => str_starts_with($k, 'quiz'))
            ->toArray();

        if (empty($submitted)) {
            return response()->json(['status' => 'error', 'message' => 'No answers submitted.'], 400);
        }

        $correctCount = 0;
        foreach ($submitted as $key => $answer) {
            if ($answer === 'correct') $correctCount++;
        }

       
        if ($correctCount === count($submitted)) {
    $rewardConfig = \App\Models\AdminReward::firstWhere('for', $type);
    $cashbackAmount = $rewardConfig ? (float)$rewardConfig->cashback_amount : 50.0;
    $voucherRate = $rewardConfig ? (float)$rewardConfig->voucher_rate : 200.0;

    // Update user's wallet (call addCashback only once)
    $wallet = \App\Models\Wallet::firstOrCreate(['user_id' => auth()->id()]);
    $wallet->addCashback($cashbackAmount, $voucherRate);

    // Record user reward
    \App\Models\UserReward::create([
        'user_id' => auth()->id(),
        'amount' => $cashbackAmount,
        'type' => $type,
        'source' => $pageId,
    ]);

    return response()->json([
        'status' => 'success',
        'message' => "You earned ₦" . number_format($cashbackAmount, 2) . "!",
        'cashback_balance' => (float) $wallet->cashback_balance,
        'voucher_balance' => (int) $wallet->voucher_balance,
        'voucher_rate' => $voucherRate,
    ]);
}

        return response()->json(['status' => 'error', 'message' => '❌ Some answers are incorrect.'], 200);

    } catch (\Throwable $e) {
        \Log::error('Quiz submission failed', ['error' => $e->getMessage()]);
        return response()->json(['status' => 'error', 'message' => 'An unexpected error occurred.'], 500);
    }
}

}
