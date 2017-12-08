<?php

class LuokkaController extends BaseController {

    public static function luokat() {
        self::check_logged_in();
        $luokat = Luokka::kaikki();
        $luokkalista = array();

        foreach ($luokat as $luokka) {
            $yliluokka = Luokka::find($luokka->yliluokka);
            if ($yliluokka != null) {
                $yliluokka_nimi = $yliluokka->nimi;
                $yliluokka_id = $yliluokka->id;
            } else {
                $yliluokka_nimi = null;
                $yliluokka_id = null;
            }

            $luokkalista[] = array(
                'id' => $luokka->id,
                'nimi' => $luokka->nimi,
                'kuvaus' => $luokka->kuvaus,
                'luotupvm' => $luokka->luotupvm,
                'yliluokka_id' => $yliluokka_id,
                'yliluokka_nimi' => $yliluokka_nimi
            );
        }
        View::make('luokka/luokat.html', array('luokat' => $luokat, 'luokkalista' => $luokkalista));
    }

    public static function yksiluokka($id) {
        self::check_logged_in();
        $luokka = Luokka::find($id);

        $aliluokat = Luokka::aliluokat($id); //palauttaa listan luokan (ali)luokka-olioita

        $yliluokka = $luokka->yliluokka;
        if ($yliluokka != NULL) {
            $yliluokka = Luokka::find($luokka->yliluokka);
        } //yliluokka on id

        View::make('luokka/luokka1.html', array('tehtava' => $luokka->tehtavat, 'yliluokka' => $yliluokka, 'luokka' => $luokka, 'aliluokat' => $aliluokat));
    }

    public static function muokkaaluokka($id) {
        self::check_logged_in();
        $luokka = Luokka::find($id);
        $luokat = Luokka::kaikki();
        $yliluokka = Luokka::find($luokka->yliluokka);
        //jatkokehitys: luokkalistaan ei anneta valittavaksi luokan aliluokkia, eli luokka ei voi sisältyä luokkaan, jonka se sisältää
        View::make('luokka/luokanmuokkaus.html', array('yliluokka' => $yliluokka, 'attributes' => $luokka, 'luokat' => $luokat));
    }

    public static function uusiluokka() {
        self::check_logged_in();
        $luokat = Luokka::kaikki();
        View::make('luokka/uusiluokka.html', array('luokat' => $luokat));
    }

    public static function talleta() {
        self::check_logged_in();
        $params = $_POST;
        $yliluokka = $params['yliluokka'];
        $nimi_on = array();

        if ($params['yliluokka'] == '') {
            $yliluokka = NULL;
        }

        $attributes = array(
            'nimi' => $params['nimi'],
            'kuvaus' => $params['kuvaus'],
            'yliluokka' => $yliluokka
        );

        if (in_array($params['nimi'], array_column(Luokka::kaikki(), 'nimi'))) {
            $nimi_on[] = 'Tämän niminen luokka on jo olemassa! Keksippä joku muu.';
        }

        $luokka = new Luokka($attributes);
        $error = $luokka->errors();
        $errors = array_merge($error, $nimi_on);
       
        if (count($errors == 0)) {
            $luokka->save();
            Redirect::to('/luokat/' . $luokka->id, array('message' => 'Luokka lisätty!'));
        } else {
            View::make('/luokka/uusiluokka.html', array('errors' => $errors, 'attributes' => $attributes));
        }
    }

    public static function update($id) {
        self::check_logged_in();
        $params = $_POST;
        $yliluokka = $params['yliluokka'];

        if ($params['yliluokka'] == '') {
            $yliluokka = NULL;
        }

        $attributes = array(
            'id' => $id,
            'nimi' => $params['nimi'],
            'yliluokka' => $yliluokka,
            'kuvaus' => $params['kuvaus']
        );

        $luokka = new Luokka($attributes);
        $errors = $luokka->errors();

        //jos nimeä muutettu, katsotaan ettei ole jo olemassa:        
        if (Luokka::find($id)->nimi != $params['nimi']) {
            $kaikki = Luokka::kaikki();
            $kaikki_nimet = array();
            foreach ($kaikki as $yksi) {
                $kaikki_nimet[] = $yksi->nimi;
            }
            if (in_array($params['nimi'], $kaikki_nimet)) {
                $errors[] = 'Tämän niminen luokka on jo olemassa! Keksippä joku muu.';
            }
        }

        if (count($errors) > 0) {
            View::make('luokka/luokanmuokkaus.html', array('errors' => $errors, 'attributes' => $attributes));
        } else {
            $luokka->update();
            Redirect::to('/luokat/' . $luokka->id, array('message' => 'Luokkaa muokattu!'));
        }
    }

    public static function poista($id) {
        self::check_logged_in();
        //alustetaan Luokka-olio annetulla $id:llä
        $luokka = new Luokka(array('id' => $id));
        //jos luokka ei sisällä muita luokkia, sen voi poistaa, muuten ohjataan käyttäjä luokkien listaussivulle virheilmoituksen kera
        //luokan sisältäviltä tehtäviltä poistuu luokkatieto, mutta tehtävä ei poistu
        if (Luokka::aliluokat($luokka->id) == null) {
            $luokka->delete();
            Redirect::to('/luokat', array('message' => 'Luokka poistettu!'));
        } else {
            Redirect::to('/luokat', array('message' => 'Et voi poistaa luokkaa, jolla on aliluokkia!'));
        }
    }

}
