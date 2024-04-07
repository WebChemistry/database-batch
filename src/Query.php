<?php declare(strict_types = 1);

namespace WebChemistry\DatabaseBatch;

use WebChemistry\DatabaseBatch\Layer\Layer;

final class Query
{

	/** @var array<callable(Query $query): void> */
	private array $hooks = [];

	/**
	 * @param non-empty-string $sql
	 * @param array<string, Bind> $binds
	 */
	public function __construct(
		public readonly string $sql,
		public readonly array $binds,
		public readonly Message $message,
	)
	{
	}

	/**
	 * @param array<callable(Query $query): void> $hooks
	 */
	public function withHooks(array $hooks): self
	{
		$query = clone $this;
		$query->hooks = $hooks;

		return $query;
	}

	/**
	 * @return array<string, string|int|bool|null>
	 */
	public function getBindMap(): array
	{
		$binds = [];

		foreach ($this->binds as $key => $bind) {
			$binds[$key] = $bind->value;
		}

		return $binds;
	}

	/**
	 * @param non-empty-string $sql
	 */
	public function withSql(string $sql): self
	{
		return (new self($sql, $this->binds, $this->message))
			->withHooks($this->hooks);
	}

	public function execute(Layer $layer): void
	{
		$layer->execute($this->sql, $this->binds);

		foreach ($this->hooks as $hook) {
			$hook($this);
		}
	}

}
