<?php

namespace App\Entity;

use App\Repository\EmpruntRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EmpruntRepository::class)]
class Emprunt
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?float $Argent = null;

    #[ORM\Column]
    private ?float $Taux = null;

    #[ORM\Column]
    private ?int $Annee = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getArgent(): ?float
    {
        return $this->Argent;
    }

    public function setArgent(float $Argent): static
    {
        $this->Argent = $Argent;

        return $this;
    }

    public function getTaux(): ?float
    {
        return $this->Taux;
    }

    public function setTaux(float $Taux): static
    {
        $this->Taux = $Taux;

        return $this;
    }

    public function getAnnee(): ?int
    {
        return $this->Annee;
    }

    public function setAnnee(int $Annee): static
    {
        $this->Annee = $Annee;

        return $this;
    }
}
