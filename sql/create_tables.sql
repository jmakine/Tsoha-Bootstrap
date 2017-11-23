-- Lisää CREATE TABLE lauseet tähän tiedostoon
CREATE TABLE Kayttaja(
id SERIAL PRIMARY KEY,
tunnus varchar(15) UNIQUE NOT NULL,
salasana varchar(15) NOT NULL CHECK(length(salasana)>8)
);

CREATE TABLE Luokka(
id SERIAL PRIMARY KEY,
nimi varchar(30) UNIQUE NOT NULL,
luotu_pvm date NOT NULL,
kuvaus varchar(400),
kayttaja_id INTEGER REFERENCES Kayttaja(id), --NOT NULL? --viiteavain Kayttaja -tauluun
luokka_id INTEGER REFERENCES Luokka(id) --viiteavain Luokka -tauluun (yliluokkaan)
);

CREATE TABLE Tehtava(
id SERIAL PRIMARY KEY,
nimi varchar(30) NOT NULL,
deadline date,
--valmis boolean DEFAULT FALSE,
luotu_pvm date NOT NULL,
kuvaus varchar(400),
tarkeys varchar(10),
kayttaja_id INTEGER REFERENCES Kayttaja(id) ON DELETE CASCADE, --viiteavain Kayttaja -tauluun
luokka_id INTEGER REFERENCES Luokka(id) ON DELETE SET NULL --viiteavain Luokka -tauluun (yliluokkaan)
--UNIQUE KEY avain (nimi, luokka_id) NOT NULL --ehto, että tehtävä-luokka yhdistelmä on unique, siis ei ole kahta saman nimistä tehtävää samassa luokassa?
);

--liitostaulu luokan ja tehtävien välillä
--CREATE TABLE Luokantehtavat(
--luokka_id INTEGER REFERENCES Luokka(id) NOT NULL, --viiteavain Luokka -tauluun
--tehtava_id INTEGER REFERENCES Tehtava(id) PRIMARY KEY --viiteavain Tehtava-tauluun
--);

--liitostaulu luokan ja luokan välillä
--CREATE TABLE Luokanluokat(
--aliluokka INTEGER REFERENCES Luokka(id) PRIMARY KEY, --viiteavain Luokka -tauluun (yliluokkaan)
--yliluokka INTEGER REFERENCES Luokka(id) NOT NULL --viiteavain Luokka -tauluun (yliluokkaan)
--);

