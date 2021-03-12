<?php

declare(strict_types=1);

namespace App\Command;

use Modix\Grpc\Service\Example\v1\Metadata\Query;
use Modix\Grpc\Service\Example\v1\Model\StatusCode;
use Modix\Grpc\Service\Example\v1\Model\ToUpperArgs;
use Modix\Grpc\Service\Example\v1\Model\ToUpperResult;
use Modix\Grpc\Service\Example\v1\QueryClient;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Class ExampleClientCommand
 *
 * @package App
 */
class ExampleClientToUpperCommand extends Command
{
    protected static $defaultName = 'client:example:toUpper';

    /**
     * Configure the command
     */
    protected function configure()
    {
        $this
            ->setDescription('Example gRPC client')
            ->addArgument('string', InputArgument::REQUIRED, 'The string for toUppper()');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->info("Calling client::toUpper('". $input->getArgument('string') ."')...");

        $client = new QueryClient("host.docker.internal:3886", [
            'credentials' => \Grpc\ChannelCredentials::createInsecure()
        ]);

        /** @var ToUpperResult $result */
        $args = (new ToUpperArgs)->setString($input->getArgument('string'));
        [$result, $status] = $client->toUpper($args)->wait();

        switch ($status->code) {
            case StatusCode::OK:
                $io->success($result->getString());
                return Command::SUCCESS;
            case StatusCode::EMPTY_STRING:
                $io->error("The ExampleStatus::EMPTY was thrown: ". $status->details);
                return Command::FAILURE;
            default:
                print_r($status);
                $io->error(sprintf("Other Error: %s (Code: %d)", $status->details, $status->code));
                return Command::FAILURE;
        }
    }
}
