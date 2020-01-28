# Datenbank anlegen
CREATE DATABASE <database>

# Nutzer anlegen
CREATE USER <username>;

# Passwort setzen
SET password FOR <username> = PASSWORD('<password>');

# Rechte vergeben
GRANT ALL PRIVILEGES ON <database>.* TO dku IDENTIFIED BY '<password>';

# In Datenbank wechseln
USE <database>

# Ungültige Zeitstempel zulassen
SET SQL_MODE='ALLOW_INVALID_DATES';

#
# T A B E L L E N
#

# Berechtigungsrollen
CREATE TABLE Rolle (
	Id INT NOT NULL AUTO_INCREMENT,
    Bezeichnung VARCHAR(32) NOT NULL UNIQUE KEY,
    PRIMARY KEY (Id)
);

# Primäre-Nutzer-Tabelle
CREATE TABLE Nutzer (
	Id INT NOT NULL AUTO_INCREMENT,
    Benutzername VARCHAR(32) NOT NULL UNIQUE KEY,
    Vorname VARCHAR(32) NOT NULL,
    Nachname VARCHAR(32) NOT NULL,
    Email VARCHAR(254) NOT NULL UNIQUE KEY,
    Geburtsdatum DATE NOT NULL,
    Rolle_Id INT NOT NULL,
    Passwort CHAR(128),
    PRIMARY KEY (Id),
    FOREIGN KEY (Rolle_Id) REFERENCES Rolle(Id)
);

# Protokolltyp-Tabelle
CREATE TABLE Protokolltyp (
	Id INT NOT NULL AUTO_INCREMENT,
    Bezeichnung VARCHAR(32) UNIQUE KEY,
    PRIMARY KEY (Id)
);

# Zentrale-Protokollierungs-Tabelle
CREATE TABLE Protokoll (
	Zeitstempel TIMESTAMP NOT NULL,
    Nutzer_Id INT NOT NULL,
    Protokolltyp_Id INT NOT NULL,
    Info LONGTEXT NOT NULL,
    PRIMARY KEY (Zeitstempel),
    FOREIGN KEY (Nutzer_Id) REFERENCES Nutzer(Id),
    FOREIGN KEY (Protokolltyp_Id) REFERENCES Protokolltyp(Id)
);

# Lehrer-Tabelle
CREATE TABLE Lehrer (
	Id INT NOT NULL AUTO_INCREMENT,
    Kuerzel VARCHAR(8) NOT NULL UNIQUE KEY,
    PRIMARY KEY (Id),
    FOREIGN KEY (Id) REFERENCES Nutzer(Id) ON DELETE CASCADE
);

# Tabelle der Ausbildungsbetriebe
CREATE TABLE Betrieb (
	Id INT NOT NULL AUTO_INCREMENT,
    Name VARCHAR(32) NOT NULL UNIQUE KEY,
    PRIMARY KEY (Id)
);

# Schüler-Tabelle
CREATE TABLE Schueler (
	Id INT NOT NULL AUTO_INCREMENT,
    Klasse_Id INT NOT NULL,
    Betrieb_Id INT NOT NULL,
    PRIMARY KEY (Id),
    FOREIGN KEY (Id) REFERENCES Nutzer(Id) ON DELETE CASCADE,
    FOREIGN KEY (Betrieb_Id) REFERENCES Betrieb(Id)
);

# Klasse-Tabelle
CREATE TABLE Klasse (
	Id INT NOT NULL AUTO_INCREMENT,
    Kuerzel VARCHAR(8) UNIQUE KEY,
    Lehrer_Id INT NOT NULL,
    PRIMARY KEY (Id),
    FOREIGN KEY (Lehrer_Id) REFERENCES Lehrer(Id)
);

# ----
# Konrekte Datensätze des Klassenbuchs
# ---

# Raum-Tabelle
CREATE TABLE Raum (
	Id INT NOT NULL AUTO_INCREMENT,
    Gebaeude VARCHAR(32) NOT NULL,
    Etage VARCHAR(32) NOT NULL,
    Raum VARCHAR(32) NOT NULL,
    PRIMARY KEY (Id)
);

