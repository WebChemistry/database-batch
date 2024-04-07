<?php declare(strict_types = 1);

namespace WebChemistry\DatabaseBatch;

use WebChemistry\DatabaseBatch\Dialect\Dialect;
use WebChemistry\DatabaseBatch\Schema\Blueprint;

final class Batch
{

	/**
	 * @param array<callable(): void> $hooks
	 */
	public function __construct(
		private Dialect $dialect,
		private array $hooks = [],
	)
	{
	}

	/**
	 * @param array<mixed[]> $data
	 * @param mixed[] $options
	 */
	public function insert(Blueprint $blueprint, array $data, bool $skipDuplications = false, array $options = []): Query
	{
		return $this->dialect->insert($this->createMessage($blueprint, $data, $options), $skipDuplications)
			->withHooks($this->hooks);
	}

	/**
	 * @param array<mixed[]> $data
	 * @param mixed[] $options
	 */
	public function insertIgnore(Blueprint $blueprint, array $data, array $options = []): Query
	{
		return $this->dialect->insertIgnore($this->createMessage($blueprint, $data, $options))
			->withHooks($this->hooks);
	}

	/**
	 * @param array<mixed[]> $data
	 * @param mixed[] $options
	 */
	public function upsert(Blueprint $blueprint, array $data, array $options = []): Query
	{
		return $this->dialect->upsert($this->createMessage($blueprint, $data, $options))
			->withHooks($this->hooks);
	}

	/**
	 * @param array<mixed[]> $data
	 * @param mixed[] $options
	 */
	public function update(Blueprint $blueprint, array $data, array $options = []): Query
	{
		return $this->dialect->update($this->createMessage($blueprint, $data, $options))
			->withHooks($this->hooks);
	}

	/**
	 * @param array<mixed[]> $data
	 * @param mixed[] $options
	 */
	private function createMessage(Blueprint $blueprint, array $data, array $options): Message
	{
		return new Message($blueprint->tableName, array_map(
			fn (array $row) => $blueprint->createPacket($row, $this->dialect),
			$data,
		), $options);
	}

}
