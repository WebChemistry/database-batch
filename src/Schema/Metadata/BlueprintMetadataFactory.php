<?php declare(strict_types = 1);

namespace WebChemistry\DatabaseBatch\Schema\Metadata;

interface BlueprintMetadataFactory
{

	/**
	 * @param class-string $className
	 */
	public function supports(string $className): bool;

	/**
	 * @template TClass of object
	 * @param class-string<TClass> $className
	 * @return BlueprintMetadata<TClass>
	 */
	public function create(string $className): BlueprintMetadata;

}
