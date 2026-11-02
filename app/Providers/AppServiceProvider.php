<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Blade;

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
        Auth::guard('web')->setRememberDuration(43200);

        Blade::directive('DurationFormat', function($e){
            return "<?php 
                \$v = (float)\$e;
                \$r = floor(\$v);
                \$d = \$v - \$r;

                if (\$d >= 0.25 && \$d < 0.75) {
                    echo \$r . '½';
                } elseif (\$d >= 0.75) {
                    echo \$r + 1;
                } else {
                    echo \$r;
                }
            ?>";
        });
    }
}
