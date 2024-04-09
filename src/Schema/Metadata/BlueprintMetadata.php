<?php declare(strict_types = 1);

namespace WebChemistry\DatabaseBatch\Schema\Metadata;

use WebChemistry\DatabaseBatch\Type\FieldType;

/**
 * @template TClass of object
 */
interface BlueprintMetadata
{

	public function getName(): ?string;

	public function getTableName(): ?string;

	public function getFieldType(string $field): ?FieldType;

	public function getColumnName(string $field): ?string;

}
