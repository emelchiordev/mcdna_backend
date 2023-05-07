<?php

namespace App\Entity;


use App\Validator\ValideDate;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use App\Repository\PromotionsRepository;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

#[ORM\Entity(repositoryClass: PromotionsRepository::class)]
#[ApiResource(operations: [
    new Post(),
    new GetCollection(),
    new Get()
])]
#[ApiFilter(SearchFilter::class, properties: ['products.id' => 'exact'])]
class Promotions
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 5, scale: 2)]
    #[Assert\NotBlank(message: "Vous devez saisir un pourcentage de remise")]
    private ?string $percentage = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Assert\NotBlank(message: "Vous devez saisir une date de début")]
    private ?\DateTimeInterface $startDate = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Assert\NotBlank(message: "Vous devez saisir une date de fin")]
    private ?\DateTimeInterface $endDate = null;

    #[ORM\ManyToOne(inversedBy: 'promotions')]
    private ?Products $products = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPercentage(): ?string
    {
        return $this->percentage;
    }

    public function setPercentage(string $percentage): self
    {
        $this->percentage = $percentage;

        return $this;
    }

    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->startDate;
    }

    public function setStartDate(?\DateTimeInterface $startDate): self
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getEndDate(): ?\DateTimeInterface
    {
        return $this->endDate;
    }

    public function setEndDate(?\DateTimeInterface $endDate): self
    {
        $this->endDate = $endDate;

        return $this;
    }

    public function getProducts(): ?Products
    {
        return $this->products;
    }

    public function setProducts(?Products $products): self
    {
        $this->products = $products;

        return $this;
    }

    // On vérifie si la date de fin est bien supérieur ou égale à la date de début sinon, on renvoie une Assertion
    #[Assert\Callback]
    public  function promotionIsValide(ExecutionContextInterface $context)
    {

        if ($this->endDate < $this->startDate) {
            $context->buildViolation('La date de fin doit être supérieure à la date de début')
                ->atPath('endDate')
                ->addViolation();
        }
    }

    #[Assert\Callback()]
    public function promotionDateIsValid(ExecutionContextInterface $context)
    {

        // Récupération de toutes les promotions liées au produit
        $promotionsProduct = $this->getProducts()->getPromotions();

        foreach ($promotionsProduct as $promotion) {
            if (($this->getStartDate() >= $promotion->getStartDate() && $this->getStartDate() <= $promotion->getEndDate()) ||
                ($this->getEndDate() >= $promotion->getStartDate() && $this->getEndDate() <= $promotion->getEndDate())
            ) {
                $context->buildViolation('La promotion se chevauche sur une promotion existante.')
                    ->atPath('startDate')
                    ->addViolation();
            }
        }
    }
}
