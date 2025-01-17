<?php

namespace Spatie\LaravelTypeScriptTransformer;

use Illuminate\Support\Arr;
use Illuminate\Support\ServiceProvider;
use Spatie\LaravelTypeScriptTransformer\Commands\TypeScriptTransformCommand;
use Spatie\TypeScriptTransformer\TypeScriptTransformerConfig;
use Spatie\TypeScriptTransformer\Writers\TypeDefinitionWriter;

class TypeScriptTransformerServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                TypeScriptTransformCommand::class,
            ]);
        }

        $this->publishes([
            __DIR__ . '/../config/typescript-transformer.php' => config_path('typescript-transformer.php'),
        ]);
    }

    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/typescript-transformer.php',
            'typescript-transformer'
        );

        $this->app->bind(
            TypeScriptTransformerConfig::class,
            fn () => TypeScriptTransformerConfig::create()
                ->autoDiscoverTypes(...Arr::wrap(config('typescript-transformer.auto_discover_types')))
                ->collectors(config('typescript-transformer.collectors'))
                ->transformers(config('typescript-transformer.transformers'))
                ->defaultTypeReplacements(config('typescript-transformer.default_type_replacements'))
                ->writer(config('typescript-transformer.writer'))
                ->outputFile(config('typescript-transformer.output_file'))
                ->writer(config('typescript-transformer.writer', TypeDefinitionWriter::class))
                ->formatter(config('typescript-transformer.formatter'))
                ->transformToNativeEnums(config('typescript-transformer.transform_to_native_enums', false))
        );
    }
}
