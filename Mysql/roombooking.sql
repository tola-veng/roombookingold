CREATE DATABASE IF NOT EXISTS `roombooking`;

USE `roombooking`;

/*Table structure for table `tb_booking` */

DROP TABLE IF EXISTS `tb_booking`;

CREATE TABLE `tb_booking` (
  `booking_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `group_id` tinyint(4) DEFAULT NULL,
  `room_id` varchar(8) DEFAULT NULL,
  `capacity` tinyint(4) DEFAULT NULL,
  `booking_date` date DEFAULT NULL,
  `booking_start` time DEFAULT NULL,
  `booking_end` time DEFAULT NULL,
  `notification` tinyint(1) DEFAULT NULL,
  `booked_by` tinyint(4) NOT NULL,
  PRIMARY KEY (`booking_id`)
) ENGINE=InnoDB AUTO_INCREMENT=55 DEFAULT CHARSET=utf8;

/*Data for the table `tb_booking` */

insert  into `tb_booking`(`booking_id`,`group_id`,`room_id`,`capacity`,`booking_date`,`booking_start`,`booking_end`,`notification`,`booked_by`) values (13,1,'TC 204',13,'2015-10-28','09:30:00','10:30:00',0,1),(14,1,'TC 204',13,'2015-10-28','10:30:00','11:30:00',0,1),(15,2,'TC 204',13,'2015-10-30','13:30:00','14:30:00',0,1),(16,2,'TC 204',13,'2015-10-30','14:30:00','15:30:00',0,1),(17,1,'TC 204',13,'2015-10-28','13:30:00','14:30:00',0,1),(19,1,'TC 204',13,'2015-10-28','14:30:00','15:30:00',0,1),(20,1,'TC 204',13,'2015-10-26','15:30:00','16:30:00',0,1),(21,1,'TC 204',13,'2015-10-26','16:30:00','17:30:00',0,1),(22,1,'TC 204',13,'2015-10-26','08:30:00','09:30:00',0,1),(24,1,'TC 204',13,'2015-11-11','09:30:00','10:30:00',0,1),(25,1,'TC 204',13,'2015-11-11','10:30:00','11:30:00',0,1),(26,1,'TC 204',13,'2015-11-11','08:30:00','09:30:00',0,1),(27,1,'TC 204',13,'2015-10-26','14:30:00','15:30:00',0,1),(28,6,'TC 303',10,'2015-11-05','08:30:00','09:30:00',0,1),(29,6,'TC 303',10,'2015-11-05','09:30:00','10:30:00',0,1),(30,6,'TC 303',10,'2015-11-05','10:30:00','11:30:00',0,1),(31,6,'TC 303',10,'2015-11-05','11:30:00','12:30:00',0,1),(32,6,'TC 303',10,'2015-11-06','08:30:00','09:30:00',0,1),(33,6,'TC 303',10,'2015-11-06','09:30:00','10:30:00',0,1),(34,6,'TC 303',10,'2015-11-06','10:30:00','11:30:00',0,1),(35,6,'TC 303',10,'2015-11-06','11:30:00','12:30:00',0,1),(36,6,'TC 303',10,'2015-11-04','13:30:00','14:30:00',0,1),(37,6,'TC 303',10,'2015-11-04','14:30:00','15:30:00',0,1),(38,6,'TC 303',10,'2015-11-04','15:30:00','16:30:00',0,1),(39,6,'TC 303',10,'2015-11-04','16:30:00','17:30:00',0,1),(40,6,'TC 303',10,'2015-11-04','17:30:00','18:30:00',0,1),(41,8,'TD 301',14,'2015-11-26','08:30:00','09:30:00',0,5),(42,8,'TD 301',14,'2015-11-26','09:30:00','10:30:00',0,5),(43,8,'TD 301',14,'2015-11-26','10:30:00','11:30:00',0,5),(44,8,'TD 301',14,'2015-11-27','10:30:00','11:30:00',0,5),(45,8,'TD 301',14,'2015-11-27','09:30:00','10:30:00',0,5),(46,8,'TD 301',14,'2015-11-27','08:30:00','09:30:00',0,5),(47,8,'TD 301',14,'2015-11-27','11:30:00','12:30:00',0,5),(48,8,'TD 301',14,'2015-11-26','11:30:00','12:30:00',0,5),(49,8,'TC 207',1,'2015-11-27','08:30:00','09:30:00',0,5),(50,8,'TC 207',1,'2015-11-27','09:30:00','10:30:00',0,5),(51,8,'TC 207',1,'2015-11-27','10:30:00','11:30:00',0,5),(52,8,'TC 207',1,'2015-11-26','10:30:00','11:30:00',0,5),(53,8,'TC 207',1,'2015-11-26','11:30:00','12:30:00',0,5),(54,8,'TC 207',1,'2015-11-26','12:30:00','13:30:00',0,5);

/*Table structure for table `tb_group` */

DROP TABLE IF EXISTS `tb_group`;

CREATE TABLE `tb_group` (
  `group_id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `group_name` varchar(32) DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  PRIMARY KEY (`group_id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;

/*Data for the table `tb_group` */

insert  into `tb_group`(`group_id`,`group_name`,`created_by`) values (1,'A',0),(2,'B',0),(3,'C',0),(4,'D',0),(5,'E',0),(6,'Alpha Team!',2),(7,'Demo Team',4),(8,'Admin Team',5);

/*Table structure for table `tb_member` */

DROP TABLE IF EXISTS `tb_member`;

CREATE TABLE `tb_member` (
  `group_id` tinyint(4) NOT NULL,
  `user_id` tinyint(4) NOT NULL,
  PRIMARY KEY (`group_id`,`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `tb_member` */

insert  into `tb_member`(`group_id`,`user_id`) values (1,1),(1,2),(3,3),(4,1),(4,2),(6,1),(6,2),(6,3),(7,4),(8,5);

/*Table structure for table `tb_room` */

DROP TABLE IF EXISTS `tb_room`;

CREATE TABLE `tb_room` (
  `room_id` varchar(8) NOT NULL,
  `capacity` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`room_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `tb_room` */

insert  into `tb_room`(`room_id`,`capacity`) values ('TC 204',30),('TC 207',20),('TC 303',20),('TD 301',30),('TD 303',25),('TD 305',30);

/*Table structure for table `tb_user` */

DROP TABLE IF EXISTS `tb_user`;

CREATE TABLE `tb_user` (
  `user_id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(32) DEFAULT NULL,
  `firstname` varchar(16) DEFAULT NULL,
  `lastname` varchar(16) DEFAULT NULL,
  `email` varchar(64) DEFAULT NULL,
  `password` varchar(32) DEFAULT NULL,
  `type` tinyint(1) DEFAULT NULL COMMENT '1 is admin',
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

/*Data for the table `tb_user` */

insert  into `tb_user`(`user_id`,`username`,`firstname`,`lastname`,`email`,`password`,`type`) values (1,'tola','Tola','Veng','7678886@student.swin.edu.au','827ccb0eea8a706c4c34a16891f84e7b',1),(2,'Xana','Xana','Rosini','100495156@student.swin.edu.au','e10adc3949ba59abbe56e057f20f883e',1),(3,'James','James','MacGregor','somestuff@student.swin.edu.au','e10adc3949ba59abbe56e057f20f883e',1),(4,'demo','Demo','Demo','demo@example.com','fe01ce2a7fbac8fafaed7c982a04e229',0),(5,'admin','admin','admin','admin@example.com','21232f297a57a5a743894a0e4a801fc3',1);


