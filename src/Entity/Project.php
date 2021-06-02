<?php

namespace App\Entity;

use App\Repository\ProjectRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ProjectRepository::class)
 */
class Project
{

    const PAYMENT_TYPE_CARD = "payment.type.card";
    const PAYMENT_TYPE_CASH = "payment.type.cash";
    const PAYMENT_TYPE_TRANSFER = "payment.type.transfer";
    const PAYMENT_TYPE_CHECK = "payment.type.check";


    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * 
     * Fr: code chantier
     */
    private $siteCode;

    /**
     * @ORM\Column(type="boolean")
     * 
     * Fr: feuille de route
     */
    private $roadmap;

    /**
     * @ORM\Column(type="string", length=255)
     * 
     * Fr: maitre d'ouvrage
     */
    private $projectOwner;

    /**
     * @ORM\Column(type="string", length=255)
     * 
     * Fr: maitre d'oeuvre
     */
    private $projectManager;

    /**
     * @ORM\OneToOne(targetEntity=Address::class, cascade={"persist"})
     * 
     * Fr: adresse facturation
     */
    private $billingAddres;

    /**
     * @ORM\OneToOne(targetEntity=Address::class, cascade={"persist"})
     * 
     * Fr: addresse chantier
     */
    private $siteAddress;

    /**
     * @ORM\Column(type="string", length=255)
     * 
     * Fr: description de l' operation
     */
    private $descriptionOperation;

    /**
     * @ORM\ManyToOne(targetEntity=User::class)
     * 
     * Fr: Dossier vendu par
     */
    private $soldBy;

    /**
     * @ORM\ManyToOne(targetEntity=User::class)
     * 
     * Fr: rédacteur du devis
     */
    private $quoteWriter;

    /**
     * @ORM\Column(type="integer")
     * 
     * Fr: norme en 1090
     */
    private $norm1090;

    /**
     * @ORM\Column(type="integer")
     * 
     * Fr: type de marche
     */
    private $marketType;

    /**
     * @ORM\Column(type="integer")
     * 
     * Fr: bonhomme est il 
     */
    private $bonhomePercentage;

    /**
     * @ORM\Column(type="string", length=255)
     * 
     * Fr: validation de fiche DISA
     */
    private $disaSheetValidation;

    /**
     * @ORM\Column(type="string", length=255)
     * 
     * Fr: mode de reglement acompte
     */
    private $paymentChoice;

    /**
     * @ORM\Column(type="date", length=255)
     * 
     * Fr: date d'acompte doi etre édité le
     */
    private $depositeDateEdit;

    /**
     * @ORM\Column(type="string", length=255)
     * 
     * Fr: condition négocier avec le client
     */
    private $clientCondition;

    /**
     * @ORM\Column(type="string", length=255)
     * 
     * Fr: N° du/des devis MDE validé(s)
     */
    private $quoteValidatedMDE;

    /**
     * @ORM\Column(type="string", length=255)
     * 
     * Fr: Conditions négociées avec le client
     */
    private $quoteValidatedMDEDate;

    /**
     * @ORM\Column(type="integer")
     * 
     * Fr: MONTANT GLOBAL DU MARCHE
     */
    private $globalAmount;

    /**
     * @ORM\Column(type="integer")
     * 
     * Fr: montant des travaux sous-traiter
     */
    private $amountSubcontractedWork;

    /**
     * @ORM\Column(type="string", length=255)
     * 
     * Fr: montant des traveau propre a bbi
     */
    private $amountBBISpecificWork;

    /**
     * @ORM\Column(type="integer")
     * 
     * Fr: type de dossier
     */
    private $caseType;

    /**
     * @ORM\Column(type="string", length=255)
     * 
     * Fr: planning projet
     */
    private $planningProject;

    /**
     * @ORM\Column(type="string", length=255)
     * 
     * Fr: assistant en charge du dossier
     */
    private $recordAssistant;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $contact;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSiteCode(): ?string
    {
        return $this->siteCode;
    }

    public function setSiteCode(?string $siteCode): self
    {
        $this->siteCode = $siteCode;

        return $this;
    }

    public function getRoadmap(): ?bool
    {
        return $this->roadmap;
    }

    public function setRoadmap(bool $roadmap): self
    {
        $this->roadmap = $roadmap;

        return $this;
    }

    public function getProjectOwner(): ?string
    {
        return $this->projectOwner;
    }

    public function setProjectOwner(string $projectOwner): self
    {
        $this->projectOwner = $projectOwner;

        return $this;
    }

    public function getProjectManager(): ?string
    {
        return $this->projectManager;
    }

    public function setProjectManager(string $projectManager): self
    {
        $this->projectManager = $projectManager;

        return $this;
    }

    public function getBillingAddres(): ?Address
    {
        return $this->billingAddres;
    }

    public function setBillingAddres(Address $billingAddres): self
    {
        $this->billingAddres = $billingAddres;

        return $this;
    }


