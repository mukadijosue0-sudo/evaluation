-- Ce trigger s'exécute avant l'ajout d'un étudiant.
-- Il met le nom en majuscules et vérifie que l'étudiant
-- a un âge compris entre 17 et 25 ans.
-- Si l'âge n'est pas respecté, l'insertion est annulée.

drop trigger if exists avantAjoutEtudiant;

create trigger avantAjoutEtudiant
    before insert
    on etudiant
    for each row
begin
    set new.nom = upper(new.nom);

    if new.dateNaissance not between
        CURDATE() - INTERVAL 25 YEAR
        and CURDATE() - INTERVAL 17 YEAR then

        SIGNAL sqlstate '45000'
            set message_text = 'Cette personne n\'a pas l\'âge requis pour être étudiant';
    end if;
end;

-- autre solution pour le test
-- if TIMESTAMPDIFF(YEAR, new.dateNaissance, CURDATE()) not between 17 and 25 then

drop trigger if exists avantMajEtudiant;

create trigger avantMajEtudiant
    before update
    on etudiant
    for each row
begin
    -- l'id n'est pas modifiable
    if new.id <> old.id then
        SIGNAL sqlstate '45000'
            set message_text = 'L\'id d\'un étudiant ne peut pas être modifié';
    end if;

    if new.nom != old.nom then
        set new.nom = upper(new.nom);
    end if;
    if new.dateNaissance <> old.dateNaissance then
        if new.dateNaissance not between
            CURDATE() - INTERVAL 25 YEAR
            and CURDATE() - INTERVAL 17 YEAR then

            SIGNAL sqlstate '45000'
                set message_text = 'Cette personne n\'a pas l\'âge requis pour être étudiant';
        end if;
    end if;
end;