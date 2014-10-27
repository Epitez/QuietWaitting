<?php

    /**
    * Ticket.
    */
    class Ticket extends Model {
        protected static function attributes() {
            return ['state', 'ouvert', 'ferme', 'idBorne', 'idService'];
        }

        protected static function defaults() {
            return ['state' => 'en cours'];
        }

        public $State;
        public $Ouvert;
        public $Ferme;
        public $IdBorne;
        public $IdService;

        protected $_borne;
        protected $_service;

        public function __construct() {
            parent::__construct();
        }

        public function borne(PDO $bdd) {
            if ($this->_id < 1) {
                throw new Exception("Error Empty Object");
            }
            if ($this->_borne == NULL || true) {
                $this->_borne = Borne::Get($bdd, $this->IdBorne);
            }
            return $this->_borne;
        }

        public function service(PDO $bdd) {
            if ($this->_id < 1) {
                throw new Exception("Error Empty Object");
            }
            if ($this->_service == NULL || true) {
                $this->_service = Service::Get($bdd, $this->IdService);
            }
            return $this->_service;
        }

    }

?>
