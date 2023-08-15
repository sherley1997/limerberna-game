<?php

namespace App\Entity;

use App\Repository\OrderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OrderRepository::class)]
#[ORM\Table(name: '`order`')]
class Order
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $numero = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable:true)]
    private ?string $stripSessionId = null;

    #[ORM\Column]
    private ?bool $isPaid = null;

    #[ORM\ManyToOne(inversedBy: 'orders')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\OneToMany(mappedBy: 'relatedOrder', targetEntity: OrderDetail::class, orphanRemoval: true)]
    private Collection $detailsOrder;

    public function __construct()
    {
        $this->detailsOrder = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumero(): ?string
    {
        return $this->numero;
    }

    public function setNumero(string $numero): self
    {
        $this->numero = $numero;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getStripSessionId(): ?string
    {
        return $this->stripSessionId;
    }

    public function setStripSessionId(string $stripSessionId): self
    {
        $this->stripSessionId = $stripSessionId;

        return $this;
    }

    public function isIsPaid(): ?bool
    {
        return $this->isPaid;
    }

    public function setIsPaid(bool $isPaid): self
    {
        $this->isPaid = $isPaid;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return Collection<int, OrderDetail>
     */
    public function getDetailsOrder(): Collection
    {
        return $this->detailsOrder;
    }

    public function addDetailsOrder(OrderDetail $detailsOrder): self
    {
        if (!$this->detailsOrder->contains($detailsOrder)) {
            $this->detailsOrder->add($detailsOrder);
            $detailsOrder->setRelatedOrder($this);
        }

        return $this;
    }

    public function removeDetailsOrder(OrderDetail $detailsOrder): self
    {
        if ($this->detailsOrder->removeElement($detailsOrder)) {
            // set the owning side to null (unless already changed)
            if ($detailsOrder->getRelatedOrder() === $this) {
                $detailsOrder->setRelatedOrder(null);
            }
        }

        return $this;
    }
}
