<?php

namespace App\Entity;

use App\Repository\FilterRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FilterRepository::class)]
class Filter
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 80, nullable: true)]
    private $search;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private $startdatetime;

    #[ORM\Column(type: 'date', nullable: true)]
    private $enddatetime;

    #[ORM\Column(type: 'boolean', nullable: true)]
    private $organize;

    #[ORM\Column(type: 'boolean', nullable: true)]
    private $registered;

    #[ORM\Column(type: 'boolean', nullable: true)]
    private $unregistered;

    #[ORM\Column(type: 'boolean', nullable: true)]
    private $past;

    #[ORM\OneToOne(targetEntity: Campus::class, cascade: ['persist', 'remove'])]
    private $campus;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSearch(): ?string
    {
        return $this->search;
    }

    public function setSearch(?string $search): self
    {
        $this->search = $search;

        return $this;
    }

    public function getStartdatetime(): ?\DateTimeInterface
    {
        return $this->startdatetime;
    }

    public function setStartdatetime(?\DateTimeInterface $startdatetime): self
    {
        $this->startdatetime = $startdatetime;

        return $this;
    }

    public function getEnddatetime(): ?\DateTimeInterface
    {
        return $this->enddatetime;
    }

    public function setEnddatetime(?\DateTimeInterface $enddatetime): self
    {
        $this->enddatetime = $enddatetime;

        return $this;
    }

    public function getOrganize(): ?bool
    {
        return $this->organize;
    }

    public function setOrganize(?bool $organize): self
    {
        $this->organize = $organize;

        return $this;
    }

    public function getRegistered(): ?bool
    {
        return $this->registered;
    }

    public function setRegistered(?bool $registered): self
    {
        $this->registered = $registered;

        return $this;
    }

    public function getUnregistered(): ?bool
    {
        return $this->unregistered;
    }

    public function setUnregistered(?bool $unregistered): self
    {
        $this->unregistered = $unregistered;

        return $this;
    }

    public function getPast(): ?bool
    {
        return $this->past;
    }

    public function setPast(?bool $past): self
    {
        $this->past = $past;

        return $this;
    }

    public function getCampus(): ?Campus
    {
        return $this->campus;
    }

    public function setCampus(?Campus $campus): self
    {
        $this->campus = $campus;

        return $this;
    }
}
