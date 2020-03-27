<?

namespace wladoseid\realtimedb;



/**
 * Class ReportGenerate
 * @package app\modules\report\models
 */
class GenerateDb {
	
	const TYPE_INT = 'int';
	const TYPE_STRING = 'string';
	const TYPE_FLOAT = 'float';
	
	
	/** @var int */
	public $mainTableId;
	
	/** @var string  */
	public $mainTableName;
	
	/** @var array|string[]  */
	public $mainTablePrimary = [];
	
	/** @var array  */
	public $newPrimary = [];
	
	/** @var array  */
	public $mainColumns = [];
	
	/** @var bool  */
	public $isLoad = false;
	
	/** @var array  */
	private $addColumns = [];
	
	/** @var array  */
	private $altColumns = [];
	
	/** @var array  */
	private $dropColumns = [];
	
	/** @var array  */
	private $_loadStruc = [];
	
	/** @var array  */
	private $columnSize = [
		'int' => '32',
		'string' => '255',
		'float' => '5',
		'bool' => false
	];
	
	/** @var bool  */
	private $inLineForDel = false;
	
	/**
	 * ReportGenerate constructor.
	 * @param $tableId
	 * @throws Exception
	 */
	public function __construct($tableId) {
		if (!$tableId) {
			throw new Exception('Not set table id');
		}
		$this->mainTableId = $tableId;
		$this->mainTableName = 'report_autotable_' . $tableId;
		
		if ((($autoTableSchema = \Yii::$app->db->getTableSchema('{{%' . $this->mainTableName . '}}')) !== null)) {
			$this->isLoad = true;
			$this->mainTablePrimary = $autoTableSchema->primaryKey;
			foreach ($autoTableSchema->columns as $name => $column) {
				$this->mainColumns[$name] = [
					'type' => strpos($column->phpType, 'int') === false ? $column->phpType : 'int',
					'size' => $column->size ?? $column->precision,
					'isNull' => $column->allowNull,
					'defaultValue' => $column->defaultValue,
					'comment' => $column->comment,
					'isPrimary' => $column->isPrimaryKey
				];
			}
		} else {
			$this->isLoad = false;
		}
	}
	
	public function allColumns() {
		if (isset($this->_loadStruc['all'])) {
			return $this->_loadStruc['all'];
		}
		
		return $this->_loadStruc['all'] = ReportColumn::find()->andWhere(['report_id' => $this->mainTableId])->notDel()->select('data_key')->column();
	}
	
	/**
	 * Получение колонок - дат
	 * @return array|mixed
	 */
	public function dtColumn() {
		if (isset($this->_loadStruc['dt'])) {
			return $this->_loadStruc['dt'];
		}
		return $this->_loadStruc['dt'] = ReportColumn::find()->andWhere(['report_id' => $this->mainTableId, 'is_dt' => true])->notDel()->select('data_key')->column();
	}
	
	/**
	 * Получение колонок значение которых суммируется при группировке
	 * @return array|mixed
	 */
	public function sumColumn() {
		if (isset($this->_loadStruc['sum'])) {
			return $this->_loadStruc['sum'];
		}
		return $this->_loadStruc['sum'] = ReportColumn::find()->andWhere(['report_id' => $this->mainTableId, 'is_sum' => true])->notDel()->select('data_key')->column();
	}
	
	/**
	 * Получение уникализирующих колонок
	 * @return array|mixed
	 */
	public function uniqueColumn() {
		if (isset($this->_loadStruc['unique'])) {
			return $this->_loadStruc['unique'];
		}
		return $this->_loadStruc['unique'] = ReportColumn::find()->andWhere(['report_id' => $this->mainTableId, 'is_unique' => true])->notDel()->select('data_key')->column();
	}
	
	/**
	 * Получение колонок у которыхесть внешние источники данных
	 * @return array|mixed|\yii\db\ActiveRecord[]
	 */
	public function outListColumn() {
		if (isset($this->_loadStruc['out_list'])) {
			return $this->_loadStruc['out_list'];
		}
		return $this->_loadStruc['out_list'] = ReportColumn::find()
			->andWhere(['report_id' => $this->mainTableId, 'is_sum' => false])
			->andWhere(['is not', 'url_source', null])
			->andWhere(['!=', 'url_source', ''])
			->notDel()
			->select('data_key, url_source, label')
			->asArray()
			->all();
	}
	
