CREATE TABLE 'account' (
     "id" varchar(50) NOT NULL,
     "username" varchar(20) NOT NULL,
     "password" varchar(100) NOT NULL,
     "type" varchar(100) NOT NULL,
     "status" tinyint(3) NOT NULL,
    PRIMARY KEY("id")
);