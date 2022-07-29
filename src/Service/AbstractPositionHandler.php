<?php

declare(strict_types=1);

/*
 * This file is part of the Runroom package.
 *
 * (c) Runroom <runroom@runroom.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Runroom\SortableBehaviorBundle\Service;

use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

abstract class AbstractPositionHandler implements PositionHandlerInterface
{
    private PropertyAccessorInterface $propertyAccessor;

    abstract public function getLastPosition(object $entity): int;

    abstract public function getPositionFieldByEntity($entity): string;

    public function setPropertyAccessor(PropertyAccessorInterface $propertyAccessor): self
    {
        $this->propertyAccessor = $propertyAccessor;

        return $this;
    }

    public function getPropertyAccessor(): PropertyAccessorInterface
    {
        return $this->propertyAccessor;
    }

    public function getCurrentPosition(object $entity): int
    {
        return $this->getPropertyAccessor()->getValue($entity, $this->getPositionFieldByEntity($entity));
    }

    public function getPosition(object $entity, string $movePosition, int $lastPosition): int
    {
        $currentPosition = $this->getCurrentPosition($entity);
        $newPosition = 0;

        switch ($movePosition) {
            case 'up' :
                if ($currentPosition > 0) {
                    $newPosition = $currentPosition - 1;
                }
                break;

            case 'down':
                if ($currentPosition < $lastPosition) {
                    $newPosition = $currentPosition + 1;
                }
                break;

            case 'top':
                if ($currentPosition > 0) {
                    $newPosition = 0;
                }
                break;

            case 'bottom':
                if ($currentPosition < $lastPosition) {
                    $newPosition = $lastPosition;
                }
                break;

            default:
                if (is_numeric($movePosition)) {
                    $newPosition = (int) $movePosition;
                }

        }

        return $newPosition;
    }
}
