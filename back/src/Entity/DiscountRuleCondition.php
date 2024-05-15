<?php

namespace App\Entity;

use App\Repository\DiscountRuleConditionRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DiscountRuleConditionRepository::class)]
class DiscountRuleCondition
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 48)]
    private ?string $type = null;

    #[ORM\Column(length: 48)]
    private ?string $operator = null;

    #[ORM\Column(length: 48)]
    private ?string $value = null;

    #[ORM\ManyToOne(inversedBy: 'discountRuleConditions')]
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

    public function getOperator(): ?string
    {
        return $this->operator;
    }

    public function setOperator(string $operator): static
    {
        $this->operator = $operator;

        return $this;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(string $value): static
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
