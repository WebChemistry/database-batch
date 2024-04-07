<?php declare(strict_types = 1);

namespace WebChemistry\DatabaseBatch\Layer;

use WebChemistry\DatabaseBatch\Bind;

interface Layer
{

	/**
	 * @param array<string, Bind> $binds
	 */
	public function execute(string $sql, array $binds): void;

}
