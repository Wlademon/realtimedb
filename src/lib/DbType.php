<?


namespace wladoseid\realtimedb\lib;


abstract class DbType {
	
	/**
	 * Integer Data Types
	 */
	const BIGINT = 'bigint';
	const INT = 'int';
	const SMALLINT = 'smallint';
	
	/**
	 * Decimal Data Types
	 */
	const DECIMAL = 'decimal';
	const NUMERIC = 'numeric';
	
	/**
	 * Floating-Point Data Types
	 */
	const DOUBLE_PRECISION = 'double precision';
	const REAL = 'real';
	const FLOAT = 'float';
	
	/**
	 * Character Data Types
	 */
	const CHAR = 'char';
	const VARCHAR = 'varchar';
	const TEXT = 'text';
	
	/**
	 * Date and Time Data Types
	 */
	const DATE = 'date';
	const TIME = 'time';
	const TIMESTAMP = 'timestamp';
	const INTERVAL = 'interval';
	
	/**
	 * Binary Data Types
	 */
	const BYTEA = 'bytea';
	
	/**
	 * Money Data Types
	 */
	const MONEY ='money';
	
	/**
	 * Boolean Data Types
	 */
	const BOOLEAN = 'boolean';
	
	/**
	 * Custom Data Types
	 */
	const CUSTOM_TYPE = \wladoseid\realtimedb\struct\db\Type::class;
	
	public static function integerTypes():array {
		return [
			self::BIGINT,
			self::INT,
			self::SMALLINT
		];
	}
	
	public static function stringTypes():array {
		return [
			self::CHAR,
			self::VARCHAR,
			self::TEXT
		];
	}
	
	public static function floatTypes():array {
		return [
			self::FLOAT,
			self::DECIMAL,
			self::NUMERIC,
			self::DOUBLE_PRECISION,
			self::REAL
		];
	}
	
	public static function booleanTypes():array {
		return [
			self::BOOLEAN
		];
	}
	
	public static function binaryTypes():array {
		return [
			self::BYTEA
		];
	}
	
	public static function timeTypes():array {
		return [
			self::DATE,
			self::TIME,
			self::TIMESTAMP,
			self::INTERVAL
		];
	}
	
	public static function moneyTypes():array {
		return [
			self::MONEY
		];
	}
	
	public static function tyPhpString():array {
		return array_merge(
			self::stringTypes(),
			self::integerTypes(),
			self::booleanTypes(),
			self::moneyTypes(),
			self::floatTypes(),
			self::binaryTypes(),
			self::timeTypes()
		);
	}
	
	public static function tyPhpInteger():array {
		return self::integerTypes();
	}
	
	public static function tyPhpFloat():array {
		return array_merge(
			self::integerTypes(),
			self::floatTypes(),
			self::booleanTypes()
		);
	}
	
	public static function tyPhpBoolean():array {
		return self::tyPhpString();
	}
	
}