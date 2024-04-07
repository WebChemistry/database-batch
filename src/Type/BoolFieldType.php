<?php declare(strict_types = 1);

namespace WebChemistry\DatabaseBatch\Type;

use Override;
use WebChemistry\DatabaseBatch\Bind;
use WebChemistry\DatabaseBatch\BindType;
use WebChemistry\DatabaseBatch\Dialect\Dialect;

final class BoolFieldType implements FieldType
{

	#[Override]
	public function toBind(mixed $value, Dialect $dialect): Bind
	{
		if (is_bool($value)) {
			return new Bind($value, BindType::Bool);
		}

		throw new InvalidTypeException($value, 'bool');
	}

}
