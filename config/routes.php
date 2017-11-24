<?php

// etusivu ja kirjautumislomake
$routes->get('/', function() {
    HelloWorldController::index();
});

//sisäänkirjautuminen
$routes->post('/', function() {
    KayttajaController::login();
});

//uloskirjautuminen
$routes->post('/out', function(){
KayttajaController::logout();
});

//debugging
$routes->get('/hiekkalaatikko', function() {
    HelloWorldController::sandbox();
});


//tehtavien listaussivu:
$routes->get('/tehtavat', function() {
    TehtavaController::tehtavat();
});

//tehtavan lisäys kantaan
$routes->post('/tehtavat', function() {
    TehtavaController::talleta();
});

//uuden tehtävän lisäyssivu
$routes->get('/tehtavat/lisaa', function() {
    TehtavaController::uusitehtava();
});

//tehtävän esittelysivu
$routes->get('/tehtavat/:id', function($id) {
    TehtavaController::yksitehtava($id);
});

//tehtävän muokkaussivun esittäminen
$routes->get('/tehtavat/:id/muokkaa', function($id) {
    TehtavaController::muokkaatehtava($id);
});

//tehtavan muokkauksen lisäys kantaan
$routes->post('/tehtavat/:id/muokkaa', function($id) {
    TehtavaController::update($id);
});

//poista tehtävä
$routes->post('/tehtavat/:id/poista', function($id) {
    TehtavaController::poista($id);
});

//LUOKKAAN LIITTYVÄT:
//<form method="post" action="{{base_path}}/luokat"> :uusiluokka.html:ssä
//tallentaa ja ohjaa luokan esittelysivulle tai sitten ohjaa luokan esittelysivulle virheilmoituksen kera tallentamatta: 
//luokan lisääminen kantaan
$routes->post('/luokat', function() {
    LuokkaController::talleta();
});

//luokkien listaussivu
$routes->get('/luokat', function() {
    LuokkaController::luokat();
});

//luokan lisäämissivu
$routes->get('/luokat/lisaa', function() {
    LuokkaController::uusiluokka();
});

//luokan esittelysivu
$routes->get('/luokat/:id', function($id) {
    LuokkaController::yksiluokka($id);
});

//luokan muokkaussivu
$routes->get('/luokat/:id/muokkaa', function($id) {
    LuokkaController::muokkaaluokka($id);
});

//luokan muokkauksen lisäys kantaan
$routes->post('/luokat/:id/muokkaa', function($id) {
    LuokkaController::update($id);
});

//luokan poistaminen
$routes->post('/luokat/:id/poista', function($id) {
    LuokkaController::poista($id);
});