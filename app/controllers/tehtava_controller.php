<?php

class TehtavaController extends BaseController{
    
    public static function tehtavat(){
        $tehtavat = Tehtava::kaikki();
        View::make('tehtava/tehtavat.html', array('tehtavat'=>$tehtavat));
    }
    
    public static function yksitehtava($id){
        $tehtava = Tehtava::find($id);
        
        View::make('tehtava/tehtava1.html', ['tehtava'=>$tehtava]);
    }
    
    public static function muokkaatehtava($id){
        $tehtava = Tehtava::find($id);
        $luokat = Luokka::kaikki();
        View::make('tehtava/tehtavanmuokkaus.html', ['tehtava'=>$tehtava], array('luokat'=>$luokat));
    }
    
    public static function uusitehtava(){
        $luokat = Luokka::kaikki();
        View::make('tehtava/uusitehtava.html', array('luokat'=>$luokat));
    }
    
    public static function talleta(){
        // POST-pyynnön muuttujat sijaitsevat $_POST nimisessä assosiaatiolistassa
        // esim lomakkeen 'nimi' kentän sisältö on $_POST['nimi'] 
        $params = $_POST;
        // Alustetaan uusi Tehtava-luokan olion käyttäjän syöttämillä arvoilla
        $tehtava = new Tehtava(array(
            'nimi'=>$params['nimi'],
            'kuvaus'=>$params['kuvaus'],
            'luokka'=>$params['luokka'],
            'deadline'=>$params['deadline'],
            'tarkeys'=>$params['tarkeys'],
            'luotupvm'=>new DateTime('now')            
        ));
        
        //Kint::dump($params);  ja kommentoi pois Redirect::to 
        
        //kutsutaan alustamamme Luokka-olion metodia save:
        $luokka->save();
        //ohjataan käyttäjä luokkan esittelysivulle tämän jälkeen:
        Redirect::to('/luokat/'. $luokka->id, array('message'=>'Luokka lisätty!'));
    }
    
}