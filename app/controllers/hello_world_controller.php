<?php

//require 'app/models/Luokka.php'; ei tarvita, koska Composer.json tiedostossa on classmap taulukossa app/models.
class HelloWorldController extends BaseController {

    public static function index() {
        // make-metodi renderöi app/views-kansiossa sijaitsevia tiedostoja
        View::make('home.html');
    }

    public static function sandbox() {
        $user = $_SESSION['user'];
        Kint::dump($user);
        
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
        $aliluokat=Luokka::aliluokat($luokka);
        Kint::dump($aliluokat);
        Kint::dump($aliluokat->id);
        
        $tehtavat = Luokka::tehtavat($luokka->id);
        Kint::dump($tehtavat);
    }

}
