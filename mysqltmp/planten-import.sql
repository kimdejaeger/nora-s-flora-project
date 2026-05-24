LOAD DATA LOCAL INFILE 'planten_met_afbeeldingen.csv'
INTO TABLE planten
CHARACTER SET utf8mb4
FIELDS TERMINATED BY ';'
LINES TERMINATED BY '\n'
IGNORE 1 ROWS
(
    naam,
    latijnse_naam,
    Waterbehoefte,
    Lichtbehoefte,
    Groeihoogte_cm,
    verkoopprijs_eur,
    standplaats,
    voorraad,
    bloeitijd,
    kleur,
    huisdier_vriendelijk,
    overview_image,
    additional_image1,
    additional_image2
);