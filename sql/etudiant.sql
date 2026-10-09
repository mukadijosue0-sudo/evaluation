SET default_storage_engine = InnoDb;

Set foreign_key_checks = 0;

use evaluation;

drop table if exists etudiant;
drop table if exists options;

create table options
(
    id           char(1)     not null,
    libelleCourt char(4)     not null,
    libelleLong  varchar(50) not null,
    primary key (id)
);

insert into options(id, libelleCourt, libelleLong)
values ('A', 'SISR', 'Solutions d''Infrastructure, Systèmes et Réseaux'),
       ('B', 'SLAM', 'Solutions Logicielles et Applications Métiers'),
       ('T', 'TC', 'Tronc commun premier semestre');

create table etudiant
(
    id            int auto_increment not null,
    nom           varchar(20)        not null,
    prenom        varchar(20)        NOT NULL,
    sexe	      char(1)			 NOT NULL default 'M',
    dateNaissance date               not null,
    idOption      char(1)            NOT NULL,
    photo         varchar(50)        null,
    primary key (id),
    unique (nom, prenom),
    foreign key (idOption) references options (id)
);


INSERT INTO etudiant (nom, prenom, sexe, dateNaissance, idOption, photo)
VALUES
    ('BALDE', 'Aissatou Loundou', 'F', '2006-12-11', 'A', 'BALDE Aissatou Loundou.jpg'),
    ('BOILET', 'Kameron', 'M', '2006-05-06', 'A', 'BOILET Kameron.jpg'),
    ('BOULLY', 'Alexandre', 'M', '2006-12-21', 'B', 'BOULLY Alexandre.jpg'),
    ('CAUET', 'Jules', 'M', '2003-06-16', 'A', 'CAUET Jules.jpg'),
    ('CAZIN', 'Tom', 'M', '2006-06-05', 'B', 'CAZIN Tom.jpg'),
    ('DIANI', 'Ismael', 'M', '2004-10-13', 'B', 'DIANI Ismael.jpg'),
    ('DUMONT', 'Hugo', 'M', '2006-04-30', 'B', 'DUMONT Hugo.jpg'),
    ('DUPRESSOIR', 'Mathieu', 'M', '2003-10-31', 'B', 'DUPRESSOIR Mathieu.jpg'),
    ('DUPUIS', 'Thomas', 'M', '2006-05-28', 'A', 'DUPUIS Thomas.jpg'),
    ('EBELLE MAKONGUE', 'Jean-pascal', 'M', '2003-06-14', 'A', 'EBELLE MAKONGUE Jean-pascal.jpg'),
    ('FOULON', 'Mathis', 'M', '2007-07-29', 'B', 'FOULON Mathis.jpg'),
    ('GARNIER', 'Kyllian', 'M', '2006-11-20', 'B', 'GARNIER Kyllian.jpg'),
    ('HERNU', 'Valentin', 'M', '2006-03-15', 'B', 'HERNU Valentin.jpg'),
    ('KARACA', 'Atilla', 'M', '2005-04-27', 'A', 'KARACA Atilla.jpg'),
    ('LOEMBA', 'Guy-landry', 'M', '2006-03-07', 'A', null),
    ('MARGOTIN', 'Paul', 'M', '2005-12-11', 'A', 'MARGOTIN Paul.jpg'),
    ('MERCIER', 'Alexi', 'M', '2005-01-17', 'B', 'MERCIER Alexi.jpg'),
    ('MERVILLE', 'Lucas', 'M', '2006-01-09', 'A', 'MERVILLE Lucas.jpg'),
    ('MORTELETTE', 'Clément', 'M', '2006-01-19', 'A', 'MORTELETTE Clément.jpg'),
    ('NOUHI', 'Marwan', 'M', '2004-09-10', 'A', 'NOUHI Marwan.jpg'),
    ('PAYET', 'Théo', 'M', '2005-09-09', 'A', 'PAYET Théo.jpg'),
    ('POINTIN', 'Samson', 'M', '2005-02-01', 'A', 'POINTIN Samson.jpg'),
    ('ROUSSELLE', 'Etienne', 'M', '2007-03-27', 'A', 'ROUSSELLE Etienne.jpg'),
    ('VASSEUR', 'Lorenzo', 'M', '2004-03-06', 'A', 'VASSEUR Lorenzo.jpg'),
    ('YILDIZ', 'Muhammedali', 'M', '2004-05-13', 'B', 'YILDIZ Muhammedali.jpg'),
    ('ZON', 'Jeremy', 'M', '2006-02-25', 'B', 'ZON Jeremy.jpg');
