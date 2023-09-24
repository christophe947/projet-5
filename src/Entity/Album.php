<?php

namespace App\Entity;

use App\Repository\AlbumRepository;
use App\Traits\TimeStampTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
//use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
//use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

//#[UniqueEntity(fields: ['album_name'], message: 'Il existe deja un album avec ce nom')]
#[ORM\Entity(repositoryClass: AlbumRepository::class)]
#[ORM\HasLifecycleCallbacks()]
class Album
{

    use TimeStampTrait;
    
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;


    public function __construct()
    {
        $this->picture = new ArrayCollection();
        $this->videos = new ArrayCollection();
        $this->music = new ArrayCollection();
    }

    #[ORM\Column(length: 45)]
    private ?string $album_name = null;

    #[ORM\OneToMany(mappedBy: 'album', targetEntity: Picture::class , orphanRemoval: true)]
    private Collection $picture;

    #[ORM\OneToMany(mappedBy: 'album', targetEntity: Video::class, orphanRemoval: true)]
    private Collection $videos;
    
    #[ORM\OneToMany(mappedBy: 'album', targetEntity: Music::class, orphanRemoval: true)]
    private Collection $music;

    #[ORM\ManyToOne(inversedBy: 'album', cascade: ['persist'/*, 'remove'*/])]
    #[ORM\JoinColumn(nullable: false, onDelete: 'cascade')]
    private ?User $user = null;

    
    public function getId(): ?int
    {
        return $this->id;
    }


    public function getAlbumName(): ?string
    {
        return $this->album_name;
    }

    public function setAlbumName(string $album_name): self
    {
        $this->album_name = $album_name;

        return $this;
    }

    /**
     * @return Collection<int, Picture>
     */
    public function getPicture(): Collection
    {
        return $this->picture;
    }

    public function addPicture(Picture $picture): self
    {
        if (!$this->picture->contains($picture)) {
            $this->picture->add($picture);
            $picture->setAlbum($this);
        }

        return $this;
    }

    public function removePicture(Picture $picture): self
    {
        if ($this->picture->removeElement($picture)) {
            // set the owning side to null (unless already changed)
            if ($picture->getAlbum() === $this) {
                $picture->setAlbum(null);
            }
        }

        return $this;
    }

    public function __toString(): string
    {   
        return $this->album_name;
    }

    /**
     * @return Collection<int, Video>
     */
    public function getVideos(): Collection
    {
        return $this->videos;
    }

    public function addVideo(Video $video): self
    {
        if (!$this->videos->contains($video)) {
            $this->videos->add($video);
            $video->setAlbum($this);
        }

        return $this;
    }

    public function removeVideo(Video $video): self
    {
        if ($this->videos->removeElement($video)) {
            // set the owning side to null (unless already changed)
            if ($video->getAlbum() === $this) {
                $video->setAlbum(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Music>
     */
    public function getMusic(): Collection
    {
        return $this->music;
    }

    public function addMusic(Music $music): self
    {
        if (!$this->music->contains($music)) {
            $this->music->add($music);
            $music->setAlbum($this);
        }

        return $this;
    }

    public function removeMusic(Music $music): self
    {
        if ($this->music->removeElement($music)) {
            // set the owning side to null (unless already changed)
            if ($music->getAlbum() === $this) {
                $music->setAlbum(null);
            }
        }

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

    

    
   

}