<?php declare(strict_types = 1);

namespace WebChemistry\DatabaseBatch\Schema\Metadata;

final class ChainBlueprintMetadataFactory implements BlueprintMetadataFactory
{

	/**
	 * @param BlueprintMetadataFactory[] $factories
	 */
	public function __construct(
		private array $factories,
	)
	{
	}

	public function supports(string $className): bool
	{
		foreach ($this->factories as $factory) {
			if ($factory->supports($className)) {
				return true;
			}
		}

		return false;
	}

	/**
	 * @template TClass of object
	 * @param class-string<TClass> $className
	 * @return ChainBlueprintMetadata<TClass>
	 */
	public function create(string $className): ChainBlueprintMetadata
	{
		$metadata = [];

		foreach ($this->factories as $factory) {
			if ($factory->supports($className)) {
				$metadata[] = $factory->create($className);
			}
		}

		return new ChainBlueprintMetadata($metadata);
	}

}
