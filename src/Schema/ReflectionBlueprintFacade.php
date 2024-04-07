<?php declare(strict_types = 1);

namespace WebChemistry\DatabaseBatch\Schema;

use InvalidArgumentException;
use ReflectionClass;
use ReflectionIntersectionType;
use ReflectionNamedType;
use ReflectionProperty;
use ReflectionUnionType;
use WebChemistry\DatabaseBatch\Type\BoolFieldType;
use WebChemistry\DatabaseBatch\Type\FieldType;
use WebChemistry\DatabaseBatch\Type\FloatFieldType;
use WebChemistry\DatabaseBatch\Type\IntFieldType;
use WebChemistry\DatabaseBatch\Type\NullFieldType;
use WebChemistry\DatabaseBatch\Type\StringFieldType;
use WebChemistry\DatabaseBatch\Type\UnionFieldType;

final class ReflectionBlueprintFacade
{

	private ReflectionClass $reflection; // @phpstan-ignore-line

	public function __construct(
		private BlueprintBuilder $builder,
	)
	{
		$this->reflection = new ReflectionClass($builder->name); // @phpstan-ignore-line
	}

	public function addId(string $property, ?string $column = null): self
	{
		$this->builder->addId($property, $this->getFieldType($property), $column);

		return $this;
	}

	public function addField(string $property, ?string $column = null): self
	{
		$this->builder->addField($property, $this->getFieldType($property), $column);

		return $this;
	}

	private function getFieldType(string $property): FieldType
	{
		$reflectionProperty = $this->reflection->getProperty($property);
		$reflectionType = $reflectionProperty->getType();

		if (!$reflectionType) {
			throw new InvalidArgumentException('Property ' . $property . ' has no type.');
		}

		if ($reflectionType instanceof ReflectionNamedType || $reflectionType instanceof ReflectionUnionType) {
			return $this->createFieldType($reflectionProperty, $reflectionType);
		}

		throw new InvalidArgumentException('Unsupported type ' . $reflectionType::class . ' for property ' . $property);
	}

	private function createFieldType(ReflectionProperty $property, ReflectionNamedType|ReflectionUnionType $type): FieldType
	{
		if ($type instanceof ReflectionUnionType) {
			$types = [];

			foreach ($type->getTypes() as $unionType) {
				if ($unionType instanceof ReflectionIntersectionType) {
					throw new InvalidArgumentException('Unsupported type ' . $unionType::class . ' for property ' . $property->name);
				}

				$types[] = $this->createFieldType($property, $unionType);
			}

			return new UnionFieldType($types);
		} else {
			$fieldType = match ($type->getName()) {
				'int' => new IntFieldType(),
				'float' => new FloatFieldType(),
				'string' => new StringFieldType(),
				'bool' => new BoolFieldType(),
				'null' => new NullFieldType(),
				default => throw new InvalidArgumentException('Unsupported type ' . $type->getName() . ' for property ' . $property->name),
			};
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
