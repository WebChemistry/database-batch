<?php declare(strict_types = 1);

namespace WebChemistry\DatabaseBatch\Bridge\Doctrine\Schema\Metadata;

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
use WebChemistry\DatabaseBatch\Schema\Metadata\BlueprintMetadata;
use WebChemistry\DatabaseBatch\Type\BoolFieldType;
use WebChemistry\DatabaseBatch\Type\DateFieldType;
use WebChemistry\DatabaseBatch\Type\DateTimeFieldType;
use WebChemistry\DatabaseBatch\Type\FieldType;
use WebChemistry\DatabaseBatch\Type\FloatFieldType;
use WebChemistry\DatabaseBatch\Type\IntFieldType;
use WebChemistry\DatabaseBatch\Type\StringFieldType;

/**
 * @template TClass of object
 * @implements BlueprintMetadata<TClass>
 */
final class DoctrineBlueprintMetadata implements BlueprintMetadata
{

	/**
	 * @param ClassMetadata<TClass> $metadata
	 */
	public function __construct(
		private readonly ClassMetadata $metadata,
		private readonly EntityManagerInterface $em,
	)
	{
	}

	public function getName(): string
	{
		return $this->metadata->getName();
	}

	public function getTableName(): string
	{
		return $this->metadata->getTableName();
	}

	public function getFieldType(string $field): ?FieldType
	{
		$type = PersisterHelper::getTypeOfField($field, $this->metadata, $this->em)[0] ?? null;

		if ($type === null) {
			return null;
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

		return null;
	}

	public function getColumnName(string $field): ?string
	{
		if ($this->metadata->hasField($field)) {
			return $this->metadata->getColumnName($field);
		} else if (!$this->metadata->hasAssociation($field)) {
			throw new InvalidArgumentException(sprintf('Field %s does not exist in %s.', $field, $this->metadata->getName()));
		}

		return $this->metadata->getSingleAssociationJoinColumnName($field);
	}

}
