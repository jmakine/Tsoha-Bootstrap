-- Lisää INSERT INTO lauseet tähän tiedostoon
--Kayttaja -taulun testi-data
INSERT INTO Kayttaja(tunnus, salasana) VALUES ('jenni', 'penninenn');

--Tehtava -taulun testi-data
INSERT INTO Tehtava (nimi, deadline, luotu_pvm, kuvaus, tarkeys ) VALUES ('Maksa laskut', '15.11.2017', NOW(),'sähkö, netti, vuokra, visa,...', 'korkea');
INSERT INTO Tehtava (nimi, luotu_pvm, tarkeys ) VALUES ('Pese ikkunat', NOW(), 'matala');
INSERT INTO Tehtava (nimi, deadline, luotu_pvm, tarkeys ) VALUES ('Tee muuttoilmoitus', '30.11.2017', NOW(), 'korkea');
INSERT INTO Tehtava (nimi, deadline, luotu_pvm, kuvaus, tarkeys ) VALUES ('Siirrä sähkösopimus', '25.11.2017', NOW(),'Nykyinen sopimus Fortumilla, kilpailuta ennen kun vaihdat.', 'neutraali');
INSERT INTO Tehtava (nimi, luotu_pvm, kuvaus ) VALUES ('Varaa hammaslääkäri', NOW(),'09-123456789');

--Luokka -taulun testi-data
INSERT INTO Luokka(nimi, luotu_pvm, kuvaus) VALUES ('Arkiaskareet', NOW(), 'Tänne perus kotihommia, mm. siivoiluja, ostoksia ym. muistettavaa.');
INSERT INTO Luokka(nimi, luotu_pvm, kuvaus) VALUES ('Muutto', NOW(), 'Muuttoon liittyvät tehtävät.');

--Luokantehtavat -taulun testi-data
INSERT INTO Luokantehtavat(luokka_id, tehtava_id) VALUES ();

--LuokanLuokat -taulun testi-data. Ensimmäinen Luokka_id = yliluokka (PRIMARY KEY)
INSERT INTO Luokanluokat(luokka_id, luokka_id) VALUES ();

