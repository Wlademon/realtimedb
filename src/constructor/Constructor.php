<?


namespace wladoseid\realtimedb\constructor;

/**
 * Class Constructor
 * @package wladoseid\realtimedb\constructor
 */
abstract class Constructor {
	
	/**
	 * @param $name
	 * @param $value
	 * @throws \Exception
	 */
	public function __set(string $name, $value):void {
		$setter = 'set' . ucfirst($name);
		if (method_exists($this, $setter)) {
			$this->$setter($value);
		} elseif (method_exists($this, 'get' . ucfirst($name))) {
			throw new \Exception('Setting read-only property: ' . get_class($this) . '::' . $name);
		} else {
			throw new \Exception('Setting unknown property: ' . get_class($this) . '::' . $name);
		}
	}
	
	/**
	 * @param $name
	 * @return mixed
	 * @throws \Exception
	 */
	public function __get(string $name) {
		$getter = 'get' . ucfirst($name);
		if (method_exists($this, $getter)) {
			return $this->$getter();
		} elseif (method_exists($this, 'set' . ucfirst($name))) {
			throw new \Exception('Getting write-only property: ' . get_class($this) . '::' . $name);
		}
		
		throw new \Exception('Getting unknown property: ' . get_class($this) . '::' . $name);
	}
	
	/**
	 * @param $name
	 * @return bool
	 */
	public function __isset(string $name):bool {
		$getter = 'get' . ucfirst($name);
		if (method_exists($this, $getter)) {
			return $this->$getter() !== null;
		}
		
		return false;
	}
	
	/**
	 * @param $name
	 * @throws \Exception
	 */
	public function __unset(string $name) {
		$setter = 'set' . ucfirst($name);
		if (method_exists($this, $setter)) {
			$this->$setter(null);
		} elseif (method_exists($this, 'get' . ucfirst($name))) {
			throw new \Exception('Unsetting read-only property: ' . get_class($this) . '::' . $name);
		}
	}
	
	/**
	 * @return string
	 */
	public function __toString():string {
		if (method_exists($this, 'asString')) {
			return $this->asString();
		}
		
		return self::class;
	}
	
}