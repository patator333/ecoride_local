DROP DATABASE IF EXISTS ecoride;
CREATE DATABASE ecoride;
USE ecoride;

CREATE TABLE type_compte (
    id_type_compte INT AUTO_INCREMENT PRIMARY KEY,
    nom_type VARCHAR(50) NOT NULL
);

CREATE TABLE role (
    id_role INT AUTO_INCREMENT PRIMARY KEY,
    nom_role VARCHAR(50) NOT NULL
);

CREATE TABLE compte (
    id_utilisateur INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(50) NOT NULL,
    mail VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    photo BLOB,
    actif BOOLEAN DEFAULT TRUE,
    date_creation DATE,
    dernier_acces DATE,
    credit INT DEFAULT 0,
    id_type_compte INT,
    id_role INT,
    FOREIGN KEY (id_type_compte) REFERENCES type_compte(id_type_compte),
    FOREIGN KEY (id_role) REFERENCES role(id_role)
);

CREATE TABLE preference (
    id_preference INT AUTO_INCREMENT PRIMARY KEY,
    fumeur BOOLEAN,
    animal BOOLEAN,
    remarques_particulieres VARCHAR(100),
    id_utilisateur INT UNIQUE,
    FOREIGN KEY (id_utilisateur) REFERENCES compte(id_utilisateur)
);

CREATE TABLE type_motorisation (
    id_type_motorisation INT AUTO_INCREMENT PRIMARY KEY,
    nom_type VARCHAR(50) NOT NULL
);

CREATE TABLE vehicule (
    id_vehicule INT AUTO_INCREMENT PRIMARY KEY,
    immatriculation VARCHAR(50) NOT NULL,
    date_de_premiere_immatriculation DATE,
    marque VARCHAR(50),
    modele VARCHAR(50),
    couleur VARCHAR(50),
    places_disponibles INT,
    id_utilisateur INT,
    id_type_motorisation INT,
    FOREIGN KEY (id_utilisateur) REFERENCES compte(id_utilisateur),
    FOREIGN KEY (id_type_motorisation) REFERENCES type_motorisation(id_type_motorisation)
);

CREATE TABLE covoiturage (
    id_covoiturage INT AUTO_INCREMENT PRIMARY KEY,
    date_depart DATE,
    heure_depart TIME,
    lieu_depart VARCHAR(50),
    date_arrivee DATE,
    heure_arrivee TIME,
    nombre_places INT,
    prix_par_personne DECIMAL(6,2),
    id_utilisateur INT,
    id_vehicule INT,
    FOREIGN KEY (id_utilisateur) REFERENCES compte(id_utilisateur),
    FOREIGN KEY (id_vehicule) REFERENCES vehicule(id_vehicule)
);

CREATE TABLE statistique (
    id_statistique INT AUTO_INCREMENT PRIMARY KEY,
    nombre_passager INT,
    credit_gagne_chauffeur DECIMAL(8,2),
    credit_gagne_plateforme DECIMAL(8,2),
    id_covoiturage INT UNIQUE,
    FOREIGN KEY (id_covoiturage) REFERENCES covoiturage(id_covoiturage)
);

CREATE TABLE statut_covoiturage (
    id_statut INT AUTO_INCREMENT PRIMARY KEY,
    statut VARCHAR(50),
    id_covoiturage INT UNIQUE,
    FOREIGN KEY (id_covoiturage) REFERENCES covoiturage(id_covoiturage)
);

CREATE TABLE avis (
    id_avis INT AUTO_INCREMENT PRIMARY KEY,
    date_avis DATE,
    statut_validation BOOLEAN,
    commentaire VARCHAR(255),
    id_covoiturage INT,
    id_utilisateur INT,
    FOREIGN KEY (id_covoiturage) REFERENCES covoiturage(id_covoiturage),
    FOREIGN KEY (id_utilisateur) REFERENCES compte(id_utilisateur)
);

CREATE TABLE note (
    id_note INT AUTO_INCREMENT PRIMARY KEY,
    valeur1 INT,
    valeur2 INT,
    valeur3 INT,
    valeur4 INT,
    valeur5 INT,
    id_covoiturage INT,
    id_utilisateur INT,
    FOREIGN KEY (id_covoiturage) REFERENCES covoiturage(id_covoiturage),
    FOREIGN KEY (id_utilisateur) REFERENCES compte(id_utilisateur)
);

CREATE TABLE reservation (
    id_utilisateur INT,
    id_covoiturage INT,
    date_reservation DATE,
    PRIMARY KEY (id_utilisateur, id_covoiturage),
    FOREIGN KEY (id_utilisateur) REFERENCES compte(id_utilisateur),
    FOREIGN KEY (id_covoiturage) REFERENCES covoiturage(id_covoiturage)
);

CREATE TABLE contact (
    id_contact INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL,
    commentaire TEXT NOT NULL,
    date_envoi DATETIME DEFAULT CURRENT_TIMESTAMP
);




