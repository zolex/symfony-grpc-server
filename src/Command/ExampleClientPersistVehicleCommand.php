<?php

declare(strict_types=1);

namespace App\Command;

use Modix\Grpc\Service\Example\v1\CommandClient;
use Modix\Grpc\Service\Example\v1\Model\Vehicle;
use Spiral\RoadRunner\GRPC\StatusCode;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: "client:example:persistVehicle"
)]
class ExampleClientPersistVehicleCommand extends Command
{
    public function __construct(private CommandClient $client)
    {
        parent::__construct(null);
    }

    /**
     * Configure the command
     */
    protected function configure()
    {
        $this
            ->setDescription('Example gRPC client to call persistVehicle')
            ->addArgument('make', InputArgument::REQUIRED)
            ->addArgument('model', InputArgument::REQUIRED)
            ->addArgument('type', InputArgument::REQUIRED)
            ->addArgument('dealerId', InputArgument::REQUIRED);
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
        $io->info("Calling client::persistVehicle()");

        /** @var Vehicle $result */
        $args = (new Vehicle)
            ->setDealer((int)$input->getArgument('dealerId'))
            ->setMake($input->getArgument('make'))
            ->setModel($input->getArgument('model'))
            ->setType($input->getArgument('type'));
        [$result, $status] = $this->client->persistVehicle($args)->wait();

        switch ($status->code) {
            case StatusCode::OK:
                $io->success(sprintf("new vehicle ID: %d, Make: %s, Model: %s, Type: %d, Dealer ID: %d", $result->getId(), $result->getMake(), $result->getModel(), $result->getType(), $result->getDealer()));
                return Command::SUCCESS;
            default:
                print_r($status);
                $io->error(sprintf("Other Error: %s (Code: %d)", $status->details, $status->code));
                return Command::FAILURE;
        }
    }
}
