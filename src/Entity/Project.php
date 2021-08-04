<?php

namespace App\Entity;

use App\Repository\ProjectRepository;
use App\Entity\Common\BlameableTrait;
use App\Entity\Common\SoftDeleteableTrait;
use App\Entity\Common\TimestampableTrait;
use App\Entity\Constants\Status;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Traits\ProjectTrait;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false, hardDelete=true)
 * @Gedmo\Loggable
 * @ORM\Entity(repositoryClass=ProjectRepository::class)
 */
class Project
{
    use BlameableTrait;
    use SoftDeleteableTrait;
    use TimestampableTrait;
    use ProjectTrait;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"data-project", "project:scheduler-resource", "projectEvent:scheduler"})
     */
    private $id;

    /**
     * Fr: code chantier
     * 
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"data-project", "project:scheduler-resource"})
     * @Assert\NotBlank(
     *  groups={"project:create"}
     * )
     */
    private $siteCode;

    /**
     * Fr: feuille de route
     * 
     * @ORM\Column(type="boolean", nullable=true)
     * @Assert\NotBlank(
     *  groups={"project:create"}
     * )
     * @Groups({"data-project"})
     */
    private $roadmap;

    /**
     * Fr: maitre d'ouvrage
     * 
     * @ORM\Column(nullable=true)
     * @Assert\NotBlank(
     *  groups={"project:create"}
     * )
     * @Groups({"data-project"})
     */
    private $projectOwner;

    /**
     * @ORM\Column(nullable=true)
     * @Assert\NotBlank(
     *  groups={"project:create"}
     * )
     *
     * Fr: maitre d'oeuvre
     * @Groups({"data-project"})
     */
    private $projectManager;

    /**
     * Fr: adresse facturation
     *
     * @ORM\OneToOne(targetEntity=Address::class, cascade={"persist"})
     * @Assert\Valid(
     *  groups={"project:create"}
     * )
     * @Groups({"data-project"})
     */
    private $billingAddres;

    /**
     * Fr: addresse chantier
     * 
     * @ORM\OneToOne(targetEntity=Address::class, cascade={"persist"})
     * @Assert\Valid(
     *  groups={"project:create"}
     * )
     * @Groups({"data-project"})
     */
    private $siteAddress;

    /**
     * Fr: description de l' operation
     * 
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\NotBlank(
     *  groups={"project:create"}
     * )
     * @Groups({"data-project"})
     */
    private $descriptionOperation;

    /**
     * fr: Chargé(e) d'affaire
     * 
     * @ORM\ManyToOne(targetEntity=User::class, cascade={"persist"})
     * @ORM\JoinColumn(nullable=true)
     * @Assert\NotBlank(
     *  groups={"project:create"}
     * )
     * @Groups({"data-project"})
     */
    private $businessCharge;

    /**
     * Fr: rédacteur du devis
     * 
     * @ORM\ManyToOne(targetEntity=User::class, cascade={"persist"})
     * @ORM\JoinColumn(nullable=true)
     * @Assert\NotBlank(
     *  groups={"project:create"}
     * )
     * @Groups({"data-project"})
     */
    private $economist;

    /**
     * Fr: norme en 1090
     * 
     * @ORM\Column(type="integer", nullable=true)
     * @Assert\NotBlank(
     *  groups={"project:create"}
     * )
     * @Groups({"data-project"})
     */
    private $norm1090;

    /**
     * @ORM\Column(nullable=true)
     * 
     * Fr: type de marche
     * @Groups({"data-project", "project:scheduler-resource"})
     */
    private $marketType;

    /**
     * Fr: bonhomme est il 
     * 
     * @ORM\Column(nullable=true)
     * @Groups({"data-project"})
     */
    private $bonhomePercentage;

    /**
     * Fr: validation de fiche DISA
     * 
     * @ORM\Column(type="array", nullable=true)
     * @Groups({"data-project"})
     */
    private $disaSheetValidation;

    /**
     * Fr: mode de reglement acompte
     * 
     * @ORM\Column(nullable=true)
     * @Groups({"data-project"})
     */
    private $paymentChoice;

    /**
     * Fr: mode de reglement pourcentage
     * 
     * @ORM\Column(type="float", nullable=true)
     * @Groups({"data-project"})
     */
    private $paymentPercentage;

    /**
     * Fr: date d'acompte doi etre édité le
     * 
     * @ORM\Column(type="date", length=255, nullable=true)
     * Assert\Date
     * @Groups({"data-project"})
     */
    private $depositeDateEdit;

    /**
     * Fr: condition négocier avec le client
     * 
     * @ORM\Column(type="text", length=255, nullable=true)
     * @Groups({"data-project"})
     */
    private $clientCondition;

    /**
     * Fr: N° du/des devis MDE validé(s)
     * 
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"data-project"})
     */
    private $quoteValidatedMDE;

    /**
     * Fr: Conditions négociées avec le client
     * 
     * @ORM\Column(type="date", nullable=true)
     * Assert\Date
     * @Groups({"data-project"})
     */
    private $quoteValidatedMDEDate;

    /**
     * Fr: MONTANT GLOBAL DU MARCHE
     * 
     * @ORM\Column(type="integer", nullable=true)
     * @Groups({"data-project", "project:scheduler-resource"})
     */
    private $globalAmount;

    /**
     * Fr: montant des travaux sous-traiter
     * 
     * @ORM\Column(type="integer", nullable=true)
     * @Groups({"data-project", "project:scheduler-resource"})
     */
    private $amountSubcontractedWork;

    /**
     * Fr: montant des traveau propre a bbi
     * 
     * @ORM\Column(type="integer", length=255, nullable=true)
     * @Groups({"data-project", "project:scheduler-resource"})
     */
    private $amountBBISpecificWork;

    /**
     * Fr: type de dossier
     * 
     * @ORM\Column(type="array", nullable=true)
     * @Assert\NotBlank(
     *  groups={"project:create"}
     * )
     * @Groups({"data-project", "project:scheduler-resource"})
     */
    private $caseType;

    /**
     * Fr: planning projet
     * 
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\NotBlank(
     *  groups={"project:create"}
     * )
     * @Groups({"data-project"})
     */
    private $planningProject;

    /**
     * Fr: assistant en charge du dossier
     * 
     * @ORM\ManyToOne(targetEntity=User::class, cascade={"persist"})
     * @ORM\JoinColumn(nullable=true)
     * @Groups({"data-project"})
     */
    private $recordAssistant;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, cascade={"persist"})
     * @ORM\JoinColumn(nullable=true)
     * @Assert\Valid
     * @Groups({"data-project"})
     */
    private $contact;

    /**
     * fr : CONDUC. OCBS
     * 
     * @ORM\ManyToOne(targetEntity=User::class, cascade={"persist"})
     * @ORM\JoinColumn(nullable=true)
     * @Groups({"data-project"})
     */
    private $ocbsDriver;

    /**
     * fr: CONDUC TCE
     * 
     * @ORM\ManyToOne(targetEntity=User::class, cascade={"persist"})
     * @ORM\JoinColumn(nullable=true)
     * @Groups({"data-project"})
     */
    private $tceDriver;

    /**
     * @ORM\ManyToOne(targetEntity=Client::class, cascade={"persist"}, inversedBy="projects")
     * @ORM\JoinColumn(nullable=true)
     * @Groups({"data-project", "project:scheduler-resource"})
     */
    private $prospect;

    /**
     * fr: type de chiffrage
     * 
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"data-project"})
     */
    private $encryptiontype;

    /**
     * fr: Sans objet
     * 
     * @ORM\Column(type="boolean", nullable=true)
     * @Groups({"data-project"})
     */
    private $notApplicable;

    /**
     * fr: Priorisation du dossier
     * 
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\NotBlank(
     *  groups={"project:create"}
     * )
     * @Groups({"data-project"})
     */
    private $priorizationOfFile;

    /**
     * fr: Réponse pour le
     * 
     * @ORM\Column(type="text", nullable=true)
     * @Assert\NotBlank(
     *  groups={"project:create"}
     * )
     * @Groups({"data-project"})
     */
    private $answerForThe;


    /**
     * fr: Nom du dossier sur le serveur
     * 
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\NotBlank(
     * groups={"project:create"}
     * )
     * @Groups({"data-project"})
     */
    private $folderNameOnTheServer;

    /**
     * Commentaire
     * 
     * @ORM\Column(type="text", nullable=true, nullable=true)
     * @Groups({"data-project"})
     */
    private $comment;

    /**
     * % Realisation
     * 
     * @ORM\Column(type="decimal", precision=10, scale=0, nullable=true)
     * @Groups({"data-project"})
     */
    private $completion;

    /**
     * Dernier relaunch
     * 
     * @ORM\OneToOne(targetEntity=Relaunch::class, cascade={"persist", "remove"}) 
     * @Groups({"data-project"})
     */
    private $lastRelaunch;

    /**
     * Relaunch
     * 
     * @ORM\ManyToMany(targetEntity=Relaunch::class, cascade={"persist"})
     * @Groups({"data-project"})
     */
    private $relaunches;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     * @Groups({"data-project"})
     */
    private $pcDeposit;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     * @Groups({"data-project"})
     */
    private $architect;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"data-project"})
     */
    private $name;

    /**
     * la valeur de cette champs est l'un des valeurs suivant
     * - PUBLIC_MARKET, PRIVATE_MARKET, AO_private, AO_PUBLIC
     * - un marché batiment neuf peut ^tre un ao public ou un marché priveé
     * 
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"data-project"})
     */
    private $scope;

    /**
     * @ORM\OneToMany(targetEntity=ExchangeHistory::class, mappedBy="Project")
     * @Groups({"data-project"})
     */
    private $exchangeHistories;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"data-project"})
     */
    private $status;

    /**
     * @ORM\ManyToMany(targetEntity=Action::class, cascade={"all"}, orphanRemoval=true)
     */
    private $actions;

    /**
     * @ORM\OneToOne(targetEntity=ProjectMeta::class, cascade={"all"})
     * @Groups({"data-project"})
     */
    private $meta;

    /**
     * @ORM\OneToMany(targetEntity=ProjectEvent::class, mappedBy="project", cascade={"all"}, orphanRemoval=true)
     * @Groups({"data-project"})
     */
    private $events;

    public function __construct()
    {
        $this->status = Status::STATUS_PENDING;
        $this->relaunches = new ArrayCollection();
        $this->exchangeHistories = new ArrayCollection();
        $this->actions = new ArrayCollection();
        $this->events = new ArrayCollection();
    }

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

    public function setRoadmap(?bool $roadmap): self
    {
        $this->roadmap = $roadmap;

        return $this;
    }

    public function getProjectOwner(): ?string
    {
        return $this->projectOwner;
    }

    public function setProjectOwner(?string $projectOwner): self
    {
        $this->projectOwner = $projectOwner;

        return $this;
    }

    public function getProjectManager(): ?string
    {
        return $this->projectManager;
    }

    public function setProjectManager(?string $projectManager): self
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

    public function getSiteAddress(): ?Address
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

    public function setDescriptionOperation(?string $descriptionOperation): self
    {
        $this->descriptionOperation = $descriptionOperation;

        return $this;
    }

    public function getBusinessCharge(): ?User
    {
        return $this->businessCharge;
    }

    public function setBusinessCharge(?User $businessCharge): self
    {
        $this->businessCharge = $businessCharge;

        return $this;
    }

    public function getEconomist(): ?User
    {
        return $this->economist;
    }

    public function setEconomist(?User $economist): self
    {
        $this->economist = $economist;

        return $this;
    }

    public function getNorm1090(): ?int
    {
        return $this->norm1090;
    }

    public function setNorm1090(?int $norm1090): self
    {
        $this->norm1090 = $norm1090;

        return $this;
    }

    public function getMarketType()
    {
        return $this->marketType;
    }

    public function setMarketType($marketType): self
    {
        $this->marketType = $marketType;

        return $this;
    }

    public function getBonhomePercentage(): ?string
    {
        return $this->bonhomePercentage;
    }

    public function setBonhomePercentage(?string $bonhomePercentage): self
    {
        $this->bonhomePercentage = $bonhomePercentage;

        return $this;
    }

    public function getDisaSheetValidation()
    {
        return $this->disaSheetValidation;
    }

    public function setDisaSheetValidation($disaSheetValidation): self
    {
        $this->disaSheetValidation = $disaSheetValidation;

        return $this;
    }

    public function getPaymentChoice()
    {
        return $this->paymentChoice;
    }

    public function setPaymentChoice($paymentChoice): self
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

    public function getClientCondition()
    {
        return $this->clientCondition;
    }

    public function setClientCondition($clientCondition): self
    {
        $this->clientCondition = $clientCondition;

        return $this;
    }

    public function getQuoteValidatedMDE(): ?string
    {
        return $this->quoteValidatedMDE;
    }

    public function setQuoteValidatedMDE(?string $quoteValidatedMDE): self
    {
        $this->quoteValidatedMDE = $quoteValidatedMDE;

        return $this;
    }

    public function getQuoteValidatedMDEDate(): ?\DateTime
    {
        return $this->quoteValidatedMDEDate;
    }

    public function setQuoteValidatedMDEDate(?\DateTime $quoteValidatedMDEDate): self
    {
        $this->quoteValidatedMDEDate = $quoteValidatedMDEDate;

        return $this;
    }

    public function getGlobalAmount(): ?int
    {
        return $this->globalAmount;
    }

    public function setGlobalAmount(?int $globalAmount): self
    {
        $this->globalAmount = $globalAmount;

        return $this;
    }

    public function getAmountSubcontractedWork(): ?int
    {
        return $this->amountSubcontractedWork;
    }

    public function setAmountSubcontractedWork(?int $amountSubcontractedWork): self
    {
        $this->amountSubcontractedWork = $amountSubcontractedWork;

        return $this;
    }

    public function getAmountBBISpecificWork(): ?string
    {
        return $this->amountBBISpecificWork;
    }

    public function setAmountBBISpecificWork(?string $amountBBISpecificWork): self
    {
        $this->amountBBISpecificWork = $amountBBISpecificWork;

        return $this;
    }

    public function getCaseType()
    {
        return $this->caseType;
    }

    public function setCaseType($caseType): self
    {
        $this->caseType = $caseType;

        return $this;
    }

    public function getPlanningProject(): ?string
    {
        return $this->planningProject;
    }

    public function setPlanningProject(?string $planningProject): self
    {
        $this->planningProject = $planningProject;

        return $this;
    }

    public function getRecordAssistant(): ?User
    {
        return $this->recordAssistant;
    }

    public function setRecordAssistant(?User $recordAssistant): self
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


    /**
     * @return mixed
     */
    public function getPaymentPercentage()
    {
        return $this->paymentPercentage;
    }

    /**
     * @param mixed $paymentPercentage
     */
    public function setPaymentPercentage($paymentPercentage): void
    {
        $this->paymentPercentage = $paymentPercentage;
    }

    public function getOcbsDriver(): ?User
    {
        return $this->ocbsDriver;
    }

    public function setOcbsDriver(?User $ocbsDriver): self
    {
        $this->ocbsDriver = $ocbsDriver;

        return $this;
    }

    public function getTceDriver(): ?User
    {
        return $this->tceDriver;
    }

    public function setTceDriver(?User $tceDriver): self
    {
        $this->tceDriver = $tceDriver;

        return $this;
    }

    public function getProspect(): ?Client
    {
        return $this->prospect;
    }

    public function setProspect(?Client $prospect): self
    {
        $this->prospect = $prospect;

        return $this;
    }

    public function getEncryptiontype(): ?string
    {
        return $this->encryptiontype;
    }

    public function setEncryptiontype(?string $encryptiontype): self
    {
        $this->encryptiontype = $encryptiontype;

        return $this;
    }

    public function getNotApplicable(): ?bool
    {
        return $this->notApplicable;
    }

    public function setNotApplicable(?bool $notApplicable): self
    {
        $this->notApplicable = $notApplicable;

        return $this;
    }

    public function getPriorizationOfFile(): ?string
    {
        return $this->priorizationOfFile;
    }

    public function setPriorizationOfFile(?string $priorizationOfFile): self
    {
        $this->priorizationOfFile = $priorizationOfFile;

        return $this;
    }

    public function getAnswerForThe(): ?string
    {
        return $this->answerForThe;
    }

    public function setAnswerForThe(?string $answerForThe): self
    {
        $this->answerForThe = $answerForThe;

        return $this;
    }

    public function getFolderNameOnTheServer(): ?string
    {
        return $this->folderNameOnTheServer;
    }

    public function setFolderNameOnTheServer(?string $folderNameOnTheServer): self
    {
        $this->folderNameOnTheServer = $folderNameOnTheServer;

        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): self
    {
        $this->comment = $comment;

        return $this;
    }

    public function getCompletion(): ?string
    {
        return $this->completion;
    }

    public function setCompletion(?string $completion): self
    {
        $this->completion = $completion;

        return $this;
    }

    public function getLastRelaunch(): ?Relaunch
    {
        return $this->lastRelaunch;
    }

    public function setLastRelaunch(?Relaunch $lastRelaunch): self
    {
        $this->lastRelaunch = $lastRelaunch;

        return $this;
    }

    /**
     * @return Collection|Relaunch[]
     */
    public function getRelaunches(): Collection
    {
        return $this->relaunches;
    }

    public function addRelaunch(Relaunch $relaunch): self
    {
        if (!$this->relaunches->contains($relaunch)) {
            $this->relaunches[] = $relaunch;
        }

        return $this;
    }

    public function removeRelaunch(Relaunch $relaunch): self
    {
        $this->relaunches->removeElement($relaunch);

        return $this;
    }

    public function getPcDeposit(): ?bool
    {
        return $this->pcDeposit;
    }

    public function setPcDeposit(?bool $pcDeposit): self
    {
        $this->pcDeposit = $pcDeposit;

        return $this;
    }

    public function getArchitect(): ?bool
    {
        return $this->architect;
    }

    public function setArchitect(?bool $architect): self
    {
        $this->architect = $architect;

        return $this;
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

    public function getScope(): ?string
    {
        return $this->scope;
    }

    public function setScope(?string $scope): self
    {
        $this->scope = $scope;

        return $this;
    }

    /**
     * @return Collection|ExchangeHistory[]
     */
    public function getExchangeHistories(): Collection
    {
        return $this->exchangeHistories;
    }

    public function addExchangeHistory(ExchangeHistory $exchangeHistory): self
    {
        if (!$this->exchangeHistories->contains($exchangeHistory)) {
            $this->exchangeHistories[] = $exchangeHistory;
            $exchangeHistory->setProject($this);
        }

        return $this;
    }

    public function removeExchangeHistory(ExchangeHistory $exchangeHistory): self
    {
        if ($this->exchangeHistories->removeElement($exchangeHistory)) {
            // set the owning side to null (unless already changed)
            if ($exchangeHistory->getProject() === $this) {
                $exchangeHistory->setProject(null);
            }
        }

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): self
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return Collection|action[]
     */
    public function getActions(): Collection
    {
        return $this->actions;
    }

    public function addAction(action $action): self
    {
        if (!$this->actions->contains($action)) {
            $this->actions[] = $action;
        }

        return $this;
    }

    public function removeAction(action $action): self
    {
        $this->actions->removeElement($action);

        return $this;
    }

    public function getMeta(): ?ProjectMeta
    {
        return $this->meta;
    }

    public function setMeta(?ProjectMeta $meta): self
    {
        $this->meta = $meta;

        return $this;
    }

    /**
     * @return Collection|ProjectEvent[]
     */
    public function getEvents(): Collection
    {
        return $this->events;
    }

    public function addEvent(ProjectEvent $event): self
    {
        if (!$this->events->contains($event)) {
            $this->events[] = $event;
            $event->setProject($this);
        }

        return $this;
    }

    public function removeEvent(ProjectEvent $event): self
    {
        if ($this->events->removeElement($event)) {
            // set the owning side to null (unless already changed)
            if ($event->getProject() === $this) {
                $event->setProject(null);
            }
        }

        return $this;
    }

}
