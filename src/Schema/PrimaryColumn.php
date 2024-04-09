<?php declare(strict_types = 1);

namespace WebChemistry\DatabaseBatch\Schema;

use LogicException;

final class PrimaryColumn extends Column
{

	/**
	 * @param mixed[] $values
	 */
	public function getValue(array $values): mixed
	{
		if (array_key_exists($this->field, $values)) {
			return $values[$this->field];
		}

		throw new LogicException(sprintf('Value for field "%s" is missing.', $this->field));
	}

}
