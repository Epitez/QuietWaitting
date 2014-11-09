<?php

    /**
    * Guichet that host several services.
    */
    class Guichet extends Model {
        protected static function attributes() {
            return ['name', 'ouvert'];
        }

        protected static function defaults() {
            return ['name' => '', 'ouvert' => 0];
        }

        public $Name;
        public $Ouvert;

        protected $_services;
        protected $_assoc_services;

        public function __construct() {
            parent::__construct();
        }

        public function services(PDO $bdd) {
            if ($this->_id < 1) {
                throw new Exception("Error Empty Object");
            }
            if ($this->_services == NULL || true) {
                $this->_services = Array();
                $this->_assoc_services = Services_par_guichet::GetAll($bdd,
                                                                $whereClause = 'id_guichet = :id_guichet',
                                                                $bindedVariables = array(':id_guichet' => $this->_id));
                foreach ($this->_assoc_services as $key => $assoc) {
                    $this->_services[] = $assoc->service($bdd);
                }
            }
            return $this->_services;
        }

        public function addService(PDO $bdd, Service $service) {
            if ($this->_id < 1) {
                throw new Exception("Error Empty Object");
            }
            foreach ($this->services($bdd) as $key => $s) {
                if ($s->id() == $service->id()) {
                    return $s;
                }
            }
            $assoc = new Services_par_guichet();
            if ($service->id() < 1) {
                $service->save($bdd);
            }
            $assoc->IdService = $service->id();
            $assoc->IdGuichet = $this->_id;
            $assoc->save($bdd);

            return $service;
        }
        public function removeService(PDO $bdd, Service $service) {
            if ($this->_id < 1) {
                throw new Exception("Error Empty Object");
            }
            foreach ($this->services($bdd) as $key => $s) {
                if ($s->id() == $service->id()) {
                    $assoc = Services_par_guichet::GetAll($bdd,
                            $whereClause = 'id_guichet = :id_guichet AND id_service = :id_service',
                            $bindedVariables = array(':id_guichet' => $this->_id, ':id_service' => $service->id()));
                    if (count($assoc) != 1) {
                        throw new Exception("Error Missing Association");
                    }
                    $assoc = $assoc[0];
                    $assoc->destroy($bdd);
                    return $this;
                }
            }
            return NULL;
        }

    }

?>
