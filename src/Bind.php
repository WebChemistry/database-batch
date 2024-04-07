<?php declare(strict_types = 1);

namespace WebChemistry\DatabaseBatch;

use WebChemistry\DatabaseBatch\Schema\Column;

class Bind
{

	public function __construct(
		public readonly string|int|bool|null $value,
		public readonly BindType $type,
	)
	{
	}

	public function withColumn(Column $column): ColumnBind
	{
		return new ColumnBind($column, $this->value, $this->type);
	}

}
