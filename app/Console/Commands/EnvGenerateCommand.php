<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

#[Signature('env:generate {--force : Sobrescrever o arquivo .env existente} {--example=.env.example : Arquivo de modelo a ser copiado}')]
#[Description('Gera o arquivo .env a partir do .env.example e gera a chave de aplicação (APP_KEY)')]
class EnvGenerateCommand extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $envPath = base_path('.env');
        $exampleFile = (string) ($this->option('example') ?: '.env.example');
        $examplePath = base_path($exampleFile);

        if (! File::exists($examplePath)) {
            $this->components->error("O arquivo de modelo [{$exampleFile}] não foi encontrado.");

            return self::FAILURE;
        }

        if (File::exists($envPath) && ! $this->option('force')) {
            if (! $this->components->confirm('O arquivo .env já existe. Deseja sobrescrevê-lo?', false)) {
                $this->components->warn('Operação cancelada. O arquivo .env existente foi mantido.');

                return self::SUCCESS;
            }
        }

        if (! File::copy($examplePath, $envPath)) {
            $this->components->error('Falha ao copiar o arquivo de modelo para .env.');

            return self::FAILURE;
        }

        $this->components->info('Arquivo .env criado com sucesso a partir de '.$exampleFile.'.');

        $this->call('key:generate', [
            '--force' => true,
        ]);

        return self::SUCCESS;
    }
}
