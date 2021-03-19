<?php

declare(strict_types=1);

namespace App\Command;

use App\GRPC\ClientFactory;
use Modix\Grpc\Service\Other\v1\Model\MultiplyArgs;
use Modix\Grpc\Service\Other\v1\Model\MultiplyResult;
use Modix\Grpc\Service\Other\v1\QueryClient;
use Spiral\GRPC\StatusCode;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Class OtherClientMultiplyCommand
 *
 * @package App\Command
 */
class OtherClientMultiplyCommand extends Command
{
    protected static $defaultName = 'client:other:multiply';
    private ClientFactory $clientFactory;

    public function __construct(ClientFactory $clientFactory)
    {
        parent::__construct(null);
        $this->clientFactory = $clientFactory;
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

        $client = $this->clientFactory->create(QueryClient::class);

        /** @var MultiplyResult $result */
        $args = (new MultiplyArgs)->setA($a)->setB($b);
        [$result, $status] = $client->multiply($args)->wait();

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
