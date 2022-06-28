<?php

declare(strict_types=1);

namespace App\Command;

use Modix\Grpc\Service\Other\v1\Model\MultiplyArgs;
use Modix\Grpc\Service\Other\v1\Model\MultiplyResult;
use Modix\Grpc\Service\Other\v1\QueryClient;
use Spiral\RoadRunner\GRPC\StatusCode;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: "client:other:multiply"
)]
class OtherClientMultiplyCommand extends Command
{
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
                $io->success(sprintf("Result: %d", $result->getResult()));
                return Command::SUCCESS;
            default:
                $io->error(print_r($status, true));
                return Command::FAILURE;
        }
    }
}
