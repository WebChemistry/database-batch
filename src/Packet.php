<?php declare(strict_types = 1);

namespace WebChemistry\DatabaseBatch;

use WebChemistry\DatabaseBatch\Schema\IdColumn;

final class Packet
{

	/**
	 * @param ColumnBind[] $binds
	 */
	public function __construct(
		public readonly array $binds,
	)
	{
	}

	/**
	 * @return ColumnBind[]
	 */
	public function getIdBinds(): iterable
	{
		foreach ($this->binds as $bind) {
			if ($bind->column instanceof IdColumn) {
				yield $bind;
			}
		}
	}

	/**
	 * @return ColumnBind[]
	 */
	public function getBasicBinds(): iterable
	{
		foreach ($this->binds as $bind) {
			if (!$bind->column instanceof IdColumn) {
				yield $bind;
			}
		}
	}

	/**
	 * @return string[]
	 */
	public function getIdColumns(): array
	{
		$map = [];

		foreach ($this->getIdBinds() as $bind) {
			$map[] = $bind->column->column;
		}

		return $map;
	}

	/**
	 * @return string[]
	 */
	public function getBasicColumns(): array
	{
		$map = [];

		foreach ($this->getBasicBinds() as $bind) {
			$map[] = $bind->column->column;
		}

		return $map;
	}

	/**
	 * @return array<string, string|int|bool|null>
	 */
	public function getFieldValueMap(): array
	{
		$map = [];

		foreach ($this->binds as $bind) {
			$map[$bind->column->field] = $bind->value;
		}

		return $map;
	}

	/**
	 * @return string[]
	 */
	public function getColumnNames(): array
	{
		return array_map(static fn (ColumnBind $bind): string => $bind->column->column, $this->binds);
	}

	/**
	 * @return array<string, string|int|bool|null>
	 */
	public function getColumnValueMap(): array
	{
		$map = [];

		foreach ($this->binds as $bind) {
			$map[$bind->column->column] = $bind->value;
		}

		return $map;
	}

	/**
	 * @return string[]
	 */
	public function getPlaceholders(int $index): array
	{
		return array_map(static fn (ColumnBind $bind) => $bind->getPlaceholder($index), $this->binds);
	}

}
