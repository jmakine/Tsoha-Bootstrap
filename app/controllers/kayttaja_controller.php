<?php

class KayttajaController extends BaseController {
    
    public static function login(){
        $params = $_POST;
        $user = User::authenticate($params['tunnus'], $params['salasana']);

    if(!$user){
      View::make('home.html', array('error' => 'Väärä käyttäjätunnus tai salasana!', 'tunnus' => $params['tunnus']));
    }else{
        //session_start() on kutsuttu valmiiksi index.php:ssä
      $_SESSION['user'] = $user->id;
      Redirect::to('/tehtavat', array('tervehdys' => 'Hei ' ));//. $user->name . '! Mistähän aloittaisit tänään?'));
    }    
    }
    
    public static function logout(){
    $_SESSION['user'] = null;
    Redirect::to('/', array('message' => 'Olet kirjautunut ulos!'));
  }
    
}
