<?php

namespace AcMarche\MeriteSportif\Command;

use AcMarche\MeriteSportif\Repository\CandidatRepository;
use AcMarche\MeriteSportif\Repository\ClubRepository;
use AcMarche\MeriteSportif\Repository\UserRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'merite:fix', description: 'Fix trim email'
)]
class FixCommand extends Command
{
    public function __construct(
        private UserRepository $userRepository,
        private ClubRepository $clubRepository,
        private CandidatRepository $candidatRepository,
        string $name = null
    ) {
        parent::__construct($name);
    }

    protected function configure(): void
    {

    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        foreach ($this->userRepository->findAll() as $user) {
        //    $user->setEmail(strtolower($user->getEmail()));
         //   $user->setUsername(strtolower($user->getUsername()));
        }
        foreach ($this->clubRepository->findAll() as $club) {
          //  $club->setEmail(strtolower($club->getEmail()));
        }

        foreach ($this->candidatRepository->findAll() as $candidat) {
            $candidat->setUuid($candidat->generateUuid());
        }
        $this->userRepository->flush();

        return 0;
    }
}
