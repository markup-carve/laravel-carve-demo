<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Route as RouteFacade;
use Tests\TestCase;

class DemoRouteCoverageTest extends TestCase
{
    public function test_every_public_demo_page_is_declared_and_reachable(): void
    {
        $declared = config('demo-pages.pages');
        $allowedModes = ['static', 'browser-enhanced', 'local-only'];
        $routes = collect(RouteFacade::getRoutes()->getRoutes())
            ->filter(fn (Route $route): bool => in_array('GET', $route->methods(), true))
            ->reject(fn (Route $route): bool => in_array($route->uri(), ['up', 'storage/{path}'], true));

        foreach ($routes as $route) {
            $this->assertNotNull($route->getName(), "Public GET route /{$route->uri()} must be named and classified");
        }

        $actual = $routes
            ->mapWithKeys(fn (Route $route): array => [$route->getName() => '/'.ltrim($route->uri(), '/')])
            ->all();

        $this->assertSame(array_keys($declared), array_keys($actual));

        foreach ($declared as $name => $mode) {
            $this->assertContains($mode, $allowedModes, "Unknown mode for {$name}");
            $this->get($actual[$name])
                ->assertOk()
                ->assertSee('<nav', false)
                ->assertSee('href="https://github.com/markup-carve/laravel-carve-demo"', false)
                ->assertSee('href="https://github.com/markup-carve/laravel-carve"', false);
        }
    }

    public function test_form_submission_validates_and_renders_a_preview(): void
    {
        $this->post('/form', [])->assertSessionHasErrors(['title', 'body']);

        $this->post('/form', [
            'title' => 'Preview title',
            'body' => '# Preview',
            'comment' => '*Safe comment*',
        ])->assertRedirect('/form');

        $this->get('/form')
            ->assertOk()
            ->assertSee('Preview title')
            ->assertSee('<h2>Preview</h2>', false);
    }
}
