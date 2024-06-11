<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\DiscountRuleConditionRepository;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: DiscountRuleConditionRepository::class)]
class DiscountRuleCondition
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 48)]
    #[Assert\Choice(choices: ['day_of_week', 'brand'], message: 'Les types disponibles sont {{ choices }}')]
    private ?string $type = null;

    #[ORM\Column(type: 'json')]
    private array $value = [];

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

    public function getValue(): array
    {
        return $this->value;
    }

    public function setValue(array $value): static
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
