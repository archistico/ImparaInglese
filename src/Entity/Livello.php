<?php

namespace App\Entity;

use App\Repository\LivelloRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LivelloRepository::class)]
class Livello
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $descrizione = null;

    /**
     * @var Collection<int, Frase>
     */
    #[ORM\OneToMany(targetEntity: Frase::class, mappedBy: 'livello', orphanRemoval: true)]
    private Collection $frasi;

    public function __construct()
    {
        $this->frasi = new ArrayCollection();
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
     * @return Collection<int, Frase>
     */
    public function getFrasi(): Collection
    {
        return $this->frasi;
    }

    public function addFrasi(Frase $frasi): static
    {
        if (!$this->frasi->contains($frasi)) {
            $this->frasi->add($frasi);
            $frasi->setLivello($this);
        }

        return $this;
    }

    public function removeFrasi(Frase $frasi): static
    {
        if ($this->frasi->removeElement($frasi)) {
            // set the owning side to null (unless already changed)
            if ($frasi->getLivello() === $this) {
                $frasi->setLivello(null);
            }
        }

        return $this;
    }
}
