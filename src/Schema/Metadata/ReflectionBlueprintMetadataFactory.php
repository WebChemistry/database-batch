<?php declare(strict_types = 1);

namespace WebChemistry\DatabaseBatch\Schema\Metadata;

use ReflectionClass;

final class ReflectionBlueprintMetadataFactory implements BlueprintMetadataFactory
{

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
	 * @return ReflectionBlueprintMetadata<TClass>
	 */
	public function create(string $className): ReflectionBlueprintMetadata
	{
		return new ReflectionBlueprintMetadata(new ReflectionClass($className));
	}

}
