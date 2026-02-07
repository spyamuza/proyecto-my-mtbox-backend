<?php

namespace App\Entity;

use App\Entity\User;
use App\Repository\ContenidoUsuarioRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ContenidoUsuarioRepository::class)]
class ContenidoUsuario
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\Column]
    private ?int $tmdbId = null;

   // Indica si el contenido es una película o una serie (OBLIGATORIO)
    #[ORM\Column(type: 'boolean')]
    private bool $esPelicula;

    // Indica si el usuario lo ha marcado como favorito (OBLIGATORIO)
    #[ORM\Column(type: 'boolean')]
    private bool $favorito = false;

    // Indica si el usuario ha visto el contenido
    // NULL = no indicado por el usuario
    #[ORM\Column(type: 'boolean', nullable: true)]
    private ?bool $vista = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $notaUsuario = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;
        return $this;
    }

    public function getTmdbId(): ?int
    {
        return $this->tmdbId;
    }

    public function setTmdbId(int $tmdbId): static
    {
        $this->tmdbId = $tmdbId;
        return $this;
    }

    public function isEsPelicula(): ?bool
    {
        return $this->esPelicula;
    }

    public function setEsPelicula(bool $esPelicula): static
    {
        $this->esPelicula = $esPelicula;
        return $this;
    }

    public function isFavorito(): ?bool
    {
        return $this->favorito;
    }

    public function setFavorito(bool $favorito): static
    {
        $this->favorito = $favorito;
        return $this;
    }

    public function isVista(): ?bool
    {
        return $this->vista;
    }

   public function setVista(?bool $vista): static
    {
        $this->vista = $vista;
        return $this;
    }

    public function getNotaUsuario(): ?string
    {
        return $this->notaUsuario;
    }

    public function setNotaUsuario(?string $notaUsuario): static
    {
        $this->notaUsuario = $notaUsuario;
        return $this;
    }
}