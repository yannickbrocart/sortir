<?php

namespace App\Entity;

use App\Repository\OutputRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OutputRepository::class)]
class Output
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 80)]
    private $name;

    #[ORM\Column(type: 'datetime')]
    private $startdatetime;

    #[ORM\Column(type: 'integer')]
    private $duration;

    #[ORM\Column(type: 'date')]
    private $registrationdeadline;

    #[ORM\Column(type: 'integer')]
    private $registrationmaxnumber;

    #[ORM\Column(type: 'text', nullable: true)]
    private $outputinfos;

    #[ORM\ManyToOne(targetEntity: State::class, inversedBy: 'outputs')]
    #[ORM\JoinColumn(nullable: false)]
    private $state;

    #[ORM\ManyToOne(targetEntity: Place::class, inversedBy: 'outputs')]
    #[ORM\JoinColumn(nullable: false)]
    private $place;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'outputs')]
    #[ORM\JoinColumn(nullable: false)]
    private $organizer;

    #[ORM\ManyToMany(targetEntity: User::class, mappedBy: 'registred')]
    private $users;

    #[ORM\ManyToOne(targetEntity: Campus::class, inversedBy: 'outputs')]
    #[ORM\JoinColumn(nullable: false)]
    private $campus;

    public function __construct()
    {
        $this->users = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getStartdatetime(): ?\DateTimeInterface
    {
        return $this->startdatetime;
    }

    public function setStartdatetime(\DateTimeInterface $startdatetime): self
    {
        $this->startdatetime = $startdatetime;

        return $this;
    }

    public function getDuration(): ?int
    {
        return $this->duration;
    }

    public function setDuration(int $duration): self
    {
        $this->duration = $duration;

        return $this;
    }

    public function getRegistrationdeadline(): ?\DateTimeInterface
    {
        return $this->registrationdeadline;
    }

    public function setRegistrationdeadline(\DateTimeInterface $registrationdeadline): self
    {
        $this->registrationdeadline = $registrationdeadline;

        return $this;
    }

    public function getRegistrationmaxnumber(): ?int
    {
        return $this->registrationmaxnumber;
    }

    public function setRegistrationmaxnumber(int $registrationmaxnumber): self
    {
        $this->registrationmaxnumber = $registrationmaxnumber;

        return $this;
    }

    public function getOutputinfos(): ?string
    {
        return $this->outputinfos;
    }

    public function setOutputinfos(?string $outputinfos): self
    {
        $this->outputinfos = $outputinfos;

        return $this;
    }

    public function getState(): ?State
    {
        return $this->state;
    }

    public function setState(?State $state): self
    {
        $this->state = $state;

        return $this;
    }

    public function getPlace(): ?Place
    {
        return $this->place;
    }

    public function setPlace(?Place $place): self
    {
        $this->place = $place;

        return $this;
    }

    public function getOrganizer(): ?User
    {
        return $this->organizer;
    }

    public function setOrganizer(?User $organizer): self
    {
        $this->organizer = $organizer;

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users[] = $user;
            $user->addRegistred($this);
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        if ($this->users->removeElement($user)) {
            $user->removeRegistred($this);
        }

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
