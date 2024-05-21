<?php

namespace App\Entity;

use App\Repository\DiscountRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DiscountRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Discount
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $storageDuration = null;

    #[ORM\Column]
    private ?float $rate = null;

    /**
     * @var Collection<int, Vehicle>
     */
    #[ORM\ManyToMany(targetEntity: Vehicle::class, inversedBy: 'discounts')]
    private Collection $vehicles;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    public function __construct()
    {
        $this->vehicles = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStorageDuration(): ?int
    {
        return $this->storageDuration;
    }

    public function setStorageDuration(int $storageDuration): static
    {
        $this->storageDuration = $storageDuration;

        return $this;
    }

    public function getRate(): ?float
    {
        return $this->rate;
    }

    public function setRate(float $rate): static
    {
        $this->rate = $rate;

        return $this;
    }

    /**
     * @return Collection<int, Vehicle>
     */
    public function getVehicles(): Collection
    {
        return $this->vehicles;
    }

    public function addVehicle(Vehicle $vehicle): static
    {
        if (!$this->vehicles->contains($vehicle)) {
            $this->vehicles->add($vehicle);
        }

        return $this;
    }

    public function removeVehicle(Vehicle $vehicle): static
    {
        $this->vehicles->removeElement($vehicle);

        return $this;
    }

    public function run(array $vehicleList): void
    {
        foreach ($vehicleList as $vehicle) {
            if ($vehicle->getStorageDurationInDays() >= $this->getStorageDuration()) {
                $this->addVehicle($vehicle);
            }
        }

        foreach($this->getVehicles() as $vehicle) {
            $vehicle->setDiscountPrice(1.0 - $this->getRate());
        }
    }

    public function cancel(): void
    {
        foreach($this->getVehicles() as $vehicle) {
            $vehicle->setDiscountPrice(1.0 / (1.0 - $this->getRate()));
        }
    }

    public function restore(): void
    {
        foreach($this->getVehicles() as $vehicle) {
            $vehicle->setDiscountPrice(1.0 - $this->getRate());
        }
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    #[ORM\PrePersist]
    public function onPrePersist(): void
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
    }

	#[ORM\PreUpdate]
    public function onPreUpdate(): void
    {
        $this->updatedAt = new \DateTimeImmutable();
    }
}
