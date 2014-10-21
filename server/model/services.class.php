<?php

	/**
	* Service.
	*/
	class Service extends Model {
		protected static function attributes() {
			return ['name'];
		}

		public $name;

		public function __construct() {
		$this->name = '';
			parent::__construct();
		}

	}

?>
