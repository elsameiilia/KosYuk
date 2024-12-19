CREATE DATABASE kosyuk;

USE kosyuk;

CREATE TABLE users(
    email       VARCHAR(50)     PRIMARY KEY,
    password    VARCHAR(255)    NOT NULL,
    username    VARCHAR(30)     UNIQUE NOT NULL,
    name        VARCHAR(30)     NOT NULL,
    photo       VARCHAR(255)    NULL,
    role        enum('admin','user') DEFAULT 'user'
);

INSERT INTO users(email, password, username, name, role) VALUES ('admin@gmail.com', '$2y$10$3LhGm4lnfxtVI2y7/DHJGuXnBU.dd5yCIIvKC9cJH7QnQC.aPr.F2', 'admin', 'admin', 'admin');

CREATE TABLE kosan(
    kos_id      INT PRIMARY KEY AUTO_INCREMENT,
    title       VARCHAR(100)    NOT NULL,
    description TEXT            NULL,
    lokasi		VARCHAR(255)	NULL,
    fasilitas	TEXT			NULL,
    harga		DECIMAL(10, 2)	NULL,
    banner      VARCHAR(255)    NOT NULL
);

CREATE TABLE img_kosan(
    img_kos_id  INT PRIMARY KEY AUTO_INCREMENT,
    kos_id      INT             NOT NULL,
    photo       VARCHAR(255)    NOT NULL,

    FOREIGN KEY(kos_id) REFERENCES kosan(kos_id) ON DELETE CASCADE
);

CREATE TABLE bookmark(
    bm_id       INT PRIMARY KEY AUTO_INCREMENT,
    kos_id      INT             NOT NULL,
    username    VARCHAR(30)     NOT NULL,

    FOREIGN KEY(kos_id) REFERENCES kosan(kos_id) ON DELETE CASCADE,
    FOREIGN KEY(username) REFERENCES users(username) ON DELETE CASCADE
);

CREATE TABLE feedback(
    fb_id   INT          NOT NULL PRIMARY KEY,
	nama	VARCHAR(100) NOT NULL,
    email	VARCHAR(100) NULL,
    pesan	TEXT		 NULL
);