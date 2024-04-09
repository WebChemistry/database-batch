<?php declare(strict_types = 1);

namespace WebChemistry\DatabaseBatch\Schema\Metadata;

use WebChemistry\DatabaseBatch\Type\FieldType;

/**
 * @template TClass of object
 * @implements BlueprintMetadata<TClass>
 */
final class ChainBlueprintMetadata implements BlueprintMetadata
{

	/**
	 * @param BlueprintMetadata<TClass>[] $metadata
	 */
	public function __construct(
		private readonly array $metadata,
	)
	{
	}

	public function getName(): ?string
	{
		foreach ($this->metadata as $metadata) {
			if ($name = $metadata->getName()) {
				return $name;
			}
		}

		return null;
	}

	public function getTableName(): ?string
	{
		foreach ($this->metadata as $metadata) {
			if ($name = $metadata->getTableName()) {
				return $name;
			}
		}

		return null;
	}

	public function getFieldType(string $field): ?FieldType
	{
		foreach ($this->metadata as $metadata) {
			if ($type = $metadata->getFieldType($field)) {
				return $type;
			}
		}

		return null;
	}

	public function getColumnName(string $field): ?string
	{
		foreach ($this->metadata as $metadata) {
			if ($name = $metadata->getColumnName($field)) {
				return $name;
			}
		}

		return null;
	}

}
