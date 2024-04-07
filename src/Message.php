<?php declare(strict_types = 1);

namespace WebChemistry\DatabaseBatch;

use InvalidArgumentException;

final class Message
{

	private Packet $firstPacket;

	/**
	 * @param Packet[] $packets
	 * @param mixed[] $options
	 */
	public function __construct(
		public readonly string $tableName,
		public readonly array $packets,
		public readonly array $options,
	)
	{
		if (!$this->packets) {
			throw new InvalidArgumentException('Packets cannot be empty');
		}

		$this->firstPacket = $this->packets[array_key_first($this->packets)];
	}

	/**
	 * @return Packet[]
	 */
	public function getValidPackets(): iterable
	{
		$requiredColumns = $this->getColumns();

		foreach ($this->packets as $packet) {
			$columnDiff = array_diff($requiredColumns, $names = $packet->getColumnNames());

			if ($columnDiff) {
				throw new InvalidArgumentException(sprintf('Missing columns: %s', implode(', ', $columnDiff)));
			}

			if (count($requiredColumns) !== count($names)) {
				throw new InvalidArgumentException(sprintf('Extra columns: %s', implode(', ', array_diff($names, $requiredColumns))));
			}

			yield $packet;
		}
	}

	/**
	 * @return string[]
	 */
	public function getColumns(): array
	{
		return $this->firstPacket->getColumnNames();
	}

	/**
	 * @return string[]
	 */
	public function getBasicColumns(): array
	{
		return $this->firstPacket->getBasicColumns();
	}

	/**
	 * @return string[]
	 */
	public function getIdColumns(): array
	{
		return $this->firstPacket->getIdColumns();
	}

	public function getSingleIdColumn(): ?string
	{
		$columns = $this->getIdColumns();

		return $columns[array_key_first($columns)] ?? null;
	}

}
