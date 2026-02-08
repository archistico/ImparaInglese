<?php

namespace App\Entity;

use App\Repository\TraduzioneRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TraduzioneRepository::class)]
class Traduzione
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'traduzioni')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Frase $frase = null;

    #[ORM\ManyToOne(inversedBy: 'traduzioni')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Espressione $espressione = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFrase(): ?Frase
    {
        return $this->frase;
    }

    public function setFrase(?Frase $frase): static
    {
        $this->frase = $frase;

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
}
