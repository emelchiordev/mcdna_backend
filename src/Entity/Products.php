<?php

namespace App\Entity;

use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Post;
use Doctrine\DBAL\Types\Types;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Delete;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiResource;
use App\Repository\ProductsRepository;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Core\Annotation\ApiFilter;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Bridge\Doctrine\MongoDbOdm\Filter\OrderFilter;
use App\State\ProductPromotionPaginatedProvider;
use Symfony\Component\HttpFoundation\File\File;
use Doctrine\Common\Collections\ArrayCollection;
use App\State\ProductPromotionProvider;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Filesystem\Path;
use Symfony\Component\Serializer\Annotation\Groups;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Entity(repositoryClass: ProductsRepository::class)]
#[ORM\Table(name: 'products')]
#[Vich\Uploadable]
#[ApiResource(order: ['createAt' => 'DESC'], operations: [
    new GetCollection(uriTemplate: "/products/withActivePromotion", provider: ProductPromotionProvider::class, security: "is_granted('PUBLIC_ACCESS')"),
    new GetCollection(uriTemplate: '/products/withPaginatedPromotion', provider: ProductPromotionPaginatedProvider::class, security: "is_granted('PUBLIC_ACCESS')"),
    new GetCollection(security: "is_granted('PUBLIC_ACCESS')"),
    new Get(security: "is_granted('PUBLIC_ACCESS')"),
    new Patch(security: "is_granted('ROLE_ADMIN')"),
    new Put(security: "is_granted('ROLE_ADMIN')"),
    new Post(
        inputFormats: ['multipart' => ['multipart/form-data']],
        validationContext: ["groups" => "create_product"],
        security: "is_granted('ROLE_ADMIN')"
    ),
    new Post(
        uriTemplate: "/products/{id}",
        inputFormats: ['multipart' => ['multipart/form-data']],
        validationContext: ["groups" => "update_product"],
        security: "is_granted('ROLE_ADMIN')"
    ),
    new Delete(security: "is_granted('ROLE_ADMIN')")
], normalizationContext: [
    'groups' => ['product_read', 'create_product']
])]
#[ORM\HasLifecycleCallbacks]
class Products
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['product_read', 'create_product'])]
    private ?int $id = null;

    #[Assert\NotBlank(message: "Le libellé est obligatoire", groups: ["update_product", 'create_product'])]
    #[ORM\Column(length: 255)]
    #[Groups(['product_read', 'create_product'])]
    private ?string $label = null;

    #[Assert\NotBlank(message: "La description est obligatoire", groups: ["update_product", 'create_product'])]
    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['product_read', 'create_product'])]
    private ?string $description = null;

    #[Assert\NotBlank(message: "Le prix est obligatoire", groups: ["update_product", 'create_product'])]
    #[Assert\PositiveOrZero(message: " Le prix doit être supérieur à 0 €", groups: ["update_product", 'create_product'])]
    #[Assert\Regex(
        pattern: '/^\d+(\.\d{1,2})?$/',
        message: "Le prix doit être un nombre décimal avec deux décimales",
        groups: ["update_product", 'create_product']
    )]
    #[Assert\Range(
        min: 0,
        max: 9999999.99,
        notInRangeMessage: "Le prix doit être compris entre {{ min }} € et {{ max }} €",
        invalidMessage: 'Le prix doit être un nombre décimal avec deux décimales',
        groups: ["update_product", 'create_product']
    )]
    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Groups(['product_read', 'create_product'])]
    private ?string $price = null;

    #[Vich\UploadableField(mapping: 'products', fileNameProperty: 'imageName')]
    #[Assert\NotBlank(message: "Vous devez uploader une image", groups: ['create_product'])]
    #[
        Assert\File(
            maxSize: "1M",
            mimeTypes: ['image/png', 'image/jpeg'],
            mimeTypesMessage: 'Le fichier doit être au format jpeg ou png',
            groups: ['create_product']
        )
    ]
    private ?File $imageFile = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups(['product_read', 'create_product'])]
    private ?string $imageName = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private $updatedAt;

    #[ORM\ManyToOne(inversedBy: 'products')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['product_read', 'create_product'])]
    #[Assert\NotBlank(message: "Vous devez sélectionner une catégorie", groups: ["update_product", 'create_product'])]
    private ?Category $category = null;

    #[ORM\OneToMany(mappedBy: 'products', targetEntity: Promotions::class, cascade: ['remove'])]
    private Collection $promotions;

    #[ORM\Column(nullable: true)]
    #[Groups(['product_read'])]
    private ?\DateTimeImmutable $createAt = null;


    #[ApiProperty()]
    #[Groups(['product_read'])]
    public function getDiscountPrice(): ?array
    {
        $now = new \DateTimeImmutable();
        $discountedPrice = null;

        foreach ($this->promotions as $promotion) {
            if ($now >= $promotion->getStartDate() && $now <= $promotion->getEndDate()) {
                $discountedPrice = [$this->getPrice() * (1 - $promotion->getPercentage() / 100), $promotion->getPercentage()];
                break;
            }
        }

        return $discountedPrice;
    }

    public function __construct()
    {
        $this->promotions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function setPrice(string $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    public function setImageFile(?File $imageFile = null): self
    {
        $this->imageFile = $imageFile;

        if (null !== $imageFile) {
            $this->updatedAt = new \DateTimeImmutable();
        }

        return $this;
    }


    public function getImageName(): ?string
    {
        return $this->imageName;
    }

    public function setImageName(?string $imageName): self
    {
        $this->imageName = $imageName;
        return $this;
    }


    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }

    /**
     * @return Collection<int, Promotions>
     */
    public function getPromotions(): Collection
    {
        return $this->promotions;
    }

    public function addPromotion(Promotions $promotion): self
    {
        if (!$this->promotions->contains($promotion)) {
            $this->promotions->add($promotion);
            $promotion->setProducts($this);
        }

        return $this;
    }

    public function removePromotion(Promotions $promotion): self
    {
        if ($this->promotions->removeElement($promotion)) {
            // set the owning side to null (unless already changed)
            if ($promotion->getProducts() === $this) {
                $promotion->setProducts(null);
            }
        }

        return $this;
    }

    public function getCreateAt(): ?\DateTimeImmutable
    {
        return $this->createAt;
    }

    #[ORM\PrePersist]
    public function setCreateAt(): void
    {
        $this->createAt = new \DateTimeImmutable();;
    }
}
