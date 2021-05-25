<?php

namespace App\Entity;

use App\Repository\ProjectRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ProjectRepository::class)
 */
class Project
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $siteCode;

    /**
     * @ORM\Column(type="boolean")
     */
    private $roadmap;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $projectOwner;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $projectManager;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $billingAddres;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $contactName;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $phone;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $siteAddress;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $descriptionOperation;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $soldBy;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $quoteWriter;

    /**
     * @ORM\Column(type="integer")
     */
    private $norm1090;

    /**
     * @ORM\Column(type="integer")
     */
    private $marketType;

    /**
     * @ORM\Column(type="integer")
     */
    private $bonhomePercentage;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $disaSheetValidation;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $paymentChoice;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $depositeDateEdit;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $clientCondition;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $validatedMDE;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $workDistribution;

    /**
     * @ORM\Column(type="integer")
     */
    private $globalAmount;

    /**
     * @ORM\Column(type="integer")
     */
    private $amountSubcontractedWork;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $amountBBISpecificWork;

    /**
     * @ORM\Column(type="integer")
     */
    private $caseType;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $planningProject;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $recordAssistant;

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

    public function getBillingAddres(): ?string
    {
        return $this->billingAddres;
    }

    public function setBillingAddres(string $billingAddres): self
    {
        $this->billingAddres = $billingAddres;

        return $this;
    }

    public function getContactName(): ?string
    {
        return $this->contactName;
    }

    public function setContactName(string $contactName): self
    {
        $this->contactName = $contactName;

        return $this;
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

    public function getSiteAddress(): ?string
    {
        return $this->siteAddress;
    }

    public function setSiteAddress(string $siteAddress): self
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

    public function getSoldBy(): ?string
    {
        return $this->soldBy;
    }

    public function setSoldBy(string $soldBy): self
    {
        $this->soldBy = $soldBy;

        return $this;
    }

    public function getQuoteWriter(): ?string
    {
        return $this->quoteWriter;
    }

    public function setQuoteWriter(string $quoteWriter): self
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

    public function getDepositeDateEdit(): ?string
    {
        return $this->depositeDateEdit;
    }

    public function setDepositeDateEdit(string $depositeDateEdit): self
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

    public function getValidatedMDE(): ?string
    {
        return $this->validatedMDE;
    }

    public function setValidatedMDE(string $validatedMDE): self
    {
        $this->validatedMDE = $validatedMDE;

        return $this;
    }

    public function getWorkDistribution(): ?string
    {
        return $this->workDistribution;
    }

    public function setWorkDistribution(string $workDistribution): self
    {
        $this->workDistribution = $workDistribution;

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
}
