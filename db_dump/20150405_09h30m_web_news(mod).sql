-- #30 2015.04.05 09:30:00

ALTER TABLE `web_news` CHANGE `vote_count` `votes_count` INT(11) NULL DEFAULT NULL, CHANGE `vote` `votes` FLOAT NULL DEFAULT NULL;