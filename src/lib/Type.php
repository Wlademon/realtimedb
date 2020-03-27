<?


namespace wladoseid\realtimedb\lib;


abstract class Type {
	const INTEGER = 'integer';
	const FLOAT = 'float';
	const STRING = 'string';
	const BOOLEAN = 'boolean';
	
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
}