CREATE TABLE `articles` (
  `article_id` int(9) unsigned NOT NULL auto_increment,
  `author_id` int(11) default NULL,
  `title` varchar(100) NOT NULL default '',
  `body` text,
  `datefield` date default '0000-00-00',
  `public` enum('y','n') default NULL,
  PRIMARY KEY  (`article_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;


insert  into `articles`(`article_id`,`author_id`,`title`,`body`,`datefield`,`public`) values (1,1,'Title 1','Body 1',NULL,'y'),(2,2,'Title 2','Body 2',NULL,'y'),(3,1,'Title 3','Body 3',NULL,'n'),(4,2,'Title 4','Body 4',NULL,NULL),(5,1,'Title 5','Body 5',NULL,NULL),(6,2,'Title 6','Body 6',NULL,NULL),(7,1,'Title 7','Body 7',NULL,NULL),(8,2,'Title 8','Body 8',NULL,NULL),(9,1,'Title 9','Body 9',NULL,NULL),(10,2,'Title 10','Body 10',NULL,NULL);

CREATE TABLE `articles_related` (
  `art_id` int(9) unsigned NOT NULL default '0',
  `rel_id` int(9) unsigned NOT NULL default '0',
  PRIMARY KEY  (`art_id`,`rel_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;


insert  into `articles_related`(`art_id`,`rel_id`) values (1,2),(2,1);


CREATE TABLE `authors` (
  `author_id` int(11) NOT NULL auto_increment,
  `firstname` varchar(25) NOT NULL default '',
  `lastname` varchar(25) NOT NULL default '',
  PRIMARY KEY  (`author_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;


insert  into `authors`(`author_id`,`firstname`,`lastname`) values (1,'Jhon','Doe'),(2,'Rocco','Siffredi');

CREATE TABLE `comments` (
  `comment_id` int(9) NOT NULL auto_increment,
  `article_id` int(9) NOT NULL default '0',
  `comment` text,
  PRIMARY KEY  (`comment_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;