<?php

namespace Vairogs\Utils\Position\Service;

use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Common\Persistence\Proxy;
use Doctrine\ORM\EntityManagerInterface;
use Vairogs\Utils\Position\Component\Positionable;

class PositionHandler
{
    /**
     * @var array
     */
    protected $positionFields = [];
    /**
     * @var EntityManagerInterface[]
     */
    protected $entityManagers = [];

    public function __construct(ManagerRegistry $managerRegistry, $entities)
    {
        foreach ($entities as $class => $entity) {
            $this->entityManagers[$class] = $managerRegistry->getManager($entity['manager']);
            $this->positionFields[$class] = $entity['field'];
        }
    }

    public function getLastPosition(Positionable $entity): int
    {
        $query = $this->entityManagers[\get_class($entity)]->createQuery(\sprintf('SELECT MAX(m.%s) FROM %s m', $this->getPositionFieldByEntity($entity), $entity));
        $result = $query->getResult();
        if (\array_key_exists(0, $result)) {
            return (int)$result[0][1];
        }

        return 0;
    }

    public function getPositionFieldByEntity($entity)
    {
        if (\is_object($entity)) {
            $entity = self::getRealClass(\get_class($entity));
        }

        return $this->positionFields[$entity];
    }

    public static function getRealClass($class)
    {
        if (false === $pos = \strrpos($class, '\\'.Proxy::MARKER.'\\')) {
            return $class;
        }

        return \substr($class, $pos + Proxy::MARKER_LENGTH + 2);
    }

    public function getPosition(Positionable $entity, $position, $lastPosition): int
    {
        $getter = \sprintf('get%s', \ucfirst($this->getPositionFieldByEntity($entity)));

        return (int)$this->{'move'.\ucfirst($position)}($entity->{$getter}(), $lastPosition);
    }

    protected function moveUp($actual): int
    {
        if ($actual > 0) {
            return $actual - 1;
        }

        return $actual;
    }

    protected function moveDown($actual, $last): int
    {
        if ($actual < $last) {
            return $actual + 1;
        }

        return $actual;
    }

    protected function moveTop($actual): int
    {
        if ($actual > 0) {
            return 0;
        }

        return $actual;
    }

    protected function moveBottom($actual, $last)
    {
        if ($actual < $last) {
            return $last;
        }

        return $actual;
    }
}
