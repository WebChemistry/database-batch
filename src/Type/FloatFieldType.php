<?php declare(strict_types = 1);

namespace WebChemistry\DatabaseBatch\Type;

use Override;
use WebChemistry\DatabaseBatch\Bind;
use WebChemistry\DatabaseBatch\BindType;
use WebChemistry\DatabaseBatch\Dialect\Dialect;
use WebChemistry\DatabaseBatch\Type\Helper\TypeFormatter;

final class FloatFieldType implements FieldType
{

	public function __construct(
		private bool $acceptString = false,
	)
	{
	}

	public function getType(): string
	{
		return TypeFormatter::format(['float', 'int', 'float-string' => $this->acceptString]);
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
		if (is_float($value)) {
			return new Bind((string) $value, BindType::String);
		}

		if (is_int($value)) {
			return new Bind($value, BindType::Int);
		}

		if ($this->acceptString && is_numeric($value)) {
			return new Bind((string) $value, BindType::String);
		}

		return null;
	}

	public static function accepts(string $type): bool
	{
		return $type === 'float';
	}

}
