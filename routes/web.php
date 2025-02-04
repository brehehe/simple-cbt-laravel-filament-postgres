<?php

use App\Models\ExamStudent\ExamStudent;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Route;

Route::group(['namespace'=>'App\Livewire'], function () {
    Route::group(['namespace'=>'Question'], function () {
        Route::get('/soal/soal/{question_id}/{question_item_id}/edit','QuestionItemAnswerView');
    });
    Route::group(['namespace'=>'Check'], function () {
        Route::get('/soal/finish','Exam');
    });
});

// Route::get('/sse/countdown/{id}', function ($id) {
//     return Response::stream(function () use ($id) {
//         while (true) {
//             $event = ExamStudent::find($id);
//             if (!$event) break;

//             $createdAt = Carbon::parse($event->created_at);
//             $now = Carbon::now();
//             \Log::info("Created At: " . $createdAt);
//             \Log::info("Now: " . $now);

//             // Hitung waktu berakhir berdasarkan created_at dan minutes
//             $endTime = $createdAt->addMinutes($event->minutes);
//             \Log::info("End Time: " . $endTime);

//             // Gunakan perbandingan langsung
//             $remainingTime = $endTime->timestamp - $now->timestamp; // Gunakan timestamp untuk perhitungan detik

//             \Log::info("Remaining Time in Seconds: " . $remainingTime);

//             if ($remainingTime <= 0) {
//                 \Log::info("Time has expired.");
//                 break;
//             }

//             // Kirim data SSE
//             echo "data: " . json_encode(['remaining' => $remainingTime]) . "\n\n";
//             ob_flush();
//             flush();

//             // Tunggu 1 detik sebelum update
//             sleep(1);
//         }
//     }, 200, [
//         'Content-Type' => 'text/event-stream',
//         'Cache-Control' => 'no-cache',
//         'Connection' => 'keep-alive',
//     ]);
// });


Route::get('/check-auth', function() {
    return Auth::check() ? 'User is authenticated' : 'No user authenticated';
});
