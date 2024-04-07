<?php declare(strict_types = 1);

namespace WebChemistry\DatabaseBatch\Type;

use Exception;

final class InvalidTypeException extends Exception
{

	/**
	 * @param array<array-key, bool|string>|string $expected
	 */
	public function __construct(mixed $value, array|string $expected)
	{
		parent::__construct(sprintf(
			'Invalid value %s, expected "%s"', $this->debugValue($value), $this->formatExpected($expected),
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

	/**
	 * @param array<array-key, bool|string>|string $expected
	 */
	private function formatExpected(array|string $expected): string
	{
		if (is_string($expected)) {
			return $expected;
		}

		$ret = '';

		foreach ($expected as $key => $value) {
			if (is_int($key)) {
				$ret .= $value . '|';
			} else if ($value) {
				$ret .= $key . '|';
			}
		}

		return substr($ret, 0, -1);
	}

}
