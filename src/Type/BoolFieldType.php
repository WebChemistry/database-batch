<?php declare(strict_types = 1);

namespace WebChemistry\DatabaseBatch\Type;

use Override;
use WebChemistry\DatabaseBatch\Bind;
use WebChemistry\DatabaseBatch\BindType;
use WebChemistry\DatabaseBatch\Dialect\Dialect;

final class BoolFieldType implements FieldType
{

	public function getType(): string
	{
		return 'bool';
	}

	#[Override]
	public function toBind(mixed $value, Dialect $dialect): Bind
	{
		if ($bind = $this->toNullableBind($value, $dialect)) {
			return $bind;
		}

		throw new InvalidTypeException($value, $this->getType());
	}

	#[Override]
	public function toNullableBind(mixed $value, Dialect $dialect): ?Bind
	{
		if (is_bool($value)) {
			return new Bind($value, BindType::Bool);
		}

		return null;
	}

	public static function accepts(string $type): bool
	{
		return $type === 'bool';
	}

}
