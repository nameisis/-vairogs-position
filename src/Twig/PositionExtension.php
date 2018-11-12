<?php

namespace Vairogs\Utils\Position\Twig;

use Twig_Extension;
use Vairogs\Utils\Core\Twig\TwigTrait;
use Vairogs\Utils\Position\Service\PositionHandler;

class PositionExtension extends Twig_Extension
{
    use TwigTrait;

    public const NAME = 'position_object';

    private $positionService;

    public function __construct(PositionHandler $positionService, $shortFunctions)
    {
        $this->positionService = $positionService;
        $this->shortFunctions = $shortFunctions;
    }

    public function getFunctions(): array
    {
        $input = [
            self::NAME => 'getter',
        ];

        return $this->makeArray($input, 'function');
    }

    public function getter($entity)
    {
        $getter = \sprintf('get%s', \ucfirst($this->positionService->getPositionFieldByEntity($entity)));

        return $entity->{$getter}();
    }
}
