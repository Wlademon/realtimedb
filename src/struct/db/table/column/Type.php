<?


namespace wladoseid\realtimedb\struct\db\table\column;


use wladoseid\realtimedb\constructor\Constructor;
use wladoseid\realtimedb\lib\DbType;
use wladoseid\realtimedb\struct\db\table\Column;
use wladoseid\realtimedb\struct\db\Type as CustomType;

class Type extends Constructor {
	
	private $_column;
	
	private $_dbType;
	
	private $_customLink;
	
	private $_phpType;
	
	public function __construct(Column $column, $type) {
		$this->_column = $column;
		if (is_string($type)) {
			$type = mb_strtolower($type);
			if (in_array($type, \wladoseid\realtimedb\lib\Type::allTypes())) {
				$this->_phpType = $type;
				
			} elseif (in_array($type, DbType::allTypes())) {
			
			} else {
				throw new \Exception();
			}
		} elseif ($type instanceof \wladoseid\realtimedb\struct\db\Type) {
		
		}
	}
	
	public function getColumn():Column {
		return $this->_column;
	}
	
	public function getDbType():string {
		return $this->_dbType;
	}
	
	public function getPhpType():string {
		return $this->_phpType;
	}
	
	public function isCustom(): bool {
		return (bool)$this->_customLink;
	}
	
	public function customLink():?CustomType {
		return $this->_customLink;
	}
}