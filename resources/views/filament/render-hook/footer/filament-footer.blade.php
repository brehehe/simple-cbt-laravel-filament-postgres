@if (Auth::check())
    <div>
        <footer class="fi-footer w-full px-4 md:px-6 lg:px-8 py-6 flex">
            <div>
                <span class="text-sm text-gray-500 sm:text-center dark:text-gray-400">Hak Cipta © {{ now()->year }} |
                    <a href="/" class="hover:underline">{{ Str::upper(cache('system_setting')?->name) }}</a>
                </span>
            </div>
            <div class="ml-auto">
                <span class="text-sm text-gray-500 sm:text-center dark:text-gray-400">
                    Made with ❤️
                </span>
            </div>
        </footer>
    </div>
@endif
