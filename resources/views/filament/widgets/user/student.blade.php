<x-filament-widgets::widget>
    <div class="grid grid-cols-1 p-6 bg-white shadow-lg lg:grid-cols-2 rounded-2xl dark:bg-gray-800">
        <!-- User Photo -->
        <div class="flex justify-center lg:justify-start">
            <div class="w-32 h-32 overflow-hidden border-2 border-gray-200 rounded-full dark:border-gray-600">
                <img src="{{ $user->photo ? asset('storage/'.$user->photo) : asset('assets/image/blank-profile-picture-973460_1280.png') }}" alt="{{ $user->name }}" class="object-cover w-full h-full">
            </div>
        </div>

        <!-- User Details -->
        <div class="flex flex-col justify-center space-y-4">
            <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ $user->name }}</h2>
            <p class="text-sm text-gray-600 dark:text-gray-300">
                <span class="font-medium">Email:</span> {{ $user->email }}
            </p>
            <p class="text-sm text-gray-600 dark:text-gray-300">
                <span class="font-medium">Username:</span> {{ $user->username }}
            </p>
            <p class="text-sm text-gray-600 dark:text-gray-300">
                <span class="font-medium">Nomer Handphone:</span> {{ $user->phone ?? 0 }}
            </p>
        </div>
    </div>
</x-filament-widgets::widget>
