<?php declare(strict_types = 1);

namespace WebChemistry\DatabaseBatch\Bridge\Doctrine\Schema;

use Doctrine\DBAL\Types\BigIntType;
use Doctrine\DBAL\Types\BooleanType;
use Doctrine\DBAL\Types\DateImmutableType;
use Doctrine\DBAL\Types\DateTimeImmutableType;
use Doctrine\DBAL\Types\DateTimeType;
use Doctrine\DBAL\Types\DateType;
use Doctrine\DBAL\Types\DecimalType;
use Doctrine\DBAL\Types\FloatType;
use Doctrine\DBAL\Types\IntegerType;
use Doctrine\DBAL\Types\SmallIntType;
use Doctrine\DBAL\Types\StringType;
use Doctrine\DBAL\Types\TextType;
use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Utility\PersisterHelper;
use InvalidArgumentException;
use LogicException;
use WebChemistry\DatabaseBatch\Schema\BasicColumn;
use WebChemistry\DatabaseBatch\Schema\Blueprint;
use WebChemistry\DatabaseBatch\Schema\Column;
use WebChemistry\DatabaseBatch\Schema\IdColumn;
use WebChemistry\DatabaseBatch\Type\BoolFieldType;
use WebChemistry\DatabaseBatch\Type\DateFieldType;
use WebChemistry\DatabaseBatch\Type\DateTimeFieldType;
use WebChemistry\DatabaseBatch\Type\FieldType;
use WebChemistry\DatabaseBatch\Type\FloatFieldType;
use WebChemistry\DatabaseBatch\Type\IntFieldType;
use WebChemistry\DatabaseBatch\Type\StringFieldType;

/**
 * @template TEntity of object
 */
final class DoctrineBlueprintBuilder
{

	/** @var array<string, Column> */
	private array $fields = [];

	/** @var array<string, IdColumn> */
	private array $ids = [];

	/**
	 * @param ClassMetadata<TEntity> $metadata
	 */
	public function __construct(
		private ClassMetadata $metadata,
		private EntityManagerInterface $em,
	)
	{
		foreach ($this->metadata->getIdentifierFieldNames() as $fieldName) {
			$this->ids[$fieldName] = new IdColumn(
				$fieldName,
				$this->getColumnName($fieldName),
				$this->getFieldType($fieldName),
			);
 		}
	}

	/**
	 * @return static<TEntity>
	 */
	public function addField(string $field, ?FieldType $type = null): static
	{
		$this->fields[$field] = new BasicColumn($field, $this->getColumnName($field), $type ?? $this->getFieldType($field));

		return $this;
	}

	public function build(): Blueprint
	{
		return new Blueprint($this->metadata->getName(), $this->metadata->getTableName(), $this->ids, $this->fields);
	}

	private function getFieldType(string $field): FieldType
	{
		$type = PersisterHelper::getTypeOfField($field, $this->metadata, $this->em)[0] ?? null;

		if ($type === null) {
			throw new LogicException(sprintf('Type of field %s in %s is not defined.', $field, $this->metadata->getName()));
		}

		$doctrineType = Type::getType($type);

		if ($doctrineType instanceof StringType || $doctrineType instanceof TextType) {
			return new StringFieldType();
		}

		if ($doctrineType instanceof BooleanType) {
			return new BoolFieldType();
		}

		if ($doctrineType instanceof BigIntType || $doctrineType instanceof SmallIntType || $doctrineType instanceof IntegerType) {
			return new IntFieldType();
		}

		if ($doctrineType instanceof FloatType || $doctrineType instanceof DecimalType) {
			return new FloatFieldType();
		}

		if ($doctrineType instanceof DateImmutableType || $doctrineType instanceof DateType) {
			return new DateFieldType();
		}

		if ($doctrineType instanceof DateTimeImmutableType || $doctrineType instanceof DateTimeType) {
			return new DateTimeFieldType();
		}

		throw new LogicException(sprintf('Type %s is not supported.', $doctrineType::class));
	}

	private function getColumnName(string $field): string
	{
		if ($this->metadata->hasField($field)) {
			return $this->metadata->getColumnName($field);
		} else if (!$this->metadata->hasAssociation($field)) {
			throw new InvalidArgumentException(sprintf('Field %s does not exist in %s.', $field, $this->metadata->getName()));
		}

		return $this->metadata->getSingleAssociationJoinColumnName($field);
	}

}
