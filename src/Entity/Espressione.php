<?php

namespace App\Entity;

use App\Repository\EspressioneRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EspressioneRepository::class)]
class Espressione
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $testo = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $info = null;

    #[ORM\ManyToOne(inversedBy: 'espressioni')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Lingua $lingua = null;

    #[ORM\Column]
    private ?bool $corretta = null;

    /**
     * @var Collection<int, Frase>
     */
    #[ORM\OneToMany(targetEntity: Frase::class, mappedBy: 'espressione', orphanRemoval: true)]
    private Collection $frasi;

    /**
     * @var Collection<int, Traduzione>
     */
    #[ORM\OneToMany(targetEntity: Traduzione::class, mappedBy: 'espressione', orphanRemoval: true)]
    private Collection $traduzioni;

    public function __construct()
    {
        $this->frasi = new ArrayCollection();
        $this->traduzioni = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTesto(): ?string
    {
        return $this->testo;
    }

    public function setTesto(string $testo): static
    {
        $this->testo = $testo;

        return $this;
    }

    public function getInfo(): ?string
    {
        return $this->info;
    }

    public function setInfo(?string $info): static
    {
        $this->info = $info;

        return $this;
    }

    public function getLingua(): ?Lingua
    {
        return $this->lingua;
    }

    public function setLingua(?Lingua $lingua): static
    {
        $this->lingua = $lingua;

        return $this;
    }

    public function isCorretta(): ?bool
    {
        return $this->corretta;
    }

    public function setCorretta(bool $corretta): static
    {
        $this->corretta = $corretta;

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
            $frasi->setEspressione($this);
        }

        return $this;
    }

    public function removeFrasi(Frase $frasi): static
    {
        if ($this->frasi->removeElement($frasi)) {
            // set the owning side to null (unless already changed)
            if ($frasi->getEspressione() === $this) {
                $frasi->setEspressione(null);
            }
        }

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
            $traduzioni->setEspressione($this);
        }

        return $this;
    }

    public function removeTraduzioni(Traduzione $traduzioni): static
    {
        if ($this->traduzioni->removeElement($traduzioni)) {
            // set the owning side to null (unless already changed)
            if ($traduzioni->getEspressione() === $this) {
                $traduzioni->setEspressione(null);
            }
        }

        return $this;
    }
}
