<?php

    /**
    * Base class for models,
    * implement the basics of CRUD.
    * Abstraction layer for SQL.
    */
    abstract class Model {

        protected $_id;
        protected $_className;
        /**
        * This must be set for all models, this parameter set the column of the model.
        */
        protected static function attributes() {
            return Array();
        }
        /**
        * This the column names of the model.
        */
        protected static function parameters() {
            return static::attributes();
        }

        /**
        * Construct the object.
        * If $bdd and $id are specified, will fetch the data in the database.
        * You can add a where clause with $whereClause and the $bindedVariables.
        */
        function __construct() {
            $this->_id = NULL;
            $this->_className = strtolower(get_class($this));
        }

        /**
        * Id getter.
        */
        public function id() {
            return $this->_id;
        }


        /**
        * Get implementation.
        */
        public static function Get(PDO $bdd, $id, $whereClause = '', $bindedVariables = array()) {
            $className = strtolower(get_called_class());
            $attributes = static::parameters();
            $strQuery = 'SELECT id, ';
            foreach ($attributes as $key => $attribute) { # append the attributes.
                $strQuery .= $attribute.', ';
            }
            $strQuery = trim($strQuery, ", "); # remove the trailing ', '.
            $strQuery .= ' FROM '.$className.'s WHERE id = :id';
            if (strlen($whereClause) > 0) {
                $strQuery .= ' AND '.$whereClause.' ;';
            } else {
                $strQuery .= ' ;';
            }

            $query = $bdd->prepare($strQuery);
            $query->bindValue(':id', $id);
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
                if ($key == "id") {
                    $result->_id = $value;
                    continue;
                }
                $result->$key = $value;
            }
            return $result;
        }

        public static function GetAll(PDO $bdd, $whereClause = '', $bindedVariables = array()) {
            $className = strtolower(get_called_class());
            $attributes = static::parameters();
            $strQuery = 'SELECT id, ';
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
                    if ($key == "id") {
                        $result->_id = $value;
                        continue;
                    }
                    $result->$key = $value;
                }
                $results[] = $result;
            }
            return $results;
        }

        /**
        * Create implementation.
        */
        protected function create( PDO $bdd ) {
            $strQuery = 'INSERT INTO '.$this->_className.'s ( id, ';
            foreach (static::parameters() as $key => $attribute) { # append the attributes.
                $strQuery .= $attribute.', ';
            }
            $strQuery = trim($strQuery, ", "); # remove the trailing ', '.
            $strQuery .= ' ) VALUES ( NULL, ';
            foreach (static::parameters() as $key => $attribute) { # append the values.
                $strQuery .= ':'.$attribute.', ';
            }
            $strQuery = trim($strQuery, ", "); # remove the trailing ', '.
            $strQuery .= ' ) ;';

            $query = $bdd->prepare($strQuery);
            foreach (static::parameters() as $key => $attribute) {
                $query->bindValue($attribute, $this->$attribute);
            }

            try {
                $bdd->beginTransaction();
                $query->execute() or die($query->errorinfo());
                $this->_id = $bdd->lastInsertId();
                $bdd->commit();
            } catch (PDOException $e) {
                $bdd->rollback();
                die('Error while creating '.$this->_className.': '.$e->getMessage().', '.$strQuery);
            }
            return $this;
        }


        /**
        * Update implementation.
        */
        protected function update( PDO $bdd ) {
            $strQuery = 'UPDATE '.$this->_className.'s SET ';
            foreach (static::parameters() as $key => $attribute) { # append the attributes with their values.
                $strQuery .= $attribute.' = :'.$attribute.', ';
            }
            $strQuery = trim($strQuery, ", "); # remove the trailing ', '.
            $strQuery .= ' WHERE id = :id ;';

            $query = $bdd->prepare($strQuery);
            $query->bindValue(':id', $this->_id);
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
        */
        public function save(PDO $bdd) {
            if ($this->_id > 0) { # update.
                return $this->update($bdd);
            } else {
                return $this->create($bdd);
            }
        }

        /**
        * Delete implementation.
        */
        public function destroy(PDO $bdd) {
            if ($this->_id < 1) return false;

            $strQuery = 'DELETE FROM '.$this->_className.'s WHERE id = :id ;';

            $query = $bdd->prepare($strQuery);
            $query->bindValue(':id', $this->_id);

            try {
                $bdd->beginTransaction();
                $query->execute() or die($query->errorinfo());
                $bdd->commit();
            } catch (PDOException $e) {
                $bdd->rollback();
                die('Error while doing in '.$this->_className.': '.$e->getMessage());
            }

            $this->_id = NULL;
            return true;
        }

        /**
        * Debug informations.
        */
        public function debug() {
            echo '---- Debug : '.$this->_className."<br>\n";
            echo 'id : '.$this->id()."<br>\n";
            foreach (static::parameters() as $key => $attribute) {
                echo $attribute.' : ';
                echo var_dump($this->$attribute);
                echo "<br>\n";
            }
            echo 'End Debug ----'."<br><br>\n\n";
        }

    }


?>
