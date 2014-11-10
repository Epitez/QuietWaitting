<?php

    /**
    * Ticket.
    * You must use initialize before use !
    */
    class Ticket extends Model {
        // Last distributed Ticket number for the current session
        private static $_CURRENT_NUMBER=0;
        // Last called ticket for the current session
        private static $_CALLED_NUMBER=0;
        // Current session
        private static $_CURRENT_SESSION=0;

        /**
         * Return the current session number
         */
        public static function CurrentSession() {
            return static::$_CURRENT_SESSION;
        }
        /**
         * Call an other ticket (Increment and return the current called ticket)
         */
        public static function Next() {
            static::$_CALLED_NUMBER = static::$_CALLED_NUMBER + 1;
            return static::$_CALLED_NUMBER;
        }
        /**
         * Increment and Return the Ticket number (distributed)
         */
        public static function NextDistributed() {
            static::$_CURRENT_NUMBER = static::$_CURRENT_NUMBER + 1;
            return static::$_CURRENT_NUMBER;
        }
        /**
         * Return the LAST DISTRIBUTED Ticket number
         */
        public static function Last() {
            return static::$_CURRENT_NUMBER;
        }
        /**
         * Reset the Ticket system and start a new session
         */
        public static function Reset() {
            static::$_CURRENT_NUMBER = 0;
            static::$_CALLED_NUMBER = 0;
            static::$_CURRENT_SESSION++;
        }

        protected static function attributes() {
            return ['number', 'session', 'state', 'ouvert', 'absent', 'ferme', 'idBorne', 'idService'];
        }

        protected static function defaults() {
            return ['state' => 'en cours', 'ouvert' => NULL, 'ferme' => NULL, 'absent' => 0];
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

        /**
         * Initialize $_CURRENT_SESSION with the last session number
         */
        public static function initialize($bdd) {
            $strQuery = 'SELECT MAX(session) as "last_session" FROM '.strtolower(get_called_class()).'s';
            $query = $bdd->prepare($strQuery);
            $query = static::execute($bdd, $query);
            $query = static::execute($bdd, $query);
            $last_session = $query->fetch(PDO::FETCH_ASSOC)['last_session'];
            static::$_CURRENT_SESSION = 0 + $last_session;
        }

        protected function create( PDO $bdd ) {
            $this->Number = static::NextDistributed(); // Auto-set the Displayed Number
            $this->Session = static::CurrentSession(); // Auto-set the Displayed Number
            parent::create($bdd);
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