    public function getSiteAddress(): ?string
    {
        return $this->siteAddress;
    }

    public function setSiteAddress(Address $siteAddress): self
    {
        $this->siteAddress = $siteAddress;

        return $this;
    }

    public function getDescriptionOperation(): ?string
    {
        return $this->descriptionOperation;
    }

    public function setDescriptionOperation(string $descriptionOperation): self
    {
        $this->descriptionOperation = $descriptionOperation;

        return $this;
    }

    public function getSoldBy(): ?User
    {
        return $this->soldBy;
    }

    public function setSoldBy(User $soldBy): self
    {
        $this->soldBy = $soldBy;

        return $this;
    }

    public function getQuoteWriter(): ?User
    {
        return $this->quoteWriter;
    }

    public function setQuoteWriter(User $quoteWriter): self
    {
        $this->quoteWriter = $quoteWriter;

        return $this;
    }

    public function getNorm1090(): ?int
    {
        return $this->norm1090;
    }

    public function setNorm1090(int $norm1090): self
    {
        $this->norm1090 = $norm1090;

        return $this;
    }

    public function getMarketType(): ?int
    {
        return $this->marketType;
    }

    public function setMarketType(int $marketType): self
    {
        $this->marketType = $marketType;

        return $this;
    }

    public function getBonhomePercentage(): ?int
    {
        return $this->bonhomePercentage;
    }

    public function setBonhomePercentage(int $bonhomePercentage): self
    {
        $this->bonhomePercentage = $bonhomePercentage;

        return $this;
    }

    public function getDisaSheetValidation(): ?string
    {
        return $this->disaSheetValidation;
    }

    public function setDisaSheetValidation(string $disaSheetValidation): self
    {
        $this->disaSheetValidation = $disaSheetValidation;

        return $this;
    }

    public function getPaymentChoice(): ?string
    {
        return $this->paymentChoice;
    }

    public function setPaymentChoice(string $paymentChoice): self
    {
        $this->paymentChoice = $paymentChoice;

        return $this;
    }

    public function getDepositeDateEdit(): ?\DateTime
    {
        return $this->depositeDateEdit;
    }

    public function setDepositeDateEdit(?\DateTime $depositeDateEdit): self
    {
        $this->depositeDateEdit = $depositeDateEdit;

        return $this;
    }

    public function getClientCondition(): ?string
    {
        return $this->clientCondition;
    }

    public function setClientCondition(string $clientCondition): self
    {
        $this->clientCondition = $clientCondition;

        return $this;
    }

    public function getQuoteValidatedMDE(): ?string
    {
        return $this->quoteValidatedMDE;
    }

    public function setQuoteValidatedMDE(string $quoteValidatedMDE): self
    {
        $this->quoteValidatedMDE = $quoteValidatedMDE;

        return $this;
    }

    public function getQuoteValidatedMDEDate(): ?string
    {
        return $this->quoteValidatedMDEDate;
    }

    public function setQuoteValidatedMDEDate(string $quoteValidatedMDEDate): self
    {
        $this->quoteValidatedMDEDate = $quoteValidatedMDEDate;

        return $this;
    }

    public function getGlobalAmount(): ?int
    {
        return $this->globalAmount;
    }

    public function setGlobalAmount(int $globalAmount): self
    {
        $this->globalAmount = $globalAmount;

        return $this;
    }

    public function getAmountSubcontractedWork(): ?int
    {
        return $this->amountSubcontractedWork;
    }

    public function setAmountSubcontractedWork(int $amountSubcontractedWork): self
    {
        $this->amountSubcontractedWork = $amountSubcontractedWork;

        return $this;
    }

    public function getAmountBBISpecificWork(): ?string
    {
        return $this->amountBBISpecificWork;
    }

    public function setAmountBBISpecificWork(string $amountBBISpecificWork): self
    {
        $this->amountBBISpecificWork = $amountBBISpecificWork;

        return $this;
    }

    public function getCaseType(): ?int
    {
        return $this->caseType;
    }

    public function setCaseType(int $caseType): self
    {
        $this->caseType = $caseType;

        return $this;
    }

    public function getPlanningProject(): ?string
    {
        return $this->planningProject;
    }

    public function setPlanningProject(string $planningProject): self
    {
        $this->planningProject = $planningProject;

        return $this;
    }

    public function getRecordAssistant(): ?string
    {
        return $this->recordAssistant;
    }

    public function setRecordAssistant(string $recordAssistant): self
    {
        $this->recordAssistant = $recordAssistant;

        return $this;
    }

    public function getContact(): ?User
    {
        return $this->contact;
    }

    public function setContact(?User $contact): self
    {
        $this->contact = $contact;

        return $this;
    }

    public static function getPaymentTypeChoices($associative = false)
    {
        $choices = [
            self::PAYMENT_TYPE_CHECK,
            self::PAYMENT_TYPE_TRANSFER,
        ];

        return $associative ? array_combine($choices, $choices) : $choices;
    }
}
