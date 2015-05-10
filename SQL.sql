-- phpMyAdmin SQL Dump
-- version 4.1.14
-- http://www.phpmyadmin.net
--
-- Värd: 127.0.0.1
-- Tid vid skapande: 10 maj 2015 kl 21:05
-- Serverversion: 5.6.17
-- PHP-version: 5.5.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Databas: `dald15`
--

-- --------------------------------------------------------

--
-- Tabellstruktur `category`
--

CREATE TABLE IF NOT EXISTS `category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` char(20) COLLATE utf8_swedish_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci AUTO_INCREMENT=5 ;

--
-- Dumpning av Data i tabell `category`
--

INSERT INTO `category` (`id`, `name`) VALUES
(1, 'rmstore'),
(2, 'newmovies'),
(3, 'oldmovies'),
(4, 'movieworld');

-- --------------------------------------------------------

--
-- Tabellstruktur `content`
--

CREATE TABLE IF NOT EXISTS `content` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `slug` char(80) CHARACTER SET utf8 DEFAULT NULL,
  `url` char(80) CHARACTER SET utf8 DEFAULT NULL,
  `TYPE` char(80) CHARACTER SET utf8 DEFAULT NULL,
  `title` varchar(80) CHARACTER SET utf8 DEFAULT NULL,
  `DATA` text CHARACTER SET utf8,
  `FILTER` char(80) CHARACTER SET utf8 DEFAULT NULL,
  `published` datetime DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  `deleted` datetime DEFAULT NULL,
  `display` varchar(5) COLLATE utf8_swedish_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  UNIQUE KEY `url` (`url`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci AUTO_INCREMENT=30 ;

--
-- Dumpning av Data i tabell `content`
--

INSERT INTO `content` (`id`, `slug`, `url`, `TYPE`, `title`, `DATA`, `FILTER`, `published`, `created`, `updated`, `deleted`, `display`) VALUES
(23, 'nya-filmer-p-ing-ng', NULL, NULL, 'Nya filmer på ingång!', 'Vi har nu under våren jobbat hårt för att få in fler filmer att hyra, dvs. mer än våra 10st redan existerade filmer. Mer om detta kommer snart.', '', '2015-05-10 20:37:50', NULL, NULL, NULL, 'yes'),
(24, 'pulp-fiction', NULL, NULL, 'Pulp Fiction: fortfarande sv&aring;rslagen...', 'The Guardian har skrivit en nyhet om den älskade filmen Pulp Fiction: http://www.theguardian.com/film/2015/apr/13/my-favourite-cannes-winner-pulp-fiction', 'link', '2015-05-10 20:41:15', NULL, NULL, NULL, 'yes'),
(25, 's-ker-filmer', NULL, NULL, 'Vad vill ni se för filmer?', 'Vi på RM Rental Moviestore vill utöka vårt sortiment men vi vet inte vad ni i publiken vill ha! Skriv till oss!', '', '2015-05-10 20:44:01', NULL, NULL, NULL, 'yes'),
(26, 'bio', NULL, NULL, 'Biofilmer', 'Annie\r\nFifty Shades of Grey\r\nI nöd eller lust\r\nInto the Woods\r\nNatt på museet: Gravkammarens hemlighet\r\nSvampBob Fyrkant: Äventyr på torra land\r\n', 'bbcode,link,shortcode,nl2br,markdown', '2015-05-10 20:46:43', NULL, NULL, NULL, 'yes'),
(27, 'rm', NULL, NULL, 'Ny till RM?', 'Hej! Om du är ny till RM Moviestores hemsida, var inte rädd för att kolla dig omkring för diverse godsaker i vårt filmsortiment!', '', '2015-05-10 20:47:44', NULL, NULL, NULL, 'yes'),
(28, 'fast-n-furious', NULL, NULL, 'Fast and Furious 8?', 'Kommer det en ny Fast and Furious? Läs mer här: http://www.ibtimes.co.uk/fast-furious-8-can-vin-diesel-make-great-action-series-without-paul-walker-1500558', 'link', '2015-05-10 20:52:04', NULL, NULL, NULL, 'yes'),
(29, 'rm-moviestore', NULL, NULL, 'RM Rental Moviestore', 'Stora nyheter kommer snart...', '', '2015-05-10 20:52:57', NULL, NULL, NULL, 'yes');

-- --------------------------------------------------------

--
-- Tabellstruktur `content2category`
--

CREATE TABLE IF NOT EXISTS `content2category` (
  `idContent` int(11) NOT NULL,
  `idCategory` int(11) NOT NULL,
  PRIMARY KEY (`idContent`,`idCategory`),
  KEY `content2category_ibfk_2` (`idCategory`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci;

--
-- Dumpning av Data i tabell `content2category`
--

INSERT INTO `content2category` (`idContent`, `idCategory`) VALUES
(23, 1),
(24, 1),
(25, 1),
(27, 1),
(29, 1),
(23, 2),
(25, 2),
(28, 2),
(24, 3),
(24, 4),
(26, 4),
(28, 4);

-- --------------------------------------------------------

--
-- Tabellstruktur `genre`
--

CREATE TABLE IF NOT EXISTS `genre` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` char(20) CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci AUTO_INCREMENT=13 ;

--
-- Dumpning av Data i tabell `genre`
--

INSERT INTO `genre` (`id`, `name`) VALUES
(1, 'comedy'),
(2, 'romance'),
(3, 'college'),
(4, 'crime'),
(5, 'drama'),
(6, 'thriller'),
(7, 'animation'),
(8, 'adventure'),
(9, 'family'),
(10, 'svenskt'),
(11, 'action'),
(12, 'horror');

-- --------------------------------------------------------

--
-- Tabellstruktur `movie`
--

CREATE TABLE IF NOT EXISTS `movie` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(100) CHARACTER SET utf8 NOT NULL,
  `price` double DEFAULT NULL,
  `director` varchar(100) CHARACTER SET utf8 DEFAULT NULL,
  `LENGTH` int(11) DEFAULT NULL,
  `YEAR` int(11) NOT NULL DEFAULT '1900',
  `plot` text CHARACTER SET utf8,
  `image` varchar(100) CHARACTER SET utf8 DEFAULT NULL,
  `youtube` varchar(200) CHARACTER SET utf8 DEFAULT NULL,
  `imdb` varchar(200) CHARACTER SET utf8 DEFAULT 'Saknas',
  `updated` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci AUTO_INCREMENT=11 ;

--
-- Dumpning av Data i tabell `movie`
--

INSERT INTO `movie` (`id`, `title`, `price`, `director`, `LENGTH`, `YEAR`, `plot`, `image`, `youtube`, `imdb`, `updated`) VALUES
(1, 'Pulp Fiction', 99, 'Quentin Tarantino', 154, 1900, 'Jules Winnfield and Vincent Vega are two hitmen who are out to retrieve a suitcase stolen from their employer, mob boss Marsellus Wallace. Wallace has also asked Vincent to take his wife Mia out a few days later when Wallace himself will be out of town. Butch Coolidge is an aging boxer who is paid by Wallace to lose his next fight. The lives of these seemingly unrelated people are woven together comprising of a series of funny, bizarre and uncalled-for incidents.', 'img/movie/pulp-fiction.jpg', 'https://www.youtube.com/watch?v=s7EdQ4FqbhY', 'http://www.imdb.com/title/tt0110912/', '2015-04-21 15:36:42'),
(2, 'American Pie', 49.9, 'Paul Weitz, Chris Weitz', 95, 1999, 'Jim, Oz, Finch and Kevin are four friends who make a pact that before they graduate they will all lose their virginity. The hard job now is how to reach that goal by prom night. Whilst Oz begins singing to grab attention and Kevin tries to persuade his girlfriend, Finch tries any easy route of spreading rumors and Jim fails miserably. Whether it is being caught on top of a pie or on the Internet, Jim always end up with his trusty sex advice from his father. Will they achieve their goal of getting laid by prom night? or will they learn something much different.', 'img/movie/american-pie.jpg', 'https://www.youtube.com/watch?v=Sithad108Og', 'http://www.imdb.com/title/tt0163651/', '2015-04-18 21:35:42'),
(3, 'Pokemon The Movie 2000', 59, 'Michael Haigney, Kunihiko Yuyama', 84, 1999, 'An evil genius in a flying fortress is trying to kidnap the birds of the fire, ice, and lightning islands in hopes of luring the sea power, Lugia, and controlling the world. Ash and his friends, in the process of carrying out an island tradition that requires visiting those three islands, discover the plot and try to stop it. Even the comic villains, Team Rocket, turn good and help out in the end against the threat of world destruction. And Pokemon assemble from all around the world in case they can be of help (but they end up helping only the merchandising).', 'img/movie/pokemon.jpg', 'https://www.youtube.com/watch?v=mw5ef0UPdv0', 'http://www.imdb.com/title/tt0210234', '2015-04-21 15:54:54'),
(4, 'Kopps', 199, 'Josef Fares', 90, 2003, 'Police officer Benny is obsessed with American police cliches and livens up his own boring everyday life with dreams of duels with bad guys. But poor Benny and his colleagues doesn''t have much to do in the small town of H&ouml;gbotr&auml;sk. Most of their days are spent drinking coffee, eating sausage waffles and chasing down stray cows. Peace and quiet is the dream of every politician, but for the Swedish authorities, the lack of crooks is reason to close the local police station. When the cops investigate a suspected act of vandalism, they realise that they themselves may be able to raise the crime statistics high enough to stay in business.', 'img/movie/kopps.jpg', 'https://www.youtube.com/watch?v=aJFdePDqKrY', 'http://www.imdb.com/title/tt0339230/', '2015-04-21 15:24:17'),
(5, 'From Dusk Till Dawn', 9.99, 'Robert Rodriguez', 108, 1996, 'After a bank heist in Abilene with several casualties, the bank robber Seth Gecko and his psychopath and rapist brother Richard Gecko continue their crime spree in a convenience store in the middle of the desert while heading to Mexico with a hostage. They decide to stop for a while in a low-budget motel. Meanwhile the former minister Jacob Fuller is traveling on vacation with his son Scott and his daughter Kate in a RV. Jacob lost his faith after the death of his beloved wife in a car accident and quit his position of pastor of his community and stops for the night in the same motel Seth and Richard are lodged. When Seth sees the recreational vehicle, he abducts Jacob and his family to help his brother and him to cross the Mexico border, promising to release them on the next morning. They head to the truck drivers and bikers bar Titty Twister where Seth will meet with his partner Carlos in the dawn.', 'img/movie/from-dusk-till-dawn.jpg', 'https://www.youtube.com/watch?v=jNuIn4T-CLk', 'http://www.imdb.com/title/tt0116367', '2015-04-21 15:24:27'),
(6, 'The Shawshank Redemption', 99, 'Frank Darabont', 142, 1994, 'Andy Dufresne is a young and successful banker whose life changes drastically when he is convicted and sentenced to life imprisonment for the murder of his wife and her lover. Set in the 1940''s, the film shows how Andy, with the help of his friend Red, the prison entrepreneur, turns out to be a most unconventional prisoner.', 'img/movie/the-shawshank-redemption.jpg', 'https://www.youtube.com/watch?v=6hB3S9bIaco', 'http://www.imdb.com/title/tt0111161', '2015-04-21 15:24:35'),
(7, 'Gudfadern', 199, 'Francis Ford Coppola', 175, 1972, 'When the aging head of a famous crime family decides to transfer his position to one of his subalterns, a series of unfortunate events start happening to the family, and a war begins between all the well-known families leading to insolence, deportation, murder and revenge, and ends with the favorable successor being finally chosen. ', 'img/movie/the-godfather.jpg', 'https://www.youtube.com/watch?v=yO23UaOK1Is', 'http://www.imdb.com/title/tt0068646', '2015-04-01 00:00:00'),
(8, 'The Dark Knight', 59.9, 'Christopher Nolan', 152, 2008, 'Batman raises the stakes in his war on crime. With the help of Lieutenant Jim Gordon and District Attorney Harvey Dent, Batman sets out to dismantle the remaining criminal organizations that plague the city streets. The partnership proves to be effective, but they soon find themselves prey to a reign of chaos unleashed by a rising criminal mastermind known to the terrified citizens of Gotham as The Joker.', 'img/movie/the-dark-knight.jpg', 'https://www.youtube.com/watch?v=yQ5U8suTUw0', 'http://www.imdb.com/title/tt0468569', '2015-04-18 22:26:14'),
(9, 'Schindler''s List', 49, 'Steven Spielberg', 195, 1900, 'Oskar Schindler is a vainglorious and greedy German businessman who becomes an unlikely humanitarian amid the barbaric Nazi reign when he feels compelled to turn his factory into a refuge for Jews. Based on the true story of Oskar Schindler who managed to save about 1100 Jews from being gassed at the Auschwitz concentration camp, it is a testament for the good in all of us.', 'img/movie/schindlers-list.jpg', 'https://www.youtube.com/watch?v=dwfIf1WMhgc', 'http://www.imdb.com/title/tt0108052', '2015-04-02 00:00:00'),
(10, 'The Lord of the Rings: The Return of the King', 199, 'Peter Jackson', 201, 1900, 'While Frodo & Sam continue to approach Mount Doom to destroy the One Ring, unaware of the path Gollum is leading them, the former Fellowship aid Rohan & Gondor in a great battle in the Pelennor Fields, Minas Tirith and the Black Gates as Sauron wages his last war against Middle-Earth. ', 'img/movie/return-of-the-king.jpg', 'https://www.youtube.com/watch?v=r5X-hFf6Bwo', 'http://www.imdb.com/title/tt0167260', '2015-04-08 00:00:00');

-- --------------------------------------------------------

--
-- Tabellstruktur `movie2genre`
--

CREATE TABLE IF NOT EXISTS `movie2genre` (
  `idMovie` int(11) NOT NULL,
  `idGenre` int(11) NOT NULL,
  PRIMARY KEY (`idMovie`,`idGenre`),
  KEY `idGenre` (`idGenre`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci;

--
-- Dumpning av Data i tabell `movie2genre`
--

INSERT INTO `movie2genre` (`idMovie`, `idGenre`) VALUES
(4, 1),
(2, 2),
(5, 2),
(8, 2),
(6, 4),
(7, 4),
(7, 5),
(9, 5),
(10, 8),
(1, 12),
(3, 12);

-- --------------------------------------------------------

--
-- Tabellstruktur `user`
--

CREATE TABLE IF NOT EXISTS `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `acronym` char(12) CHARACTER SET utf8 NOT NULL,
  `name` varchar(80) CHARACTER SET utf8 DEFAULT NULL,
  `password` char(32) CHARACTER SET utf8 DEFAULT NULL,
  `salt` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `acronym` (`acronym`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci AUTO_INCREMENT=3 ;

--
-- Dumpning av Data i tabell `user`
--

INSERT INTO `user` (`id`, `acronym`, `name`, `password`, `salt`) VALUES
(1, 'doe', 'John/Jane Doe', 'efdec0da5ad352413d50aa566b91012e', 1427141284),
(2, 'admin', 'Administrator', '0fc1cde0c0d21655f514c21823209315', 1427141284);

-- --------------------------------------------------------

--
-- Ersättningsstruktur för vy `vmovie`
--
CREATE TABLE IF NOT EXISTS `vmovie` (
);
-- --------------------------------------------------------

--
-- Struktur för vy `vmovie`
--
DROP TABLE IF EXISTS `vmovie`;
-- används(#1356 - View 'dald15.vmovie' references invalid table(s) or column(s) or function(s) or definer/invoker of view lack rights to use them)

--
-- Restriktioner för dumpade tabeller
--

--
-- Restriktioner för tabell `content2category`
--
ALTER TABLE `content2category`
  ADD CONSTRAINT `content2category_ibfk_1` FOREIGN KEY (`idContent`) REFERENCES `content` (`id`),
  ADD CONSTRAINT `content2category_ibfk_2` FOREIGN KEY (`idCategory`) REFERENCES `category` (`id`);

--
-- Restriktioner för tabell `movie2genre`
--
ALTER TABLE `movie2genre`
  ADD CONSTRAINT `movie2genre_ibfk_1` FOREIGN KEY (`idMovie`) REFERENCES `movie` (`id`),
  ADD CONSTRAINT `movie2genre_ibfk_2` FOREIGN KEY (`idGenre`) REFERENCES `genre` (`id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
