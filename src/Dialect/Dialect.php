<?php declare(strict_types = 1);

namespace WebChemistry\DatabaseBatch\Dialect;

use WebChemistry\DatabaseBatch\Message;
use WebChemistry\DatabaseBatch\Platform\Platform;
use WebChemistry\DatabaseBatch\Query;

interface Dialect
{

	public function getPlatform(): Platform;

	public function insert(Message $message, bool $skipDuplications = false): Query;

	public function insertIgnore(Message $message): Query;

	public function upsert(Message $message): Query;

	public function update(Message $message, bool $ignore = false): Query;

}
