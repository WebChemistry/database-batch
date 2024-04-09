<?php declare(strict_types = 1);

namespace WebChemistry\DatabaseBatch\Schema;

use WebChemistry\DatabaseBatch\Type\FieldType;

final class BlueprintBuilder
{

	/** @var array<string, PrimaryColumn> */
	private array $ids = [];

	/** @var array<string, Column> */
	private array $fields = [];

	public function __construct(
		public readonly string $name,
		public readonly string $tableName,
	)
	{
	}

	public static function create(string $name, string $tableName): self
	{
		return new self($name, $tableName);
	}

	public function addPrimary(string $field, FieldType $type, ?string $column = null): self
	{
		$this->ids[$field] = new PrimaryColumn($field, $column ?? $field, $type);

		return $this;
	}

	public function addField(string $field, FieldType $type, ?string $column = null): self
	{
		$this->fields[$field] = new BasicColumn($field, $column ?? $field, $type);

		return $this;
	}

	public function build(): Blueprint
	{
		return new Blueprint($this->name, $this->tableName, $this->ids, $this->fields);
	}

}
