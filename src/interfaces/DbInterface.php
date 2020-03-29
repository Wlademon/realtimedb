<?


namespace wladoseid\realtimedb\interfaces;


interface DbInterface {
	
	public function __construct($db);
	
	public function load():bool;
	
	public function readOnly():bool;
	
	public function getName():string;
	
	public function tables():array;
	
	public function types(): array;
	
	public function views(): array;
	
	public function tFunctions():array;
	
	public function functions(): array;
	
	public function procedures(): array;
	
	public function sequences(): array;
}