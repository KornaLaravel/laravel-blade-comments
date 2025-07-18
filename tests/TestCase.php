<?php

namespace Spatie\BladeComments\Tests;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;
use Livewire\Livewire;
use Livewire\LivewireServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;
use Spatie\BladeComments\BladeCommentsServiceProvider;
use Spatie\BladeComments\Tests\TestSupport\BladeComponents\TestBladeComponent;
use Spatie\BladeComments\Tests\TestSupport\Livewire\TestLivewireComponent;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        ray()->newScreen($this->name());

        $this->artisan('view:clear');
    }

    protected function getPackageProviders($app): array
    {
        return [
            BladeCommentsServiceProvider::class,
            LivewireServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app): void
    {
        config()->set('app.key', '6rE9Nz59bGRbeMATftriyQjrpF7DcOQm');
        config()->set('blade-comments.enable', true);
        config()->set('blade-comments.blade_paths', false);

        View::addLocation(__DIR__.'/TestSupport/views');

        Blade::component('test-component', TestBladeComponent::class);
        Livewire::component('test-component', TestLivewireComponent::class);
    }

    public function rerunServiceProvider(): void
    {
        $provider = new BladeCommentsServiceProvider($this->app);

        $provider->packageBooted();
    }

    public function preparedLivewireHtmlForSnapshot(string $html): string
    {
        // remove all wire:* attributes
        $html = preg_replace('/(\s+(wire:[\w-]+)=(?<q>[\'"]).*?(?P=q))/s', '', $html);

        // remove wire-end random string
        $html = preg_replace('/wire-end:[^ ]+\s*/', '', $html);

        // remove "livewire component" comment that is added only by livewire <= 2.x
        $html = str_replace("\n<!-- Livewire Component -->", '', $html);

        return $html;
    }
}
