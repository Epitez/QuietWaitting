<?php

    /**
    * Borne that give tickets.
    */
    class Borne extends Model {
        protected static function attributes() {
            return ['state', 'nb_delivered'];
        }

        public $state;
        public $nb_delivered;

        protected $_tickets;

        public function __construct() {
            $this->state = 1;
            $this->nb_delivered = 0;
            parent::__construct();
        }

        public function tickets(PDO $bdd) {
            if ($this->_id < 1) {
                throw new Exception("Error Empty Object");
            }
            if ($this->_tickets == NULL || true) {
                $this->_tickets = Ticket::GetAll($bdd,
                                                                  $whereClause = ' id_borne = :id_borne',
                                                                  $bindedVariables = array(':id_borne' => $this->_id));
            }
            return $this->_tickets;
        }

        public function createTicket(PDO $bdd, Service $service) {
            if ($this->_id < 1 || $service->id() < 1) {
                throw new Exception("Error Empty Object");
            }
            $ticket = new Ticket();
            $ticket->id_borne = $this->id();
            $ticket->id_service = $service->id();
            $ticket->save($bdd);
            return $ticket;
        }

    }

?>
