<?


namespace wladoseid\realtimedb\lib;


abstract class Type {
	
	const INTEGER = 'integer';
	const FLOAT = 'float';
	const STRING = 'string';
	const BOOLEAN = 'boolean';
	const NULL = 'null';
	
	const INT = self::INTEGER;
	const BOOL = self::BOOLEAN;
	
	public static function numberTypes():array {
		return [
			self::INTEGER,
			self::FLOAT
		];
	}
	
	public static function stringTypes():array {
		return [
			self::STRING
		];
	}
	
	public static function booleanTypes():array {
		return [
			self::BOOLEAN
		];
	}
	
	public static function notSetTypes():array {
		return [
			self::NULL
		];
	}
	
	public static function typeMatching():array {
		return [
			self::STRING => DbType::tyPhpString(),
			self::INTEGER => DbType::tyPhpInteger(),
			self::BOOLEAN => DbType::tyPhpBoolean(),
			self::FLOAT => DbType::tyPhpFloat()
		];
	}
	
	public static function allTypes():array {
		return [
			self::STRING,
			self::FLOAT,
			self::BOOLEAN,
			self::INTEGER
		];
	}
}