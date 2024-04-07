<?php declare(strict_types = 1);

namespace WebChemistry\DatabaseBatch\Type;

use Exception;

final class InvalidTypeException extends Exception
{

	public function __construct(mixed $value, string $expected)
	{
		parent::__construct(sprintf(
			'Invalid value %s, expected "%s"', $this->debugValue($value), $expected,
		));
	}

	private function debugValue(mixed $value): string
	{
		if (is_numeric($value)) {
			if (is_string($value)) {
				return sprintf('"%s"', $value);
			}

			return (string) $value;
		}

		if (is_string($value)) {
			return sprintf('"%s"', substr($value, 0, 100) . (strlen($value) > 100 ? '...' : ''));
		}

		if (is_bool($value)) {
			return $value ? 'true' : 'false';
		}

		return get_debug_type($value);
	}

}
