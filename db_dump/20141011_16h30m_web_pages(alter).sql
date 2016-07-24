-- #25 2014.10.11 16:30:00

ALTER TABLE `web_page` CHANGE `vote_count` `votes_count` INT(11) NULL DEFAULT NULL, CHANGE `vote` `votes` FLOAT NULL DEFAULT NULL;