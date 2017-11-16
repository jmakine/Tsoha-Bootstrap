<?php

class Tehtava extends BaseModel {
    
    public $id, $nimi, $deadline, $luotupvm, $kuvaus, $luokka, $tarkeys; //$luokka on Luokka-olio
    
    public function __construct($attributes) {
        parent::__construct($attributes);
        
    }
    
    /*Tässä saa jotenkin suunnan mukaan järjestettyä kyselyn, ja sitten esitettyä sen, liittyy sorttaukseen
     * public static function kaikki($suunta){
        $kysely = DB::connection()->prepare('SELECT * FROM Tehtava ORDER BY :suunta');
        $kysely->execute(array('suunta' => $suunta));*/
    public static function kaikki(){
        $kysely = DB::connection()->prepare('SELECT * FROM Tehtava');
        $kysely->execute();
        $rows = $kysely->fetchAll();
        $tehtavat = array();

        foreach ($rows as $row) {
            $tehtavat[] = new Tehtava(array(
                'id' => $row['id'],
                'nimi' => $row['nimi'],
                'kuvaus' => $row['kuvaus'],
                'luokka' => $row['luokka_id'], //luokka_id on tietokantataulun Tehtava luokan sarakkeen nimi
                'luotupvm' => $row['luotu_pvm'], 
                'tarkeys' => $row['tarkeys'],
                'deadline' => $row['deadline']
            ));
        }
        return $tehtavat;
    }
    
    public static function find($id){
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
        $query = DB::connection()->prepare('INSERT INTO Tehtava (nimi, deadline, tarkeys, luotu_pvm, kuvaus, luokka_id) VALUES (:nimi, :deadline, :tarkeys, :luotu_pvm, :kuvaus, :luokka) RETURNING id');
        // Muistathan, että olion attribuuttiin pääse syntaksilla $this->attribuutin_nimi
        $query->execute(array('nimi' => $this->nimi, 'deadline' => $this->deadline, 'tarkeys' => $this->tarkeys, 'luotu_pvm' => $this->luotupvm, 'kuvaus' => $this->kuvaus, 'luokka_id' => $this->luokka));
        // Haetaan kyselyn tuottama rivi, joka sisältää lisätyn rivin id-sarakkeen arvon
        $row = $query->fetch();
        // Asetetaan lisätyn rivin id-sarakkeen arvo oliomme id-attribuutin arvoksi
        $this->id = $row['id'];
    }
    
    }
