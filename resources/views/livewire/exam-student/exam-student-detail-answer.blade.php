<div>
    <div class="w-full max-w-md p-4 bg-white border border-gray-300 rounded-lg dark:bg-gray-800 dark:border-gray-700">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Navigasi Soal</h2>
        <p class="mb-4 text-sm text-gray-500 dark:text-gray-400">Pilih Tombol Dibawah Ini untuk berganti soal</p>

        <hr class="border-gray-300 dark:border-gray-600">

        <div class="flex flex-wrap gap-3" style="margin-top: 20px">
            @foreach ($examStudentDetails as $examStudentDetail)
                <div wire:click="choice('{{$examStudentDetail['id']}}')" class="flex items-center justify-center w-10 h-10 font-semibold rounded-md cursor-pointer"
                    style="
                        @if($examStudentDetail['id'] == $exam_student_detail)
                            background-color: #3b82f6; color: white; /* Biru jika terpilih */
                        @else
                            @if($examStudentDetail['status'] === 'belum')
                                background-color: white; color: #09090ba0; border: 1px solid #d1d5db;
                                /* Dark mode */
                                @media (prefers-color-scheme: dark) {
                                    background-color: #1f2937; color: #d1d5db; border: 1px solid #374151;
                                }
                            @elseif($examStudentDetail['status'] === 'ragu-ragu')
                                background-color: #f97316; color: white; /* Orange */
                                /* Dark mode */
                                @media (prefers-color-scheme: dark) {
                                    background-color: #ea580c; color: white;
                                }
                            @elseif($examStudentDetail['status'] === 'terpilih')
                                background-color: #22c55e; color: white; /* Hijau */
                                /* Dark mode */
                                @media (prefers-color-scheme: dark) {
                                    background-color: #16a34a; color: white;
                                }
                            @endif
                        @endif
                    ">
                    {{ $examStudentDetail['label'] }}
                </div>
            @endforeach
        </div>
    </div>
</div>
