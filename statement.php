<?

class statement {
	private $type;
	private $output;
	private $table;
	private $sql;
	private $db;
	private $user_id;
	private $escape;
	
	public function encaps_string($s) {
		/*if($s == "NOW()") {
			return "'".$s."'";	
		}*/
		if($this->escape) {
			return "'".mysqli_escape_string($this->sql->get_connection(), $s)."'";	//htmlentities
		}
		return "'".$s."'";
	}
	private function value($key, $v) {
		if(strpos($key, "_id") !== false || $key == "id") {
			return $v;	
		}
		switch($key) {
			/*case 'user_id':
				return $this->user_id;
				break;*/
			/*case 'created':
				if(!isset($v['id'])) {
					return "NOW";	
				}
				break;
			case 'modified':
			case 'submitted':
				return "NOW()";
				break;*/
			default:
				if((gettype($v) == 'string' || strpos($v, ":") != false) && $v != "NOW()") { // && !is_numeric($v)
					return $this->encaps_string($v);
				}
				return $v;
				break;
		}
	}
	function __construct($values=NULL, $type=NULL, $table=NULL) {
		if($values != NULL) {
			$this->generate($values, $type, $table);
		}
	}
	
	public static function init($sql, $db, $user_id) {
		$instance = new self();
        $instance->set($sql, $db, $user_id);
        return $instance;
	}
	
	public function set($sql, $db, $user_id) {
		$this->sql = $sql;	
		$this->db = $db;
		$this->user_id = $user_id;
	}
	
	public function generate($values, $table=NULL, $type=NULL, $escape=false) {
		//$continue = true;
		if(isset($values['id'])) {
			if($values['id'] == "-1") {
				unset($values['id']);	
			}
		}
		if($table !== NULL) {
			$this->table = $table;	                                                
		}
		if(!isset($this->table)) { //$this->table == null
			$trace = debug_backtrace();
			$table = $trace[1]['function']."s";
			/*if($table[0] == "_") {
				$table = substr($table, 1);	
			}*/
		} else {
			$table = $this->table;	
		}
		$this->escape = $escape;
		if(isset($this->sql)) {
			$db = $this->db;
			if(strpos($table, ".") !== false) {
				$split = explode(".", $table);
				$db = $split[0];
				$table = $split[1];	
			}
			$query = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = '".$db."' AND TABLE_NAME = '".$table."'";
			//var_dump($this->sql->get_rows($query));
			foreach($this->sql->get_rows($query) as $row) {
				if($row['COLUMN_NAME'] == 'user_id' && !isset($values['id'])) {
					if($this->user_id != -1 && !isset($values['user_id'])) {
						$values['user_id'] = $this->user_id;
					}/* else {
						$continue = false;	
					}*/
				} else if($row['COLUMN_NAME'] == 'created' && !isset($values['id'])) {
					if(!isset($values['id'])) {
						$values['created'] = 'NOW()';	
					}
				} else if($row['COLUMN_NAME'] == 'modified') {
					$values['modifed'] = 'NOW()';	
				} else if($row['COLUMN_NAME'] == 'password') {
					$values['password'] = password_hash($values['password'], PASSWORD_DEFAULT);
					//var_dump($values['password']);
				}
			}
			$table = $db.".".$table;
		}
		if(isset($values['PHPSESSID'])) {
			unset($values['PHPSESSID']);
		}
		if(isset($values['action'])) {
			unset($values['action']);
		}
		if(count($values) > 0) {
			$this->output = '';
			if($type === NULL) {
				if(isset($values['id'])) {
					if($values['id'] == "-1") {
						$this->type = 1;	
					} else {
						$this->type = 0;	
					}
				} else {
					$this->type = 1;	
				}
			} else {
				$this->type = $type;	
			}
			switch($this->type) {
				case 0:
					$output = 'UPDATE '.$table.' SET ';
					if(isset($values['id'])) {
						$counter = 0;
						foreach($values as $key => $v) {
							if($key != 'id') {
								if(strlen($v) > 0) {
									if($counter > 0) {
										$output .= ', ';
									}
									$output .= $key.' = '.$this->value($key, $v);
									$counter++;
								}
							}
						}
						$output .= ' WHERE id = '.$values['id'];
					} else {
						$counter = 0;
						foreach($values as $key => $v) {
							if(strpos($key, "_id") === false) {
								if($counter > 0) {
									$output .= ", ";	
								}
								$output .= $key.' = '.$this->value($key, $v).' ';
								$counter++;
							}
						}
						$output .= " WHERE ";
						$counter = 0;
						foreach($values as $key => $v) {
							if(strpos($key, "_id") !== false) {
								if($counter > 0) {
									$output .= " AND ";	
								}
								$output .= $key.' = '.$v.' ';
								$counter++;
							}
						}
					}
					break;
				case 2:
				case 1:
					$output = 'INSERT INTO '.$table.' (';
					if($this->type == 2) {
						$output = 'REPLACE INTO '.$table.' (';
					}
					$counter = 0;
					foreach($values as $key => $v) {
						if(strlen($v) > 0) {
							if($counter > 0) {
								$output .= ', ';
							}
							$output .= $key;
							$counter++;
						}
					}
					$counter = 0;
					$output .= ") VALUES (";
					foreach($values as $key => $v) {
						if(strlen($v) > 0) {
							if($counter > 0) {
								$output .= ', ';
							}
							if($key == 'submitted') {
								$output .= "NOW()";
							} else {
								$output .= $this->value($key, $v);
							}
							$counter++;
						}
					}
					$output .= ")";
					break;
				/*case 2:
					$output = 'SELECT COUNT(*) as count FROM '.$table.' WHERE ';
					$counter = 0;
					if(isset($values['id'])) {
						$output .= " id = ".$values['id'];
					} else {
						foreach($values as $key => $v) {
							if(strpos($key, "_id") !== false) {
								if($counter > 0) {
									$output .= " AND ";	
								}
								$output .= $key.' = '.$v.' ';
								$counter++;
							}
						}
					}
					break;*/
			}
			$this->output = $output;
		}	
	}
	
	public function update($values) {
		/*$fields = array();
		$output 
		foreach($values as $field => $val) {
			
		}*/
	}
	
	public function get() {
		return $this->output;
	}
}

?>