<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:secret-id-generator',
    description: 'Création ID pour .env.dev.local',
)]
class SecretIdGeneratorCommand extends Command
{
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $envFile = '.env.dev.local';

        if (file_exists('.env.dev.local')) {
            $io->error("Le fichier {$envFile} existe déjà.");

            return Command::FAILURE;
        }

        $Secret_key = bin2hex(random_bytes(16));
        file_put_contents($envFile, "APP_SECRET={$Secret_key}");

        $io->success('Le fichier a été généré avec succès');

        return Command::SUCCESS;
    }
}
