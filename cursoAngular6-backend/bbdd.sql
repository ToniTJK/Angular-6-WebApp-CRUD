CREATE DATABASE IF NOT EXISTS curso_angular6;
USE curso_angular6;

CREATE TABLE productos(
    id int(255) auto_increment not null,
    nombre VARCHAR(255),
    description text,
    precio VARCHAR(255),
    imagen VARCHAR(255),
    CONSTRAINT pk_productos PRIMARY KEY(id)
)ENGINE=InnoDb;
