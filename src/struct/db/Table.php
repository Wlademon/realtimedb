<?


namespace wladoseid\realtimedb\struct\db;


use wladoseid\realtimedb\constructor\Constructor;
use wladoseid\realtimedb\interfaces\TableInterface;
use wladoseid\realtimedb\struct\Db;

class Table extends Constructor implements TableInterface {
	
	public function __construct(Db $db, array $Columns = [], array $indexes = [], array $triggers = []) {
	}
	
	public function readOnly(): bool {
		// TODO: Implement readOnly() method.
	}
	
	public function getDb(): Db {
		// TODO: Implement getDb() method.
	}
	
	public function columns(): array {
		// TODO: Implement columns() method.
	}
	
	public function indexes(): array {
		// TODO: Implement indexes() method.
	}
	
	public function triggers(): array {
		// TODO: Implement triggers() method.
	}
	
	public function primaryColumns(): array {
		// TODO: Implement primaryColumns() method.
	}
	
	public function uniqueColumns(): array {
		// TODO: Implement uniqueColumns() method.
	}
}