PRAGMA foreign_keys=OFF;
BEGIN TRANSACTION;
CREATE TABLE IF NOT EXISTS "users" ("id" integer primary key autoincrement not null, "name" varchar not null, "email" varchar not null, "email_verified_at" datetime, "password" varchar not null, "remember_token" varchar, "created_at" datetime, "updated_at" datetime, "is_touch_panel" tinyint(1) not null default '0', "role" varchar not null default 'default');
INSERT INTO users VALUES(1,'Jeffrey Lane','elpasojeff@gmail.com',NULL,'$2y$12$DhtSsJl8bQ5jNKifIEGLD.lD/041j7YWzQo9F./lzbtsV6AmNHPxW',NULL,'2024-07-26 04:02:07','2024-07-26 04:02:07',0,'default');
INSERT INTO users VALUES(2,'Garage Tablet','garagetablet@lane.com',NULL,'$2y$12$cm8b062Efp6A2Q1k.8HFO.lTBTdOIkqMMVl694RcU6h8tePN9GdO2',NULL,'2025-03-10 09:03:36','2025-03-15 11:25:59',0,'garagetouchpanel');
INSERT INTO users VALUES(3,'Front Tablet','fronttablet@lane.com',NULL,'$2y$12$nYLSGasSNGTsMnoYbcjA7uVnsCK8iaLXogmN9bWghPHQ4y5/XLMwi',NULL,'2025-03-15 11:18:43','2025-03-15 11:26:04',0,'fronttouchpanel');
COMMIT;
