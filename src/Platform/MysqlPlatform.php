<?php declare(strict_types = 1);

namespace WebChemistry\DatabaseBatch\Platform;

use Override;

final class MysqlPlatform implements Platform
{

	public function escapeColumn(string $column): string
	{
		return '`' . $column . '`';
	}

	#[Override]
	public function getName(): string
	{
		return 'mysql';
	}

	#[Override]
	public function getDateFormat(): string
	{
		return 'Y-m-d';
	}

	#[Override]
	public function getDateTimeFormat(): string
	{
		return 'Y-m-d H:i:s';
	}

}
