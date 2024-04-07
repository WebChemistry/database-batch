<?php declare(strict_types = 1);

namespace WebChemistry\DatabaseBatch\Schema;

use LogicException;

final class BasicColumn extends Column
{

	public mixed $default = null;

	public bool $hasDefault = false;

	/**
	 * @param mixed[] $values
	 */
	public function getValue(array $values): mixed
	{
		if (array_key_exists($this->field, $values)) {
			return $values[$this->field];
		}

		if ($this->hasDefault) {
			return $this->default;
		}

		throw new LogicException(sprintf('Value for field "%s" is missing.', $this->field));
	}

	public function setDefault(mixed $default): void
	{
		$this->default = $default;
		$this->hasDefault = true;
	}

}
