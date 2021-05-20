<?php

namespace App\Entity;

use App\Repository\ClientRepository;
use App\Entity\Common\BlameableTrait;
use App\Entity\Common\SoftDeleteableTrait;
use App\Entity\Common\TimestampableTrait;
use App\Entity\Traits\ClientTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ClientRepository::class)
 */
class Client
{
    use BlameableTrait;
    use SoftDeleteableTrait;
    use TimestampableTrait;
    use ClientTrait;

    const PAYMENT_TYPE_CARD = "payment.type.card";
    const PAYMENT_TYPE_CASH = "payment.type.cash";
    const PAYMENT_TYPE_TRANSFER = "payment.type.transfer";
    const PAYMENT_TYPE_CHECK = "payment.type.check";

    const PAYMENT_PERIOD_45_NET = 'payment_period.net_45';
    const PAYMENT_PERIOD_30_NET = 'payment_period.net_30';
    const PAYMENT_PERIOD_30_END = 'payment_period.end_30';
    const PAYMENT_PERIOD_45_END = 'payment_period.end_45';
    const PAYMENT_PERIOD_IMMEDIATE_TRANSERT = 'payment_period.immediate_transfert';

    const TYPE_CLIENT = 'client';
    const TYPE_PROSPECT = 'prospect';
    
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\NotBlank
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $shortName;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $clientNumber;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $activity;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=0, nullable=true)
     * @Assert\Range(min="0", max="100")
     */
    private $tvaRate;

    /**
     * @ORM\Column(type="string", length=30, nullable=true)
     */
    private $siret;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $paymentMethod;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $payment;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2, nullable=true)
     * 
     * Intracommunautaire tva
     */
    private $intraCommunityTva;

    /**
     * @ORM\OneToOne(targetEntity=Address::class, cascade={"persist", "remove"})
     * @Assert\Valid
     * 
     * Adresse de livraison
     */
    private $billingAddress;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $type;

    /**
     * If client can be join to many contact then remove the cascade persist
     * For now client only have relation with contact who have create with the client
     * 
     * @ORM\ManyToMany(targetEntity=User::class, cascade={"persist", "refresh", "remove"})
     */
    private $contacts;

    public function __construct()
    {
        $this->type = self::TYPE_PROSPECT;
        $this->contacts = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getShortName(): ?string
    {
        return $this->shortName;
    }

    public function setShortName(?string $shortName): self
    {
        $this->shortName = $shortName;

        return $this;
    }

    public function getClientNumber(): ?string
    {
        return $this->clientNumber;
    }

    public function setClientNumber(?string $clientNumber): self
    {
        $this->clientNumber = $clientNumber;

        return $this;
    }

    public function getActivity(): ?string
    {
        return $this->activity;
    }

    public function setActivity(?string $activity): self
    {
        $this->activity = $activity;

        return $this;
    }

    public function getTvaRate(): ?string
    {
        return $this->tvaRate;
    }

    public function setTvaRate(?string $tvaRate): self
    {
        $this->tvaRate = $tvaRate;

        return $this;
    }

    public function getSiret(): ?string
    {
        return $this->siret;
    }

    public function setSiret(?string $siret): self
    {
        $this->siret = $siret;

        return $this;
    }

    public function getPaymentMethod(): ?string
    {
        return $this->paymentMethod;
    }

    public function setPaymentMethod(?string $paymentMethod): self
    {
        $this->paymentMethod = $paymentMethod;

        return $this;
    }

    public function getPayment(): ?string
    {
        return $this->payment;
    }

    public function setPayment(?string $payment): self
    {
        $this->payment = $payment;

        return $this;
    }

    public function getIntraCommunityTva(): ?string
    {
        return $this->intraCommunityTva;
    }

    public function setIntraCommunityTva(?string $intraCommunityTva): self
    {
        $this->intraCommunityTva = $intraCommunityTva;

        return $this;
    }

    public function getBillingAddress(): ?Address
    {
        return $this->billingAddress;
    }

    public function setBillingAddress(?Address $billingAddress): self
    {
        $this->billingAddress = $billingAddress;

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

    /**
     * @return Collection|User[]
     */
    public function getContacts(): Collection
    {
        return $this->contacts;
    }

    public function addContact(User $contact): self
    {
        if (!$this->contacts->contains($contact)) {
            $this->contacts[] = $contact;
        }

        return $this;
    }

    public function removeContact(User $contact): self
    {
        $this->contacts->removeElement($contact);

        return $this;
    }
}
