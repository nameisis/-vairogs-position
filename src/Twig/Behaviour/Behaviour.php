<?php

namespace Vairogs\Utils\Position\Twig\Behaviour;

interface Behaviour
{
    public function getPaginationData($totalPages, $currentPage, $omittedPagesIndicator = -1);
}
