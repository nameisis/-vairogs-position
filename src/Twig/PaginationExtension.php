<?php

namespace Vairogs\Utils\Position\Twig;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Twig_Extension;
use Twig_SimpleFunction;
use Vairogs\Utils\Core\Twig\TwigTrait;
use Vairogs\Utils\Position\Twig\Behaviour\Behaviour;
use Vairogs\Utils\Position\Twig\Behaviour\FixedLength;

class PaginationExtension extends Twig_Extension
{
    use TwigTrait;

    /**
     * @var Behaviour[]
     */
    private $functions;

    public function __construct($shortFunctions)
    {
        $this->functions = [];
        $this->shortFunctions = $shortFunctions;
    }

    public function setContainer(ContainerInterface $container): void
    {
        if ($container->hasParameter('vairogs.utils.pagination.behaviour')) {
            foreach ($container->getParameter('vairogs.utils.pagination.behaviour') ?? [] as $function) {
                if ($this->shortFunctions) {
                    $this->functions[] = $this->withFunction(\array_keys($function)[0], \array_values($function)[0]);
                }
                $this->functions[] = $this->withFunction('vairogs_'.\array_keys($function)[0], \array_values($function)[0]);
            }
        }
    }

    public function withFunction($functionName, $behaviour)
    {
        $functionName = $this->suffixFunctionName($functionName);
        $behaviour = new FixedLength($behaviour);
        $clone = clone $this;
        $clone->functions[$functionName] = new Twig_SimpleFunction($functionName, [
            $behaviour,
            'getPaginationData',
        ]);

        return $clone->functions[$functionName];
    }

    private function suffixFunctionName(string $functionName): string
    {
        return \preg_replace('/(_pagination)$/', '', $functionName).'_pagination';
    }

    public function withoutFunction($functionName)
    {
        $functionName = $this->suffixFunctionName($functionName);
        $clone = clone $this;
        unset($clone->functions[$functionName]);

        return $clone;
    }

    public function getFunctions(): array
    {
        return \array_values($this->functions);
    }
}
