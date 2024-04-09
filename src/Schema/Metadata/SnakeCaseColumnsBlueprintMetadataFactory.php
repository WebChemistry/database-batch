<?php declare(strict_types = 1);

namespace WebChemistry\DatabaseBatch\Schema\Metadata;

final class SnakeCaseColumnsBlueprintMetadataFactory implements BlueprintMetadataFactory
{

	public function supports(string $className): bool
	{
		return true;
	}

	/**
	 * @template TClass of object
	 * @param class-string<TClass> $className
	 * @return SnakeCaseColumnsBlueprintMetadata<TClass>
	 */
	public function create(string $className): SnakeCaseColumnsBlueprintMetadata
	{
		/** @var SnakeCaseColumnsBlueprintMetadata<TClass> */
		return new SnakeCaseColumnsBlueprintMetadata();
	}

}
