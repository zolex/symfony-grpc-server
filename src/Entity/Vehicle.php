<?php

namespace App\Entity;

use App\Repository\VehicleRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: VehicleRepository::class)]
class Vehicle
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private int $id;

    #[ORM\Column(type: "string", length: 255)]
    private string $make;

    #[ORM\Column(type: "string", length: 255)]
    private string $model;

    #[ORM\Column(type: "string", length: 255)]
    private string $type;

    #[ORM\ManyToOne(targetEntity: Dealer::class, inversedBy: "vehicles")]
    #[ORM\JoinColumn(nullable: true)]
    private Dealer|null $dealer;

    public function getId(): ?int
    {
        return $this->id ?? null;
    }

    public function getMake(): ?string
    {
        return $this->make ?? null;
    }

    public function setMake(string $make): self
    {
        $this->make = $make;

        return $this;
    }

    public function getModel(): ?string
    {
        return $this->model ?? null;
    }

    public function setModel(string $model): self
    {
        $this->model = $model;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type ?? null;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getDealer(): ?Dealer
    {
        return $this->dealer ?? null;
    }

    public function setDealer(?Dealer $dealer): self
    {
        $this->dealer = $dealer;

        return $this;
    }

    public function getMessage(): \Modix\Grpc\Service\Example\v1\Model\Vehicle
    {
        return new \Modix\Grpc\Service\Example\v1\Model\Vehicle([
            'id' => $this->getId(),
            'model' => $this->getModel(),
            'make' => $this->getMake(),
            'type' => $this->getType(),
            'dealer' => $this->getDealer()?->getId(),
        ]);
    }
}
