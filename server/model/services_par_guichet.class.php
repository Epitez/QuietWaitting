<?php

    /**
    * Guichet that host several services.
    */
    class Services_par_guichet extends Model {
        protected static function attributes() {
            return ['idGuichet', 'idService'];
        }

        protected static function defaults() {
            return ['idGuichet' => 0, 'idService' => 0];
        }

        public $IdGuichet;
        public $IdService;

        protected $_service;
        protected $_guichet;

        public function __construct() {
            parent::__construct();
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

        public function guichet(PDO $bdd) {
            if ($this->_id < 1) {
                throw new Exception("Error Empty Object");
            }
            if ($this->_guichet == NULL || true) {
                $this->_guichet = Guichet::Get($bdd, $this->IdGuichet);
            }
            return $this->_guichet;
        }

    }

?>
