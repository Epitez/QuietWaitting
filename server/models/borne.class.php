<?php

    /**
    * Borne that give tickets.
    */
    class Borne extends Model {
        protected static function attributes() {
            return ['token', 'type', 'state', 'nbDelivered'];
        }

        protected static function defaults() {
            return ['state' => 1, 'nbDelivered' => 0, 'token' => 0, 'type' => 'Borne'];
        }

        public $Token;
        public $Type;
        public $State;
        public $NbDelivered;

        protected $_tickets;

        public function __construct() {
            parent::__construct();
        }

        /**
        * has_many tickets
        * return an array of Ticket
        */
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

        /**
        * Create a ticket with this borne.
        * return Ticket
        */
        public function createTicket(PDO $bdd, Service $service) {
            if ($this->_id < 1 || $service->id() < 1) {
                throw new Exception("Error Empty Object");
            }
            $ticket = new Ticket();
            $ticket->IdBorne = $this->id();
            $ticket->IdService = $service->id();
            $ticket->save($bdd);
            $this->NbDelivered += 1;
            $this->save($bdd);
            return $ticket;
        }

    }

?>
