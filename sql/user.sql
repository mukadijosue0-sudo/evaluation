drop user if exists evaluation@localhost;

create user evaluation@localhost identified by 'BNHbpsyaqi0Xnnf7OyPh!';

grant select on evaluation.* to evaluation@localhost;
grant all privileges on evaluation.* to evaluation@localhost;

flush privileges;

