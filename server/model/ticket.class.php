<?php

    /**
    * Ticket.
    */
    class Ticket extends Model {
        protected static function attributes() {
            return ['state', 'ouvert', 'ferme', 'id_borne', 'id_service'];
        }

        public $state;
        public $ouvert;
        public $ferme;
        public $id_borne;

        protected $_borne;
        protected $_service;

        public function __construct() {
            $this->state = 'en cours';
            $this->ouvert = NULL;
            $this->ferme = NULL;
            $this->id_borne = NULL;
            $this->id_service = NULL;
            parent::__construct();
        }

        public function borne(PDO $bdd) {
            if ($this->_id < 1) {
                throw new Exception("Error Empty Object");
            }
            if ($this->_borne == NULL || true) {
                $this->_borne = Borne::Get($bdd, $this->id_borne);
            }
            return $this->_borne;
        }

        public function service(PDO $bdd) {
            if ($this->_id < 1) {
                throw new Exception("Error Empty Object");
            }
            if ($this->_service == NULL || true) {
                $this->_service = Service::Get($bdd, $this->id_service);
            }
            return $this->_service;
        }

    }

?>
