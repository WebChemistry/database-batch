<?php declare(strict_types = 1);

namespace WebChemistry\DatabaseBatch;

use WebChemistry\DatabaseBatch\Schema\Column;

final class ColumnBind extends Bind
{

	public function __construct(
		public readonly Column $column,
		string|int|bool|null $value,
		BindType $type,
	)
	{
		parent::__construct($value, $type);
	}

	public function getPlaceholder(int $index): string
	{
		return sprintf(':%s_%d', $this->column->column, $index);
	}

}
