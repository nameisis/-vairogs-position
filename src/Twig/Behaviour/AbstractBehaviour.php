<?php

namespace Vairogs\Utils\Position\Twig\Behaviour;

use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;

abstract class AbstractBehaviour implements Behaviour
{
    protected function guardPaginationData($totalPages, $currentPage, $omittedPagesIndicator = -1): void
    {
        $this->guardTotalPagesMinimumValue($totalPages);
        $this->guardCurrentPageMinimumValue($currentPage);
        $this->guardCurrentPageExistsInTotalPages($totalPages, $currentPage);
        $this->guardOmittedPagesIndicatorType($omittedPagesIndicator);
        $this->guardOmittedPagesIndicatorIntValue($totalPages, $omittedPagesIndicator);
    }

    private function guardTotalPagesMinimumValue($totalPages): void
    {
        if ($totalPages < 1) {
            throw new InvalidArgumentException(\sprintf('Total number of pages (%d) should not be lower than 1.', $totalPages));
        }
    }

    private function guardCurrentPageMinimumValue($currentPage): void
    {
        if ($currentPage < 1) {
            throw new InvalidArgumentException(\sprintf('Current page (%d) should not be lower than 1.', $currentPage));
        }
    }

    private function guardCurrentPageExistsInTotalPages($totalPages, $currentPage): void
    {
        if ($currentPage > $totalPages) {
            throw new InvalidArgumentException(\sprintf('Current page (%d) should not be higher than total number of pages (%d).', $currentPage, $totalPages));
        }
    }

    private function guardOmittedPagesIndicatorType($indicator): void
    {
        if (!\is_int($indicator) && !\is_string($indicator)) {
            throw new InvalidArgumentException('Omitted pages indicator should either be a string or an int.');
        }
    }

    private function guardOmittedPagesIndicatorIntValue($totalPages, $indicator): void
    {
        if (\is_int($indicator) && $indicator >= 1 && $indicator <= $totalPages) {
            throw new InvalidArgumentException(\sprintf('Omitted pages indicator (%d) should not be between 1 and total number of pages (%d).', $indicator, $totalPages));
        }
    }
}
