<?php

class Tehtava extends BaseModel {

    public $id, $nimi, $deadline, $luotupvm, $kuvaus, $luokka, $tarkeys;

    public function __construct($attributes) {
        parent::__construct($attributes);
        $this->validators = array('validate_nimi', 'validate_kuvaus');
    }

    public function validate_nimi() {
        $errors = array();

        if ($this->nimi == '' || $this->nimi == null) {
            $errors[] = 'Nimi ei saa olla tyhjä!';
        }
        if (strlen($this->nimi) < 4 || strlen($this->nimi > 30)) {
            $errors[] = 'Nimen pituuden tulee olla vähintään neljä ja enintään 30 merkkiä!';
        }
        return $errors;
    }

    public function validate_kuvaus() {
        $errors = array();
        if (strlen($this->kuvaus) > 400) {
            $errors[] = 'Max. pituus tehtavan kuvaukselle 400 merkkiä!';
        }
        return $errors;
    }
    
    public static function kaikki($options) {
        $query_string = 'SELECT * FROM Tehtava WHERE kayttaja_id = :kayttaja_id';       
        $option = array('kayttaja_id' => $_SESSION['user']);
        
        if(isset($options['tarkeys'])) {
            $query_string .= ' AND tarkeys = :sel';
            $option['sel'] = $options['tarkeys'];
        }
        if(isset($options['luokka'])) {
            $query_string .= ' AND luokka_id = :sel';
            $option['sel'] = $options['luokka'];
        }
        $query_string .= ' ORDER BY deadline asc';
        
        $kysely = DB::connection()->prepare($query_string);
        $kysely->execute($option); 
        $rows = $kysely->fetchAll();
        $tehtavat = array();

        foreach ($rows as $row) {
            $tehtavat[] = new Tehtava(array(
                'id' => $row['id'],
                'nimi' => $row['nimi'],
                'kuvaus' => $row['kuvaus'],
                'luokka' => $row['luokka_id'],
                'luotupvm' => $row['luotu_pvm'],
                'tarkeys' => $row['tarkeys'],
                'deadline' => $row['deadline']
            ));
        }
        return $tehtavat;
    }

    public static function find($id) {
        $kysely = DB::connection()->prepare('SELECT * FROM Tehtava WHERE id = :id LIMIT 1');
        $kysely->execute(array('id' => $id));
        $row = $kysely->fetch();

        if ($row) {
            $tehtava = new Tehtava(array(
                'id' => $row['id'],
                'nimi' => $row['nimi'],
                'kuvaus' => $row['kuvaus'],
                'luokka' => $row['luokka_id'],
                'luotupvm' => $row['luotu_pvm'],
                'tarkeys' => $row['tarkeys'],
                'deadline' => $row['deadline']
            ));

            return $tehtava;
        }
        return null;
    }

    public function save() {
        $query = DB::connection()->prepare(
                'INSERT INTO Tehtava (kayttaja_id, nimi, deadline, tarkeys, luotu_pvm, kuvaus, luokka_id) '
                . 'VALUES (:kayttaja_id, :nimi, :deadline, :tarkeys, NOW(), :kuvaus, :luokka_id) '
                . 'RETURNING id, luotu_pvm');
        $query->execute(array(
            'kayttaja_id' => $_SESSION['user'], 
            'nimi' => $this->nimi, 
            'deadline' => $this->deadline, 
            'tarkeys' => $this->tarkeys, 
            'kuvaus' => $this->kuvaus, 
            'luokka_id' => $this->luokka
                ));
        $row = $query->fetch();
        $this->id = $row['id'];
        $this->luotupvm = $row['luotu_pvm'];
    }

    public function delete() {
        $query = DB::connection()->prepare('DELETE FROM Tehtava WHERE id = :id');
        $query->execute(array('id' => $this->id));
    }

    public function update() {
        $query = DB::connection()->prepare('UPDATE Tehtava SET nimi = :nimi, deadline = :deadline, tarkeys = :tarkeys, kuvaus = :kuvaus, luokka_id = :luokka_id WHERE id = :id');
        $query->execute(array('id' => $this->id, 'nimi' => $this->nimi, 'deadline' => $this->deadline, 'tarkeys' => $this->tarkeys, 'kuvaus' => $this->kuvaus, 'luokka_id' => $this->luokka));
    }

}
