<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;

final class PromotionEndDateProcessor implements ProcessorInterface
{

    private $decorated;

    public function __construct(ProcessorInterface $decorated)
    {
        $this->decorated = $decorated;
    }

    public function process($data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        $endDate = $data->getEndDate();
        $endDate->setTime(21, 00, 00);
        $data->setEndDate($endDate);
        $result = $this->decorated->process($data, $operation, $uriVariables, $context);
        return $result;
    }
}
