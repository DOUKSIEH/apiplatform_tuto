<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AuteurRepository")
 */
class Auteur
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"listAuteurfull","listAuteurSimple"})
     * 
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"listGenrefull","listAuteurfull","listAuteurSimple"})
     * 
     */
    private $nom;

    /**
     * @ORM\Column(type="string", length=255)
     *@Groups({"listGenrefull","listAuteurfull","listAuteurSimple"})
     */
    private $prenom;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Nationnalite", inversedBy="auteurs")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"listGenrefull","listAuteurfull","listAuteurSimple"}) 
     */
    private $relation;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Livre", mappedBy="auteur")
     * @Groups({"listAuteurfull"})
     */
    private $livres;

    public function __construct()
    {
        $this->livres = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getRelation(): ?Nationnalite
    {
        return $this->relation;
    }

    public function setRelation(?Nationnalite $relation): self
    {
        $this->relation = $relation;

        return $this;
    }

    /**
     * @return Collection|Livre[]
     */
    public function getLivres(): Collection
    {
        return $this->livres;
    }

    public function addLivre(Livre $livre): self
    {
        if (!$this->livres->contains($livre)) {
            $this->livres[] = $livre;
            $livre->setAuteur($this);
        }

        return $this;
    }

    public function removeLivre(Livre $livre): self
    {
        if ($this->livres->contains($livre)) {
            $this->livres->removeElement($livre);
            // set the owning side to null (unless already changed)
            if ($livre->getAuteur() === $this) {
                $livre->setAuteur(null);
            }
        }

        return $this;
    }
    public function __toString() : string
    {
        return $this->nom .' - '. $this->prenom;
    }
}
