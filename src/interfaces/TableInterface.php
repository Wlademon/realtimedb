<?php

namespace wladoseid\realtimedb\interfaces;

use wladoseid\realtimedb\struct\Db;

interface TableInterface {
	
	public function __construct(Db $db, array $Columns = [], array $indexes = [], array $triggers = []);
	
	public function readOnly():bool;
	
	public function getDb():Db;
	
	public function columns():array;
	
	public function indexes():array;
	
	public function triggers():array;
	
	public function primaryColumns():array;
	
	public function uniqueColumns():array;
}