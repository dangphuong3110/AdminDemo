<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Route;

class SearchRoutes extends Command
{
    protected $signature = 'route:search {uri}';

    protected $description = 'Searches for a route by URI.';

    public function handle()
    {
        $uri = $this->argument('uri');

        $routes = Route::getRoutes()->getIterator();

        foreach ($routes as $route) {
            if (str_contains($route->uri(), $uri)) {
                $this->line('');
                $this->info("Route Name: {$route->getName()}");
                $this->info("Method: {$route->methods()[0]}");
                $this->info("URI: {$route->uri()}");
                $this->info("Action: {$route->getActionName()}");
                $this->line('');
            }
        }

        $this->line("<info>Route search complete.</info>");
    }
}
