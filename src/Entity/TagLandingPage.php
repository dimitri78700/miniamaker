<?php

namespace App\Entity;

use App\Repository\TagLandingPageRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TagLandingPageRepository::class)]
class TagLandingPage
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'tagLandingPages')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Tag $tag = null;

    #[ORM\ManyToOne(inversedBy: 'tagLandingPages')]
    #[ORM\JoinColumn(nullable: false)]
    private ?LandingPage $landing_page = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTag(): ?Tag
    {
        return $this->tag;
    }

    public function setTag(?Tag $tag): static
    {
        $this->tag = $tag;

        return $this;
    }

    public function getLandingPage(): ?LandingPage
    {
        return $this->landing_page;
    }

    public function setLandingPage(?LandingPage $landing_page): static
    {
        $this->landing_page = $landing_page;

        return $this;
    }
}
