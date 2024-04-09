<?php declare(strict_types = 1);

namespace WebChemistry\DatabaseBatch\Schema;

use InvalidArgumentException;
use WebChemistry\DatabaseBatch\Schema\Metadata\BlueprintMetadata;
use WebChemistry\DatabaseBatch\Type\FieldType;

final class PartialBlueprintBuilder
{

	/** @var array<string, PrimaryColumn> */
	private array $ids = [];

	/** @var array<string, Column> */
	private array $fields = [];

	/**
	 * @template TClass of object
	 * @param class-string<TClass> $name
	 * @param BlueprintMetadata<TClass> $metadata
	 */
	public function __construct(
		public readonly string $name,
		public readonly string $tableName,
		private BlueprintMetadata $metadata,
	)
	{
	}

	public function addPrimary(string $field, ?FieldType $type = null, ?string $column = null): self
	{
		$type ??= $this->metadata->getFieldType($field);
		$column ??= $this->metadata->getColumnName($field);

		if ($type === null) {
			throw new InvalidArgumentException(sprintf('Type for field %s not found, must be set.', $field));
		}

		$this->ids[$field] = new PrimaryColumn($field, $column ?? $field, $type);

		return $this;
	}

	public function addField(string $field, ?FieldType $type = null, ?string $column = null): self
	{
		$type ??= $this->metadata->getFieldType($field);
		$column ??= $this->metadata->getColumnName($field);

		if ($type === null) {
			throw new InvalidArgumentException(sprintf('Type for field %s not found, must be set.', $field));
		}

		$this->fields[$field] = new BasicColumn($field, $column ?? $field, $type);

		return $this;
	}

	public function build(): Blueprint
	{
		return new Blueprint($this->name, $this->tableName, $this->ids, $this->fields);
	}

}
