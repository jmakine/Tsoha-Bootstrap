<?php

class Tehtava extends BaseModel {
 
    public $id, $nimi, $deadline, $luotupvm, $kuvaus, $luokka, $tarkeys;  //luokka on luokka_id, INT

    public function __construct($attributes) {
        parent::__construct($attributes);
        $this->validators = array('validate_nimi', 'validate_kuvaus', 'validate_deadline');
    }

    public function validate_nimi() {
        $errors = array();
        if ($this->nimi == '' || $this->nimi == null) {
            $errors[] = 'Nimi ei saa olla tyhjä!';
        }
        if (strlen($this->nimi) < 6 || strlen($this->nimi > 30)) {
            $errors[] = 'Nimen pituuden tulee olla vähintään kuusi merkkiä ja enintään 30!';
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

    //j.n.YY = 1.1.2018, dd.mm.YY = 01.01.2018 
    public function validate_deadline() {
        $errors = array();
       
        if ($this->deadline == ""){          
            $this->deadline = null;
            return $errors;
        }
        
         $pvm = explode('.', $this->deadline);

        if ($this->deadline != "" && !checkdate($pvm[1], $pvm[0], $pvm[2])) {
            $errors[] = 'Päivämäärä ei kelpaa!';
        }
        
        return $errors;
    }

    public static function kaikki() {
        $kysely = DB::connection()->prepare('SELECT * FROM Tehtava WHERE kayttaja_id = :kayttaja_id');
        $kysely->execute(array('kayttaja_id'=>$_SESSION['user']));
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
        // Lisätään RETURNING id tietokantakyselymme loppuun, niin saamme lisätyn rivin id-sarakkeen arvon 
        // luotu_pvm ei päivitetä
        $query = DB::connection()->prepare('INSERT INTO Tehtava (kayttaja_id, nimi, deadline, tarkeys, luotu_pvm, kuvaus, luokka_id) VALUES ('. $_SESSION['user'] .', :nimi, :deadline, :tarkeys, NOW(), :kuvaus, :luokka_id) RETURNING id, luotu_pvm');
        // Muistathan, että olion attribuuttiin pääse syntaksilla $this->attribuutin_nimi
        $query->execute(array('nimi' => $this->nimi, 'deadline' => $this->deadline, 'tarkeys' => $this->tarkeys, 'kuvaus' => $this->kuvaus, 'luokka_id' => $this->luokka));
        // Haetaan kyselyn tuottama rivi, joka sisältää lisätyn rivin id-sarakkeen arvon
        $row = $query->fetch();
        // Asetetaan lisätyn rivin id-sarakkeen arvo ja lisäyshetki olioomme 
        $this->id = $row['id'];
        $this->luotupvm = $row['luotu_pvm'];
    }

    public function delete() {
        $query = DB::connection()->prepare('DELETE FROM Tehtava WHERE id = :id');
        $query->execute(array('id'=>$this->id));
    }

    public function update() {
        $query = DB::connection()->prepare('UPDATE Tehtava SET nimi = :nimi, deadline = :deadline, tarkeys = :tarkeys, kuvaus = :kuvaus, luokka_id = :luokka_id WHERE id = :id');
        $query->execute(array('id' => $this->id, 'nimi'=> $this->nimi, 'deadline'=> $this->deadline, 'tarkeys'=> $this->tarkeys, 'kuvaus'=>$this->kuvaus, 'luokka_id'=>$this->luokka));
        
    }
    
    //public static 
    
}