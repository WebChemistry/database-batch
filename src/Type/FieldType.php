<?php declare(strict_types = 1);

namespace WebChemistry\DatabaseBatch\Type;

use WebChemistry\DatabaseBatch\Bind;
use WebChemistry\DatabaseBatch\Dialect\Dialect;

interface FieldType
{

	public function getType(): string;

	public function toBind(mixed $value, Dialect $dialect): Bind;

	public function toNullableBind(mixed $value, Dialect $dialect): ?Bind;

}
