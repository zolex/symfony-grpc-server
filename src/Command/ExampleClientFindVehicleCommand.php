<?php

declare(strict_types=1);

namespace App\Command;

use Modix\Grpc\Service\Example\v1\Model\Vehicle;
use Modix\Grpc\Service\Example\v1\Model\VehicleFilter;
use Modix\Grpc\Service\Example\v1\QueryClient;
use Spiral\GRPC\StatusCode;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Zolex\GrpcBundle\GRPC\ClientFactory;

/**
 * Class ExampleClientFindVehicleCommand
 *
 * @package App
 */
class ExampleClientFindVehicleCommand extends Command
{
    protected static $defaultName = 'client:example:findVehicle';
    private \Zolex\GrpcBundle\GRPC\ClientFactory $clientFactory;

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

        $client = $this->clientFactory->create(QueryClient::class);

        /** @var Vehicle $result */
        $args = (new VehicleFilter)->setId((int)$input->getArgument('id'));
        [$result, $status] = $client->findVehicle($args)->wait();

        switch ($status->code) {
            case StatusCode::OK:
                $io->success(sprintf("found vehicle ID: %d, Make: %s, Model: %s, Type: %d, Dealer ID: %d", $result->getId(), $result->getMake(), $result->getModel(), $result->getType(), $result->getDealer()));
                return Command::SUCCESS;
            default:
                print_r($status);
                $io->error(sprintf("Other Error: %s (Code: %d)", $status->details, $status->code));
                return Command::FAILURE;
        }
    }
}
