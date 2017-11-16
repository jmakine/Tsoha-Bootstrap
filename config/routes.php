<?php

// etusivu
$routes->get('/', function() {
    HelloWorldController::index();
});

//tästä luo tunnukset sivu...ei vielä tehty
$routes->get('/login', function() {
    HelloWorldController::login();
});

//debugging
$routes->get('/hiekkalaatikko', function() {
    HelloWorldController::sandbox();
});

//(sorttaus suunnan mukaan tulee määrittää täältä..)
//tehtavien listaussivu:
$routes->get('/tehtavat', function() {
    TehtavaController::tehtavat();
});

//tehtävän muokkaussivu
$routes->get('/tehtavat/muokkaa/:id', function($id) {
    TehtavaController::muokkaatehtava($id);
});

//uuden tehtävän lisäyssivu
$routes->get('/tehtavat/lisaa', function(){
TehtavaController::uusitehtava();
});

//tehtävän esittelysivu
$routes->get('/tehtavat/:id', function($id) {
    TehtavaController::yksitehtava($id);
});

//tehtavan lisäys kantaan
$routes->post('/tehtavat', function(){
TehtavaController::talleta();
});

//LUOKKAAN LIITTYVÄT:

//luokan lisääminen kantaan
$routes->post('/luokat', function() {
    LuokkaController::talleta();
});

//luokkien listaussivu
$routes->get('/luokat', function() {
    LuokkaController::luokat();
});

//luokan muokkaussivu
$routes->get('/luokat/muokkaa/:id', function($id) {
    LuokkaController::muokkaaluokka($id);
});

//luokan lisäämissivu
$routes->get('/luokat/lisaa', function(){
LuokkaController::lisaaluokka();
});

//luokan esittelysivu
$routes->get('/luokat/:id', function($id) {
    LuokkaController::yksiluokka($id);
});