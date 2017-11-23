<?php

//require 'app/models/Luokka.php'; ei tarvita, koska Composer.json tiedostossa on classmap taulukossa app/models.
class HelloWorldController extends BaseController {

    public static function index() {
        // make-metodi renderöi app/views-kansiossa sijaitsevia tiedostoja
        View::make('home.html');
        //echo 'Tämä on etusivu!';
    }

    public static function sandbox() {
        //testataan toimiiko validiointi nimelle 6-20 merkkiä ja pvm:lle (pv 1-31, kk 1-12, vvvv ?)
        $task = new Tehtava(array(
            'nimi' => 'job',
            'luotupvm' => '01.01.2018',
            'deadline' => '13.13.2017'));
        $errors = $task->errors();
        Kint::dump($errors);

        //testataan myös Luokka-olion nimen validiointi 4-20 merkkiä
        $testiluokka = new Luokka(array(
            'nimi' => '123',
            'kuvaus' => 'hommia'));
        $errors1 = $testiluokka->errors();
        Kint::dump($errors1);


        $luokka = Luokka::find(1);
        $aliluokat=$luokka->aliluokat();
        Kint::dump($aliluokat);
        
        $luokat = Luokka::kaikki();
        $tehtava = Tehtava::find(1);
        $tehtavat = Tehtava::kaikki();
        // Kint-luokan dump-metodi tulostaa muuttujan arvon
        Kint::dump($luokat);
        Kint::dump($luokka);
        Kint::dump($tehtava);
        Kint::dump($tehtavat);
    }

    public static function login() {
        View::make('suunnitelmat/login.html');
    }

    public static function tehtavalista() {
        View::make('suunnitelmat/tehtavalista.html');
    }

    public static function yksitehtava() {
        View::make('suunnitelmat/tehtava1.html');
    }

    public static function luokat() {
        View::make('suunnitelmat/luokat.html');
    }

    public static function yksiluokka() {
        View::make('suunnitelmat/luokka1.html');
    }

    public static function muokkaaluokka() {
        View::make('suunnitelmat/luokanmuokkaus.html');
    }

    public static function muokkaatehtava() {
        View::make('suunnitelmat/tehtavanmuokkaus.html');
    }

}
