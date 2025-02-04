<div>
    <div class="py-6">
        <div class="flex flex-wrap justify-center gap-6">
            @foreach ($examStudentDetails as $id => $detail)
                <div class="flex items-center">
                    <!-- Tombol dengan Angka, yang akan mengubah status saat diklik -->
                    <button
                        class="text-xl font-medium text-white transition-all duration-300 ease-in-out
                            {{$detail['status'] == 'belum' ? 'bg-gray-400' :
                            ($detail['status'] == 'ragu-ragu' ? 'bg-orange-500' : 'bg-green-500') }}
                            hover:scale-105 hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                        style="border: 2px solid #333333;"
                        wire:click="toggleStatus('{{ $id }}')">
                        {{ $detail['number'] }}
                    </button>
                </div>
            @endforeach
        </div>
    </div>

</div>
