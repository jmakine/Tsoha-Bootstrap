<?php

class LuokkaController extends BaseController {

    public static function luokat() {
        self::check_logged_in();
        $luokat = Luokka::kaikki();
        View::make('luokka/luokat.html', array('luokat' => $luokat));
    }

    public static function yksiluokka($id) {
        self::check_logged_in();
        $luokka = Luokka::find($id);

        $aliluokat = Luokka::aliluokat($id); //palauttaa listan luokan aliluokista (luokka-olioita)
        
        $yliluokka = null;
        if($luokka->yliluokka != NULL){
        $yliluokka = Luokka::find($luokka->yliluokka);
        } //yliluokka on id
        
        View::make('luokka/luokka1.html', array('tehtava' => $luokka->tehtavat, 'yliluokka' => $yliluokka, 'luokka' => $luokka, 'aliluokat' => $aliluokat));
    }

    public static function muokkaaluokka($id) {
        self::check_logged_in();
        $luokka = Luokka::find($id);
        $luokat = Luokka::kaikki();
        $yliluokka = Luokka::find($luokka->yliluokka);
        View::make('luokka/luokanmuokkaus.html', array('yliluokka'=>$yliluokka, 'attributes' => $luokka, 'luokat' => $luokat));
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
        
        if($params['yliluokka'] == ''){
            $yliluokka = NULL;
        }
        
        $attributes = array(
            'nimi' => $params['nimi'],
            'kuvaus' => $params['kuvaus'],
            'yliluokka' => $yliluokka
        );
        
        if(in_array($params['nimi'], array_column(Luokka::kaikki(), 'nimi'))){
            $nimi_on[] = 'Tämän niminen luokka on jo olemassa! Keksippä joku muu.';  
        }
        
        $luokka = new Luokka($attributes);        
       $error = $luokka->errors();
       $errors = array_merge($error, $nimi_on);
                
        if (count($errors > 0)) {
                        View::make('/luokka/uusiluokka.html', array('errors' => $errors, 'attributes' => $attributes));
        } else {
            $luokka->save();
            Redirect::to('/luokat/' . $luokka->id, array('message' => 'Luokka lisätty!'));
        }
    }

    public static function update($id) {
        self::check_logged_in();
        $params = $_POST;
        $yliluokka = $params['yliluokka'];
        
        if ($params['yliluokka'] == ''){
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
        $luokka->delete();
        Redirect::to('/luokat', array('message' => 'Luokka poistettu!'));
    }

}
