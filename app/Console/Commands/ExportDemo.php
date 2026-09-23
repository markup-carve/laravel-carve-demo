<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;
use Illuminate\Routing\Route as IlluminateRoute;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;
use RuntimeException;

class ExportDemo extends Command
{
    protected $signature = 'demo:export {output=dist}';

    protected $description = 'Export the declared demo pages as a static site';

    public function handle(Kernel $kernel): int
    {
        if (! config('demo-pages.static_export')) {
            $this->components->error('Set STATIC_EXPORT=true before exporting.');

            return self::FAILURE;
        }

        $outputArgument = (string) $this->argument('output');
        if (str_starts_with($outputArgument, '/') || str_contains($outputArgument, '..')) {
            throw new RuntimeException('The output must be a relative path inside the project.');
        }
        $output = base_path($outputArgument);
        if (is_dir($output)) {
            throw new RuntimeException("Output directory already exists: {$output}");
        }

        mkdir($output, 0777, true);
        file_put_contents($output.'/.nojekyll', '');
        URL::forceRootUrl((string) config('app.url'));
        URL::forceScheme((string) parse_url((string) config('app.url'), PHP_URL_SCHEME));

        foreach (config('demo-pages.pages') as $name => $mode) {
            $route = Route::getRoutes()->getByName($name);
            if (! $route instanceof IlluminateRoute) {
                throw new RuntimeException("Declared route does not exist: {$name}");
            }

            $uri = '/'.ltrim($route->uri(), '/');
            $request = Request::create($uri, 'GET');
            $response = $kernel->handle($request);
            if ($response->getStatusCode() !== 200) {
                throw new RuntimeException("{$uri} returned {$response->getStatusCode()}");
            }

            $directory = $uri === '/' ? $output : $output.$uri;
            if (! is_dir($directory)) {
                mkdir($directory, 0777, true);
            }
            $basePath = rtrim((string) parse_url((string) config('app.url'), PHP_URL_PATH), '/');
            $html = preg_replace(
                '/((?:href|src|action)=(["\']))\/(?!\/|'.preg_quote(ltrim($basePath, '/'), '/').'\/)/',
                '$1'.$basePath.'/',
                (string) $response->getContent(),
            );
            file_put_contents($directory.'/index.html', $html);
            $kernel->terminate($request, $response);
            $this->line("{$uri} [{$mode}]");
        }

        return self::SUCCESS;
    }
}
