<?php

declare(strict_types=1);

namespace App\Command;

use App\GRPC\ClientFactory;
use Modix\Grpc\Service\Example\v1\CommandClient;
use Modix\Grpc\Service\Example\v1\Model\Vehicle;
use Spiral\GRPC\StatusCode;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Class ExampleClientPersistVehicleCommand
 *
 * @package App
 */
class ExampleClientPersistVehicleCommand extends Command
{
    protected static $defaultName = 'client:example:persistVehicle';
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

        $client = $this->clientFactory->create(CommandClient::class);

        /** @var Vehicle $result */
        $args = (new Vehicle)
            ->setDealer((int)$input->getArgument('dealerId'))
            ->setMake($input->getArgument('make'))
            ->setModel($input->getArgument('model'))
            ->setType($input->getArgument('type'));
        [$result, $status] = $client->persistVehicle($args)->wait();

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
