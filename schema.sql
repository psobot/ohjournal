CREATE TABLE "IPs" ( "id" INTEGER PRIMARY KEY DEFAULT NULL, "ip" varchar(255) DEFAULT NULL);

CREATE TABLE "config" ( "id" INTEGER PRIMARY KEY AUTOINCREMENT , "name" varchar(255) , "value" varchar(255) );
INSERT INTO 'config' ('id', 'name', 'value') VALUES ('1', 'owner', '');
INSERT INTO 'config' ('id', 'name', 'value') VALUES ('2', 'cronUser', '');
INSERT INTO 'config' ('id', 'name', 'value') VALUES ('3', 'mailFile', '');
INSERT INTO 'config' ('id', 'name', 'value') VALUES ('4', 'tblEntries', 'entries');
INSERT INTO 'config' ('id', 'name', 'value') VALUES ('5', 'tblUser', 'user');
INSERT INTO 'config' ('id', 'name', 'value') VALUES ('6', 'tblVisits', 'visits');
INSERT INTO 'config' ('id', 'name', 'value') VALUES ('7', 'userEmail', '');
INSERT INTO 'config' ('id', 'name', 'value') VALUES ('8', 'serverEmail', '');
INSERT INTO 'config' ('id', 'name', 'value') VALUES ('9', 'fromEmail', '');
INSERT INTO 'config' ('id', 'name', 'value') VALUES ('10', 'rememberText', 'Hey, remember this?');
INSERT INTO 'config' ('id', 'name', 'value') VALUES ('11', 'emailDate', 'l, F jS, Y');
INSERT INTO 'config' ('id', 'name', 'value') VALUES ('12', 'emailTime', '8:00 PM EST');
INSERT INTO 'config' ('id', 'name', 'value') VALUES ('13', 'webRead', '1');
INSERT INTO 'config' ('id', 'name', 'value') VALUES ('14', 'webDate', 'l, F j<sup>S</sup>, Y');
INSERT INTO 'config' ('id', 'name', 'value') VALUES ('15', 'tblIPs', 'IPs');
INSERT INTO 'config' ('id', 'name', 'value') VALUES ('16', 'timezone', '');

CREATE TABLE entries(id integer primary key autoincrement, sent datetime, received datetime, headers varchar(8192), entry varchar(8192), reflected int, mood real default 0);
CREATE TABLE user (username varchar(50), password varchar(50), cookie varchar(50));
CREATE TABLE visits (id integer primary key autoincrement, time datetime, ip varchar(255), page varchar(255), loggedin int);
