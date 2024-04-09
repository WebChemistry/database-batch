<?php declare(strict_types = 1);

namespace WebChemistry\DatabaseBatch\Bridge\Cycle\Schema\Metadata;

use Cycle\ORM\ORMInterface;
use ReflectionClass;
use WebChemistry\DatabaseBatch\Schema\Metadata\BlueprintMetadataFactory;

final class CycleBlueprintMetadataFactory implements BlueprintMetadataFactory
{

	public function __construct(
		private readonly ORMInterface $orm,
	)
	{
	}

	/**
	 * @param class-string $className
	 */
	public function supports(string $className): bool
	{
		return true;
	}

	/**
	 * @template TClass of object
	 * @param class-string<TClass> $className
	 * @return CycleBlueprintMetadata<TClass>
	 */
	public function create(string $className): CycleBlueprintMetadata
	{
		return new CycleBlueprintMetadata(new ReflectionClass($className), $this->orm);
	}

}
