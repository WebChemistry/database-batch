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

	public readonly string $name;

	public readonly string $tableName;

	/**
	 * @template TClass of object
	 * @param BlueprintMetadata<TClass> $metadata
	 */
	public function __construct(
		private BlueprintMetadata $metadata,
		?string $name = null,
		?string $tableName = null,
	)
	{
		$name ??= $metadata->getName();
		$tableName ??= $metadata->getTableName();

		if (!$name) {
			throw new InvalidArgumentException('Name must be set.');
		}

		if (!$tableName) {
			throw new InvalidArgumentException('Table name must be set.');
		}

		$this->name = $name;
		$this->tableName = $tableName;
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
