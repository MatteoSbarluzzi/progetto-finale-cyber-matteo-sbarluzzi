<?php

namespace App\Providers;

use App\Models\Category;
use App\Models\Tag;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
// aggiunte per il rate limiting
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;

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

        if (Schema::hasTable('categories')) {
            $categories = Category::all();
            View::share(['categories' => $categories]);
        }
        if (Schema::hasTable('tags')) {
            $tags = Tag::all();
            View::share(['tags' => $tags]);
        }
        
        // Rate limiter
        RateLimiter::for('articles-search', function (Request $request) {
            $ip = $request->ip();
            
            return [
                Limit::perMinute(30)->by($ip)->response(function () use ($ip) {
                    // Logga quando un IP supera il limite
                    \Log::warning("429 Too Many Requests per IP {$ip} su /articles/search");
                    // Blocco IP per 15 minuti
                    Cache::put("ip-over-limit:$ip", true, now()->addMinutes(15));
                    
                    return response()->json([
                        'message' => 'Troppi tentativi: IP temporaneamente bloccato.'
                    ], 429);
                }),
            ];
        });
        
    }
}
