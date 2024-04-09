<?php declare(strict_types = 1);

namespace WebChemistry\DatabaseBatch\Bridge\Cycle\Schema\Metadata;

use Cycle\Annotated\Annotation\Column;
use Cycle\ORM\ORMInterface;
use ReflectionClass;
use WebChemistry\DatabaseBatch\Schema\Metadata\BlueprintMetadata;
use WebChemistry\DatabaseBatch\Type\DateFieldType;
use WebChemistry\DatabaseBatch\Type\DateTimeFieldType;
use WebChemistry\DatabaseBatch\Type\FieldType;

/**
 * @template TClass of object
 * @implements BlueprintMetadata<TClass>
 */
final class CycleBlueprintMetadata implements BlueprintMetadata
{

	/**
	 * @param ReflectionClass<TClass> $reflection
	 */
	public function __construct(
		private readonly ReflectionClass $reflection,
		private readonly ORMInterface $orm,
	)
	{
	}

	public function getName(): string
	{
		return $this->reflection->name;
	}

	public function getTableName(): string
	{
		return $this->orm->getSource($this->reflection->name)->getTable();
	}

	public function getFieldType(string $field): ?FieldType
	{
		$columns = $this->reflection->getProperty($field)->getAttributes(Column::class);

		foreach ($columns as $column) {
			/** @var Column $instance */
			$instance = $column->newInstance();

			if ($instance->getType() === 'date') {
				return new DateFieldType();
			}

			if ($instance->getType() === 'datetime') {
				return new DateTimeFieldType();
			}
		}

		return null;
	}

	public function getColumnName(string $field): ?string
	{
		$columns = $this->reflection->getProperty($field)->getAttributes(Column::class);

		foreach ($columns as $column) {
			/** @var Column $instance */
			$instance = $column->newInstance();

			if ($columnName = $instance->getColumn()) {
				return $columnName;
			}
		}

		return null;
	}

}
