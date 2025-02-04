<?php

namespace App\Providers;

use App\Models\SystemSetting\SystemSetting;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Vite::prefetch(concurrency: 3);

        $this->_forceSchemeHttps();
        $this->_getSystemSetting();
        $this->_customBladeDirective();
        $this->_customRequest();
    }

    private function _forceSchemeHttps()
    {
        if (config('app.env') != 'local') {
            URL::forceHttps(true);
            URL::forceScheme('https');
            $this->app['request']->server->set('HTTPS', true);
        }
    }

    private function _getSystemSetting()
    {
        if(!Schema::hasTable('cache') && !Schema::hasTable('system_settings')) return true;

        Cache::rememberForever('system_setting', function () {
            $columns = Schema::getColumnListing('system_settings');
            $desiredColumns = array_diff($columns, ['id', 'deleted_at', 'created_at', 'updated_at']);

            return SystemSetting::select($desiredColumns)->first();
        });
    }

    private function _customBladeDirective()
    {
        Blade::directive('rupiah', function ($amount) {
            return "<?php echo 'Rp. ' . Number::format($amount, locale: 'id'); ?>";
        });

        Blade::directive('number', function ($amount) {
            return "<?php echo Number::format($amount, locale: 'id'); ?>";
        });
    }

    private function _customRequest()
    {
        Request::macro('subdomain', function () {
            return current(explode('.', $this->getHost()));
        });
    }
}
