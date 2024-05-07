<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\VehicleRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: VehicleRepository::class)]
class Vehicle
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 8)]
    #[Assert\NotBlank]
    #[Assert\Length(max: 8)]
    #[Assert\Regex('/^\d+/')]
    private ?string $power = null;

    #[ORM\Column(length: 8, nullable: true)]
    #[Assert\NotBlank]
    #[Assert\Length(max: 8)]
    #[Assert\Regex('/^\d+/')]
    private ?string $space = null;


    #[ORM\ManyToOne(inversedBy: 'vehicles')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull]
    private ?Brand $brand = null;

    /**
     * @var Collection<int, Color>
     */
    #[ORM\ManyToMany(targetEntity: Color::class, inversedBy: 'vehicles')]
    private Collection $color;

    #[ORM\ManyToOne(inversedBy: 'vehicles')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull]
    private ?Type $type = null;


    #[ORM\ManyToOne(inversedBy: 'vehicles')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull]
    private ?Motorization $motorization = null;

    #[ORM\ManyToOne(inversedBy: 'vehicles')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull]
    private ?Model $model = null;

    public function __construct()
    {
        $this->color = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPower(): ?string
    {
        return $this->power;
    }

    public function setPower(string $power): static
    {
        $this->power = $power;

        return $this;
    }

    public function getSpace(): ?string
    {
        return $this->space;
    }

    public function setSpace(?string $space): static
    {
        $this->space = $space;

        return $this;
    }

    public function getBrand(): ?Brand
    {
        return $this->brand;
    }

    public function setBrand(?Brand $brand): static
    {
        $this->brand = $brand;

        return $this;
    }

    /**
     * @return Collection<int, Color>
     */
    public function getColor(): Collection
    {
        return $this->color;
    }

    public function addColor(Color $color): static
    {
        if (!$this->color->contains($color)) {
            $this->color->add($color);
        }

        return $this;
    }

    public function removeColor(Color $color): static
    {
        $this->color->removeElement($color);

        return $this;
    }

    public function getType(): ?Type
    {
        return $this->type;
    }

    public function setType(?Type $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getMotorization(): ?Motorization
    {
        return $this->motorization;
    }

    public function setMotorization(?Motorization $motorization): static
    {
        $this->motorization = $motorization;

        return $this;
    }

    public function getModel(): ?Model
    {
        return $this->model;
    }

    public function setModel(?Model $model): static
    {
        $this->model = $model;

        return $this;
    }
}
