<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;


#[AsCommand(
    name: 'app:is-verified-deleted',
    description: 'Vérifier les user verified à supprimer au bout de 1 heure',
)]
class IsVerifiedDeletedCommand extends Command
{
    public function __construct(
        private readonly EntityManagerInterface $EM
    )
    {
        parent::__construct();
    }
    //tâche Cron
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $users = $this->EM
        ->getRepository(User::class)
        ->createQueryBuilder('utilisateur')
        ->where('utilisateur.isVerified = false')
        ->andWhere('utilisateur.joinedAt < :expiration')
        ->setParameter('expiration', new \DateTimeImmutable('-1 hour'))
        ->getQuery()
        ->getResult();

        foreach ($users as $user) {
            $this->EM->remove($user);
        }

        $this->EM->flush();
        $io->success(count($users) . ' Utilisateurs ont été supprimés.');
        return Command::SUCCESS;
    }
}
