<?php

use App\Entity\Products;
use App\Entity\Promotions;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Exception\ConstraintViolationException;

final class PromotionsTest extends TestCase
{
    public function testCreatePromotionEndDateBeforeStartDate()
    {
        $product = new Products();
        $promotion = new Promotions();
        $promotion->setProducts($product);
        $promotion->setPercentage('10');
        $promotion->setStartDate(new \DateTime());
        $promotion->setEndDate(new \DateTime('-1 day'));


        $validator = Validation::createValidatorBuilder()
            ->enableAnnotationMapping()
            ->getValidator();
        $violations = $validator->validate($promotion);

        $this->assertCount(1, $violations);
    }
}
