<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\InvoiceRepository;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=InvoiceRepository::class)
 * @ApiResource(
 *   denormalizationContext={"disable_type_enforcement"=true},
 *   normalizationContext={ "groups"={"invoices_read"}},
 *   attributes={
 *         "pagination_enabled"=true,
 *         "pagination_items_per_page" = 20,
 *         "order" = {"sentAt"="desc"}
 *  },
 *  subresourceOperations={
 *  "api_customers_invoices_get_subresources"={
 *      "normalization_context"={
 *          "groups"={"invoices_subresource"}
 *        }
 *      }
 *   }
 *  
 * )
 *@ApiFilter(OrderFilter::class, properties ={"sentAt","amount"})
 */
class Invoice
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @groups({"invoices_read","customers_read","invoices_subresource"})
     */
    private $id;

    /**
     * @ORM\Column(type="float")
     * @groups({"invoices_read","customers_read","invoices_subresource"})
     * @Assert\Type(type="numeric",message="le format doit être numerique")
     * 
     */
    private $amount;

    /**
     * @ORM\Column(type="datetime")
     * @groups({"invoices_read","customers_read","invoices_subresource"})
     * @Assert\DateTime(message="en format YYYY-MM-DD")
     * @Assert\NotBlank(message ="le champs doit être obligatoire")
     */
    private $sentAt;

    /**
     * @ORM\Column(type="string", length=255)
     * @groups({"invoices_read","customers_read","invoices_subresource"})
     * @Assert\NotBlank(message ="le champs doit être obligatoire")
     * @Assert\Choice(choices="SENT","PAID","CANCELED")
     */
    private $status;

    /**
     * @ORM\ManyToOne(targetEntity=Customer::class, inversedBy="invoices")
     * @ORM\JoinColumn(nullable=false)
     * @groups({"invoices_read"})
     * @Assert\NotBlank(message ="le champs doit être obligatoire")
     */
    private $customer;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"invoices_read","customers_read","invoices_subresource"})
     */
    private $chrono;

    /**
     * Permet récuperer le User à qui appartient la facture
     * @groups({"invoices_read","invoices_subresource"})
     * @return User
     */
    public function getUser(): ?User
    {
        return $this->customer->getUser();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAmount(): ?float
    {
        return $this->amount;
    }

    public function setAmount( $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function getSentAt(): ?\DateTimeInterface
    {
        return $this->sentAt;
    }

    public function setSentAt( $sentAt): self
    {
        $this->sentAt = $sentAt;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getCustomer(): ?Customer
    {
        return $this->customer;
    }

    public function setCustomer(?Customer $customer): self
    {
        $this->customer = $customer;

        return $this;
    }

    public function getChrono(): ?int
    {
        return $this->chrono;
    }

    public function setChrono($chrono): self
    {
        $this->chrono = $chrono;

        return $this;
    }
}
