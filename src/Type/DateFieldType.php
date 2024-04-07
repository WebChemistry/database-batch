<?php declare(strict_types = 1);

namespace WebChemistry\DatabaseBatch\Type;

use DateTime;
use DateTimeInterface;
use Exception;
use Override;
use Throwable;
use WebChemistry\DatabaseBatch\Bind;
use WebChemistry\DatabaseBatch\BindType;
use WebChemistry\DatabaseBatch\Dialect\Dialect;

final class DateFieldType implements FieldType
{

	public function __construct(
		private bool $acceptString = false,
	)
	{
	}

	#[Override]
	public function toBind(mixed $value, Dialect $dialect): Bind
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

		throw new InvalidTypeException($value, [DateTimeInterface::class, 'date-string' => $this->acceptString]);
	}

}
