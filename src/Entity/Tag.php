<?php

namespace App\Entity;

use App\Repository\TagRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TagRepository::class)]
class Tag
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 80)]
    private ?string $name = null;

    /**
     * @var Collection<int, TagLandingPage>
     */
    #[ORM\OneToMany(targetEntity: TagLandingPage::class, mappedBy: 'tag')]
    private Collection $tagLandingPages;

    public function __construct()
    {
        $this->tagLandingPages = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

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
            $tagLandingPage->setTag($this);
        }

        return $this;
    }

    public function removeTagLandingPage(TagLandingPage $tagLandingPage): static
    {
        if ($this->tagLandingPages->removeElement($tagLandingPage)) {
            // set the owning side to null (unless already changed)
            if ($tagLandingPage->getTag() === $this) {
                $tagLandingPage->setTag(null);
            }
        }

        return $this;
    }
}
