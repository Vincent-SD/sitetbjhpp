<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\BoutadeCategoryRepository")
 */
class BoutadeCategory
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $description;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Boutade", mappedBy="category")
     */
    private $boutades;

    public function __construct()
    {
        $this->boutades = new ArrayCollection();
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return Collection|Boutade[]
     */
    public function getBoutades(): Collection
    {
        return $this->boutades;
    }

    public function addBoutade(Boutade $boutade): self
    {
        if (!$this->boutades->contains($boutade)) {
            $this->boutades[] = $boutade;
            $boutade->setCategory($this);
        }

        return $this;
    }

    public function removeBoutade(Boutade $boutade): self
    {
        if ($this->boutades->contains($boutade)) {
            $this->boutades->removeElement($boutade);
            // set the owning side to null (unless already changed)
            if ($boutade->getCategory() === $this) {
                $boutade->setCategory(null);
            }
        }

        return $this;
    }
}
