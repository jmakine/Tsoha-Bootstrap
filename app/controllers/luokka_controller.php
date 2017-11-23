<?php

class LuokkaController extends BaseController {

    public static function luokat() {
        self::check_logged_in();
        // Haetaan kaikki luokat tietokannasta. Välitetään $luokat muuttuja, tämä on käytössä luokat.html:ssä
        $luokat = Luokka::kaikki();
        // Renderöidään views/luokka kansiossa sijaitseva tiedosto luokat.html muuttujan $luokat datalla
        View::make('luokka/luokat.html', array('luokat' => $luokat));
    }

//nyt näkymässä luokat.html on käytössä luokat -niminen taulukko tietokantani luokista

    public static function yksiluokka($id) {
        self::check_logged_in();
        $luokka = Luokka::find($id);

        $aliluokat = $luokka->aliluokat(); //palauttaa listan aliluokista
        //$kaikkitehtavat = array();

        //$aliluokan_tehtavat = $aliluokat->tehtavat();      

        $luokka_id = $luokka->yliluokka;
        $yliluokka = Luokka::find($luokka_id);
        View::make('luokka/luokka1.html', array('tehtava' => $luokka->tehtavat, 'yliluokka' => $yliluokka, 'luokka' => $luokka, 'aliluokat' => $aliluokat));//, 'aliluokantehtavat' => $aliluokan_tehtavat));
    }

    //
    public static function aliluokat($id) {
        self::check_logged_in();
        $aliluokat = Luokka::aliluokat($id);
        View::make('luokka/luokat.html', array('luokat' => $aliluokat));
    }

    public static function muokkaaluokka($id) {
        self::check_logged_in();
        $luokka = Luokka::find($id);
        $luokat = Luokka::kaikki();
        View::make('luokka/luokanmuokkaus.html', array('attributes' => $luokka, 'luokat' => $luokat));
    }

    public static function uusiluokka() {
        self::check_logged_in();
        $luokat = Luokka::kaikki();
        View::make('luokka/uusiluokka.html', array('luokat' => $luokat));
    }

    public static function talleta() {
        self::check_logged_in();
        // POST-pyynnön muuttujat sijaitsevat $_POST nimisessä assosiaatiolistassa
        // esim lomakkeen 'nimi' kentän sisältö on $_POST['nimi'] 
        $params = $_POST;
        // Alustetaan uusi Luokka-luokan olion käyttäjän syöttämillä arvoilla
        $attributes = array(
            'nimi' => $params['nimi'],
            'kuvaus' => $params['kuvaus'],
            'yliluokka' => $params['yliluokka']
        );

        $luokka = new Luokka($attributes);
        $errors = $luokka->errors();

        if (count($errors == 0)) {
            $luokka->save();
            //ohjataan käyttäjä luokan esittelysivulle tämän jälkeen ja kerrotaan että tallennus onnistui:
            Redirect::to('/luokat/' . $luokka->id, array('message' => 'Luokka lisätty!'));
        } else {
            View::make('/luokka/uusiluokka.html', array('errors' => $errors, 'attributes' => $attributes));
        }
    }

    public static function update($id) {
        self::check_logged_in();
        $params = $_POST;
        $attributes = array(
            'id' => $id,
            'nimi' => $params['nimi'],
            'luokka_id' => $params['yliluokkaa'],
            'kuvaus' => $params['kuvaus'],
        );
        $luokka = new Luokka($attributes);
        $errors = $luokka->errors();

        if (count($errors) > 0) {
            View::make('luokka/luokanmuokkaus.html', array('errors' => $errors, 'attributes' => $attributes));
        } else {
            // Kutsutaan alustetun olion update-metodia, joka päivittää luokan tiedot tietokannassa
            $luokka->update();
            Redirect::to('/luokat/' . $luokka->id, array('message' => 'Luokkaa muokattu!'));
        }
    }

    public static function poista($id) {
        self::check_logged_in();
        //alustetaan Luokka-olio annetulla $id:llä
        $luokka = new Luokka(array('id' => $id));
        $luokka->delete();
        Redirect::to('/luokat', array('message' => 'Luokka poistettu!'));
    }

}
