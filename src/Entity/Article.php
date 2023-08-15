<?php

namespace App\Entity;

use App\Repository\ArticleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

use Gedmo\Mapping\Annotation as Gedmo;

#[ORM\Entity(repositoryClass: ArticleRepository::class)]
class Article
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type:'integer')]
    private $id;

    #[ORM\Column(type:'string',length: 255)]
    private $titre ;

    #[ORM\Column(type: Types::TEXT)]
    private  $description ;

    #[ORM\Column(type:'string',length: 255)]
    private  $createur ;

    #[ORM\Column(type:'float')]
    private  $prix ;

    #[ORM\Column(type:'string',nullable:true, length: 255)]
    private $photo ;

    #[ORM\Column(type: Types::DATE_MUTABLE,nullable:true)]
    private ?\DateTimeInterface $dateDeSortie ;

    #[Gedmo\Timestampable(on: "create")]
    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private \DateTimeInterface $createdAt ;

    #[Gedmo\Timestampable(on: 'update')]
    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private \DateTimeInterface $updatedAt;

    #[ORM\ManyToMany(targetEntity: Plateforme::class, inversedBy: 'articles', cascade: ['persist', 'remove'])]
    private Collection $plateforme;

    public function __construct()
    {
        $this->plateforme = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): self
    {
        $this->titre = $titre;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getCreateur(): ?string
    {
        return $this->createur;
    }

    public function setCreateur(string $createur): self
    {
        $this->createur = $createur;

        return $this;
    }

    public function getPrix(): ?float
    {
        return $this->prix;
    }

    public function setPrix(float $prix): self
    {
        $this->prix = $prix;

        return $this;
    }

    public function getPhoto(): ?string
    {
        return $this->photo;
    }

    public function setPhoto(string $photo): self
    {
        $this->photo = $photo;

        return $this;
    }

    public function getDateDeSortie(): ?\DateTimeInterface
    {
        return $this->dateDeSortie;
    }

    public function setDateDeSortie(\DateTimeInterface $dateDeSortie): self
    {
        $this->dateDeSortie = $dateDeSortie;

        return $this;
    }

    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): \DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * @return Collection<int, Plateforme>
     */
    public function getPlateforme(): Collection
    {
        return $this->plateforme;
    }

    public function addPlateforme(Plateforme $plateforme): self
    {
        if (!$this->plateforme->contains($plateforme)) {
            $this->plateforme->add($plateforme);
        }

        return $this;
    }

    public function removePlateforme(Plateforme $plateforme): self
    {
        $this->plateforme->removeElement($plateforme);

        return $this;
    }

    public function __toString(){
        return $this->titre;
    }
}
