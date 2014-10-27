<?php

	/**
	* Service given by the guichets.
	*/
	class Service extends Model {
		protected static function attributes() {
			return ['name'];
		}

		public $name;

		protected $_tickets;
		protected $_guichets;
		protected $_assoc_guichets;
		protected $_assoc_guichets_time = NULL;

		public function __construct() {
			$this->name = '';
			parent::__construct();
		}

		public function tickets(PDO $bdd) {
			if ($this->_id < 1) {
				throw new Exception("Error Empty Object");
			}
			if ($this->_tickets == NULL || true) {
				$this->_tickets = Ticket::GetAll($bdd,
																$whereClause = ' id_service = :id_service',
																$bindedVariables = array(':id_service' => $this->_id));
			}
			return $this->_tickets;
		}

		public function guichets(PDO $bdd) {
			if ($this->_id < 1) {
				throw new Exception("Error Empty Object");
			}
			if ($this->_guichets == NULL || true) {
				$this->_guichets = Array();
				$this->_assoc_guichets = Services_par_guichet::GetAll($bdd,
																$whereClause = ' id_service = :id_service',
																$bindedVariables = array(':id_service' => $this->_id));
				foreach ($this->_assoc_guichets as $key => $assoc) {
					$this->_guichets[] = $assoc->guichet($bdd);
				}
			}
			return $this->_guichets;
		}

		private function addGuichet(PDO $bdd, Guichet $guichet) {
			if ($this->_id < 1) {
				throw new Exception("Error Empty Object");
			}
			$this->guichets($bdd);
			$assoc = new Services_par_guichet();
			if ($guichet->id() < 1) {
				$guichet->save($bdd);
			}
			$assoc->id_service = $this->_id;
			$assoc->id_guichet = $guichet->id();
			$assoc->save($bdd);

			return $guichet;
		}

	}

?>