	/**
	 * Получение колонок к которым пременим поиск с помощью прямого сравнения
	 * @return array|mixed
	 */
	public function wholeColumn() {
		if (isset($this->_loadStruc['whole'])) {
			return $this->_loadStruc['whole'];
		}
		return $this->_loadStruc['whole'] = ReportColumn::find()
			->andWhere(['report_id' => $this->mainTableId])
			->andWhere([
				'or',
				['type' => self::TYPE_INT],
				[
					'and',
					['is not', 'url_source', null],
					['!=', 'url_source', '']
				]
				])
			->andWhere(['is_dt' => false])
			->notDel()
			->select('data_key')->column();
	}
	
	/**
	 * Получение колонок к которым пременим поиск с помощью оператора like
	 * @return array|mixed
	 */
	public function noWholeColumn() {
		if (isset($this->_loadStruc['no_whole'])) {
			return $this->_loadStruc['no_whole'];
		}
		
		return $this->_loadStruc['no_whole'] = ReportColumn::find()
			->andWhere(['report_id' => $this->mainTableId])
			->andWhere([
				'and',
				['type' => self::TYPE_STRING],
				[
					'or',
					['is', 'url_source', null],
					['url_source' => '']
				],
				['is_dt' => false]])
			->notDel()
			->select('data_key')->column();
	}
	
	/**
	 * Получение колонки оси X см. chartData
	 * @return array|mixed
	 */
	public function axisXColumn() {
		if (isset($this->_loadStruc['axis_x'])) {
			return $this->_loadStruc['axis_x'];
		}
		
		return $this->_loadStruc['axis_x'] = ReportColumn::find()
			->andWhere(['report_id' => $this->mainTableId])
			->notDel()->andWhere(['is_axis_x' => true])->select('data_key')->limit(1)->column();
	}
	
	/**
	 * Получение колонки оси Y см. chartData
	 * @return array|mixed
	 */
	public function axisYColumn() {
		if (isset($this->_loadStruc['axis_y'])) {
			return $this->_loadStruc['axis_y'];
		}
		
		return $this->_loadStruc['axis_y'] = ReportColumn::find()
			->andWhere(['report_id' => $this->mainTableId])
			->notDel()->andWhere(['is_axis_y' => true])->select('data_key')->limit(1)->column();
	}
	
	/**
	 * Получение группировочного поля см. chartData
	 * @return array|mixed
	 */
	public function groupColumn() {
		if (isset($this->_loadStruc['group'])) {
			return $this->_loadStruc['group'];
		}
		
		return $this->_loadStruc['group'] = ReportColumn::find()
				->andWhere(['report_id' => $this->mainTableId])
				->notDel()->andWhere(['is_group' => true])->select('data_key')->limit(1)->column();
	}
	
	/**
	 * Данные для графика
	 * @return array|bool
	 */
	public function chartData() {
		$out = [];
		$axisX = $this->axisXColumn();
		if (!count($axisX)) {
			return false;
		}
		$axisY = $this->axisYColumn();
		if (!count($axisY)) {
			return false;
		}
		$group = $this->groupColumn();
		$axisY = implode('', $axisY);
		$axisX = implode('', $axisX);
		if (strpos($axisY, 'dt') !== false || strpos($axisX, 'dt') !== false) {
			$out['xPeriod'] = 'day';
			if (strpos($axisY, 'dt') !== false) {
				$out['x'] = $axisY;
				$out['y'] = $axisX;
			} else {
				$out['x'] = $axisX;
				$out['y'] = $axisY;
			}
		} else {
			$out['x'] = $axisX;
			$out['y'] = $axisY;
		}
		if (count($group)) {
			$group = implode('', $group);
			$out['group'] = $group;
		}
		
		return $out;
	}
	
	/**
	 * Удалить таблицу
	 * @throws \yii\db\Exception
	 */
	public function dropTable() {
		\Yii::$app->db->createCommand("DROP TABLE IF EXISTS {$this->mainTableName};")->execute();
		$this->mainTableId = null;
		$this->mainTableName = null;
		\Yii::$app->db->close();
		\Yii::$app->cache->flush();
		\Yii::$app->db->open();
	}
	
	/**
	 * Rename column
	 * @param string $oldName
	 * @param string $newName
	 * @return bool
	 */
	public function renameColumn(string $oldName, string $newName): bool {
		try {
			\Yii::$app->db->createCommand(<<<SQL
 ALTER TABLE  {$this->mainTableName}
    RENAME COLUMN {$oldName} TO {$newName};
SQL)->execute();
		} catch (\Throwable $throwable) {
			return false;
		}
		\Yii::$app->db->close();
		\Yii::$app->cache->flush();
		\Yii::$app->db->open();
		try {
			$this->__construct($this->mainTableId);
		} catch (\Throwable $throwable) {
			return false;
		}
		
		return true;
	}
	
