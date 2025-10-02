<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate; // 👈 Add this
use App\Models\Paper;                  // 👈 Add this
use App\Policies\PaperPolicy; 

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
        // This line manually registers your policy. It's guaranteed to work.
         Gate::policy(Paper::class, PaperPolicy::class);

        

    }
}
