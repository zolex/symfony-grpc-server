<?php

declare(strict_types=1);

namespace App\Command;

use Modix\Grpc\Service\Other\v1\Model\MultiplyArgs;
use Modix\Grpc\Service\Other\v1\Model\MultiplyResult;
use Modix\Grpc\Service\Other\v1\QueryClient;
use Spiral\GRPC\StatusCode;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Zolex\GrpcBundle\GRPC\ClientFactory;

/**
 * Class OtherClientMultiplyCommand
 *
 * @package App\Command
 */
class OtherClientMultiplyCommand extends Command
{
    protected static $defaultName = 'client:other:multiply';

    public function __construct(private QueryClient $client)
    {
        parent::__construct(null);
    }

    /**
     * Configure the command
     */
    protected function configure()
    {
        $this
            ->setDescription('Other gRPC client')
            ->addArgument('a', InputArgument::REQUIRED)
            ->addArgument('b', InputArgument::REQUIRED);
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $a = (int)$input->getArgument('a');
        $b = (int)$input->getArgument('b');

        $io = new SymfonyStyle($input, $output);
        $io->info("Calling client::multiply(". $a .", ". $b .")...");

        /** @var MultiplyResult $result */
        $args = (new MultiplyArgs)->setA($a)->setB($b);
        [$result, $status] = $this->client->multiply($args)->wait();

        switch ($status->code) {
            case StatusCode::OK:
                $io->success($result->getResult());
                return Command::SUCCESS;
            default:
                $io->error(print_r($status, true));
                return Command::FAILURE;
        }
    }
}
