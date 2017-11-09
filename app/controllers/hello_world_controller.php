<?php

  class HelloWorldController extends BaseController{

    public static function index(){
      // make-metodi renderöi app/views-kansiossa sijaitsevia tiedostoja
   	  View::make('home.html');
             //echo 'Tämä on etusivu!';
    }

    public static function sandbox(){
      // Testaa koodiasi täällä
      //echo 'Hello World!';
      View::make('helloworld.html');
    }
    
    public static function login(){
        View::make('suunnitelmat/login.html');
    }
    
    public static function tehtavalista(){
        View::make('suunnitelmat/tehtavalista.html');
    }
    
    public static function yksitehtava(){
        View::make('suunnitelmat/tehtava1.html');
    }
    
    public static function luokat(){
        View::make('suunnitelmat/luokat.html');        
    }
    
    public static function yksiluokka(){
        View::make('suunnitelmat/luokka1.html');
    }
    
    public static function muokkaaluokka(){
        View::make('suunnitelmat/luokanmuokkaus.html');
    }
    
    public static function muokkaatehtava(){
        View::make('suunnitelmat/tehtavanmuokkaus.html');
    }
    
    
  }
