<?php

namespace App\Entity;

use App\Repository\FriendRepository;
use App\Traits\TimeStampTrait;
//use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FriendRepository::class)]
#[ORM\HasLifecycleCallbacks()]
class Friend
{
    use TimeStampTrait;
    
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $friend_status = null;

    #[ORM\ManyToOne(inversedBy: 'friends')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'friends')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $friend = null;

    
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFriendStatus(): ?int
    {
        return $this->friend_status;
    }

    public function setFriendStatus(int $friend_status): static
    {
        $this->friend_status = $friend_status;

        return $this;
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

    public function getFriend(): ?User
    {
        return $this->friend;
    }

    public function setFriend(?User $friend): static
    {
        $this->friend = $friend;

        return $this;
    }

   
}