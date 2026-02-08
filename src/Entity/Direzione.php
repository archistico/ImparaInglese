<?php

namespace App\Entity;

use App\Repository\DirezioneRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DirezioneRepository::class)]
class Direzione
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $descrizione = null;

    #[ORM\ManyToOne(inversedBy: 'direzioni')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Lingua $linguaPartenza = null;

    #[ORM\ManyToOne(inversedBy: 'direzioni')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Lingua $linguaArrivo = null;

    /**
     * @var Collection<int, Frase>
     */
    #[ORM\OneToMany(targetEntity: Frase::class, mappedBy: 'direzione', orphanRemoval: true)]
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

    public function getLinguaPartenza(): ?Lingua
    {
        return $this->linguaPartenza;
    }

    public function setLinguaPartenza(?Lingua $linguaPartenza): static
    {
        $this->linguaPartenza = $linguaPartenza;

        return $this;
    }

    public function getLinguaArrivo(): ?Lingua
    {
        return $this->linguaArrivo;
    }

    public function setLinguaArrivo(?Lingua $linguaArrivo): static
    {
        $this->linguaArrivo = $linguaArrivo;

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
            $frasi->setDirezione($this);
        }

        return $this;
    }

    public function removeFrasi(Frase $frasi): static
    {
        if ($this->frasi->removeElement($frasi)) {
            // set the owning side to null (unless already changed)
            if ($frasi->getDirezione() === $this) {
                $frasi->setDirezione(null);
            }
        }

        return $this;
    }
}
