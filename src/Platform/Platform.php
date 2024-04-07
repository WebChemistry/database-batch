<?php declare(strict_types = 1);

namespace WebChemistry\DatabaseBatch\Platform;

interface Platform
{

	public function getName(): string;

	public function getDateFormat(): string;

	public function getDateTimeFormat(): string;

	public function escapeColumn(string $column): string;

}
