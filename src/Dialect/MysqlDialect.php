<?php declare(strict_types = 1);

namespace WebChemistry\DatabaseBatch\Dialect;

use InvalidArgumentException;
use Override;
use WebChemistry\DatabaseBatch\Message;
use WebChemistry\DatabaseBatch\Packet;
use WebChemistry\DatabaseBatch\Platform\Platform;
use WebChemistry\DatabaseBatch\Query;

final class MysqlDialect implements Dialect
{

	public function __construct(
		private Platform $platform,
	)
	{
	}

	public function getPlatform(): Platform
	{
		return $this->platform;
	}

	#[Override]
	public function insert(Message $message, bool $skipDuplications = false): Query
	{
		return $this->buildInsert($message, false, $skipDuplications);
	}

	#[Override]
	public function insertIgnore(Message $message): Query
	{
		return $this->buildInsert($message, true);
	}

	#[Override]
	public function upsert(Message $message): Query
	{
		$query = $this->buildInsert($message);

		$updateColumns = implode(', ', array_map(
			fn (string $column) => sprintf('%s = VALUES(%s)', $escaped = $this->platform->escapeColumn($column), $escaped),
			$message->getBasicColumns(),
		));

		$sql = sprintf('%s ON DUPLICATE KEY UPDATE %s', $query->sql, $updateColumns);

		return $query->withSql($sql);
	}

	#[Override]
	public function update(Message $message, bool $ignore = false): Query
	{
		$sql = '';
		$binds = [];

		foreach ($message->getValidPackets() as $i => $packet) {
			if ($ignore) {
				$fragment = sprintf('UPDATE IGNORE %s SET', $message->tableName);
			} else {
				$fragment = sprintf('UPDATE %s SET', $message->tableName);
			}

			foreach ($packet->getBasicBinds() as $bind) {
				$fragment .= sprintf(
					' %s = %s,',
					$this->platform->escapeColumn($bind->column->column),
					$bind->getPlaceholder($i),
				);

				$binds[$bind->getPlaceholder($i)] = $bind;
			}

			$fragment = sprintf('%s WHERE', substr($fragment, 0, -1));

			foreach ($packet->getIdBinds() as $bind) {
				$fragment .= sprintf(
					' %s = %s AND',
					$this->platform->escapeColumn($bind->column->column),
					$bind->getPlaceholder($i),
				);

				$binds[$bind->getPlaceholder($i)] = $bind;
			}

			$fragment = substr($fragment, 0, -4) . ";\n";

			$sql .= $fragment;
		}

		$sql = substr($sql, 0, -1);

		if (!$sql) {
			throw new InvalidArgumentException('No valid packets');
		}

		return new Query($sql, $binds, $message);
	}

	private function buildInsert(Message $message, bool $ignore = false, bool $skipDuplications = false): Query
	{
		$sql = sprintf(
			'%s INTO %s (%s) VALUES',
			$ignore ? 'INSERT IGNORE' : 'INSERT',
			$message->tableName,
			implode(', ', array_map($this->platform->escapeColumn(...), $message->getColumns())),
		);

		$binds = [];

		foreach ($message->getValidPackets() as $i => $packet) {
			$sql .= sprintf(' (%s),', implode(', ', $packet->getPlaceholders($i)));

			foreach ($packet->binds as $bind) {
				$binds[$bind->getPlaceholder($i)] = $bind;
			}
		}

		$sql = substr($sql, 0, -1);

		if ($skipDuplications && $id = $message->getSingleIdColumn()) {
			$id = $this->platform->escapeColumn($id);

			$sql = sprintf('%s ON DUPLICATE KEY UPDATE %s = %s', $sql, $id, $id);
		}

		if (!$sql) {
			throw new InvalidArgumentException('No valid packets');
		}

		return new Query($sql, $binds, $message);
	}

}
