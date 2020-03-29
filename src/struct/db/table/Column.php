<?


namespace wladoseid\realtimedb\struct\db\table;


use wladoseid\realtimedb\constructor\Constructor;
use wladoseid\realtimedb\interfaces\ColumnInterface;
use wladoseid\realtimedb\struct\db\Table;

class Column extends Constructor implements ColumnInterface {
	
	private $_name;
	
	private $_comment;
	
	private $_type;
	
	private $_defaultValue;
	
	private $_isNull;
	
	public function __construct(
		Table $table,
		string $name,
		$type,
		bool $isNull = false,
		$defaultValue = null,
		?string $comment = null,
		bool $isPrimary = false,
		bool $isUnique = false
	) {
	
	}
	
	public function readOnly(): bool {
		// TODO: Implement readOnly() method.
	}
	
	public function getTable(): Table {
		// TODO: Implement getTable() method.
	}
	
	public function type(): Type {
		// TODO: Implement type() method.
	}
	
	public function isUnique(): bool {
		// TODO: Implement isUnique() method.
	}
	
	public function isPrimary(): bool {
		// TODO: Implement isPrimary() method.
	}
	
	public function typeName(): string {
		// TODO: Implement typeName() method.
	}
	
	public function typeInPhp(): string {
		// TODO: Implement typeInPhp() method.
	}
	
	public function isConvertType($oldType, $newType): bool {
		// TODO: Implement isConvertType() method.
	}
}