	/**
	 * Add column in table
	 *
	 * @param $name
	 * @param $type
	 * @param string $comment
	 * @param bool $isNull
	 * @param null $defaultValue
	 * @return $this
	 */
	public function setColumn(string $name, $type, string $comment = '',bool $isNull = true, $defaultValue = null, $isPrimary = false): ReportGenerate {
		if ($name && $type) {
			if (is_array($type)) {
				$key = array_keys($type)[0];
				$size = $type[$key];
				$type = $key;
			} else {
				$size = $this->columnSize[$type] ?? false;
			}
			if ($this->isLoad) {
				if (isset($this->mainColumns[$name])) {
					if ($type !== $this->mainColumns[$name]['type']) {
						$this->altColumn($name,'type', $type);
					}
					if ($comment !== $this->mainColumns[$name]['comment']) {
						$this->altColumn($name,'comment', $comment);
					}
					if ($isNull !== $this->mainColumns[$name]['isNull']) {
						$this->altColumn($name,'isNull', $isNull);
					}
					if ($defaultValue !== $this->mainColumns[$name]['defaultValue']) {
						$this->altColumn($name,'defaultValue', $defaultValue);
					}
					if ($isPrimary !== $this->mainColumns[$name]['isPrimary']) {
						$this->altColumn($name,'isPrimary', $isPrimary);
					}
				} else {
					$this->mainColumns[$name] = [
						'type' => $type,
						'size' => $size,
						'isNull' => $isNull,
						'defaultValue' => $defaultValue,
						'comment' => $comment,
						'isPrimary' => $isPrimary
					];
					$this->addColumns[] = $name;
				}
			} else {
				$this->mainColumns[$name] = [
					'type' => $type,
					'size' => $size,
					'isNull' => $isNull,
					'defaultValue' => $defaultValue,
					'comment' => $comment,
					'isPrimary' => $isPrimary
				];
				if ($isPrimary) {
					$this->newPrimary[] = $name;
				}
				$this->addColumns[] = $name;
			}
		}
		
		return $this;
	}
	
	/**
	 * Alter column in table
	 * @param string $property
	 * @param $meaning
	 * @return ReportGenerate
	 */
	public function altColumn(string $columnName, string $property, $meaning): ReportGenerate {
		
		if ($property) {
			$this->altColumns[$columnName][$property] = $meaning;
		}
		
		return $this;
	}
	
	/**
	 * Drop column out table
	 * @param $name
	 * @return $this
	 */
	public function unsetColumn(string $name): ReportGenerate {
		if ($name) {
			$this->dropColumns[] = $name;
			$this->dropColumns = array_unique($this->dropColumns);
		}
		
		return $this;
	}
	
	/**
	 * Drop column out table
	 * @param $name
	 * @return $this
	 */
	public function unsetColumns(array $names): ReportGenerate {
		if (count($names)) {
			$this->dropColumns = array_unique(array_merge($this->dropColumns, $names));
		}
		
		return $this;
	}
	
	/**
	 * Создание/удаление/изменение структуры таблиц
	 * @param int $priorityAdd // false - is off
	 * @param int $priorityAlt // false - is off
	 * @param int $priorityDrop // false - is off
	 * @return bool
	 */
	public function generate($priorityAdd = 0, $priorityAlt = 1, $priorityDrop = 2) {
		$sequence = ['add' => $priorityAdd, 'alt' => $priorityAlt, 'drop' => $priorityDrop];
		$this->_loadStruc = [];
		asort($sequence);
		$this->unsetPrimaryKey(!$this->isLoad);
		if (!$this->isLoad) {
			$status = $this->add();
			unset($sequence['add']);
			if ($status instanceof \Throwable) {
				return $status;
			}
		}
		foreach ($sequence as $index => $item) {
			if ($item !== false) {
				$status = $this->{$index}();
				if ($status instanceof \Throwable) {
					return $status;
				}
			}
		}
		$status = $this->setPrimaryKey();
		if ($status instanceof \Throwable) {
			throw $status;
		}
		
		\Yii::$app->cache->flush();
		$this->isLoad = true;
		
		return true;
	}
	
	
	private function loadNewStruc() {
		$newStruc = ReportColumn::find()->notDel()->andWhere(['report_id' => $this->mainTableId])->all();
		$newColumns = ArrayHelper::getColumn($newStruc, 'data_key');
		$oldColumns = array_keys($this->mainColumns);
		$unsetColumns = array_diff($oldColumns, $newColumns);
		
		if ($unsetColumns) {
			$this->unsetColumns($unsetColumns)->generate(false, false);
		}
		
		foreach ($newStruc as $key => $record) {
			$this->setColumn(
				$record->data_key,
				$record->type,
				$record->label,
				!$record->is_unique,
				null,
				$record->is_unique
			);
		}
		
		$status = $this->generate(0, 1, false);
		
		return !($status instanceof \Throwable);
	}
	
