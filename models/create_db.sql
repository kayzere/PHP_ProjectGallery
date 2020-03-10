DROP TABLE IF EXISTS albums;
DROP TABLE IF EXISTS photos;
DROP TABLE IF EXISTS users;

CREATE TABLE albums(
  album_id INTEGER PRIMARY KEY AUTOINCREMENT,  
  album_name TEXT NOT NULL
);

CREATE TABLE photos(
  photo_id INTEGER PRIMARY KEY AUTOINCREMENT, 
  album_id INTEGER NOT NULL, 
  photo_name TEXT NOT NULL,
  fullsize BLOB TEXT NOT NULL,
  thumbnail BLOB TEXT NOT NULL,
  FOREIGN KEY(album_id) REFERENCES albums(album_id) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE users (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  username TEXT NOT NULL UNIQUE,
  password TEXT NOT NULL UNIQUE
);

/*ajouter une table qui fait reference au albumid et photoid pour chaque users.
