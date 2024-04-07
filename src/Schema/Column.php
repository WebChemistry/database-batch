<?php declare(strict_types = 1);

namespace WebChemistry\DatabaseBatch\Schema;

use WebChemistry\DatabaseBatch\Type\FieldType;

abstract class Column
{
	public function __construct(
		public readonly string $field,
		public readonly string $column,
		public readonly FieldType $type,
	)
	{
	}

	/**
	 * @param array<string, mixed> $values
	 */
	abstract public function getValue(array $values): mixed;

}