	/**
	 * Unset PrimaryKey
	 * @param bool $isNew
	 * @return bool
	 */
	private function unsetPrimaryKey(bool $isNew = false): bool {
		try {
			if (!$isNew) {
				\Yii::$app->db->createCommand()->dropPrimaryKey("primary_{$this->mainTableId}", $this->mainTableName)->execute();
			}
		} catch (\Throwable $throwable) {
			return false;
		}
		
		return true;
	}
	
	/**
	 * @param bool $isNew
	 */
	private function setPrimaryKey() {
		try {
			if (count($this->newPrimary) || count($this->mainTablePrimary)) {
				$this->_loadStruc['unique'] = null;
				$newPK = implode(',', $this->uniqueColumn());
				\Yii::$app->db->createCommand()->addPrimaryKey("primary_{$this->mainTableId}", $this->mainTableName, $newPK)->execute();
			}
		} catch (\Throwable $throwable) {
			return new \Exception(\Yii::t('app', 'Error add primary key ' . $throwable->getMessage() ));
		}
		
		return true;
	}
	
	/**
	 *
	 * @return bool|\Exception
	 * @throws \yii\db\Exception
	 */
	private function add() {
		try {
			$columns = [];
			foreach ($this->addColumns as $key => $addColumn) {
				$column = $this->mainColumns[$addColumn];
				$query = $this->queryCreat($addColumn, $column);
				$columns[$addColumn] = $query;
			}
			
			if ($this->isLoad && count($columns)) {
				foreach ($columns as $name => $columnQuery) {
					$this->addColumn($name, $columnQuery);
				}
			} elseif (count($columns)) {
				$this->createTable($columns);
			}
		} catch (\Throwable $throwable) {
			if (!$this->isLoad && \Yii::$app->db->getTableSchema('{{%' . $this->mainTableName . '}}')) {
				\Yii::$app->db->createCommand()->dropTable($this->mainTableName)->execute();
			}
			return new \Exception(\Yii::t('app', 'Error add column ' . $throwable->getMessage()));
		}
		
		return true;
	}
	
	/**
	 * Создать таблицу
	 * @param $columns
	 * @throws \yii\db\Exception
	 */
	private function createTable($columns) {
		\Yii::$app->db->createCommand()->createTable($this->mainTableName, $columns)->execute();
	}
	
	/**
	 * Добавить колонку
	 * @param $column
	 * @param $type
	 * @throws \yii\db\Exception
	 */
	private function addColumn($column, $type) {
		\Yii::$app->db->createCommand()->addColumn($this->mainTableName, $column, $type)->execute();
	}
	
	/**
	 * @return bool|\Exception
	 */
	private function drop() {
		try {
			foreach ($this->dropColumns as $dropColumn) {
				unset($this->mainColumns[$dropColumn]);
				if (isset($this->mainTablePrimary[$dropColumn])) {
					unset($this->mainTablePrimary[$dropColumn]);
				}
				\Yii::$app->db->createCommand()->dropColumn($this->mainTableName, $dropColumn)->execute();
			}
		} catch (\Throwable $throwable) {
			return new \Exception(\Yii::t('app', 'Error drop column'));
		}
		
		return true;
	}
	
	/**
	 * Отчистка таблицы
	 * @throws \yii\db\Exception
	 */
	public function clear() {
		if ($this->isLoad) {
			\Yii::$app->db->createCommand()->delete($this->mainTableName)->execute();
		}
		$this->inLineForDel = false;
	}
	
