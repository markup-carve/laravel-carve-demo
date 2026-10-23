<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function test_includes_page_renders_file_and_configuration_content(): void
    {
        $this->get('/includes')
            ->assertOk()
            ->assertSee('Reusable content')
            ->assertSee('parts/intro.crv')
            ->assertSee('Account status');
    }

    public function test_includes_page_selects_regions_code_and_csv_data(): void
    {
        $this->get('/includes')
            ->assertOk()
            ->assertSee('<li>PHP 8.2 or newer</li>', escape: false)
            ->assertDontSee('This intro stays out of the handbook')
            ->assertSee('<code class="language-js">const server', escape: false)
            ->assertDontSee('export default server')
            ->assertSee('<td>Enterprise, annual</td>', escape: false);
    }
}
