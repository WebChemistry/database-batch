<?php declare(strict_types = 1);

namespace WebChemistry\DatabaseBatch\Bridge\Doctrine\Schema;

use Doctrine\ORM\EntityManagerInterface;

final class DoctrineBlueprintBuilderFactory
{

	public function __construct(
		private EntityManagerInterface $em,
	)
	{
	}

	/**
	 * @template TEntity of object
	 * @param class-string<TEntity> $entity
	 * @return DoctrineBlueprintBuilder<TEntity>
	 */
	public function create(string $entity): DoctrineBlueprintBuilder
	{
		/** @var DoctrineBlueprintBuilder<TEntity> */
		return new DoctrineBlueprintBuilder($this->em->getClassMetadata($entity), $this->em);
	}

}