	/**
	 * @return bool|\Exception
	 */
	private function alt() {
		foreach ($this->altColumns as $key => $altColumn) {
			$query = $this->queryAlt($key, $altColumn);
			try {
				// drop default
				\Yii::$app->db->createCommand(<<<SQL
ALTER TABLE {$this->mainTableName}
    ALTER COLUMN {$key} DROP DEFAULT;
SQL)->execute();
			} catch (\Throwable $throwable) {}
			try {
				// clear if reset type
				if($this->inLineForDel) $this->clear();
				// alter table and/or comment
				$arrQuery = explode(';', $query);
				foreach ($arrQuery as $item) {
					if (strlen($item) > 10) {
						\Yii::$app->db->createCommand($item . ';')->execute();
					}
				}
				
			} catch (\Throwable $throwable) {
				return new \Exception(\Yii::t('app', 'Error alter column: ' . $throwable->getMessage()));
			}
			try {
				// set default
				\Yii::$app->db->createCommand(<<<SQL
ALTER TABLE {$this->mainTableName}
    ALTER COLUMN {$key} SET DEFAULT NULL;
SQL)->execute();
			} catch (\Throwable $throwable) {}
		}
		
		return true;
	}
	
	use SchemaBuilderTrait;
	
	
	/**
	 * {@inheritdoc}
	 * @since 2.0.6
	 */
	protected function getDb() {
		return \Yii::$app->db;
	}
	
	/**
	 * @param $name
	 * @param $column
	 * @return string
	 */
	private function queryCreat($name ,$column, $isAlter = false) {
		$query = $this;
		if ($column['type'] === self::TYPE_INT) {
			if ($column['size']) {
				$query = $query->integer($column['size']);
			} else {
				$query = $query->integer();
			}
		} elseif ($column['type'] === self::TYPE_STRING) {
			if ($column['size']) {
				$query = $query->string($column['size']);
			} else {
				$query = $query->string();
			}
		} elseif ($column['type'] === self::TYPE_FLOAT) {
			if ($column['size']) {
				$query = $query->float($column['size']);
			} else {
				$query = $query->float();
			}
		} else {
			if ($column['size']) {
				$query = $query->string($column['size']);
			} else {
				$query = $query->string();
			}
		}
		if (!$isAlter) {
			if ($column['isNull'] && !$column['isPrimary']) {
				if (!$isAlter) {
					$query->null();
				}
			} else {
				$query->notNull();
			}
			
			if ($column['isPrimary']) {
				$this->newPrimary[] = $name;
			} else {
				if ($column['defaultValue'] !== null) {
					$query->defaultValue($column['defaultValue']);
				}
			}
			if ($column['comment']) {
				$query .= $this->comment($column['comment']);
			}
		}
		
		return $query . '';
	}
	
	/**
	 * @param string $columnName
	 * @param array $column
	 * @return string
	 */
	private function queryAlt(string $columnName ,array $column) {
		$query = <<<SQL
ALTER TABLE {$this->mainTableName}
SQL;
		$arrayAlterQuery = [];
		$alterComment = '';
		foreach ($column as $prop => $val) {
			if ($prop === 'type') {
				$this->inLineForDel = true;
				$arrayAlterQuery['type'] = $this->resetTypeQueru($columnName, $val);
			} elseif ($prop === 'isNull') {
				if ($val && !$this->mainColumns[$columnName]['isNull']) {
					$arrayAlterQuery['isNull'] = "ALTER COLUMN $columnName SET NOT NULL";
				} elseif (!$val && $this->mainColumns[$columnName]['isNull']) {
					$arrayAlterQuery['isNull'] = "ALTER COLUMN $columnName DROP NOT NULL";
				}
			} elseif ($prop === 'comment') {
				if ($val) {
					$alterComment = "COMMENT ON COLUMN {$this->mainTableName}.$columnName IS '$val'";
				} else {
					$alterComment = "COMMENT ON COLUMN {$this->mainTableName}.$columnName IS NULL";
				}
			}
		}
		
		if (count($arrayAlterQuery)) {
			$query .= implode(',', $arrayAlterQuery) . ';';
		} else {
			$query = '';
		}
		
		if ($alterComment) {
			$query .= $alterComment;
		}
		
		return $query;
	}
	
	private function resetTypeQueru($name, $type) {
		$query = <<<SQL
 ALTER COLUMN $name TYPE
SQL;
		
		if ($type === self::TYPE_INT) {
			$query .= ' INT USING ' . $name . '::integer';
		} elseif ($type === self::TYPE_STRING) {
			$query .= ' varchar(' . $this->columnSize[self::TYPE_STRING] . ')';
		} elseif ($type === self::TYPE_FLOAT) {
			$query .= ' numeric(' . $this->columnSize[self::TYPE_INT] . ',' . $this->columnSize[self::TYPE_FLOAT] . ') USING ' . $name . '::numeric';
		} else {
			$query .= ' VARCHAR';
		}
		
		return $query;
	}
	
	protected function comment($comment)
	{
		return '';
	}
}