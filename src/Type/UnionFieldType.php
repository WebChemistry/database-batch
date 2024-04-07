<?php declare(strict_types = 1);

namespace WebChemistry\DatabaseBatch\Type;

use WebChemistry\DatabaseBatch\Bind;
use WebChemistry\DatabaseBatch\Dialect\Dialect;

final class UnionFieldType implements FieldType
{

	/**
	 * @param FieldType[] $types
	 */
	public function __construct(
		private array $types,
	)
	{
	}

	public function getType(): string
	{
		return implode('|', array_map(fn (FieldType $type) => $type->getType(), $this->types));
	}

	public function toBind(mixed $value, Dialect $dialect): Bind
	{
		foreach ($this->types as $type) {
			if ($bind = $type->toNullableBind($value, $dialect)) {
				return $bind;
			}
		}

		throw new InvalidTypeException($value, $this->getType());
	}

	public function toNullableBind(mixed $value, Dialect $dialect): ?Bind
	{
		foreach ($this->types as $type) {
			if ($bind = $type->toNullableBind($value, $dialect)) {
				return $bind;
			}
		}

		return null;
	}

}
