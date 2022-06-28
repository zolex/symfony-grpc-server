<?php

declare(strict_types=1);

namespace App\Command;

use Modix\Grpc\Service\Example\v1\Model\Vehicle;
use Modix\Grpc\Service\Example\v1\Model\VehicleFilter;
use Modix\Grpc\Service\Example\v1\QueryClient;
use Spiral\RoadRunner\GRPC\StatusCode;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: "client:example:findVehicle"
)]
class ExampleClientFindVehicleCommand extends Command
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
            ->setDescription('Example gRPC client to call finVehicle')
            ->addArgument('id', InputArgument::REQUIRED);
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
        $io->info("Calling client::findVehicle()");

        /** @var Vehicle $result */
        $args = (new VehicleFilter)->setId((int)$input->getArgument('id'));
        [$result, $status] = $this->client->findVehicle($args)->wait();

        switch ($status->code) {
            case StatusCode::OK:
                $io->success(sprintf("found vehicle ID: %d, Make: %s, Model: %s, Type: %d, Dealer ID: %d", $result->getId(), $result->getMake(), $result->getModel(), $result->getType(), $result->getDealer()));
                return Command::SUCCESS;
            case StatusCode::NOT_FOUND:
                $io->error(sprintf("NOT_FOUND: %s", $status->details));
                return Command::FAILURE;
            default:
                print_r($status);
                $io->error(sprintf("Other Error: %s (Code: %d)", $status->details, $status->code));
                return Command::FAILURE;
        }
    }
}
