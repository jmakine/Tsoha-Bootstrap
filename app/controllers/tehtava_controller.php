<?php

class TehtavaController extends BaseController {

    public static function tehtavat() {
        self::check_logged_in();

        $options = array();

        $params = $_GET;
        if (isset($params['tarkeys'])) {
            $options['tarkeys'] = $params['tarkeys'];
        }
        if (isset($params['luokka'])) {
            $options['luokka'] = $params['luokka'];
        }
        $tehtavat = Tehtava::kaikki($options);

        $luokat = Luokka::kaikki();
        $tehtavalista = array();

        foreach ($tehtavat as $tehtava) {
            $luokka = Luokka::find($tehtava->luokka);
            if ($luokka != NULL) {
                $luokka_id = $luokka->id;
                $luokka_nimi = $luokka->nimi;
            } else {
                $luokka_id = NULL;
                $luokka_nimi = NULL;
            }

            $tehtavalista[] = array(
                'tehtava_nimi' => $tehtava->nimi,
                'tehtava_id' => $tehtava->id,
                'kuvaus' => $tehtava->kuvaus,
                'tarkeys' => $tehtava->tarkeys,
                'luokka_id' => $luokka_id,
                'luokka_nimi' => $luokka_nimi,
                'deadline' => $tehtava->deadline,
                'luotupvm' => $tehtava->luotupvm
            );
        }
        $pvm = (new \DateTime())->format('Y-m-d');

        View::make('tehtava/tehtavat.html', array('tehtavat' => $tehtavat, 'luokat' => $luokat, 'tehtavalista' => $tehtavalista, 'pvm' => $pvm));
    }

    public static function yksitehtava($id) {
        self::check_logged_in();
        $tehtava = Tehtava::find($id);
        $luokka_id = $tehtava->luokka;
        $luokka = Luokka::find($luokka_id);
        View::make('tehtava/tehtava1.html', array('tehtava' => $tehtava, 'luokka' => $luokka));
    }

    public static function muokkaatehtava($id) {
        self::check_logged_in();
        $tehtava = Tehtava::find($id);
        $luokat = Luokka::kaikki();
        $luokka = Luokka::find($tehtava->luokka);
        View::make('tehtava/tehtavanmuokkaus.html', array('attributes' => $tehtava, 'luokat' => $luokat, 'luokka' => $luokka));
    }

    public static function update($id) {
        self::check_logged_in();
        $params = $_POST;


        if ($params['luokka'] == '') {
            $params['luokka'] = NULL;
        }
        if ($params['deadline'] == '') {
            $params['deadline'] = NULL;
        }
        $attributes = array(
            'id' => $id,
            'nimi' => $params['nimi'],
            'deadline' => $params['deadline'],
            'kuvaus' => $params['kuvaus'],
            'tarkeys' => $params['tarkeys'],
            'luokka' => $params['luokka'],
        );
        $tehtava = new Tehtava($attributes);
        $errors = $tehtava->errors();

        //jos nimeä muutettu, katsotaan ettei ole jo olemassa:   
        if (Tehtava::find($id)->nimi != $params['nimi']) {
            $kaikki = Tehtava::kaikki(null);
            $kaikki_nimet = array();
            foreach ($kaikki as $yksi) {
                $kaikki_nimet[] = $yksi->nimi;
            }
            if (in_array($params['nimi'], $kaikki_nimet)) {
                $errors[] = 'Tämän niminen tehtävä on jo olemassa! Keksippä joku muu.';
            }
        }

        if (count($errors) > 0) {
            Redirect::to('/tehtavat/' . $tehtava->id . '/muokkaa', array('errors' => $errors, 'attributes' => $attributes));
        } else {
            $tehtava->update();
            Redirect::to('/tehtavat/' . $tehtava->id, array('message' => 'Tehtävää muokattu!'));
        }
    }

    public static function uusitehtava() {
        self::check_logged_in();
        $luokat = Luokka::kaikki();
        View::make('tehtava/uusitehtava.html', array('luokat' => $luokat));
    }

    public static function talleta() {
        self::check_logged_in();
        $params = $_POST;

        if ($params['luokka'] == '') {
            $params['luokka'] = NULL;
        }
        if ($params['deadline'] == '') {
            $params['deadline'] = NULL;
        }

        $nimi_on = array();
        $kaikki = Tehtava::kaikki(null);
        if (in_array($params['nimi'], array_column($kaikki, 'nimi'))) {
            $nimi_on[] = 'Tämän niminen tehtava on jo olemassa! Keksippä joku muu.';
        }

        $attributes = array(
            'nimi' => $params['nimi'],
            'kuvaus' => $params['kuvaus'],
            'luokka' => $params['luokka'],
            'deadline' => $params['deadline'],
            'tarkeys' => $params['tarkeys'],
        );

        $tehtava = new Tehtava($attributes);
        $errors1 = $tehtava->errors();

        $errors = array_merge($errors1, $nimi_on);

        if (count($errors) > 0) {
            View::make('/tehtava/uusitehtava.html', array('errors' => $errors, 'attributes' => $attributes));
        } else {
            $tehtava->save();
            Redirect::to('/tehtavat/' . $tehtava->id, array('message' => 'Tehtävä lisätty!'));
        }
    }

    public static function poista($id) {
        self::check_logged_in();
        $tehtava = new Tehtava(array('id' => $id));
        $tehtava->delete();
        Redirect::to('/tehtavat', array('message' => 'Tehtävä poistettu!'));
    }

}
