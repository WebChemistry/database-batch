<?php declare(strict_types = 1);

namespace WebChemistry\DatabaseBatch\Bridge\Cycle\Layer;

use Cycle\Database\DatabaseInterface;
use WebChemistry\DatabaseBatch\Bind;
use WebChemistry\DatabaseBatch\Layer\Layer;

final class CycleDatabaseLayer implements Layer
{

	public function __construct(
		private DatabaseInterface $db,
	)
	{
	}

	/**
	 * @param array<string, Bind> $binds
	 */
	public function execute(string $sql, array $binds): void
	{
		$params = [];

		foreach ($binds as $name => $bind) {
			$params[$name] = $bind->value;
		}

		$this->db->execute($sql, $params);
	}

}
