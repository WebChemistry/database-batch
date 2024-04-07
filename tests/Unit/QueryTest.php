<?php declare(strict_types = 1);

use WebChemistry\DatabaseBatch\Batch;
use WebChemistry\DatabaseBatch\Dialect\MysqlDialect;
use WebChemistry\DatabaseBatch\Platform\MysqlPlatform;
use WebChemistry\DatabaseBatch\Schema\BlueprintBuilder;
use WebChemistry\DatabaseBatch\Type\DateFieldType;
use WebChemistry\DatabaseBatch\Type\FloatFieldType;
use WebChemistry\DatabaseBatch\Type\IntFieldType;
use WebChemistry\DatabaseBatch\Type\StringFieldType;

test('insert', function () {
	$blueprint = BlueprintBuilder::create('name', 'table')
		->addId('id', new IntFieldType())
		->addField('name', new StringFieldType())
		->addField('weight', new FloatFieldType())
		->addField('birthday', new DateFieldType(true))
		->build();

	$batch = new Batch(new MysqlDialect(new MysqlPlatform()));

	$query = $batch->insert($blueprint, [
		['id' => 1, 'name' => 'John', 'weight' => 80.5, 'birthday' => new DateTime('1990-01-01')],
		['id' => 2, 'name' => 'Jane', 'weight' => 60.5, 'birthday' => '1995-01-01'],
	]);

    expect($query->sql)->toBe('INSERT INTO table (`id`, `name`, `weight`, `birthday`) VALUES (:id_0, :name_0, :weight_0, :birthday_0), (:id_1, :name_1, :weight_1, :birthday_1)');

	expect($query->getBindMap())->toBe([
		':id_0' => 1,
		':name_0' => 'John',
		':weight_0' => '80.5',
		':birthday_0' => '1990-01-01',
		':id_1' => 2,
		':name_1' => 'Jane',
		':weight_1' => '60.5',
		':birthday_1' => '1995-01-01',
	]);
});

test('insert skip duplications', function () {
	$blueprint = BlueprintBuilder::create('name', 'table')
		->addId('id', new IntFieldType())
		->addField('name', new StringFieldType())
		->addField('weight', new FloatFieldType())
		->addField('birthday', new DateFieldType(true))
		->build();

	$batch = new Batch(new MysqlDialect(new MysqlPlatform()));

	$query = $batch->insert($blueprint, [
		['id' => 1, 'name' => 'John', 'weight' => 80.5, 'birthday' => new DateTime('1990-01-01')],
		['id' => 2, 'name' => 'Jane', 'weight' => 60.5, 'birthday' => '1995-01-01'],
	], skipDuplications: true);

	expect($query->sql)->toBe('INSERT INTO table (`id`, `name`, `weight`, `birthday`) VALUES (:id_0, :name_0, :weight_0, :birthday_0), (:id_1, :name_1, :weight_1, :birthday_1) ON DUPLICATE KEY UPDATE `id` = `id`');

	expect($query->getBindMap())->toBe([
		':id_0' => 1,
		':name_0' => 'John',
		':weight_0' => '80.5',
		':birthday_0' => '1990-01-01',
		':id_1' => 2,
		':name_1' => 'Jane',
		':weight_1' => '60.5',
		':birthday_1' => '1995-01-01',
	]);
});

test('insert ignore', function () {
	$blueprint = BlueprintBuilder::create('name', 'table')
		->addId('id', new IntFieldType())
		->addField('name', new StringFieldType())
		->addField('weight', new FloatFieldType())
		->addField('birthday', new DateFieldType(true))
		->build();

	$batch = new Batch(new MysqlDialect(new MysqlPlatform()));

	$query = $batch->insertIgnore($blueprint, [
		['id' => 1, 'name' => 'John', 'weight' => 80.5, 'birthday' => new DateTime('1990-01-01')],
		['id' => 2, 'name' => 'Jane', 'weight' => 60.5, 'birthday' => '1995-01-01'],
	]);

    expect($query->sql)->toBe('INSERT IGNORE INTO table (`id`, `name`, `weight`, `birthday`) VALUES (:id_0, :name_0, :weight_0, :birthday_0), (:id_1, :name_1, :weight_1, :birthday_1)');

	expect($query->getBindMap())->toBe([
		':id_0' => 1,
		':name_0' => 'John',
		':weight_0' => '80.5',
		':birthday_0' => '1990-01-01',
		':id_1' => 2,
		':name_1' => 'Jane',
		':weight_1' => '60.5',
		':birthday_1' => '1995-01-01',
	]);
});

test('update', function () {
	$blueprint = BlueprintBuilder::create('name', 'table')
		->addId('id', new IntFieldType())
		->addField('name', new StringFieldType())
		->addField('weight', new FloatFieldType())
		->addField('birthday', new DateFieldType(true))
		->build();

	$batch = new Batch(new MysqlDialect(new MysqlPlatform()));

	$query = $batch->update($blueprint, [
		['id' => 1, 'name' => 'John', 'weight' => 80.5, 'birthday' => new DateTime('1990-01-01')],
		['id' => 2, 'name' => 'Jane', 'weight' => 60.5, 'birthday' => '1995-01-01'],
	]);

	expect($query->sql)->toBe('UPDATE table SET `name` = :name_0, `weight` = :weight_0, `birthday` = :birthday_0 WHERE `id` = :id_0;
UPDATE table SET `name` = :name_1, `weight` = :weight_1, `birthday` = :birthday_1 WHERE `id` = :id_1;');

	expect($query->getBindMap())->toBe([
		':name_0' => 'John',
		':weight_0' => '80.5',
		':birthday_0' => '1990-01-01',
		':id_0' => 1,
		':name_1' => 'Jane',
		':weight_1' => '60.5',
		':birthday_1' => '1995-01-01',
		':id_1' => 2,
	]);
});

test('upsert', function () {
	$blueprint = BlueprintBuilder::create('name', 'table')
		->addId('id', new IntFieldType())
		->addField('name', new StringFieldType())
		->addField('weight', new FloatFieldType())
		->addField('birthday', new DateFieldType(true))
		->build();

	$batch = new Batch(new MysqlDialect(new MysqlPlatform()));

	$query = $batch->upsert($blueprint, [
		['id' => 1, 'name' => 'John', 'weight' => 80.5, 'birthday' => new DateTime('1990-01-01')],
		['id' => 2, 'name' => 'Jane', 'weight' => 60.5, 'birthday' => '1995-01-01'],
	]);

	expect($query->sql)->toBe('INSERT INTO table (`id`, `name`, `weight`, `birthday`) VALUES (:id_0, :name_0, :weight_0, :birthday_0), (:id_1, :name_1, :weight_1, :birthday_1) ON DUPLICATE KEY UPDATE `name` = VALUES(`name`), `weight` = VALUES(`weight`), `birthday` = VALUES(`birthday`)');

	expect($query->getBindMap())->toBe([
		':id_0' => 1,
		':name_0' => 'John',
		':weight_0' => '80.5',
		':birthday_0' => '1990-01-01',
		':id_1' => 2,
		':name_1' => 'Jane',
		':weight_1' => '60.5',
		':birthday_1' => '1995-01-01',
	]);
});
