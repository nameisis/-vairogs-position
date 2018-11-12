<?php

namespace Vairogs\Utils\Position\Twig\Behaviour;

use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;

final class FixedLength extends AbstractBehaviour
{
    public const MIN_VISIBLE = 3;

    /**
     * @var int
     */
    private $maximumVisible;

    public function __construct($maximumVisible)
    {
        $this->setMaximumVisible($maximumVisible);
    }

    private function setMaximumVisible($maximumVisible): void
    {
        $maximumVisible = (int)$maximumVisible;
        $this->guardMaximumVisibleMinimumValue($maximumVisible);
        $this->maximumVisible = $maximumVisible;
    }

    private function guardMaximumVisibleMinimumValue($maximumVisible): void
    {
        if ($maximumVisible < self::MIN_VISIBLE) {
            throw new InvalidArgumentException(\sprintf('Maximum of number of visible pages (%d) should be at least %d.', $maximumVisible, self::MIN_VISIBLE));
        }
    }

    public function withMaximumVisible($maximumVisible)
    {
        $clone = clone $this;
        $clone->setMaximumVisible($maximumVisible);

        return $clone;
    }

    public function getMaximumVisible(): int
    {
        return $this->maximumVisible;
    }

    /**
     * {@inheritdoc}
     */
    public function getPaginationData($totalPages, $currentPage, $omittedPagesIndicator = -1): array
    {
        $this->guardPaginationData($totalPages, $currentPage, $omittedPagesIndicator);
        if ($totalPages <= $this->maximumVisible) {
            return $this->getPaginationDataWithNoOmittedChunks($totalPages);
        }
        if ($this->hasSingleOmittedChunk($totalPages, $currentPage)) {
            return $this->getPaginationDataWithSingleOmittedChunk($totalPages, $currentPage, $omittedPagesIndicator);
        }

        return $this->getPaginationDataWithTwoOmittedChunks($totalPages, $currentPage, $omittedPagesIndicator);
    }

    private function getPaginationDataWithNoOmittedChunks($totalPages): array
    {
        return \range(1, $totalPages);
    }

    public function hasSingleOmittedChunk($totalPages, $currentPage): bool
    {
        return $this->hasSingleOmittedChunkNearLastPage($currentPage) || $this->hasSingleOmittedChunkNearStartPage($totalPages, $currentPage);
    }

    private function hasSingleOmittedChunkNearLastPage($currentPage): bool
    {
        return $currentPage <= $this->getSingleOmissionBreakpoint();
    }

    private function getSingleOmissionBreakpoint(): int
    {
        return (int)\floor($this->maximumVisible / 2) + 1;
    }

    private function hasSingleOmittedChunkNearStartPage($totalPages, $currentPage): bool
    {
        return $currentPage >= $totalPages - $this->getSingleOmissionBreakpoint() + 1;
    }

    private function getPaginationDataWithSingleOmittedChunk($totalPages, $currentPage, $omittedPagesIndicator): array
    {
        if ($this->hasSingleOmittedChunkNearLastPage($currentPage)) {
            $rest = $this->maximumVisible - $currentPage;
            $omitPagesFrom = ((int)\ceil($rest / 2)) + $currentPage;
            $omitPagesTo = $totalPages - ($this->maximumVisible - $omitPagesFrom);
        } else {
            $rest = $this->maximumVisible - ($totalPages - $currentPage);
            $omitPagesFrom = (int)\ceil($rest / 2);
            $omitPagesTo = ($currentPage - ($rest - $omitPagesFrom));
        }
        $pagesLeft = \range(1, $omitPagesFrom - 1);
        $pagesRight = \range($omitPagesTo + 1, $totalPages);

        return \array_merge($pagesLeft, [$omittedPagesIndicator], $pagesRight);
    }

    private function getPaginationDataWithTwoOmittedChunks($totalPages, $currentPage, $omittedPagesIndicator): array
    {
        $visibleExceptForCurrent = $this->maximumVisible - 1;
        if ($currentPage <= \ceil($totalPages / 2)) {
            $visibleLeft = \ceil($visibleExceptForCurrent / 2);
            $visibleRight = \floor($visibleExceptForCurrent / 2);
        } else {
            $visibleLeft = \floor($visibleExceptForCurrent / 2);
            $visibleRight = \ceil($visibleExceptForCurrent / 2);
        }
        $omitPagesLeftFrom = \floor($visibleLeft / 2) + 1;
        $omitPagesLeftTo = $currentPage - ($visibleLeft - $omitPagesLeftFrom) - 1;
        $omitPagesRightFrom = \ceil($visibleRight / 2) + $currentPage;
        $omitPagesRightTo = $totalPages - ($visibleRight - ($omitPagesRightFrom - $currentPage));
        $pagesLeft = \range(1, $omitPagesLeftFrom - 1);
        $pagesCenter = \range($omitPagesLeftTo + 1, $omitPagesRightFrom - 1);
        $pagesRight = \range($omitPagesRightTo + 1, $totalPages);

        return \array_merge($pagesLeft, [$omittedPagesIndicator], $pagesCenter, [$omittedPagesIndicator], $pagesRight);
    }
}
