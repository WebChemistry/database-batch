<?php declare(strict_types = 1);

namespace WebChemistry\DatabaseBatch\Type;

use Override;
use WebChemistry\DatabaseBatch\Bind;
use WebChemistry\DatabaseBatch\BindType;
use WebChemistry\DatabaseBatch\Dialect\Dialect;

final class IntFieldType implements FieldType
{

	public function __construct(
		private bool $acceptFloat = false,
		private bool $acceptNumeric = false,
	)
	{
	}

	#[Override]
	public function toBind(mixed $value, Dialect $dialect): Bind
	{
		if (is_int($value)) {
			return new Bind($value, BindType::Int);
		}

		if ($this->acceptFloat && is_float($value)) {
			return new Bind((int) $value, BindType::Int);
		}

		if ($this->acceptNumeric && is_numeric($value)) {
			return new Bind((int) $value, BindType::Int);
		}

		throw new InvalidTypeException($value, ['int', 'float' => $this->acceptFloat, 'numeric-string' => $this->acceptNumeric]);
	}

}
