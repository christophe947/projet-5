<?php

namespace App\Entity;

use App\Repository\PictureRepository;
use App\Traits\TimeStampTrait;
//use Doctrine\Common\Collections\ArrayCollection;
//use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
//use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: PictureRepository::class)]
#[ORM\HasLifecycleCallbacks()]
class Picture
{

    use TimeStampTrait;
    
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $filename = null;

    /*#[Assert\NotBlank(
        message: 'Veuillez renseigner ce champ'
    )]*/
    #[ORM\Column(length: 45)]
    private ?string $legend = null;

    #[ORM\Column(length: 45)]
    private ?string $alt = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column]
    private ?int $status = null;


    #[ORM\ManyToOne(inversedBy: 'picture')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'cascade')] //test permet la cascade suppression duser supprime photo associé
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'picture'/*, cascade: ['persist', 'remove']*/)]  //avis : a lair de bloquer la suppression de photo possedant un album
    #[ORM\JoinColumn(nullable: true /*, onDelete: 'cascade'*/)]
    private ?Album $album = null;

    #[ORM\Column(nullable: true)]
    private ?bool $profil = null;

    

    
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFilename(): ?string
    {
        return $this->filename;
    }

    public function setFilename(string $filename): self
    {
        $this->filename = $filename;

        return $this;
    }

    public function getLegend(): ?string
    {
        return $this->legend;
    }

    public function setLegend(/*string*/ $legend): self     ///*string*/ permet la validation dentité existante
    {
        $this->legend = $legend;

        return $this;
    }

    public function getAlt(): ?string
    {
        return $this->alt;
    }

    public function setAlt(/*string*/ $alt): self
    {
        $this->alt = $alt;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function setStatus(int $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getAlbum(): ?Album
    {
        return $this->album;
    }

    public function setAlbum(?Album $album): self
    {
        $this->album = $album;

        return $this;
    }

    public function isProfil(): ?bool
    {
        return $this->profil;
    }

    public function setProfil(?bool $profil): static
    {
        $this->profil = $profil;

        return $this;
    }

   
    
}