<div>
    <div class="flex justify-center space-x-2">
        <h1 class="text-lg font-bold text-gray-900 dark:text-gray-100">Sisa Waktu :</h1> &nbsp;
        <p id="countdown" class="text-lg font-semibold text-red-600 dark:text-red-400"></p>
    </div>
</div>

@push('scripts')
<script>
    function startCountdown(totalSeconds) {
        const countdownElement = document.getElementById("countdown");
        let remainingTime = totalSeconds;
        let interval; // Deklarasikan interval di sini

        function updateCountdown() {
            if (remainingTime <= 0) {
                countdownElement.innerHTML = "Waktu Habis";
                clearInterval(interval); // interval sekarang dapat diakses

                // Cek apakah Livewire tersedia
                if (window.Livewire) {
                    setTimeout(() => {
                        Livewire.dispatch('processExam'); // Pastikan Livewire tersedia
                    }, 100); // Tambahkan delay kecil
                } else {
                    console.error('Livewire tidak terdeteksi!');
                }
                return;
            }

            const hours = Math.floor(remainingTime / 3600);
            const minutes = Math.floor((remainingTime % 3600) / 60);
            const seconds = remainingTime % 60;

            countdownElement.innerHTML = `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
            remainingTime--;
        }

        interval = setInterval(updateCountdown, 1000); // Inisialisasi interval setelah fungsi dideklarasikan
        updateCountdown(); // Panggil update pertama kali agar tidak delay 1 detik
    }

    document.addEventListener("DOMContentLoaded", function() {
        const totalSeconds = {{$remainingTime}};
        // const totalSeconds = 10;
        startCountdown(totalSeconds);
    });
</script>
@endpush


{{-- <div>
    <div x-data="{
        remainingTime: {{ $remainingTime }},
        stop: false
    }"
    x-init="
        let interval = setInterval(() => {
            if (remainingTime > 0) {
                remainingTime--;
            } else {
                if (!stop) {
                    stop = true;
                    Livewire.dispatch('processExam'); // Panggil event saat waktu habis
                }
                clearInterval(interval);
            }
        }, 1000);
    ">
    <p class="text-lg font-bold">
        Sisa waktu: <span x-text="new Date(remainingTime * 1000).toISOString().substr(11, 8)"></span>
    </p>
    </div>

</div> --}}
