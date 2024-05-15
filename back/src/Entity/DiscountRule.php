<?php

namespace App\Entity;

use App\Repository\DiscountRuleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DiscountRuleRepository::class)]
class DiscountRule
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $description = null;

    /**
     * @var Collection<int, DiscountRuleCondition>
     */
    #[ORM\OneToMany(targetEntity: DiscountRuleCondition::class, mappedBy: 'DiscountRule', orphanRemoval: true)]
    private Collection $discountRuleConditions;

    /**
     * @var Collection<int, DiscountRuleAction>
     */
    #[ORM\OneToMany(targetEntity: DiscountRuleAction::class, mappedBy: 'DiscountRule', orphanRemoval: true)]
    private Collection $discountRuleActions;

    public function __construct()
    {
        $this->discountRuleConditions = new ArrayCollection();
        $this->discountRuleActions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return Collection<int, DiscountRuleCondition>
     */
    public function getDiscountRuleConditions(): Collection
    {
        return $this->discountRuleConditions;
    }

    public function addDiscountRuleCondition(DiscountRuleCondition $discountRuleCondition): static
    {
        if (!$this->discountRuleConditions->contains($discountRuleCondition)) {
            $this->discountRuleConditions->add($discountRuleCondition);
            $discountRuleCondition->setDiscountRule($this);
        }

        return $this;
    }

    public function removeDiscountRuleCondition(DiscountRuleCondition $discountRuleCondition): static
    {
        if ($this->discountRuleConditions->removeElement($discountRuleCondition)) {
            // set the owning side to null (unless already changed)
            if ($discountRuleCondition->getDiscountRule() === $this) {
                $discountRuleCondition->setDiscountRule(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, DiscountRuleAction>
     */
    public function getDiscountRuleActions(): Collection
    {
        return $this->discountRuleActions;
    }

    public function addDiscountRuleAction(DiscountRuleAction $discountRuleAction): static
    {
        if (!$this->discountRuleActions->contains($discountRuleAction)) {
            $this->discountRuleActions->add($discountRuleAction);
            $discountRuleAction->setDiscountRule($this);
        }

        return $this;
    }

    public function removeDiscountRuleAction(DiscountRuleAction $discountRuleAction): static
    {
        if ($this->discountRuleActions->removeElement($discountRuleAction)) {
            // set the owning side to null (unless already changed)
            if ($discountRuleAction->getDiscountRule() === $this) {
                $discountRuleAction->setDiscountRule(null);
            }
        }

        return $this;
    }
}
