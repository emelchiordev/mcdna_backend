<?php

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Promotions;
use PHPUnit\Framework\TestCase;
use App\State\PromotionEndDateProcessor;

class PromotionEndDateProcessorTest extends TestCase
{
    public function testProcess()
    {
        $decorated = $this->createMock(ProcessorInterface::class);

        $decorated->expects($this->once())
            ->method('process')
            ->willReturn('result'); 

        $processor = new PromotionEndDateProcessor($decorated);

        $data = new Promotions(); 
        $data->setStartDate(new DateTimeImmutable("now"));
        $data->setEndDate(new DateTimeImmutable("now+1"));
        $operation = $this->createMock(Operation::class);
        $uriVariables = [];
        $context = [];

        $result = $processor->process($data, $operation, $uriVariables, $context);

        $this->assertEquals('result', $result);
    }
}
