<?php

namespace App\Entity\Security;

use App\Entity\Address;
use App\Entity\Calendar\Event;
use App\Entity\Company\Company;
use App\Entity\Finance\Account;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Hashids\Hashids;
use Ramsey\Uuid\UuidInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @ORM\EntityListeners({"App\EntityListener\UserListener"})
 * @UniqueEntity(fields="email", message="An account already exists linked to that email address. Try signing in.")
 * @ORM\Table(name="security_users")
 */
class User implements UserInterface
{
    /**
     * @var UuidInterface
     *
     * @ORM\Id
     * @ORM\Column(type="uuid", unique=true)
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class="Ramsey\Uuid\Doctrine\UuidGenerator")
     * @Groups({"read", "list", "tree"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"read", "list", "tree"})
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=15)
     * @Groups({"read", "list"})
     */
    private $phone;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"read", "list", "tree"})
     */
    private $name;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Groups({"read", "list"})
     */
    private $confirmedAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $lastAccessAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $passwordUpdateAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $disabledAt;

    /**
     * @ORM\Column(type="string", nullable=false)
     *
     * @var string
     */
    private $password;

    /**
     * @ORM\Embedded(class=Address::class, columnPrefix="addr_")
     * @Groups({"read"})
     */
    protected $address;

    /**
     * @ORM\Column(type="string", length=64, nullable=true)
     */
    private $userHash;

    /**
     * @ORM\Column(type="json", nullable=false)
     * @Groups({"read", "list"})
     */
    private $roles = [];

    /**
     * @ORM\ManyToOne(targetEntity=User::class)
     */
    private $parentUser;

    /**
     * @ORM\OneToMany(targetEntity=User::class, mappedBy="parentUser")
     * @Groups({"tree"})
     */
    private $children;

    /**
     * @ORM\Column(type="string", length=1000, nullable=true)
     * @Groups({"read", "list", "tree"})
     */
    private $avatar;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Calendar\Event", mappedBy="participants")
     */
    private $events;

    /**
     * @var Account
     *
     * @ORM\OneToOne(targetEntity=Account::class, cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=true)
     */
    private $account;

    /**
     * @var Company
     *
     * @ORM\ManyToOne(targetEntity=Company::class)
     */
    private $company;

    /**
     * @ORM\Column(type="string", length=10, nullable=true)
     * @Groups({"read", "list"})
     */
    protected $inviteCode;

    public function __construct()
    {
        $this->address = new Address();
        $this->events = new ArrayCollection();
        $this->children = new ArrayCollection();
    }

    public function getId(): ?UuidInterface
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): self
    {
        $this->phone = $phone;

        return $this;
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

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getConfirmedAt(): ?\DateTimeInterface
    {
        return $this->confirmedAt;
    }

    public function setConfirmedAt(\DateTimeInterface $confirmedAt)
    {
        $this->confirmedAt = $confirmedAt;

        return $this;
    }

    public function isConfirmed(): bool
    {
        return (bool) $this->confirmedAt;
    }

    public function getLastAccessAt(): ?\DateTimeInterface
    {
        return $this->lastAccessAt;
    }

    public function setLastAccessAt(?\DateTimeInterface $lastAccessAt): self
    {
        $this->lastAccessAt = $lastAccessAt;

        return $this;
    }

    public function getPasswordUpdateAt(): ?\DateTimeInterface
    {
        return $this->passwordUpdateAt;
    }

    public function setPasswordUpdateAt(\DateTimeInterface $passwordUpdateAt): self
    {
        $this->passwordUpdateAt = $passwordUpdateAt;

        return $this;
    }

    public function getDisabledAt(): ?\DateTimeInterface
    {
        return $this->disabledAt;
    }

    public function setDisabledAt(?\DateTimeInterface $disabledAt): self
    {
        $this->disabledAt = $disabledAt;

        return $this;
    }

    public function eraseCredentials()
    {
    }

    public function getRoles()
    {
        $roles = $this->roles;

        if (!in_array('ROLE_USER', $roles)) {
            $roles[] = 'ROLE_USER';
        }

        return $roles;
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function addRole($role)
    {
        $this->roles[] = $role;

        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password)
    {
        $this->password = $password;

        return $this;
    }

    public function getSalt()
    {
        return '';
    }

    public function getUsername(): string
    {
        return $this->getEmail();
    }

    public function getUserIdHash()
    {
        $hashids = new Hashids(get_class($this), 12);

        return $hashids->encode($this->getId());
    }

    public function getAddress(): Address
    {
        return $this->address;
    }

    public function setAddress($address): self
    {
        $this->address = $address;

        return $this;
    }

    public static function generateHash(User $user): string
    {
        $id = $user->getId();
        $email = $user->getEmail();
        $date = $user->getCreatedAt()->getTimestamp();
        $hash = md5("{$id}:{$email}:{$date}");

        return $hash;
    }

    public function getUserHash(): ?string
    {
        return $this->userHash;
    }

    public function setUserHash(?string $userHash): self
    {
        $this->userHash = $userHash;

        return $this;
    }

    public function getParentUser(): ?self
    {
        return $this->parentUser;
    }

    public function setParentUser(?self $parentUser): self
    {
        $this->parentUser = $parentUser;

        return $this;
    }

    public function getAvatar(): ?string
    {
        return $this->avatar;
    }

    public function setAvatar(?string $avatar): self
    {
        $this->avatar = $avatar;

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getChildren(): Collection
    {
        return $this->children;
    }

    public function __toString()
    {
        return $this->getName();
    }

    /**
     * @return Collection|Event[]
     */
    public function getEvents(): Collection
    {
        return $this->events;
    }

    public function addEvent(Event $event): self
    {
        if (!$this->events->contains($event)) {
            $this->events[] = $event;
            $event->addParticipant($this);
        }

        return $this;
    }

    public function removeEvent(Event $event): self
    {
        if ($this->events->contains($event)) {
            $this->events->removeElement($event);
            $event->removeParticipant($this);
        }

        return $this;
    }

    public function getAccount(): ?Account
    {
        return $this->account;
    }

    public function setAccount(?Account $account): self
    {
        $this->account = $account;

        return $this;
    }

    public function getCompany(): ?Company
    {
        return $this->company;
    }

    public function setCompany(?Company $company): self
    {
        $this->company = $company;

        return $this;
    }

    public function getInviteCode(): ?string
    {
        return $this->inviteCode;
    }

    public function setInviteCode(?string $inviteCode)
    {
        $this->inviteCode = $inviteCode;

        return $this;
    }

    /**
     * @Groups({"tree"})
     */
    public function getParentUserId()
    {
        return $this->getParentUser() ? $this->getParentUser()->getId() : '';
    }

    /**
     * @Groups({"tree"})
     */
    public function isAgent()
    {
        return in_array('ROLE_AGENT', $this->getRoles());
    }

    /**
     * @Groups({"tree"})
     */
    public function isInvestor()
    {
        return in_array('ROLE_INVESTOR', $this->getRoles());
    }

    /**
     * @Groups({"tree"})
     */
    public function isTenant()
    {
        return in_array('ROLE_TENANT', $this->getRoles());
    }
}
