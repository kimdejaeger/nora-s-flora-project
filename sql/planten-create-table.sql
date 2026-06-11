-- DDL script voor MariaDB zonder ENUM

CREATE TABLE `planten` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,

    `naam` VARCHAR(255) NOT NULL,
    `latijnse_naam` VARCHAR(255) DEFAULT NULL,

    `Waterbehoefte` VARCHAR(50) DEFAULT NULL,
    `Lichtbehoefte` VARCHAR(50) DEFAULT NULL,

    `Groeihoogte_cm` INT DEFAULT NULL,
    `verkoopprijs_eur` DECIMAL(10,2) DEFAULT NULL,

    `standplaats` VARCHAR(100) DEFAULT NULL,
    `voorraad` INT DEFAULT 0,

    `bloeitijd` VARCHAR(50) DEFAULT NULL,
    `kleur` VARCHAR(50) DEFAULT NULL,

    `huisdier_vriendelijk` VARCHAR(10) DEFAULT NULL,

    `overview_image` VARCHAR(255) DEFAULT NULL,
    `additional_image1` VARCHAR(255) DEFAULT NULL,
    `additional_image2` VARCHAR(255) DEFAULT NULL,

    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    PRIMARY KEY (`id`)
);
--ENGINE=InnoDB
--DEFAULT CHARSET=utf8mb4
--COLLATE=utf8mb4_unicode_ci;