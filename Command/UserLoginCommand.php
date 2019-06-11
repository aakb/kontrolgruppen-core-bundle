<?php

/*
 * This file is part of aakb/kontrolgruppen-core-bundle.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace Kontrolgruppen\CoreBundle\Command;

use Doctrine\ORM\EntityManagerInterface;
use Kontrolgruppen\CoreBundle\Repository\UserRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\RuntimeException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Routing\RouterInterface;

class UserLoginCommand extends Command
{
    protected static $defaultName = 'kontrolgruppen:user:login';

    /** @var \App\Repository\UserRepository */
    private $userRepository;

    /** @var \Doctrine\ORM\EntityManagerInterface */
    private $entityManager;

    /** @var \Symfony\Component\Routing\RouterInterface */
    private $router;

    public function __construct(UserRepository $userRepository, EntityManagerInterface $entityManager, RouterInterface $router)
    {
        parent::__construct();
        $this->userRepository = $userRepository;
        $this->entityManager = $entityManager;
        $this->router = $router;
    }

    public function configure()
    {
        $this
            ->addArgument('username', InputArgument::REQUIRED, 'The username')
            ->addArgument('destination', InputArgument::OPTIONAL, 'The destination');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $username = $input->getArgument('username');

        $user = $this->userRepository->find($username) ?? $this->userRepository->findOneBy(['email' => $username]);
        if (null === $user) {
            throw new RuntimeException(sprintf('Invalid username: %s', $username));
        }

        $token = uniqid('', true);
        $user->setCliLoginToken($token);
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $parameters = [
            'cli-login-token' => $token,
        ];
        $destination = $input->getArgument('destination');
        if ($destination) {
            $parameters['destination'] = $destination;
        }

        $url = $this->router->generate('fos_user_security_login', $parameters, RouterInterface::ABSOLUTE_URL);
        $output->writeln($url);
    }
}
