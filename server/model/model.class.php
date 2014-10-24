<?php

    /**
    * Base class for models,
    * implement the basics of CRUD.
    * Abstraction layer for SQL.
    */
    abstract class Model {

        protected $_className;  // The classname (used for the table name)

        /**
         * This must be set for all models, this parameter set the column of the model.
         * Example : return ['author', 'content', 'article', 'created_at'];
         **/
        protected static function attributes() {
            return Array();
        }

        /**
         * This must be set for all models, this parameter set the primary key. This class handles only one primary key
         * Example : return 'id';
         **/
        protected static function primary() {
            return String();
        }

        /**
         * This must be set for all models, this parameter set the defaults.
         * Example : return ['name' => 'defaultValue'];
         **/
        protected static function defaults() {
            return Array();
        }

        /**
         * This is the column names of the model.
         **/
        protected static function parameters() {
            $tmp = [];
            foreach(static::attributes() as $key => $value) {
                array_push($tmp, static::_camelToSnake($value));
            };
            return $tmp;
        }

        /**
         * This is the primary key of the model.
         **/
        protected static function primaryKey() {
            return static::_camelToSnake(static::primary());
        }

        function __construct() {
            /**
             * Set the primary key, result : $this->_nameOfPrimaryKey
             **/
            $newPrimaryKey = '_'.static::primaryKey();
            $this->$newPrimaryKey = '';

            /**
             * Set the class name, used as table name and for debugging
             **/
            $this->_className = strtolower(get_class($this));

            /**
             * Load by defaults the attributes, the result is $this->NameOfYourVar = NULL
             * If you want to override this value, set the value in the child ctor
             **/
            foreach(static::attributes() as $key => $value) {
                $newName = ucwords($key);
                $this->$newName = NULL;
            }

            /**
             * Set the defaults defined
             **/
            foreach(static::defaults() as $key => $value) {
                $newName = ucwords($key);
                $this->$newName = $value;
            }
        }

        /**
         * Transform camel case to snake case
         *
         * @param string
         * @return string
         **/
        private static function _camelToSnake($val) {
            return strtolower(preg_replace('/(?<=\\w)(?=[A-Z])/',"_$1", $val));
        }

        /**
         * Transform snake case to camel case
         *
         * @param string
         * @return string
         **/
        private static function _snakeToCamel($val) {
            return str_replace(' ', '', ucwords(str_replace('_', ' ', $val)));
        }

        /**
         * Id getter.
         **/
        public function id() {
            $_primaryKey = '_'.static::primaryKey();
            return $this->$_primaryKey;
        }


        /**
         * Get implementation.
         * If $primary is specified, will fetch the data in the database.
         * You can add a where clause with $whereClause and the $bindedVariables.
         **/
        public static function Get(PDO $bdd, $primary, $whereClause = '', $bindedVariables = array()) {
            $className = strtolower(get_called_class());
            $attributes = static::parameters();
            $primaryKey = static::primaryKey();
            $_primaryKey = '_'.$primaryKey;

            $strQuery = 'SELECT ' . $primaryKey . ', ';
            foreach ($attributes as $key => $attribute) { # append the attributes.
                $strQuery .= $attribute.', ';
            }
            $strQuery = trim($strQuery, ", "); # remove the trailing ', '.
            $strQuery .= ' FROM '.$className.'s WHERE ' . $primaryKey . ' = :'.$primaryKey;
            if (strlen($whereClause) > 0) {
                $strQuery .= ' AND '.$whereClause.' ;';
            } else {
                $strQuery .= ' ;';
            }
            $query = $bdd->prepare($strQuery);
            $query->bindValue(':'.$primaryKey, $primary); # bind the primary key
            foreach ($bindedVariables as $key => $variable) {
                $query->bindValue($key, $variable);
            }

            try {
                $bdd->beginTransaction();
                $query->execute() or die($query->errorinfo());
                $bdd->commit();
            } catch (PDOException $e) {
                $bdd->rollback();
                die('Error while fetching '.$className.': '.$e->getMessage());
            }

            $rows = $query->FetchALL(PDO::FETCH_ASSOC);
            if (count($rows) < 1) {
                die ('Unable to find '.$className.' with id '.$id.'.');
            }
            $result = new $className();
            foreach ($rows[0] as $key => $value) {
                if ($key == $primaryKey) {
                    $result->$_primaryKey = $value;
                    $result->_id = $value;
                    continue;
                }
                // Comment the next line if you want snake case
                $key = static::_snakeToCamel($key);
                $result->$key = $value;
            }
            return $result;
        }

        /**
         * GetAll implementation.
         * You can add a where clause with $whereClause and the $bindedVariables.
         **/
        public static function GetAll(PDO $bdd, $whereClause = '', $bindedVariables = array()) {
            $className = strtolower(get_called_class());
            $attributes = static::parameters();
            $primaryKey = static::primaryKey();
            $_primaryKey = '_'.$primaryKey;

            $strQuery = 'SELECT '.$primaryKey.', ';
            foreach ($attributes as $key => $attribute) { # append the attributes.
                $strQuery .= $attribute.', ';
            }
            $strQuery = trim($strQuery, ", "); # remove the trailing ', '.
            $strQuery .= ' FROM '.$className.'s';
            if (strlen($whereClause) > 0) {
                $strQuery .= 'WHERE '.$whereClause.' ;';
            } else {
                $strQuery .= ' ;';
            }

            $query = $bdd->prepare($strQuery);
            foreach ($bindedVariables as $key => $variable) {
                $query->bindValue($key, $variable);
            }

            try {
                $bdd->beginTransaction();
                $query->execute() or die($query->errorinfo());
                $bdd->commit();
            } catch (PDOException $e) {
                $bdd->rollback();
                die('Error while fetching all '.$className.': '.$e->getMessage());
            }

            $rows = $query->FetchALL(PDO::FETCH_ASSOC);
            $results = Array();
            foreach ($rows as $index => $row) {
                $result = new $className();
                foreach ($row as $key => $value) {
                    if ($key == $primaryKey) {
                        $result->$_primaryKey = $value;
                        continue;
                    }
                    // Comment the next line if you want snake case
                    $key = static::_snakeToCamel($key);
                    $result->$key = $value;
                }
                $results[] = $result;
            }
            return $results;
        }

        /**
         * Create implementation.
         **/
        protected function create( PDO $bdd ) {
            $primaryKey = static::primaryKey();
            $_primaryKey = '_'.$primaryKey;
            $strQuery = 'INSERT INTO '.$this->_className.'s ( '. $primaryKey .', ';
            foreach (static::parameters() as $key => $attribute) { # append the attributes.
                $strQuery .= $attribute.', ';
            }
            $strQuery = trim($strQuery, ", "); # remove the trailing ', '.
            $strQuery .= ' ) VALUES ( ';
            $strQuery .= $this->$_primaryKey ? ':'.$primaryKey.', ' : '';
            foreach (static::parameters() as $key => $attribute) { # append the values.
                $strQuery .= ':'.$attribute.', ';
            }
            $strQuery = trim($strQuery, ", "); # remove the trailing ', '.
            $strQuery .= ' ) ;';

            $query = $bdd->prepare($strQuery);

            $valPrimaryKey = $this->$_primaryKey ? $this->$_primaryKey : 'NULL';
            if ($this->$_primaryKey) $query->bindValue($primaryKey, $valPrimaryKey); # bind the primaryKey if needed
            foreach (static::parameters() as $key => $attribute) {
                $newAttr = static::_snakeToCamel($attribute);
                $query->bindValue($attribute, $this->$newAttr);
            }

            try {
                $bdd->beginTransaction();
                $query->execute() or die($query->errorinfo());
                $this->$_primaryKey = $bdd->lastInsertId();
                $bdd->commit();
            } catch (PDOException $e) {
                $bdd->rollback();
                die('Error while creating '.$this->_className.': '.$e->getMessage().', '.$strQuery);
            }
            return $this;
        }


        /**
         * Update implementation.
         **/
        protected function update( PDO $bdd ) {
            $primaryKey = static::primaryKey();$
            $_primaryKey = '_'.$primaryKey;
            $strQuery = 'UPDATE '.$this->_className.'s SET ';
            foreach (static::parameters() as $key => $attribute) { # append the attributes with their values.
                $strQuery .= $attribute.' = :'.$attribute.', ';
            }
            $strQuery = trim($strQuery, ", "); # remove the trailing ', '.
            $strQuery .= ' WHERE '.$primaryKey.' = :'.$primaryKey.';'; # primary key

            $query = $bdd->prepare($strQuery);
            $query->bindValue(':'.$primaryKey, $this->$_primaryKey);
            foreach (static::parameters() as $key => $attribute) {
                $query->bindValue($attribute, $this->$attribute);
            }

            try {
                $bdd->beginTransaction();
                $query->execute() or die($query->errorinfo());
                $bdd->commit();
            } catch (PDOException $e) {
                $bdd->rollback();
                die('Error while updating '.$this->_className.': '.$e->getMessage());
            }

            return $this;
        }

        /**
         * Create/Update call.
         **/
        public function save(PDO $bdd) {
            $primaryKey = static::primaryKey();
            $_primaryKey = '_'.$primaryKey;
            if ($this->$_primaryKey > 0) { # update.
                return $this->update($bdd);
            } else {
                return $this->create($bdd);
            }
        }

        /**
         * Delete implementation.
         **/
        public function destroy(PDO $bdd) {
            $primaryKey = static::primaryKey();
            $_primaryKey = '_'.$primaryKey;
            if ($this->$primaryKey < 1) return false;

            $strQuery = 'DELETE FROM '.$this->_className.'s WHERE '.$primaryKey.' = :'.$primaryKey.' ;';

            $query = $bdd->prepare($strQuery);
            $query->bindValue(':'.$primaryKey, $this->$_primaryKey);

            try {
                $bdd->beginTransaction();
                $query->execute() or die($query->errorinfo());
                $bdd->commit();
            } catch (PDOException $e) {
                $bdd->rollback();
                die('Error while doing in '.$this->_className.': '.$e->getMessage());
            }

            $this->$_primaryKey = NULL;
            return true;
        }

        /**
         * Debug informations.
         **/
        public function debug() {
            $primaryKey = static::primaryKey();
            $_primaryKey = '_'.$primaryKey;
            echo '---- Debug : '.$this->_className."<br>\n";
            echo $primaryKey.' : '.$this->id()."<br>\n";
            foreach (static::parameters() as $key => $attribute) {
                echo $attribute.' : ';
                echo var_dump($this->$attribute);
                echo "<br>\n";
            }
            echo 'End Debug ----'."<br><br>\n\n";
        }

    }


?>
