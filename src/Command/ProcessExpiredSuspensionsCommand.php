<?php

namespace App\Command;

use App\Service\Security\SuspensionService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Lève automatiquement les suspensions arrivées à échéance.
 *
 * À planifier toutes les minutes / heures, par exemple :
 *   * * * * * php bin/console app:suspensions:process-expired
 */
#[AsCommand(
    name: 'app:suspensions:process-expired',
    description: 'Lève automatiquement les suspensions d’organisations et de comptes arrivées à échéance.'
)]
class ProcessExpiredSuspensionsCommand extends Command
{
    public function __construct(
        private readonly SuspensionService $suspensionService
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $now = new \DateTimeImmutable();

        $counts = $this->suspensionService->processExpired($now);

        if ($counts['organizations'] === 0 && $counts['users'] === 0) {
            $io->success('Aucune suspension arrivée à échéance.');
        } else {
            $io->success(sprintf(
                '%d organisation(s) et %d compte(s) réactivé(s) automatiquement.',
                $counts['organizations'],
                $counts['users']
            ));
        }

        return Command::SUCCESS;
    }
}