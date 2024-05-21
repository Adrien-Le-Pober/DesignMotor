<?php

namespace App\Entity;

use App\Repository\DiscountRuleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DiscountRuleRepository::class)]
#[ORM\HasLifecycleCallbacks]
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

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

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
