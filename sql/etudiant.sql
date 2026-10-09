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
    nom           varchar(20)         not null,
    prenom        varchar(20)         not null,
    sexe          char(1)             not null default 'M',
    dateNaissance date                not null,
    idOption      char(1)             not null,
    photo         varchar(50)         null,

    constraint pk_etudiant
        primary key (id),

    constraint uq_etudiant_nom_prenom
        unique (nom, prenom),

    constraint ck_etudiant_sexe
        check (sexe in ('M', 'F')),

    constraint ck_etudiant_nom
        check (trim(nom) <> ''),

    constraint ck_etudiant_prenom
        check (trim(prenom) <> ''),

        constraint fk_etudiant_option
        foreign key (idOption) references options (id)
);



INSERT INTO etudiant (nom, prenom, sexe, dateNaissance, idOption, photo)
VALUES
    ('BALDE', 'Aissatou Loundou', 'F', '2006-12-11', 'A', 'balde_aissatou_loundou.jpg'),
    ('BOILET', 'Kameron', 'M', '2006-05-06', 'A', 'boilet_kameron.jpg'),
    ('BOULLY', 'Alexandre', 'M', '2006-12-21', 'B', 'boully_alexandre.jpg'),
    ('CAUET', 'Jules', 'M', '2003-06-16', 'A', 'cauet_jules.jpg'),
    ('CAZIN', 'Tom', 'M', '2006-06-05', 'B', 'cazin_tom.jpg'),
    ('DIANI', 'Ismael', 'M', '2004-10-13', 'B', 'diani_ismael.jpg'),
    ('DUMONT', 'Hugo', 'M', '2006-04-30', 'B', 'dumont_hugo.jpg'),
    ('DUPONT', 'Hervé', 'M', '2005-02-15', 'A', null),
    ('DUPRESSOIR', 'Mathieu', 'M', '2003-10-31', 'B', 'dupressoir_mathieu.jpg'),
    ('DUPUIS', 'Thomas', 'M', '2006-05-28', 'A', 'dupuis_thomas.jpg'),
    ('EBELLE MAKONGUE', 'Jean-pascal', 'M', '2003-06-14', 'A', 'ebelle_makongue_jean-pascal.jpg'),
    ('FOULON', 'Mathis', 'M', '2007-07-29', 'B', 'foulon_mathis.jpg'),
    ('GARNIER', 'Kyllian', 'M', '2006-11-20', 'B', 'garnier_kyllian.jpg'),
    ('HERNU', 'Valentin', 'M', '2006-03-15', 'B', 'hernu_valentin.jpg'),
    ('KARACA', 'Atilla', 'M', '2005-04-27', 'A', 'karaca_atilla.jpg'),
    ('LOEMBA', 'Guy-landry', 'M', '2006-03-07', 'A', 'loemba_guy-landry.jpg'),
    ('MARGOTIN', 'Paul', 'M', '2005-12-11', 'A', 'margotin_paul.jpg'),
    ('MERCIER', 'Alexi', 'M', '2005-01-17', 'B', 'mercier_alexi.jpg'),
    ('MERVILLE', 'Lucas', 'M', '2006-01-09', 'A', 'merville_lucas.jpg'),
    ('MORTELETTE', 'Clément', 'M', '2006-01-19', 'A', 'mortelette_clement.jpg'),
    ('NOUHI', 'Marwan', 'M', '2004-09-10', 'A', 'nouhi_marwan.jpg'),
    ('PAYET', 'Théo', 'M', '2005-09-09', 'A', 'payet_theo.jpg'),
    ('POINTIN', 'Samson', 'M', '2005-02-01', 'A', 'pointin_samson.jpg'),
    ('ROUSSELLE', 'Etienne', 'M', '2007-03-27', 'A', 'rousselle_etienne.jpg'),
    ('VASSEUR', 'Lorenzo', 'M', '2004-03-06', 'A', 'vasseur_lorenzo.jpg'),
    ('YILDIZ', 'Muhammedali', 'M', '2004-05-13', 'B', 'yildiz_muhammedali.jpg'),
    ('ZON', 'Jeremy', 'M', '2006-02-25', 'B', 'zon_jeremy.jpg');