# Schulfächer-Tabelle
CREATE TABLE Fach (
	Id INT NOT NULL AUTO_INCREMENT,
    Kuerzel VARCHAR(8) NOT NULL UNIQUE KEY,
    Bezeichnung VARCHAR(32) NOT NULL,
    PRIMARY KEY (Id)
);

# Vorgefertigte Syntax zur Erstellung des Stundenplanes einer Klasse.
# Wichtig ist hierbei, dass jede Klasse eine eigene Tabelle bekommt!
CREATE TABLE Stundenplan_KLASSE_ID (
	Tag VARCHAR(10) NOT NULL,
    Stunde INT NOT NULL,
    Fach_Id INT NOT NULL,
    Lehrer_Id INT NOT NULL,
    Raum_Id INT NOT NULL,
    PRIMARY KEY (Tag, Stunde),
    FOREIGN KEY (Fach_Id) REFERENCES Fach(Id),
    FOREIGN KEY (Lehrer_Id) REFERENCES Lehrer(Id),
    FOREIGN KEY (Raum_Id) REFERENCES Raum(Id)
);

# Vorgefertigte Syntax zur Erstellung der Wochenansicht des Klassenbuchs von einer Klasse.
# Wichtig ist hierbei, dass jede Klasse eine eigene Tabelle bekommt!
CREATE TABLE Klassenbuch_KLASSE_ID_Woche (
	Block_Nr INT NOT NULL,
    Wochen_Nr INT NOT NULL,
    Datum_Von DATE NOT NULL,
    Datum_Bis DATE NOT NULL,
    KW INT NOT NULL,
    Signiert_KL_Id INT,
    Signiert_SL_Id INT,
    Fehlzeiten_Best_Id INT,
    PRIMARY KEY (Block_Nr, Wochen_NR),
    FOREIGN KEY (Signiert_KL_Id) REFERENCES Lehrer(Id),
    FOREIGN KEY (Signiert_SL_Id) REFERENCES Nutzer(Id),
    FOREIGN KEY (Fehlzeiten_Best_Id) REFERENCES Nutzer(Id)
);

# Vorgefertigte Syntax zur Erstellung der Tagesansicht des Klassenbuchs von einer Klasse.
# Wichtig ist hierbei, dass jede Klasse eine eigene Tabelle bekommt!
CREATE TABLE Klassenbuch_KLASSE_ID_Tag (
	Datum DATE NOT NULL,
    Stunde INT NOT NULL,
    Fach_Id_Soll INT NOT NULL,
    Fach_Id_Ist INT NOT NULL,
    Lehrer_Id INT NOT NULL,
    Thema VARCHAR(255),
    Hausaufgabe VARCHAR(255),
    Best_Lehrer_Id INT,
    PRIMARY KEY (Datum, Stunde),
    FOREIGN KEY (Fach_Id_Soll) REFERENCES Fach(Id),
    FOREIGN KEY (Fach_Id_Ist) REFERENCES Fach(Id),
    FOREIGN KEY (Lehrer_Id) REFERENCES Lehrer(Id),
    FOREIGN KEY (Best_Lehrer_Id) REFERENCES Lehrer(Id)
);

# Tabelle zur Speicherung sämtlicher Fehlzeit-Kategorien.
CREATE TABLE Fehlzeit_Typ (
	Id INT NOT NULL AUTO_INCREMENT,
    Bezeichnung VARCHAR(32) UNIQUE KEY,
    PRIMARY KEY (Id)
);

# Vorgefertigte Syntax zur Erstellung der Fehlzeiten einer Klasse.
# Wichtig ist hierbei, dass jede Klasse eine eigene Tabelle bekommt!
CREATE TABLE Klassenbuch_KLASSE_ID_Fehlzeit (
	Datum DATE NOT NULL,
    Stunde INT NOT NULL,
    Schueler_Id INT NOT NULL,
    Fehlzeit_Typ_Id INT NOT NULL,
    Infotext VARCHAR(255),
    PRIMARY KEY (Datum, Stunde, Schueler_Id),
    FOREIGN KEY (Schueler_Id) REFERENCES Schueler(Id),
    FOREIGN KEY (Fehlzeit_Typ_Id) REFERENCES Fehlzeit_Typ(Id)
);