<?php declare(strict_types = 1);

namespace WebChemistry\DatabaseBatch\Bridge\Doctrine\Schema\Metadata;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\Mapping\MappingException;
use WebChemistry\DatabaseBatch\Schema\Metadata\BlueprintMetadataFactory;

final class DoctrineBlueprintMetadataFactory implements BlueprintMetadataFactory
{

	public function __construct(
		private readonly EntityManagerInterface $em,
	)
	{
	}

	/**
	 * @param class-string $className
	 */
	public function supports(string $className): bool
	{
		try {
			$this->em->getClassMetadata($className);

			return true;
		} catch (MappingException) {
			return false;
		}
	}

	/**
	 * @template TClass of object
	 * @param class-string<TClass> $className
	 * @return DoctrineBlueprintMetadata<TClass>
	 */
	public function create(string $className): DoctrineBlueprintMetadata
	{
		/** @var DoctrineBlueprintMetadata<TClass> */
		return new DoctrineBlueprintMetadata($this->em->getClassMetadata($className), $this->em);
	}

}
