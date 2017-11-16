<?php

class Luokka extends BaseModel {

    public $id, $nimi, $kuvaus, $yliluokka, $luotupvm, $aliluokat, $tehtavat, $aliluokka_count, $tehtavat_count;

//$aliluokat on lista ko. luokan aliluokista. Sivulle, missä on lista luokista, tulee näkyviin luokasta sen aliluokkien lkm. 
//Sitä numeroa klikkaamalla aukeaa lista näistä aliluokista. Sama juttu tehtävien kanssa, näkyviin tulee luokan ja sen aliluokkien sisältämien tehtävien lkm yhteensä.
//Sitä lukua klikkaamalla pääsee tehtävälistaan, jossa nämä näkyy.

    public function __construct($attributes) {
        parent::__construct($attributes);
        //tänne $this->aliluokat = aliluokat($this->id); tjn..
    }

    public static function kaikki() {
// Alustetaan kysely tietokantayhteydellämme (prepare=PDO:n metodi)
        $kysely = DB::connection()->prepare('SELECT * FROM Luokka');
// Suoritetaan kysely. (PDOStatement = excecute ja fetchAll)
        $kysely->execute();
// Kyselyn suorittamisen jälkeen kyselyn tuottamiin riveihin pääsee käsiksi kutsumalla fetchAll-metodia, 
// joka palauttaa rivit taulukkona assosiaatiolistoja, jonka avaimina toimivat sarakkeiden nimet ja arvoina niiden sisältö.
        $rows = $kysely->fetchAll();
        $luokat = array();

// Käydään kyselyn tuottamat rivit läpi
        foreach ($rows as $row) {
            $tehtavat = Luokka::tehtavat($row['id']);
            $aliluokat = Luokka::aliluokat($row['id']);
            $luokat[] = new Luokka(array(
                'id' => $row['id'],
                'nimi' => $row['nimi'],
                'kuvaus' => $row['kuvaus'],
                'yliluokka' => $row['luokka_id'],
                'luotupvm' => $row['luotu_pvm'],
                'tehtavat' =>  $tehtavat,
                'tehtavat_count' => count($tehtavat),
                'aliluokat' => $aliluokat,
                'aliluokka_count' => count($aliluokat)
            ));
        }
        return $luokat;
    }

    //palauttaa uuden taulukon luokkia, joiden yliluokka on tarkasteltava luokka
    //NYT VAIN LÄHIMMÄT ALILUOKAT, mutta jos/kun halutaan lista, missä on lukan kaikki aliluokat, myös siis aliluokkien aliluokat, pitää tehdä jotain...
    public static function aliluokat($id) {
        $kysely = DB::connection()->prepare('SELECT * FROM Luokka WHERE luokka_id = :id');
        //execute-metodissa upotettavien muuttujien arvot assosiaatiolistana, jonka avaimina toimivat muuttujien nimet ja arvoina niitä vastaavat arvot. 
        $kysely->execute(array('id' => $id));
        $rows = $kysely->fetchAll(); //vai fetch() ?
        $aliluokat = array();

        foreach ($rows as $row) {
            $tehtavat = Luokka::tehtavat($row['id']);
            $aliluokan_aliluokat = Luokka::aliluokat($row['id']);
            $aliluokat[] = new Luokka(array(
                'id' => $row['id'],
                'nimi' => $row['nimi'],
                'kuvaus' => $row['kuvaus'],
                'yliluokka' => $row['luokka_id'],
                'luotupvm' => $row['luotu_pvm'],
                'tehtavat' => $tehtavat,
                'aliluokat' => $aliluokan_aliluokat,
                'aliluokkt_count' => count($aliluokan_aliluokat)
            ));
            return $aliluokat;
        }
    }

    //nyt hakee vain suoraan ryhmän alla olevat tehtävät, mutta miten ryhmän aliryhmien tehtävät myös?
    // ja voiko tässä luoda Tehtava-taulukon? require jotain?

    public static function tehtavat($id) {
        $kysely = DB::connection()->prepare('SELECT * FROM Tehtava WHERE luokka_id = :id');
        $kysely->execute(array('id' => $id));
        $rows = $kysely->fetchAll(); //palauttaa assosiaatiolistan kaikista kyselyn tuottamista riveistä

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
        $row = $kysely->fetch(); //palauttaa assosiaatiolistan vain kyselyn tuottamasta ensimmäisestä rivistä

        if ($row) {
            $tehtavat = Luokka::tehtavat($row['id']);
            $aliluokat = Luokka::aliluokat($row['id']);
            $luokka = new Luokka(array(
                'id' => $row['id'],
                'nimi' => $row['nimi'],
                'kuvaus' => $row['kuvaus'],
                'yliluokka' => $row['luokka_id'],
                'luotupvm' => $row['luotu_pvm'],
                'tehtavat' =>  $tehtavat,
                'tehtavat_count' => count($tehtavat),
                'aliluokat' => $aliluokat,
                'aliluokka_count' => count($aliluokat)
            ));

            return $luokka;
        }
        return null;
    }

    public function save() {
        // Lisätään RETURNING id tietokantakyselymme loppuun, niin saamme lisätyn rivin id-sarakkeen arvon
        $query = DB::connection()->prepare('INSERT INTO Luokka (nimi, luotu_pvm, kuvaus, luokka_id) VALUES (:nimi, :luotupvm, :kuvaus, :yliluokka) RETURNING id');
        // Muistathan, että olion attribuuttiin pääse syntaksilla $this->attribuutin_nimi
        $query->execute(array('nimi' => $this->nimi, 'luotu_pvm' => $this->luotupvm, 'kuvaus' => $this->kuvaus, 'luokka_id' => $this->yliluokka));
        // Haetaan kyselyn tuottama rivi, joka sisältää lisätyn rivin id-sarakkeen arvon
        $row = $query->fetch();
        // Asetetaan lisätyn rivin id-sarakkeen arvo oliomme id-attribuutin arvoksi
        $this->id = $row['id'];
    }

}
