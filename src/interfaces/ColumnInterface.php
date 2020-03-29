<?php

namespace wladoseid\realtimedb\interfaces;

use wladoseid\realtimedb\struct\db\Table;
use wladoseid\realtimedb\struct\db\table\Type;

interface ColumnInterface {
	
	public function __construct(
		Table $table,
		string $name,
		$type,
		bool $isNull = false,
		$defaultValue = null,
		?string $comment = null,
		bool $isPrimary = false,
		bool $isUnique = false
	);
	
	public function readOnly():bool;
	
	public function getTable():Table;
	
	public function type():Type;
	
	public function typeName():string;
	
	public function typeInPhp():string;
	
	public function isPrimary():bool;
	
	public function isUnique():bool;
	
	public function isConvertType($oldType, $newType):bool;
}