<?php

namespace App\Entity;

use App\Entity\Traits\UserTrait;
use App\Entity\Common\BlameableTrait;
use App\Entity\Common\SoftDeleteableTrait;
use App\Entity\Common\TimestampableTrait;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\HttpFoundation\File\File;

/**
 * for vich upload bundle
 */
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Serializer\Annotation\Groups;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @ORM\Table(name="`user`")
 * SoftDeleteable annotation must be used with SoftDeleteableTrait
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false, hardDelete=true)
 * @Gedmo\Loggable
 * @UniqueEntity("email")
 * @Vich\Uploadable
 */
class User implements UserInterface, \Serializable
{
    use BlameableTrait;
    use SoftDeleteableTrait;
    use TimestampableTrait;
    use UserTrait;

    const TYPE_EXTERNAL = 'external';
    const TYPE_INTERNAL = 'internal';

    const GROUP_USER_PROJECT= 'project.user';

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({self::GROUP_USER_PROJECT, "project-form-data", "data-project", "exchange-history"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     * @Assert\Email
     * @Assert\NotBlank
     * @Groups({self::GROUP_USER_PROJECT, "project-form-data", "exchange-history"})
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     * @Assert\NotBlank(groups={"user:create"})
     * @Assert\Length(
     *      min = 2, groups={"user:create"}
     * )
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\NotBlank(groups={"user:create"})
     * @Assert\Length(
     *      min = 2,
     * )
     * @Groups({self::GROUP_USER_PROJECT, "project-form-data", "exchange-history"})
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\NotBlank(groups={"user:create"})
     * @Assert\Length(
     *      min = 2,
     * )
     * @Groups({self::GROUP_USER_PROJECT, "project-form-data", "exchange-history"})
     */
    private $lastName;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     * @Assert\NotBlank(groups={"user:create"})
     * @Groups({self::GROUP_USER_PROJECT})
     */
    private $phone;

    /**
     * @Groups({self::GROUP_USER_PROJECT})
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $job;

    /**
     * @Groups({self::GROUP_USER_PROJECT})
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $fax;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({self::GROUP_USER_PROJECT})
     */
    private $rawAddress;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $type;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $canLogin;
    
    public function __construct()
    {
        $this->type = self::TYPE_INTERNAL;
        $this->canLogin = true;
        $this->projects = new ArrayCollection();
    }

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"project-form-data"})
     */
    private $profileName;
    
    /**
     * NOTE: This is not a mapped field of entity metadata, just a simple property.
     * 
     * @Vich\UploadableField(mapping="user_profile", fileNameProperty="profileName", originalName="originalName")
     * 
     * @var File|null
     */
    private $profileFile;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $originalName;


    public function getId(): ?int
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

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(?string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(?string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(?string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getJob(): ?string
    {
        return $this->job;
    }

    public function setJob(?string $job): self
    {
        $this->job = $job;

        return $this;
    }

    public function getFax(): ?string
    {
        return $this->fax;
    }

    public function setFax(?string $fax): self
    {
        $this->fax = $fax;

        return $this;
    }

    public function getRawAddress(): ?string
    {
        return $this->rawAddress;
    }

    public function setRawAddress(?string $rawAddress): self
    {
        $this->rawAddress = $rawAddress;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getCanLogin(): ?bool
    {
        return $this->canLogin;
    }

    public function setCanLogin(?bool $canLogin): self
    {
        $this->canLogin = $canLogin;

        return $this;
    }

    public function getProfileName(): ?string
    {
        return $this->profileName;
    }

    public function setProfileName(?string $profileName): self
    {
        $this->profileName = $profileName;

        return $this;
    }

    public function getOriginalName(): ?string
    {
        return $this->originalName;
    }

    public function setOriginalName(?string $originalName): self
    {
        $this->originalName = $originalName;

        return $this;
    }

    /**
     * If manually uploading a file (i.e. not using Symfony Form) ensure an instance
     * of 'UploadedFile' is injected into this setter to trigger the  update. If this
     * bundle's configuration parameter 'inject_on_load' is set to 'true' this setter
     * must be able to accept an instance of 'File' as the bundle will inject one here
     * during Doctrine hydration.
     *
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile|null $profileFile
     */
    public function setProfileFile(?File $profileFile = null)
    {
        $this->profileFile = $profileFile;

        if (null !== $profileFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new \DateTimeImmutable();
        }
    }

    public function getProfileFile(): ?File
    {
        return $this->profileFile;
    }

    public function serialize()
    {
        return serialize([
            $this->id,
            $this->email,
            $this->firstName,
            $this->lastName,
            $this->password,
            $this->profileName,
        ]);
        
    }

    public function unserialize($serialized)
    {
        list (
            $this->id,
            $this->email,
            $this->firstName,
            $this->lastName,
            $this->password,
            $this->profileName
        ) = unserialize($serialized);
    }
}
