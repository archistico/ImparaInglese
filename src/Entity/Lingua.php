<?php

namespace App\Entity;

use App\Repository\LinguaRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LinguaRepository::class)]
class Lingua
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $descrizione = null;

    /**
     * @var Collection<int, Direzione>
     */
    #[ORM\OneToMany(targetEntity: Direzione::class, mappedBy: 'linguaPartenza', orphanRemoval: true)]
    private Collection $direzioni;

    /**
     * @var Collection<int, Espressione>
     */
    #[ORM\OneToMany(targetEntity: Espressione::class, mappedBy: 'lingua', orphanRemoval: true)]
    private Collection $espressioni;

    public function __construct()
    {
        $this->direzioni = new ArrayCollection();
        $this->espressioni = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDescrizione(): ?string
    {
        return $this->descrizione;
    }

    public function setDescrizione(string $descrizione): static
    {
        $this->descrizione = $descrizione;

        return $this;
    }

    /**
     * @return Collection<int, Direzione>
     */
    public function getDirezioni(): Collection
    {
        return $this->direzioni;
    }

    public function addDirezioni(Direzione $direzioni): static
    {
        if (!$this->direzioni->contains($direzioni)) {
            $this->direzioni->add($direzioni);
            $direzioni->setLinguaPartenza($this);
        }

        return $this;
    }

    public function removeDirezioni(Direzione $direzioni): static
    {
        if ($this->direzioni->removeElement($direzioni)) {
            // set the owning side to null (unless already changed)
            if ($direzioni->getLinguaPartenza() === $this) {
                $direzioni->setLinguaPartenza(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Espressione>
     */
    public function getEspressioni(): Collection
    {
        return $this->espressioni;
    }

    public function addEspressioni(Espressione $espressioni): static
    {
        if (!$this->espressioni->contains($espressioni)) {
            $this->espressioni->add($espressioni);
            $espressioni->setLingua($this);
        }

        return $this;
    }

    public function removeEspressioni(Espressione $espressioni): static
    {
        if ($this->espressioni->removeElement($espressioni)) {
            // set the owning side to null (unless already changed)
            if ($espressioni->getLingua() === $this) {
                $espressioni->setLingua(null);
            }
        }

        return $this;
    }
}
