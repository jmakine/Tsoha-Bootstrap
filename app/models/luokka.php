<?php

class Luokka extends BaseModel {

    public $id, $nimi, $kuvaus, $yliluokka, $luotupvm, $aliluokat, $tehtavat, $aliluokka_count, $tehtavat_count;

    public function __construct($attributes) {
        parent::__construct($attributes);
        $this->validators = array('validate_nimi', 'validate_kuvaus');
        //$this->aliluokat = $this->aliluokat();
        //$this->aliluokka_count = count($this->aliluokat);
        //$this->tehtavat = $this->tehtavat();
        //$this->tehtavat_count = count($this->tehtavat);
    }

    public function validate_nimi() {
        $errors = array();
        if ($this->nimi == '' || $this->nimi == null) {
            $errors[] = 'Nimi ei saa olla tyhjä!';
        }
        if (strlen($this->nimi) < 4 || strlen($this->nimi > 30)) {
            $errors[] = 'Nimen pituuden tulee olla vähintään kuusi ja enintään 30 merkkiä!';
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
    
    //ei varmaan tarvii muita validiointimetodeja, koska kuvaus saa olla tyhjä, yliluokka valitaan listasta ja muut lasketaan/tulee automaattisesti
    
    public static function kaikki() {
    // Alustetaan kysely tietokantayhteydellämme (prepare=PDO:n metodi)
        $kysely = DB::connection()->prepare('SELECT * FROM Luokka WHERE kayttaja_id = :kayttaja_id');
    //(PDOStatement:n metodeja = excecute ja fetchAll/fetch)
        $kysely->execute(array('kayttaja_id'=>$_SESSION['user']));
    // Kyselyn suorittamisen jälkeen kyselyn tuottamiin riveihin pääsee käsiksi kutsumalla fetchAll-metodia, 
    // joka palauttaa rivit taulukkona assosiaatiolistoja, jonka avaimina toimivat sarakkeiden nimet ja arvoina niiden sisältö.
        $rows = $kysely->fetchAll();
        $luokat = array();

        foreach ($rows as $row) {
            //$tehtavat = $this->tehtavat();
            //$aliluokat = Luokka::aliluokat($row['id']);
            $luokat[] = new Luokka(array(
                'id' => $row['id'],
                'nimi' => $row['nimi'],
                'kuvaus' => $row['kuvaus'],
                'yliluokka' => $row['luokka_id'],
                'luotupvm' => $row['luotu_pvm'],
                'tehtavat' => self::tehtavat(),
                //'tehtavat_count' => count($this->tehtavat),
                'aliluokat' => self::aliluokat(),
                //'aliluokka_count' => count($this->aliluokat)
            ));
        }
        return $luokat;
    }

    //palauttaa uuden taulukon luokkia, joiden yliluokka on tarkasteltava luokka
    public static function aliluokat() {
        $kysely = DB::connection()->prepare('SELECT * FROM Luokka WHERE luokka_id = :id AND kayttaja_id = :kayttaja_id');
        $kysely->execute(array('id' => $this->id, 'kayttaja_id'=>$_SESSION['user']));
        $rows = $kysely->fetchAll();
        $aliluokat = array();

        foreach ($rows as $row) {
            //$tehtavat = Luokka::tehtavat($row['id']);
            //$aliluokan_aliluokat = Luokka::aliluokat($row['id']);
            $aliluokat[] = new Luokka(array(
                'id' => $row['id'],
                'nimi' => $row['nimi'],
                'kuvaus' => $row['kuvaus'],
                'yliluokka' => $row['luokka_id'],
                'luotupvm' => $row['luotu_pvm'],
                'tehtavat' => self::tehtavat(),
                'aliluokat' => self::aliluokat(),
                //'aliluokka_count' => count(Luokka::aliluokat($row['id']))
            ));
            return $aliluokat;
        }
    }

    public static function tehtavat() {
        $kysely = DB::connection()->prepare('SELECT * FROM Tehtava WHERE luokka_id = :id AND kayttaja_id = :kayttaja_id');
        $kysely->execute(array('id' => $this->id, 'kayttaja_id'=>$_SESSION['user'])); 
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

    public static function find($id) {
        $kysely = DB::connection()->prepare('SELECT * FROM Luokka WHERE id = :id LIMIT 1');
        $kysely->execute(array('id' => $id));
        $row = $kysely->fetch(); //palauttaa assosiaatiolistan vain kyselyn tuottamasta ensimmäisestä! rivistä

        if ($row) {
            //$tehtavat = Luokka::tehtavat($row['id']);
            //$aliluokat = Luokka::aliluokat($row['id']);
        //    $luokka = new Luokka();
            $luokka = new Luokka(array(
                'id' => $row['id'],
                'nimi' => $row['nimi'],
                'kuvaus' => $row['kuvaus'],
                'yliluokka' => $row['luokka_id'],
                'luotupvm' => $row['luotu_pvm'],
                //'tehtavat' =>  $tehtavat,
                //'tehtavat_count' => count($tehtavat),
                //'aliluokat' => $aliluokat,
                //'aliluokka_count' => count($aliluokat)
            ));

            return $luokka;
        }
        return null;
    }

    public function save() {
        $query = DB::connection()->prepare('INSERT INTO Luokka (kayttaja_id, nimi, kuvaus, luotu_pvm, luokka_id) VALUES (' . $_SESSION['user'] . ', :nimi, :kuvaus, NOW(), ' . $this->yliluokka . ') LIMIT 1 RETURNING id, luotu_pvm');
        $query->bindValue('nimi', $this->nimi, PDO::PARAM_STR);
        $query->bindValue('kuvaus', $this->kuvaus, PDO::PARAM_STR);
        $query->execute();
        $row = $query->fetch();
        $this->id = $row['id'];
        $this->luotupvm = $row['luotu_pvm'];
    }

    
    public function delete() {
        $query = DB::connection()->prepare('DELETE FROM Luokka WHERE id = :id');
        $query->execute(array('id'=>$this->id));
    }

    public function update() {
        $query = DB::connection()->prepare('UPDATE Luokka SET nimi = :nimi, kuvaus = :kuvaus, luokka_id = :luokka_id WHERE id = :id LIMIT 1');
        $query->execute(array('id'=> $this->id, 'nimi'=> $this->nimi, 'kuvaus'=>$this->kuvaus, 'luokka_id'=>$this->yliluokka));        
    }
    
       public static function getId(){
        return $this->id;
    }
    
    public static function getNimi(){
        return $this->nimi;
    }
    
}
