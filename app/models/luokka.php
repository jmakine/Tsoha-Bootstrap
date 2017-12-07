<?php

class Luokka extends BaseModel {

    public $id, $nimi, $kuvaus, $yliluokka, $luotupvm, $aliluokat, $tehtavat, $aliluokka_count, $tehtavat_count;

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
            $errors[] = 'Max. pituus luokan kuvaukselle 400 merkkiä!';
        }
        return $errors;
    }
   
    public static function kaikki() {
    
        $kysely = DB::connection()->prepare(
                'SELECT id, nimi, kuvaus, luokka_id, luotu_pvm, kayttaja_id FROM Luokka WHERE kayttaja_id = :kayttaja_id ');
                
        $kysely->execute(array('kayttaja_id'=>$_SESSION['user']));
    
        $rows = $kysely->fetchAll();
        $luokat = array();

        foreach ($rows as $row) {
            
            $luokat[] = new Luokka(array(
                'id' => $row['id'],
                'nimi' => $row['nimi'],
                'kuvaus' => $row['kuvaus'],
                'yliluokka' => $row['luokka_id'],
                'luotupvm' => $row['luotu_pvm'],
                //'tehtavat' => Luokka::tehtavat($row['id']),
                //'tehtavat_count' => count(Luokka::tehtavat($row['id'])),
                //'aliluokat' => Luokka::aliluokat($row['id'])
                //'aliluokka_count' => count(Luokka::aliluokat($row['id']))
            ));
        }
        return $luokat;
    }
    
    public static function find($id) {
        $kysely = DB::connection()->prepare('SELECT * FROM Luokka WHERE id = :id LIMIT 1');
        $kysely->execute(array('id' => $id));
        $row = $kysely->fetch(); 
        
        if ($row) {
            
            $luokka = new Luokka(array(
                'id' => $row['id'],
                'nimi' => $row['nimi'],
                'kuvaus' => $row['kuvaus'],
                'yliluokka' => $row['luokka_id'],
                'luotupvm' => $row['luotu_pvm'],
                //'tehtavat' = Luokka::tehtavat($row['id']);
                'aliluokat' => Luokka::aliluokat($row['id'])
                //'tehtavat_count' => count(Luokka::tehtavat($row['id'])),
                //'aliluokka_count' => count(Luokka::aliluokat($row['id']))
            ));

            return $luokka;
        }
        return null;
    }

    //palauttaa uuden taulukon luokkia, joiden yliluokka on tarkasteltava luokka
    public static function aliluokat($luokka_id) {
        $kysely = DB::connection()->prepare('SELECT * FROM Luokka WHERE luokka_id = :luokka_id AND kayttaja_id = :kayttaja_id');
        $kysely->execute(array('luokka_id' => $luokka_id, 'kayttaja_id'=>$_SESSION['user']));
        $rows = $kysely->fetchAll();
        $aliluokat = array();

        foreach ($rows as $row) {
            
            $aliluokat[] = new Luokka(array(
                'id' => $row['id'],
                'nimi' => $row['nimi'],
                'kuvaus' => $row['kuvaus'],
                'yliluokka' => $row['luokka_id'],
                'luotupvm' => $row['luotu_pvm']
                //'tehtavat' => Luokka::tehtavat($row['id']),
                //'tehtavat_count' => count(Luokka::tehtavat($row['id'])),
                //'aliluokat' => Luokka::aliluokat($row['id'])
                //'aliluokka_count' => count(Luokka::aliluokat($row['id']))
            ));

        }
        return $aliluokat;
    }

    public static function tehtavat($id) {
        $kysely = DB::connection()->prepare('SELECT * FROM Tehtava WHERE luokka_id = :id AND kayttaja_id = :kayttaja_id');
        $kysely->execute(array('id' => $id, 'kayttaja_id'=>$_SESSION['user'])); 
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
            return $tehtavat;
        }
    }

    public function save() {
        $query = DB::connection()->prepare('INSERT INTO Luokka (kayttaja_id, nimi, kuvaus, luotu_pvm, luokka_id) VALUES (:kayttaja_id, :nimi, :kuvaus, NOW(), :luokka_id) RETURNING id, luotu_pvm');
        $query->execute(array('nimi'=>$this->nimi, 'kuvaus'=>$this->kuvaus, 'kayttaja_id'=>$_SESSION['user'], 'luokka_id' => $this->yliluokka));
        $row = $query->fetch();
        $this->id = $row['id'];
        $this->luotupvm = $row['luotu_pvm'];
    }

    
    public function delete() {
        $query = DB::connection()->prepare('DELETE FROM Luokka WHERE id = :id');
        $query->execute(array('id'=>$this->id));
    }

    public function update() {
        $query = DB::connection()->prepare('UPDATE Luokka SET nimi = :nimi, kuvaus = :kuvaus, luokka_id = :luokka_id WHERE id = :id');
        $query->execute(array('id'=> $this->id, 'nimi'=> $this->nimi, 'kuvaus'=>$this->kuvaus, 'luokka_id'=>$this->yliluokka));        
    }
        
    public function getNimi(){
        return $this->nimi;
    }
    
}
