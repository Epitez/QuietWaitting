<?php

    /**
    * Guichet that host several services.
    */
    class Services_par_guichet extends Model {
        protected static function attributes() {
            return ['id_guichet', 'id_service'];
        }

        public $id_guichet;
        public $id_service;

        protected $_service;
        protected $_guichet;

        public function __construct() {
            $this->id_guichet = 0;
            $this->id_service = 0;
            parent::__construct();
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

        public function guichet(PDO $bdd) {
            if ($this->_id < 1) {
                throw new Exception("Error Empty Object");
            }
            if ($this->_guichet == NULL || true) {
                $this->_guichet = Guichet::Get($bdd, $this->id_guichet);
            }
            return $this->_guichet;
        }

    }

?>
