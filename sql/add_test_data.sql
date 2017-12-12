-- Lisää INSERT INTO lauseet tähän tiedostoon
--Kayttaja -taulun testi-data
INSERT INTO Kayttaja(tunnus, salasana) VALUES ('jenni', 'penninenn');

--Luokka -taulun testi-data
INSERT INTO Luokka(nimi, luotu_pvm, kuvaus, luokka_id, kayttaja_id) 
VALUES ('Arkiaskareet', NOW(), 'Tänne perus kotihommia, mm. siivoiluja, ostoksia ym. muistettavaa.', NULL, 1), -- id = 1
        ('Muutto', NOW(), 'Muuttoon liittyvät tehtävät.', NULL, 1), -- id = 2
        ('Luokka3', NOW(), 'Sisältää luokan4', NULL, 1), --id = 3
        ('Luokka4', NOW(), 'Sisältyy luokkaan 3', 3, 1); -- id = 4

--Tehtava -taulun testi-data
INSERT INTO Tehtava (nimi, deadline, luotu_pvm, kuvaus, tarkeys, luokka_id, kayttaja_id) 
VALUES ('Maksa laskut', '15.11.2017', NOW(),'sähkö, netti, vuokra, visa,...', 'korkea', 1, 1),
        ('Pese ikkunat', NULL, NOW(), NULL, 'matala', 1, 1),
        ('Tee muuttoilmoitus', '30.11.2017', NOW(), NULL, 'korkea', 2, 1),
        ('Siirrä sähkösopimus', '25.11.2017', NOW(),'Nykyinen sopimus Fortumilla, kilpailuta ennen kun vaihdat.', 'neutraali', 2, 1),
        ('Varaa hammaslääkäri', NULL, NOW(),'09-123456789', NULL, NULL, 1),
        ('Käy kaupassa', '3.12.2017', NOW(), NULL ,NULL, NULL, 1),
        ('Testi1', '20.11.2017', NOW(), NULL, 'korkea', 3, 1),
        ('Testi2', NULL, NOW(),'123', 'matala', 3, 1),
        ('Testi3', '12.12.2017', NOW(),'diibadaa', NULL, 4, 1);
