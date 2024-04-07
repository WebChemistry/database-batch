<?php declare(strict_types = 1);

namespace WebChemistry\DatabaseBatch\Type;

use Override;
use Stringable;
use WebChemistry\DatabaseBatch\Bind;
use WebChemistry\DatabaseBatch\BindType;
use WebChemistry\DatabaseBatch\Dialect\Dialect;
use WebChemistry\DatabaseBatch\Type\Helper\TypeFormatter;

final class StringFieldType implements FieldType
{

	public function __construct(
		private bool $acceptStringable = false,
	)
	{
	}

	public function getType(): string
	{
		return TypeFormatter::format(['string', Stringable::class => $this->acceptStringable]);
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
		if (is_string($value)) {
			return new Bind($value, BindType::String);
		}

		if ($this->acceptStringable && is_object($value) && $value instanceof Stringable) {
			return new Bind((string) $value, BindType::String);
		}

		return null;
	}

	public static function accepts(string $type): bool
	{
		return $type === 'string';
	}

}
