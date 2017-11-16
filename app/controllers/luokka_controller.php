<?php

class LuokkaController extends BaseController {

    public static function luokat() {
        // Haetaan kaikki luokat tietokannasta. Välitetään $luokat muuttuja, tämä on käytössä luokat.html:ssä
        $luokat = Luokka::kaikki();
        // Renderöidään views/luokka kansiossa sijaitseva tiedosto luokat.html muuttujan $luokat datalla
        View::make('luokka/luokat.html', array('luokat' => $luokat));
    }   //nyt näkymässä luokat.html on käytössä luokat -niminen taulukko tietokantani luokista

    public static function yksiluokka($id) {
        $luokka = Luokka::find($id);
        View::make('luokka/luokka1.html', ['luokka' => $luokka]);
    }
    
    public static function aliluokat($id) {
        $aliluokat = Luokka::aliluokat($id);
        View::make('luokka/luokat.html', array('luokat' => $aliluokat));
    }
    
    public static function muokkaaluokka($id){
        $luokka = Luokka::find($id);
        View::make('luokka/luokanmuokkaus.html', ['luokka'=> $luokka]);
    }
    
    public static function lisaaluokka(){
        $luokat = Luokka::kaikki();
        View::make('luokka/uusiluokka.html', array('luokat'=> $luokat));
    }
    
    public static function talleta(){
        // POST-pyynnön muuttujat sijaitsevat $_POST nimisessä assosiaatiolistassa
        // esim lomakkeen 'nimi' kentän sisältö on $_POST['nimi'] 
        $params = $_POST;
        // Alustetaan uusi Luokka-luokan olion käyttäjän syöttämillä arvoilla
        $luokka = new Luokka(array(
            'nimi'=>$params['nimi'],
            'kuvaus'=>$params['kuvaus'],
            'yliluokka'=>$params['yliluokka'],
            'luotupvm'=>new DateTime('now')            
        ));
        
        //Kint::dump($params);  ja kommentoi pois Redirect::to 
        
        //kutsutaan alustamamme Luokka-olion metodia save:
        $luokka->save();
        //ohjataan käyttäjä luokkan esittelysivulle tämän jälkeen:
        Redirect::to('/luokat/'. $luokka->id, array('message'=>'Luokka lisätty!'));
    }

}