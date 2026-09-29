<?php

use Illuminate\Support\Facades\File;

test('env generate fails if example file does not exist', function () {
    $this->artisan('env:generate', ['--example' => '.non_existent_example'])
        ->assertFailed();
});

test('env generate prompts for confirmation if .env already exists without force', function () {
    $this->artisan('env:generate')
        ->expectsConfirmation('O arquivo .env já existe. Deseja sobrescrevê-lo?', 'no')
        ->assertSuccessful();
});

test('env generate overwrites .env when force option is passed', function () {
    $tempExample = base_path('.env.test_example');
    File::put($tempExample, "APP_NAME=TestGenerated\nAPP_KEY=\n");

    $this->artisan('env:generate', [
        '--example' => '.env.test_example',
        '--force' => true,
    ])->assertSuccessful();

    $content = File::get(base_path('.env'));
    expect($content)->toContain('APP_NAME=TestGenerated');

    // Clean up temporary example file and restore .env from .env.example
    File::delete($tempExample);
    File::copy(base_path('.env.example'), base_path('.env'));
    $this->artisan('key:generate', ['--force' => true]);
});
