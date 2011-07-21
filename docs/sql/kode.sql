SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Database: `kode`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin_users`
--

DROP TABLE IF EXISTS `admin_users`;
CREATE TABLE `admin_users` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `firstname` varchar(20) NOT NULL,
  `lastname` varchar(40) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(40) NOT NULL,
  `password_valid` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `email` varchar(340) NOT NULL,
  `phone_number` varchar(20) NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '0',
  `last_login` timestamp NULL DEFAULT NULL,
  `last_password_update` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_username` (`username`),
  KEY `idx_email` (`email`(255))
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

--
-- Dumping data for table `admin_users`
--

INSERT INTO `admin_users` (`id`, `firstname`, `lastname`, `username`, `password`, `password_valid`, `email`, `phone_number`, `active`, `last_login`, `last_password_update`) VALUES (`1`,`Kode`,`Admin`,`admin`,`6c64e8dcebc17e3d08546a355b52817f63eb6fe2`,`0`,`root@localhost`,``,`1`,``,``);

-- --------------------------------------------------------

--
-- Table structure for table `admin_users_groups`
--

DROP TABLE IF EXISTS `admin_users_groups`;
CREATE TABLE `admin_users_groups` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `group_id` int(11) unsigned NOT NULL,
  `user_id` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_group_id` (`group_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

--
-- Dumping data for table `admin_users_groups`
--

