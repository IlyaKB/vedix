-- #21 2014.06.13 11:30:00

ALTER TABLE `sec_user` ADD UNIQUE `socnetlogin` (`social_network`,`login`);