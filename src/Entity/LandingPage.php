<?php

namespace App\Entity;

use App\Repository\LandingPageRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LandingPageRepository::class)]
#[ORM\HasLifecycleCallbacks] // Gestion de created et updated
class LandingPage
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 80)]
    private ?string $type = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $updated_at = null;

    #[ORM\ManyToOne(inversedBy: 'landingPages')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Detail $detail = null;

    #[ORM\OneToOne(mappedBy: 'landing_page', cascade: ['persist', 'remove'])]
    private ?LpContent $lpContent = null;

    /**
     * @var Collection<int, TagLandingPage>
     */
    #[ORM\OneToMany(targetEntity: TagLandingPage::class, mappedBy: 'landing_page')]
    private Collection $tagLandingPages;

    public function __construct()
    {
        $this->tagLandingPages = new ArrayCollection();
    }

    #[ORM\PrePersist]
    public function setCreatedAtValue() 
    {
        $this->created_at = new \DateTimeImmutable();
    }

    #[ORM\PreUpdate]
    public function setUpdatedAtValue() 
    {
        $this->updated_at = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeImmutable $created_at): static
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(\DateTimeImmutable $updated_at): static
    {
        $this->updated_at = $updated_at;

        return $this;
    }

    public function getDetail(): ?Detail
    {
        return $this->detail;
    }

    public function setDetail(?Detail $detail): static
    {
        $this->detail = $detail;

        return $this;
    }

    public function getLpContent(): ?LpContent
    {
        return $this->lpContent;
    }

    public function setLpContent(LpContent $lpContent): static
    {
        // set the owning side of the relation if necessary
        if ($lpContent->getLandingPage() !== $this) {
            $lpContent->setLandingPage($this);
        }

        $this->lpContent = $lpContent;

        return $this;
    }

    /**
     * @return Collection<int, TagLandingPage>
     */
    public function getTagLandingPages(): Collection
    {
        return $this->tagLandingPages;
    }

    public function addTagLandingPage(TagLandingPage $tagLandingPage): static
    {
        if (!$this->tagLandingPages->contains($tagLandingPage)) {
            $this->tagLandingPages->add($tagLandingPage);
            $tagLandingPage->setLandingPage($this);
        }

        return $this;
    }

    public function removeTagLandingPage(TagLandingPage $tagLandingPage): static
    {
        if ($this->tagLandingPages->removeElement($tagLandingPage)) {
            // set the owning side to null (unless already changed)
            if ($tagLandingPage->getLandingPage() === $this) {
                $tagLandingPage->setLandingPage(null);
            }
        }

        return $this;
    }
}
