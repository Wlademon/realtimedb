<?


namespace wladoseid\realtimedb\struct\db\table;


class Column {
	
	private $_name;
	
	private $_comment;
	
	private $_type;
	
	private $_defaultValue;
	
	private $_isNull;
	
	/**
	 * @return mixed
	 */
	public function getName():string {
		return $this->_name;
	}
	
	/**
	 * @return mixed
	 */
	public function getComment():string {
		return $this->_comment;
	}
	
	/**
	 * @return mixed
	 */
	public function getDefaultValue() {
		return $this->_defaultValue;
	}
	
	/**
	 * @return mixed
	 */
	public function getIsNull():bool {
		return $this->_isNull;
	}
	
	/**
	 * @return mixed
	 */
	public function getType() {
		return $this->_type;
	}
}