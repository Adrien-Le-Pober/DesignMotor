<?php

namespace App\Entity;

use App\Repository\DiscountRuleActionRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DiscountRuleActionRepository::class)]
class DiscountRuleAction
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 48)]
    private ?string $type = null;

    #[ORM\Column]
    private ?int $value = null;

    #[ORM\ManyToOne(inversedBy: 'discountRuleActions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?DiscountRule $DiscountRule = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getValue(): ?int
    {
        return $this->value;
    }

    public function setValue(int $value): static
    {
        $this->value = $value;

        return $this;
    }

    public function getDiscountRule(): ?DiscountRule
    {
        return $this->DiscountRule;
    }

    public function setDiscountRule(?DiscountRule $DiscountRule): static
    {
        $this->DiscountRule = $DiscountRule;

        return $this;
    }
}
