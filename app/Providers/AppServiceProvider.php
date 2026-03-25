<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Blade; 
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Blade::directive('money', function ($expression) {
            return "<?php echo 'Bs. ' . number_format($expression, 2, '.', ','); ?>";
        });

        // Compartir el contador del carrito con todas las vistas (Tu código original intacto)
        View::composer('*', function ($view) {
            $cartCount = 0;
            if (Auth::check()) {
                $cartCount = Auth::user()->cartCount();
            }
            $view->with('cartCount', $cartCount);
        });
    }
}