INSERT INTO `admin_users_groups` (`id`, `group_id`, `user_id`) VALUES (1, 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `assignments`
--

DROP TABLE IF EXISTS `assignments`;
CREATE TABLE `assignments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `assignment_title` varchar(50) NOT NULL,
  `assignment_created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;


--
-- Table structure for table `assignment_class`
--

DROP TABLE IF EXISTS `assignment_class`;
CREATE TABLE `assignment_class` (
  `assignment_id` int(11) NOT NULL,
  `class_id` int(11) NOT NULL,
  `assignment_start_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `assignment_due_date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `assignment_end_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`assignment_id`,`class_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `attachments`
--

DROP TABLE IF EXISTS `attachments`;
CREATE TABLE `attachments` (
  `file_id` int(11) NOT NULL AUTO_INCREMENT,
  `file_name` varchar(50) NOT NULL,
  `file_unique` varchar(50) NOT NULL,
  `file_path` varchar(1024) NOT NULL,
  `file_mime` char(20) NOT NULL,
  PRIMARY KEY (`file_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `class`
--

DROP TABLE IF EXISTS `class`;
CREATE TABLE `class` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `course_id` int(11) NOT NULL,
  `class_name` varchar(50) NOT NULL,
  `class_term` enum('1','2') NOT NULL,
  `class_year` varchar(2) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

--
-- Table structure for table `class_assistant`
--

DROP TABLE IF EXISTS `class_assistant`;
CREATE TABLE `class_assistant` (
  `class_id` int(11) NOT NULL,
  `admin_user_id` int(11) NOT NULL,
  PRIMARY KEY (`class_id`,`admin_user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Table structure for table `course`
--

DROP TABLE IF EXISTS `course`;
CREATE TABLE `course` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `course_name` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `course_name` (`course_name`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

--
-- Table structure for table `flags`
--

DROP TABLE IF EXISTS `flags`;
CREATE TABLE `flags` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `description` varchar(255) NOT NULL,
  `active_on_dev` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `active_on_prod` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

--
-- Dumping data for table `flags`
--

INSERT INTO `flags` (`id`, `name`, `description`, `active_on_dev`, `active_on_prod`) VALUES
(1, 'admin-groups', 'Controller for user groups', 1, 1),
(2, 'admin-index', 'Default entry point in the application', 1, 1),
(3, 'admin-privileges', 'Allows the users to perform CRUD operations on privileges', 1, 1),
(4, 'admin-account', 'Allows user to manage their profile data', 1, 1),
(5, 'admin-system', 'Allow the admins to manage critical info, users, groups, permissions, etc.', 1, 1),
(6, 'admin-users', 'Allows the users to perform CRUD operations on other users', 1, 1),
(7, 'frontend-index', 'Default entry point in the application', 1, 1),
(8, 'admin-flags', 'Allows user to manage the flags', 1, 1),
(9, 'admin-course', 'Course declration', 1, 1),
(10, 'admin-class', 'Class controller', 1, 1),
(11, 'admin-assignment', 'Assignment controller', 1, 1),
(12, 'admin-problem', 'Problems', 1, 1),
(13, 'admin-submission', 'Submissions in admin', 1, 1),
(45, 'frontend-account', 'Frontend account configuration and login/logout', 1, 1),
(46, 'frontend-assignment', 'Assignment listing in frontend', 1, 1),
(47, 'frontend-problem', 'Problem listing in frontend', 1, 1),
(48, 'frontend-answer', 'Answering problems in front end', 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `flippers`
--

DROP TABLE IF EXISTS `flippers`;
CREATE TABLE `flippers` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `group_id` int(11) unsigned NOT NULL,
  `flag_id` int(11) unsigned NOT NULL,
  `privilege_id` int(11) unsigned NOT NULL,
  `allow` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

--
-- Dumping data for table `flippers`
--

INSERT INTO `flippers` (`id`, `group_id`, `flag_id`, `privilege_id`, `allow`) VALUES
(1, 1, 1, 1, 1),
(3, 1, 1, 3, 1),
(4, 1, 1, 4, 1),
(5, 1, 1, 5, 1),
(6, 1, 2, 6, 1),
(7, 1, 3, 7, 1),
(8, 1, 3, 8, 1),
(9, 1, 3, 9, 1),
(10, 1, 3, 10, 1),
(11, 1, 4, 11, 1),
(12, 1, 4, 12, 1),
(13, 1, 4, 13, 1),
(14, 1, 4, 14, 1),
(15, 1, 4, 15, 1),
(16, 1, 5, 16, 1),
(17, 1, 5, 17, 1),
(18, 1, 6, 18, 1),
(19, 1, 6, 19, 1),
(20, 1, 6, 20, 1),
(21, 1, 6, 21, 1),
(22, 1, 6, 22, 1),
(23, 1, 7, 23, 1),
(24, 2, 4, 11, 1),
(25, 2, 4, 12, 1),
(26, 2, 4, 13, 1),
(27, 2, 4, 14, 1),
(28, 2, 4, 15, 1),
(29, 2, 7, 23, 1),
(30, 3, 4, 14, 1),
(31, 3, 4, 14, 1),
(32, 3, 7, 23, 1),
(33, 3, 4, 14, 1),
(34, 3, 7, 23, 1),
(35, 3, 4, 14, 1),
(36, 3, 7, 23, 1),
(37, 1, 1, 1, 1),
(38, 1, 1, 3, 1),
(39, 1, 1, 4, 1),
(40, 1, 1, 5, 1),
(41, 1, 2, 6, 1),
(42, 1, 3, 7, 1),
(43, 1, 3, 8, 1),
(44, 1, 3, 9, 1),
(45, 1, 3, 10, 1),
(46, 1, 4, 11, 1),
(47, 1, 4, 12, 1),
(48, 1, 4, 13, 1),
(49, 1, 4, 14, 1),
(50, 1, 4, 15, 1),
(51, 1, 5, 16, 1),
(52, 1, 5, 17, 1),
(53, 1, 6, 18, 1),
(54, 1, 6, 19, 1),
(55, 1, 6, 20, 1),
(56, 1, 6, 21, 1),
(57, 1, 6, 22, 1),
(58, 1, 7, 23, 1),
(59, 1, 5, 284, 1),
(60, 1, 9, 285, 1),
(61, 1, 9, 286, 1),
(62, 1, 9, 287, 1),
(63, 1, 6, 288, 1),
(64, 1, 6, 289, 1),
(65, 1, 6, 290, 1),
(66, 1, 6, 291, 1),
(67, 1, 10, 292, 1),
(68, 1, 10, 293, 1),
(69, 1, 10, 294, 1),
(70, 1, 10, 295, 1),
(71, 1, 11, 300, 1),
(72, 1, 12, 301, 1),
(73, 1, 12, 302, 1),
(74, 1, 12, 303, 1),
(75, 1, 12, 304, 1),
(76, 1, 11, 305, 1),
(77, 1, 12, 306, 1),
(78, 1, 10, 307, 1),
(79, 4, 45, 308, 1),
(80, 3, 45, 309, 1),
(81, 3, 45, 311, 1),
(82, 1, 6, 312, 1),
(83, 1, 6, 313, 1),
(84, 1, 6, 314, 1),
(85, 1, 6, 315, 1),
(86, 1, 6, 316, 1),
(87, 3, 45, 317, 1),
(88, 3, 45, 318, 1),
(89, 3, 46, 319, 1),
(90, 3, 46, 320, 1),
(91, 3, 46, 321, 1),
(92, 3, 47, 322, 1),
(93, 3, 47, 323, 1),
(94, 3, 45, 324, 1),
(95, 3, 47, 325, 1),
(96, 2, 4, 11, 1),
(97, 2, 4, 12, 1),
(98, 2, 4, 13, 1),
(99, 2, 4, 14, 1),
(100, 2, 4, 15, 1),
(101, 2, 7, 23, 1),
(102, 2, 12, 301, 1),
(103, 2, 12, 302, 1),
(104, 2, 12, 303, 1),
(105, 2, 12, 304, 1),
(106, 2, 12, 306, 1),
(107, 3, 47, 330, 1),
(108, 3, 47, 331, 1),
(109, 3, 47, 332, 3),
(110, 3, 4, 14, 1),
(111, 3, 7, 23, 1),
(112, 3, 45, 309, 1),
(113, 3, 45, 311, 1),
(114, 3, 45, 317, 1),
(115, 3, 45, 318, 1),
(116, 3, 45, 324, 1),
(117, 3, 46, 319, 1),
(118, 3, 46, 320, 1),
(119, 3, 46, 321, 1),
(120, 3, 47, 322, 1),
(121, 3, 47, 323, 1),
(122, 3, 47, 325, 1),
(123, 3, 47, 330, 1),
(124, 3, 47, 331, 1),
(125, 3, 47, 332, 1),
(126, 3, 48, 333, 1),
(127, 3, 48, 334, 1),
(128, 3, 48, 335, 1),
(129, 3, 48, 336, 1),
(130, 3, 48, 337, 1);

-- --------------------------------------------------------

--
-- Table structure for table `groups`
--

DROP TABLE IF EXISTS `groups`;
CREATE TABLE `groups` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(25) DEFAULT NULL,
  `parent_id` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  KEY `idx_name` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

--
-- Dumping data for table `groups`
--

INSERT INTO `groups` (`id`, `name`, `parent_id`) VALUES
(1, 'professor', 0),
(2, 'assistants', 0),
(3, 'students', 0),
(4, 'guests', 0);

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

DROP TABLE IF EXISTS `notifications`;
CREATE TABLE `notifications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `notification` varchar(500) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `privileges`
--

DROP TABLE IF EXISTS `privileges`;
CREATE TABLE `privileges` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `flag_id` varchar(20) NOT NULL,
  `description` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`,`flag_id`),
  KEY `idx_resource_id` (`flag_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

--
-- Dumping data for table `privileges`
--

INSERT INTO `privileges` (`id`, `name`, `flag_id`, `description`) VALUES
(1, 'index', '1', 'Allows the user to view all the user groups registered\nin the application'),
(2, 'add', '1', 'Allows the user to add another user group in the\napplication'),
(3, 'edit', '1', 'Edits an existing user group'),
(4, 'delete', '1', 'Allows the user to delete an existing user group. All the users attached to\nthis group *WILL NOT* be deleted, they will just lose all'),
(5, 'flippers', '1', 'Allows the user to manage individual permissions for each\nuser group'),
(7, 'index', '3', 'Allows the user to view all the permissions registered\nin the application'),
(8, 'add', '3', 'Allows the user to add another privilege in the application'),
(9, 'edit', '3', 'Edits an existing privilege'),
(10, 'delete', '3', 'Allows the user to delete an existing privilege. All the acl''s related to\nthis privilege will be removed'),
(11, 'index', '4', 'Allows users to see their dashboards'),
(12, 'edit', '4', 'Allows the users to update their profiles'),
(13, 'change-password', '4', 'Allows users to change their passwords'),
(14, 'login', '4', 'Allows users to log into the application'),
(15, 'logout', '4', 'Allows users to log out of the application'),
(16, 'index', '5', 'Controller''s entry point'),
(17, 'example', '5', 'Theme example page'),
(18, 'index', '6', 'Allows users to see all other users that are registered in\nthe application'),
(19, 'add', '6', 'Allows users to add new users in the application\n(should be reserved for administrators)'),
(20, 'edit', '6', 'Allows users to edit another users'' data\n(should be reserved for administrators)'),
(21, 'view', '6', 'Allows users to see other users'' profiles'),
(22, 'delete', '6', 'Allows users to logically delete other users\n(should be reserved for administrators)'),
(23, 'index', '7', 'Controller''s entry point'),
(25, 'index', '8', 'Allows the user to view all the flags registered in the application'),
(48, 'toogle-prod', '8', 'Change the active status of a flag on production'),
(49, 'toogle-dev', '8', 'Change the active status of a flag on development'),
(115, 'toogleprod', '8', 'Change the active status of a flag on production'),
(116, 'toogledev', '8', 'Change the active status of a flag on development'),
(117, 'add', '8', 'Updates flag and flippers'),
(123, 'index', '11', 'Allows the user to view all the user groups registered\nin the application'),
(146, 'add', '11', 'Allows to add new category'),
(147, 'delete', '11', 'Delete category'),
(148, 'tooglepublish', '11', 'Change publish state'),
(149, 'edit', '11', 'Edit an exiting content category'),
(279, 'index', '2', 'Index action'),
(280, 'register', '4', 'Register account'),
(281, 'lostcredentials', '4', 'Account Recovery'),
(282, 'resetpassword', '4', 'Reset password'),
(283, 'recoverusername', '4', 'Sends username to user email'),
(284, 'index', '9', 'Course listing'),
(285, 'add', '9', 'Add new course'),
(286, 'edit', '9', 'Edit course'),
(287, 'delete', '9', 'Delete course'),
(288, 'assistants', '6', ''),
(289, 'toggleactive', '6', 'Toggle User activation'),
(290, 'addassistant', '6', 'Add user as assistant'),
(291, 'editassistant', '6', 'Edit an assistant user'),
(292, 'index', '10', 'Index action for class controller'),
(293, 'add', '10', 'Add new class'),
(294, 'edit', '10', 'Edit class'),
(295, 'delete', '10', 'Delete class'),
(300, 'copy', '11', 'Copy assignment'),
(301, 'index', '12', 'Index action for problems'),
(302, 'add', '12', 'Add action for problems'),
(303, 'edit', '12', 'Edit action for problems'),
(304, 'delete', '12', 'delete action for problems'),
(305, 'batchadd', '11', 'Batch Add for assignment'),
(306, 'batchadd', '12', 'Batch add for problem'),
(307, 'assistants', '10', 'Assign each class an assistant'),
(308, 'login', '45', 'Login action for frontend'),
(309, 'logout', '45', 'Logout from acount at the front'),
(310, 'register', '45', 'Register an account at the frontend'),
(311, 'resetpassword', '45', 'Reset password for frontend users'),
(312, 'students', '6', 'Student management at Admin'),
(313, 'toggleactivestudent', '6', 'Toggle User activation (students)'),
(314, 'deletestudent', '6', 'Delete students'),
(315, 'editstudent', '6', 'Edit a student user'),
(316, 'addstudent', '6', 'Add student in admin'),
(317, 'edit', '45', 'Edit student in frontend'),
(318, 'change-password', '45', 'Student change password'),
(319, 'index', '46', 'Assignment listing in frontend'),
(320, 'print', '46', 'Assignment printing in frontend'),
(321, 'download', '46', 'Downloading assigment in frontend'),
(322, 'index', '47', 'Problem listing in frontend'),
(323, 'answer', '47', 'Answer the problem at frontend'),
(324, 'index', '45', 'Account index'),
(325, 'submissions', '47', 'Students submissions in frontend'),
(326, 'index', '13', 'ارسال'),
(327, 'deleteattachment', '12', 'Delete attachment action in admin'),
(328, 'downloadattachment', '12', 'Downloads an attachment in admin'),
(329, 'download', '12', 'Download full archive'),
(330, 'view', '47', 'View problem in frontend'),
(331, 'download', '47', 'Download a problem'),
(332, 'downloadattachment', '47', 'Download an attachment'),
(333, 'index', '48', 'Answer problem main form'),
(334, 'download', '48', 'Downloading attachments'),
(335, 'delete', '48', 'delete answer'),
(336, 'deleteattachment', '48', 'deleting attachments'),
(337, 'downloadattachment', '48', 'Download answer attachments'),
(338, 'grade', '13', 'grade submissions in admin'),
(339, 'gradeall', '13', 'grade all submissions using file import'),
(340, 'download', '13', 'Download submission for a problem'),
(341, 'downloadall', '13', 'Downl all submissions for a problem'),
(342, 'downloadattachment', '13', 'Download an attachment'),
(343, 'deleteattachment', '13', 'deleting attachment'),
(344, 'search', '6', 'Search students');

-- --------------------------------------------------------

--
-- Table structure for table `problems`
--

DROP TABLE IF EXISTS `problems`;
CREATE TABLE `problems` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `assignment_id` int(11) NOT NULL,
  `problem_desc` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Stand-in structure for view `problems_view`
--
DROP VIEW IF EXISTS `problems_view`;
CREATE TABLE `problems_view` (
`id` int(11)
,`assignment_id` int(11)
,`problem_desc` text
,`class_id` int(11)
);
-- --------------------------------------------------------

--
-- Table structure for table `problem_attach`
--

DROP TABLE IF EXISTS `problem_attach`;
CREATE TABLE `problem_attach` (
  `problem_id` int(11) NOT NULL,
  `file_id` int(11) NOT NULL,
  PRIMARY KEY (`problem_id`,`file_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
-- --------------------------------------------------------

--
-- Stand-in structure for view `problem_attach_view`
--
DROP VIEW IF EXISTS `problem_attach_view`;
CREATE TABLE `problem_attach_view` (
`problem_id` int(11)
,`file_id` int(11)
,`file_name` varchar(50)
,`file_unique` varchar(50)
,`file_path` varchar(1024)
,`file_mime` char(20)
);
-- --------------------------------------------------------

--
-- Table structure for table `problem_class`
--

DROP TABLE IF EXISTS `problem_class`;
CREATE TABLE `problem_class` (
  `problem_id` int(11) NOT NULL,
  `class_id` int(11) NOT NULL,
  PRIMARY KEY (`problem_id`,`class_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
-- --------------------------------------------------------

--
-- Table structure for table `submissions`
--

DROP TABLE IF EXISTS `submissions`;
CREATE TABLE `submissions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `problem_id` int(11) NOT NULL,
  `submission_desc` text NOT NULL,
  `submission_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
-- --------------------------------------------------------

--
-- Table structure for table `submission_attach`
--

DROP TABLE IF EXISTS `submission_attach`;
CREATE TABLE `submission_attach` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `submission_id` int(11) NOT NULL,
  `submission_file_name` varchar(50) NOT NULL,
  `submission_file_unique` varchar(50) NOT NULL,
  `submission_file_path` varchar(1024) NOT NULL,
  `submission_file_mime` char(20) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
-- --------------------------------------------------------

--
-- Table structure for table `submission_grade`
--

DROP TABLE IF EXISTS `submission_grade`;
CREATE TABLE `submission_grade` (
  `submission_id` int(11) NOT NULL,
  `admin_user_id` int(11) NOT NULL,
  `grade` decimal(5,2) NOT NULL,
  `grade_desc` text NOT NULL,
  `grade_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`submission_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `firstname` varchar(50) NOT NULL,
  `lastname` varchar(50) NOT NULL,
  `username` varchar(10) NOT NULL,
  `email` varchar(200) NOT NULL,
  `password` varchar(40) NOT NULL,
  `active` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `class_id` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `last_login` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `student_number` (`username`),
  UNIQUE KEY `uq_email` (`email`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

--
-- Table structure for table `users_groups`
--

DROP TABLE IF EXISTS `users_groups`;
CREATE TABLE `users_groups` (
  `id` int(11) DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
-- --------------------------------------------------------

--
-- Table structure for table `users_notifications`
--

DROP TABLE IF EXISTS `users_notifications`;
CREATE TABLE `users_notifications` (
  `notification_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `unread` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`notification_id`,`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
-- --------------------------------------------------------

--
-- Structure for view `problems_view`
--
DROP TABLE IF EXISTS `problems_view`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `problems_view` AS select `p`.`id` AS `id`,`p`.`assignment_id` AS `assignment_id`,`p`.`problem_desc` AS `problem_desc`,`pc`.`class_id` AS `class_id` from (`problems` `p` join `problem_class` `pc` on((`p`.`id` = `pc`.`problem_id`)));

-- --------------------------------------------------------

--
-- Structure for view `problem_attach_view`
--
DROP TABLE IF EXISTS `problem_attach_view`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `problem_attach_view` AS select `pa`.`problem_id` AS `problem_id`,`a`.`file_id` AS `file_id`,`a`.`file_name` AS `file_name`,`a`.`file_unique` AS `file_unique`,`a`.`file_path` AS `file_path`,`a`.`file_mime` AS `file_mime` from (`problem_attach` `pa` join `attachments` `a` on((`pa`.`file_id` = `a`.`file_id`)));
