<?php declare(strict_types = 1);

namespace WebChemistry\DatabaseBatch\Type;

use Override;
use WebChemistry\DatabaseBatch\Bind;
use WebChemistry\DatabaseBatch\BindType;
use WebChemistry\DatabaseBatch\Dialect\Dialect;

final class FloatFieldType implements FieldType
{

	public function __construct(
		private bool $acceptString = false,
	)
	{
	}

	#[Override]
	public function toBind(mixed $value, Dialect $dialect): Bind
	{
		if (is_float($value)) {
			return new Bind((string) $value, BindType::String);
		}

		if (is_int($value)) {
			return new Bind($value, BindType::Int);
		}

		if ($this->acceptString && is_numeric($value)) {
			return new Bind((string) $value, BindType::String);
		}

		throw new InvalidTypeException($value, ['float', 'int', 'float-string' => $this->acceptString]);
	}

}
