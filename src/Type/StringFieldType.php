<?php declare(strict_types = 1);

namespace WebChemistry\DatabaseBatch\Type;

use Override;
use Stringable;
use WebChemistry\DatabaseBatch\Bind;
use WebChemistry\DatabaseBatch\BindType;
use WebChemistry\DatabaseBatch\Dialect\Dialect;

final class StringFieldType implements FieldType
{

	public function __construct(
		private bool $acceptStringable = false,
	)
	{
	}

	#[Override]
	public function toBind(mixed $value, Dialect $dialect): Bind
	{
		if (is_string($value)) {
			return new Bind($value, BindType::String);
		}

		if ($this->acceptStringable && is_object($value) && $value instanceof Stringable) {
			return new Bind((string) $value, BindType::String);
		}

		throw new InvalidTypeException($value, ['string', Stringable::class => $this->acceptStringable]);
	}

}
