<?php declare(strict_types = 1);

namespace WebChemistry\DatabaseBatch\Layer;

use Override;
use PDO;
use WebChemistry\DatabaseBatch\BindType;

final class PdoLayer implements Layer
{

	public function __construct(
		private PDO $pdo,
	)
	{
	}

	#[Override]
	public function execute(string $sql, array $binds): void
	{
		$stmt = $this->pdo->prepare($sql);

		foreach ($binds as $key => $bind) {
			$pdoType = match ($bind->type) {
				BindType::String => PDO::PARAM_STR,
				BindType::Int => PDO::PARAM_INT,
				BindType::Bool => PDO::PARAM_BOOL,
			};

			$stmt->bindValue($key, $bind->value, $pdoType);
		}

		$stmt->execute();
	}

}
