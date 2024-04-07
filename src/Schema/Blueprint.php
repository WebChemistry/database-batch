<?php declare(strict_types = 1);

namespace WebChemistry\DatabaseBatch\Schema;

use InvalidArgumentException;
use WebChemistry\DatabaseBatch\Dialect\Dialect;
use WebChemistry\DatabaseBatch\Packet;

final class Blueprint
{

	/**
	 * @param Column[] $ids
	 * @param Column[] $columns
	 */
	public function __construct(
		public readonly string $name,
		public readonly string $tableName,
		private array $ids,
		private array $columns,
	)
	{
		if (!$this->ids) {
			throw new InvalidArgumentException('At least one ID column must be defined.');
		}
	}

	/**
	 * @param array<string, mixed> $values
	 */
	public function createPacket(array $values, Dialect $dialect): Packet
	{
		$binds = [];

		foreach ([$this->ids, $this->columns] as $columns) {
			/** @var Column $column */
			foreach ($columns as $column) {
				$binds[] = $column->type->toBind($column->getValue($values), $dialect)
					->withColumn($column);
			}
		}

		return new Packet($binds);
	}

}
