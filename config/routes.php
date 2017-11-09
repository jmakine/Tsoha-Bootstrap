<?php

  $routes->get('/', function() {
    HelloWorldController::index();
  });

  $routes->get('/hiekkalaatikko', function() {
    HelloWorldController::sandbox();
  });
  
  $routes->get('/tehtavat', function(){
  HelloWorldController::tehtavalista();
  });
  
  $routes->get('/login', function(){
  HelloWorldController::login();
  });

  $routes->get('/tehtavat/1', function(){
      HelloWorldController::yksitehtava(); 
  });
  
  $routes->get('/luokat', function(){
  HelloWorldController::luokat();
  });
  
  $routes->get('/luokat/1', function(){
  HelloWorldController::yksiluokka();
  });
  
  $routes->get('/tehtavat/1/muokkaa', function(){
  HelloWorldController::muokkaatehtava();
  });
  
  $routes->get('/luokat/1/muokkaa', function(){
  HelloWorldController::muokkaaluokka();
  });