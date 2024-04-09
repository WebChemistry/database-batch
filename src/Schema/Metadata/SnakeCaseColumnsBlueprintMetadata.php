<?php declare(strict_types = 1);

namespace WebChemistry\DatabaseBatch\Schema\Metadata;

use WebChemistry\DatabaseBatch\Type\FieldType;

/**
 * @template TClass of object
 * @implements BlueprintMetadata<TClass>
 */
final class SnakeCaseColumnsBlueprintMetadata implements BlueprintMetadata
{

	public function getName(): ?string
	{
		return null;
	}

	public function getTableName(): ?string
	{
		return null;
	}

	public function getFieldType(string $field): ?FieldType
	{
		return null;
	}

	public function getColumnName(string $field): ?string
	{
		return $this->convertCase($field);
	}

	private function convertCase(string $field): string
	{
		$output = '';
		$length = strlen($field);
		$i = 0;
		$groupLength = 0;
		$group = 0; // 0 - start, 1 - lowercase, 2 - uppercase

		while ($i < $length) {
			$char = $field[$i];

			// lowercase
			if ($char >= 'a' && $char <= 'z') {
				if ($group === 2) {
					if ($groupLength > 1) {
						$output .= '_';
					}

					$groupLength = 0;
				}

				$output .= $char;
				$group = 1;
			} else {
				if ($group === 1) {
					$output .= '_';
				} else {
					$groupLength++;
				}

				$output .= strtolower($char);
				$group = 2;
			}

			$i++;
		}

		return $output;
	}

}
