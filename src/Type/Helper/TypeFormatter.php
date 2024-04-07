<?php declare(strict_types = 1);

namespace WebChemistry\DatabaseBatch\Type\Helper;

final class TypeFormatter
{

	/**
	 * @param array<array-key, bool|string>|string $types
	 */
	public static function format(array|string $types): string
	{
		if (is_string($types)) {
			return $types;
		}

		$ret = '';

		foreach ($types as $key => $value) {
			if (is_int($key)) {
				$ret .= $value . '|';
			} else if ($value) {
				$ret .= $key . '|';
			}
		}

		return substr($ret, 0, -1);
	}

}
