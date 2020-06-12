/* Installation script for the Authentification-Test Database.
 * Author:     Andreas Dolezal
 * Date:       2020-06-07
 */
DROP TABLE IF EXISTS users;

CREATE TABLE users(
	  userID INT UNSIGNED PRIMARY KEY AUTO_INCREMENT
    , username VARCHAR(256) NOT NULL
    , firstname VARCHAR(256) NOT NULL
    , lastname VARCHAR(256) NOT NULL
    , email VARCHAR(256) NOT NULL
    , pass VARCHAR(256) NOT NULL
);

ALTER TABLE users AUTO_INCREMENT=1000;
