<?php

class TehtavaController extends BaseController {

    public static function tehtavat() {
        self::check_logged_in();
        $tehtavat = Tehtava::kaikki();
        View::make('tehtava/tehtavat.html', array('tehtavat' => $tehtavat));
    }

    public static function yksitehtava($id) {
        self::check_logged_in();
        $tehtava = Tehtava::find($id);
        $luokka_id = $tehtava->luokka;
        $luokka = Luokka::find($luokka_id);
        View::make('tehtava/tehtava1.html', array('tehtava' => $tehtava, 'luokka'=>$luokka));
    }

    public static function muokkaatehtava($id) {
        self::check_logged_in();
        $tehtava = Tehtava::find($id);
        $luokat = Luokka::kaikki();
        $luokka = Luokka::find($tehtava->luokka);
        View::make('tehtava/tehtavanmuokkaus.html', array('attributes' => $tehtava, 'luokat' => $luokat, 'luokka'=>$luokka));
    }

    public static function update($id) {
        self::check_logged_in();
        $params = $_POST;
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

        if (count($errors) > 0) {
            Redirect::to('/tehtavat/' . $tehtava->id . '/muokkaa', array('errors' => $errors, 'attributes' => $attributes));
        } else {
            // Kutsutaan alustetun olion update-metodia, joka päivittää tehtavan tiedot tietokannassa
            $tehtava->update();
            //ja ohjataan tehtävän esittelysivulle:
            //get->('/tehtavat/:id', ...) -> ohjaa TehtavaControllerin yksitehtava($id) metodille.
            //--> esittää /tehtava1.html sivun ko. tehtavalle, annetulla messagella
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
        // POST-pyynnön muuttujat sijaitsevat $_POST nimisessä assosiaatiolistassa
        // esim lomakkeen 'nimi' kentän sisältö on $_POST['nimi'] 
        $params = $_POST;
        // Alustetaan uusi Tehtava-luokan olion käyttäjän syöttämillä arvoilla
        $attributes = array(
            'nimi' => $params['nimi'],
            'kuvaus' => $params['kuvaus'],
            'luokka' => $params['luokka'],
            'deadline' => $params['deadline'],
            'tarkeys' => $params['tarkeys'],
        );
        $tehtava = new Tehtava($attributes);
        $errors = $tehtava->errors();

        if (count($errors == 0)) {
            $tehtava->save();
            Redirect::to('/tehtavat/' . $tehtava->id, array('message' => 'Tehtävä lisätty!')); 
            //reiteissä get->tehtavat/:id näyttää näkymän tehtava1.html
        } else {
            View::make('/tehtavat/uusitehtava.html', array('errors' => $errors, 'attributes' => $attributes));
        }
        //Kint::dump($params);  ja kommentoi pois Redirect::to
    }
    
    public static function poista($id){
        self::check_logged_in();
        $tehtava = new Tehtava(array('id'=>$id));
        $tehtava->delete();
        Redirect::to('/tehtavat', array('message'=>'Tehtävä poistettu!'));
    }

}
