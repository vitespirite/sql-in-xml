<?php
	if(!empty($_FILES))
	{
		$file_info = pathinfo($_FILES['xml']['name']);
		if($file_info['extension'] == 'xml')
		{
			$handle_file = fopen($_FILES['xml']['tmp_name'], "r");
			$file_contenue = fread($handle_file, $_FILES['xml']['size']);
			fclose($handle_file);
			
			//================Récupération des donnée xml

			if(preg_match("#<dbname>(.+)</dbname>#", $file_contenue))
			{
				$file_contenue = preg_replace("#<dbname>(.+)</dbname>#",  "-- phpMyAdmin SQL Dump
				-- version 4.7.9
				-- https://www.phpmyadmin.net/
				--
				-- Hôte : 127.0.0.1
				-- Généré le :  ".date("D j F")." à ".date("H : i")."
				-- Version du serveur :  5.7.14
				-- Version de PHP :  5.6.25

				SET SQL_MODE = \"NO_AUTO_VALUE_ON_ZERO\";
				SET AUTOCOMMIT = 0;
				START TRANSACTION;
				SET time_zone = \"+00:00\";


				/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
				/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
				/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
				/*!40101 SET NAMES utf8mb4 */;

				--
				-- Base de données :  `$1`
				--

				-- --------------------------------------------------------", $file_contenue);
				$file_contenue = nl2br($file_contenue);
				if(preg_match("#<table>(.+)</table>#isU", $file_contenue))
				{
					$file_contenue = preg_replace("#(</.+>)(<br[ ]/>)#", "$1", $file_contenue);
					$file_contenue = preg_replace("#<tablename>(.+)</tablename>#", " 
					-- <br />
					-- Structure de la table `$1`<br />
					--<br />

					DROP TABLE IF EXISTS `$1`;<br />
					CREATE TABLE `$1` ( <br />", $file_contenue);
					$file_contenue = preg_replace("#<cname>(.+)</cname>#", "`$1` ", $file_contenue);
					$file_contenue = preg_replace("#<value>([0-9]+)</value>#", "($1)", $file_contenue);

					//=======Auto Incrémente
					$file_contenue = preg_replace("#<AI>true</AI>#", "AUTO_INCREMENT", $file_contenue);
					$file_contenue = preg_replace("#<AI>false</AI>#", "", $file_contenue);
					//=======NULL
					$file_contenue = preg_replace("#<null>true</null>#", "NULL", $file_contenue);
					$file_contenue = preg_replace("#<null>false</null>#", "NOT NULL", $file_contenue);
					//========Defined
					$file_contenue = preg_replace("#<AsDefined>(.+)</AsDefined>#", " DEFAULT '$1'", $file_contenue);
					//========Key
					$file_contenue = preg_replace("#<key>(.+)</key>#", "<br /><key>PRIMARY KEY `$1` (`$1`)</key>", $file_contenue);

					
					$file_contenue = preg_replace("#</column>#", ",$0", $file_contenue);
					$file_contenue = preg_replace("#[,</column>|</key>]
</table>#", "<br />)<br /> ENGINE=MyISAM DEFAULT CHARSET=latin1;", $file_contenue);

				}	

				echo $file_contenue;	
			}
			else
			{
				echo "Invalid code xml";
			}
			
		}
		else
		{
			echo 'error: please upload file type xml.';
		}
	}
?>
<!DOCTYPE html>
<html>
<head>
	<title>Convertion xml to sql</title>
</head>
<body>
	<form method="post" action="convert-xml-to-sql.php" enctype="multipart/form-data">
		<input type="file" name="xml"/>
		<input type="submit" name="submit"/>
	</form>
</body>
</html>