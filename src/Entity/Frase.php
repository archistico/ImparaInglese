<?php

namespace App\Entity;

use App\Repository\FraseRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FraseRepository::class)]
class Frase
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'frasi')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Contesto $contesto = null;

    #[ORM\ManyToOne(inversedBy: 'frasi')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Direzione $direzione = null;

    #[ORM\ManyToOne(inversedBy: 'frasi')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Livello $livello = null;

    #[ORM\ManyToOne(inversedBy: 'frasi')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Espressione $espressione = null;

    /**
     * @var Collection<int, Traduzione>
     */
    #[ORM\OneToMany(targetEntity: Traduzione::class, mappedBy: 'frase', orphanRemoval: true)]
    private Collection $traduzioni;

    public function __construct()
    {
        $this->traduzioni = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContesto(): ?Contesto
    {
        return $this->contesto;
    }

    public function setContesto(?Contesto $contesto): static
    {
        $this->contesto = $contesto;

        return $this;
    }

    public function getDirezione(): ?Direzione
    {
        return $this->direzione;
    }

    public function setDirezione(?Direzione $direzione): static
    {
        $this->direzione = $direzione;

        return $this;
    }

    public function getLivello(): ?Livello
    {
        return $this->livello;
    }

    public function setLivello(?Livello $livello): static
    {
        $this->livello = $livello;

        return $this;
    }

    public function getEspressione(): ?Espressione
    {
        return $this->espressione;
    }

    public function setEspressione(?Espressione $espressione): static
    {
        $this->espressione = $espressione;

        return $this;
    }

    /**
     * @return Collection<int, Traduzione>
     */
    public function getTraduzioni(): Collection
    {
        return $this->traduzioni;
    }

    public function addTraduzioni(Traduzione $traduzioni): static
    {
        if (!$this->traduzioni->contains($traduzioni)) {
            $this->traduzioni->add($traduzioni);
            $traduzioni->setFrase($this);
        }

        return $this;
    }

    public function removeTraduzioni(Traduzione $traduzioni): static
    {
        if ($this->traduzioni->removeElement($traduzioni)) {
            // set the owning side to null (unless already changed)
            if ($traduzioni->getFrase() === $this) {
                $traduzioni->setFrase(null);
            }
        }

        return $this;
    }
}