INSERT INTO type_compte (nom_type)
VALUES 
('utilisateur'),
('employe'),
('administrateur');

INSERT INTO role (nom_role)
VALUES 
('passager'),
('chauffeur'),
('passager_chauffeur');

INSERT INTO type_motorisation (nom_type)
VALUES 
('essence'),
('diesel'),
('hybride'),
('electrique');

INSERT INTO compte (nom, mail, password, date_creation, credit, id_type_compte, id_role)
VALUES
('Alice Dupont', 'alice@ecoride.fr', 'pwdAlice', '2025-10-01', 50, 1, 1),
('Bob Martin', 'bob@ecoride.fr', 'pwdBob', '2025-09-15', 120, 1, 2),
('Claire Leroy', 'claire@ecoride.fr', 'pwdClaire', '2025-08-20', 200, 3, 3),
('David Morel', 'david@ecoride.fr', 'pwdDavid', '2025-10-05', 80, 2, 2),
('Emma Lopez', 'emma@ecoride.fr', 'pwdEmma', '2025-09-10', 0, 1, 1);

INSERT INTO vehicule (immatriculation, date_de_premiere_immatriculation, marque, modele, couleur, places_disponibles, id_utilisateur, id_type_motorisation)
VALUES
('AB-123-CD', '2022-06-10', 'Peugeot', '208', 'bleu', 4, 2, 1),
('BC-456-DE', '2021-03-15', 'Renault', 'Clio', 'rouge', 4, 4, 2),
('CD-789-EF', '2020-11-22', 'Tesla', 'Model 3', 'noir', 5, 3, 4),
('DE-321-FG', '2019-08-09', 'Toyota', 'Yaris', 'blanche', 4, 1, 3),
('EF-654-GH', '2023-01-30', 'Volkswagen', 'Golf', 'gris', 5, 5, 1);

INSERT INTO covoiturage (date_depart, heure_depart, lieu_depart, date_arrivee, heure_arrivee, nombre_places, prix_par_personne, id_utilisateur, id_vehicule)
VALUES
('2025-10-15', '08:00:00', 'Paris', '2025-10-15', '11:00:00', 3, 15, 2, 1),
('2025-10-16', '09:30:00', 'Lyon', '2025-10-16', '12:30:00', 2, 20, 4, 2),
('2025-10-17', '07:15:00', 'Marseille', '2025-10-17', '10:00:00', 4, 10, 3, 3),
('2025-10-18', '13:00:00', 'Toulouse', '2025-10-18', '17:00:00', 3, 25, 1, 4),
('2025-10-19', '06:45:00', 'Bordeaux', '2025-10-19', '10:15:00', 2, 18, 5, 5);

INSERT INTO statistique (nombre_passager, credit_gagne_chauffeur, credit_gagne_plateforme, id_covoiturage)
VALUES
(3, 45, 5, 1),
(2, 36, 4, 2),
(4, 40, 4, 3),
(3, 75, 8, 4),
(2, 36, 4, 5);

INSERT INTO statut_covoiturage (statut, id_covoiturage)
VALUES
('prévu', 1),
('en_cours', 2),
('terminé', 3),
('annulé', 4),
('prévu', 5);

INSERT INTO avis (date_avis, statut_validation, commentaire, id_covoiturage, id_utilisateur)
VALUES
('2025-10-16', TRUE, 'Trajet agréable et ponctuel.', 1, 1),
('2025-10-17', TRUE, 'Chauffeur sympathique !', 2, 5),
('2025-10-18', FALSE, 'Un peu de retard.', 3, 4),
('2025-10-19', TRUE, 'Super expérience, je recommande.', 4, 3),
('2025-10-19', TRUE, 'Voiture propre et confortable.', 5, 2);

INSERT INTO note (valeur1, valeur2, valeur3, valeur4, valeur5, id_covoiturage, id_utilisateur)
VALUES
(5, 5, 4, 5, 5, 1, 1),
(4, 4, 5, 4, 5, 2, 5),
(3, 4, 3, 4, 3, 3, 4),
(5, 5, 5, 5, 5, 4, 3),
(4, 4, 4, 5, 4, 5, 2);

INSERT INTO preference (fumeur, animal, remarques_particulieres, id_utilisateur)
VALUES
(FALSE, TRUE, 'Aime écouter de la musique', 1),
(FALSE, FALSE, 'Préfère le silence', 2),
(TRUE, FALSE, 'Ok pour discuter', 3),
(FALSE, TRUE, 'Ne veut pas d’animaux', 4),
(TRUE, TRUE, 'Très sociable', 5);

INSERT INTO reservation (id_utilisateur, id_covoiturage, date_reservation)
VALUES
(1, 1, '2025-10-10'),
(5, 2, '2025-10-11'),
(4, 3, '2025-10-12'),
(3, 4, '2025-10-13'),
(2, 5, '2025-10-14');
