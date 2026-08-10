<?php

namespace App\Command;

use App\Entity\Identity\Administrator;
use App\Entity\Common\UserStatus;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:security:create-root',
    description: 'Crée ou met à jour l’administrateur système principal (ROLE_ROOT) de manière sécurisée.',
)]
class CreateRootUserCommand extends Command
{
    private const DEFAULT_EMAIL = 'root@diabcare.com';

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly LoggerInterface $logger,
        #[Autowire('%kernel.environment%')]
        private readonly string $kernelEnvironment
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('email', null, InputOption::VALUE_OPTIONAL, 'Email du compte root', self::DEFAULT_EMAIL)
            ->addOption('password', null, InputOption::VALUE_OPTIONAL, 'Mot de passe initial (généré si omis)')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Initialisation du compte Root Entreprise');

        if ($this->kernelEnvironment === 'prod' && !$input->getOption('password')) {
            $io->error('En environnement de production, vous devez impérativement fournir un mot de passe sécurisé via l\'option --password.');
            return Command::FAILURE;
        }

        $email = $input->getOption('email');
        $plainPassword = $input->getOption('password') ?? bin2hex(random_bytes(16));

        $userRepository = $this->entityManager->getRepository(Administrator::class);
        $user = $userRepository->findOneBy(['email' => $email]);

        $isNew = false;
        if (!$user) {
            $isNew = true;
            $user = new Administrator();
            $user->setEmail($email);
            $user->setFirstName('System');
            $user->setLastName('Root');
            $user->setGender(\App\Entity\Common\Gender::OTHER); // <-- Adaptez selon votre Enum ou string (ex: 'OTHER', 'M', 'F')
            $user->setStatus(UserStatus::ACTIVE);
            $user->setEmailVerifiedAt(new \DateTimeImmutable());
            $io->text(sprintf('Création d\'un nouveau profil root pour : %s', $email));
        } else {
            $io->text(sprintf('Mise à jour du profil root existant pour : %s', $email));
        }

        $hashedPassword = $this->passwordHasher->hashPassword($user, $plainPassword);
        $user->setPassword($hashedPassword);

        try {
            if ($isNew) {
                $this->entityManager->persist($user);
            }
            $this->entityManager->flush();

            $this->logger->critical('Action critique : Le compte administrateur ROOT a été créé ou mis à jour via la console.', [
                'email' => $email,
                'environment' => $this->kernelEnvironment
            ]);

        } catch (\Exception $e) {
            $io->error('Erreur lors de la persistance en base de données : ' . $e->getMessage());
            return Command::FAILURE;
        }

        $io->success($isNew ? 'Compte ROOT créé avec succès.' : 'Compte ROOT mis à jour avec succès.');

        $io->section('Informations d\'identification (À conserver de manière sécurisée) :');
        $io->listing([
            sprintf('E-mail : %s', $email),
            sprintf('Mot de passe : %s', $plainPassword),
        ]);

        return Command::SUCCESS;
    }
}
