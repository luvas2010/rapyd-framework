CREATE TABLE articles (
  article_id serial,
  author_id int default NULL,
  title varchar(100) NOT NULL default '',
  body text,
  datefield date default NULL,
  public varchar(255) default NULL,
  check (public in ('yes', 'no'))
);

insert  into articles(article_id,author_id,title,body,datefield,public) 
values (1,1,'Title 1','Body 1',NULL,'y');
insert  into articles(article_id,author_id,title,body,datefield,public) 
values (2,2,'Title 2','Body 2',NULL,'y');
insert  into articles(article_id,author_id,title,body,datefield,public) 
values (3,1,'Title 3','Body 3',NULL,'n');
insert  into articles(article_id,author_id,title,body,datefield,public) 
values (4,2,'Title 4','Body 4',NULL,NULL);
insert  into articles(article_id,author_id,title,body,datefield,public) 
values (5,1,'Title 5','Body 5',NULL,NULL);
insert  into articles(article_id,author_id,title,body,datefield,public) 
values (6,2,'Title 6','Body 6',NULL,NULL);
insert  into articles(article_id,author_id,title,body,datefield,public) 
values (7,1,'Title 7','Body 7',NULL,NULL);
insert  into articles(article_id,author_id,title,body,datefield,public) 
values (8,2,'Title 8','Body 8',NULL,NULL);
insert  into articles(article_id,author_id,title,body,datefield,public) 
values (9,1,'Title 9','Body 9',NULL,NULL);
insert  into articles(article_id,author_id,title,body,datefield,public) 
values (10,2,'Title 10','Body 10',NULL,NULL);

CREATE TABLE articles_related (
  art_id int NOT NULL default '0',
  rel_id int NOT NULL default '0'
);

insert into articles_related(art_id,rel_id) values (1,2);
insert into articles_related(art_id,rel_id) values (2,1);

CREATE TABLE authors (
  author_id serial,
  firstname varchar(25) NOT NULL default '',
  lastname varchar(25) NOT NULL default ''
);

insert into authors(author_id,firstname,lastname) values (1,'Jhon','Doe');
insert into authors(author_id,firstname,lastname) values (2,'Rocco','Siffredi');

CREATE TABLE comments (
  comment_id serial,
  article_id int NOT NULL default '0',
  comment text
);
