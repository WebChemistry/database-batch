<?php declare(strict_types = 1);

namespace WebChemistry\DatabaseBatch\Schema\Metadata;

use ReflectionClass;
use ReflectionIntersectionType;
use ReflectionNamedType;
use ReflectionType;
use ReflectionUnionType;
use WebChemistry\DatabaseBatch\Type\BoolFieldType;
use WebChemistry\DatabaseBatch\Type\FieldType;
use WebChemistry\DatabaseBatch\Type\FloatFieldType;
use WebChemistry\DatabaseBatch\Type\IntFieldType;
use WebChemistry\DatabaseBatch\Type\NullFieldType;
use WebChemistry\DatabaseBatch\Type\StringFieldType;
use WebChemistry\DatabaseBatch\Type\UnionFieldType;

/**
 * @template TClass of object
 * @implements BlueprintMetadata<TClass>
 */
final class ReflectionBlueprintMetadata implements BlueprintMetadata
{

	/**
	 * @param ReflectionClass<TClass> $reflection
	 */
	public function __construct(
		private readonly ReflectionClass $reflection,
	)
	{
	}

	public function getName(): string
	{
		return $this->reflection->name;
	}

	public function getTableName(): ?string
	{
		return null;
	}

	public function getFieldType(string $field): ?FieldType
	{
		$type = $this->reflection->getProperty($field)->getType();

		if ($type === null) {
			return null;
		}

		return $this->createFieldType($type);
	}

	public function getColumnName(string $field): ?string
	{
		return null;
	}

	private function createFieldType(ReflectionType $type): ?FieldType
	{
		if ($type instanceof ReflectionIntersectionType) {
			return null;
		} else if ($type instanceof ReflectionUnionType) {
			$types = [];

			foreach ($type->getTypes() as $unionType) {
				$returnType = $this->createFieldType($unionType);

				if ($returnType === null) {
					return null;
				}

				$types[] = $returnType;
			}

			return new UnionFieldType($types);
		} else if ($type instanceof ReflectionNamedType) {
			$fieldType = match ($type->getName()) {
				'int' => new IntFieldType(),
				'float' => new FloatFieldType(),
				'string' => new StringFieldType(),
				'bool' => new BoolFieldType(),
				'null' => new NullFieldType(),
				default => null,
			};

			if ($fieldType === null) {
				return null;
			}
		} else {
			return null;
		}

		if ($type->allowsNull() && $type->getName() !== 'null') {
			return new UnionFieldType([
				new NullFieldType(),
				$fieldType,
			]);
		}

		return $fieldType;
	}

}
