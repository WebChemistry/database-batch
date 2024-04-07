<?php declare(strict_types = 1);

namespace WebChemistry\DatabaseBatch\Type;

use Override;
use WebChemistry\DatabaseBatch\Bind;
use WebChemistry\DatabaseBatch\BindType;
use WebChemistry\DatabaseBatch\Dialect\Dialect;

final class NullFieldType implements FieldType
{

	public function getType(): string
	{
		return 'null';
	}

	#[Override]
	public function toBind(mixed $value, Dialect $dialect): Bind
	{
		if ($bind = $this->toNullableBind($value, $dialect)) {
			return $bind;
		}

		throw new InvalidTypeException($value, 'null');
	}

	#[Override]
	public function toNullableBind(mixed $value, Dialect $dialect): ?Bind
	{
		if ($value === null) {
			return new Bind($value, BindType::Null);
		}

		return null;
	}

	public static function accepts(string $type): bool
	{
		return $type === 'null';
	}

}
