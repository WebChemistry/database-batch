<?php declare(strict_types = 1);

namespace WebChemistry\DatabaseBatch\Type;

use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use Exception;
use Override;
use Throwable;
use WebChemistry\DatabaseBatch\Bind;
use WebChemistry\DatabaseBatch\BindType;
use WebChemistry\DatabaseBatch\Dialect\Dialect;
use WebChemistry\DatabaseBatch\Type\Helper\TypeFormatter;

final class DateFieldType implements FieldType
{

	public function __construct(
		private bool $acceptString = false,
	)
	{
	}

	public function getType(): string
	{
		return TypeFormatter::format([DateTimeInterface::class, 'date-string' => $this->acceptString]);
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
		if ($value instanceof DateTimeInterface) {
			return new Bind($value->format($dialect->getPlatform()->getDateFormat()), BindType::String);
		}

		if ($this->acceptString && is_string($value)) {
			$dateTime = null;

			try {
				$dateTime = new DateTime($value);
			} catch (Throwable) {
				// noop
			}

			if ($dateTime) {
				return new Bind($dateTime->format($dialect->getPlatform()->getDateFormat()), BindType::String);
			}
		}

		return null;
	}

	public static function accepts(string $type): bool
	{
		return is_a($type, DateTimeInterface::class, true);
	}

}
