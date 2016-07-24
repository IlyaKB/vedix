-- Generate users
INSERT INTO `sec_user` (`id`, `group_id`, `login`, `fullname`, `email`, `about`, `country`, `subject_id`, `district`, `locality`, `iswoman`, `bornyear`, `bornmonth`, `bornday`, `phone`, `contacts`, `pw`, `regdate`, `csid`, `isbanned`, `status`, `restpwcode`, `photo`, `social_network`, `social_profile`, `lastdate`, `quanqr`, `quanqr_sa`, `quan_sessions`) VALUES
(101, 99, 'tester101', NULL, '*', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, md5('1'), '2016-07-22 12:00:00', NULL, NULL, 1, NULL, NULL, NULL, NULL, '2016-07-22 12:00:00', 0, 0, 0),
(102, 99, 'tester102', NULL, '*', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, md5('1'), '2016-07-22 12:00:00', NULL, NULL, 1, NULL, NULL, NULL, NULL, '2016-07-22 12:00:00', 0, 0, 0),
(103, 99, 'tester103', NULL, '*', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, md5('1'), '2016-07-22 12:00:00', NULL, NULL, 1, NULL, NULL, NULL, NULL, '2016-07-22 12:00:00', 0, 0, 0);

