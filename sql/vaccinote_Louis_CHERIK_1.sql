-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : mar. 23 nov. 2021 à 16:33
-- Version du serveur : 10.4.21-MariaDB
-- Version de PHP : 8.0.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `vaccinote`
--

-- --------------------------------------------------------

--
-- Structure de la table `vac_rdv`
--

CREATE TABLE `vac_rdv` (
  `id` int(11) NOT NULL,
  `date_rdv` datetime NOT NULL,
  `description_rdv` text NOT NULL,
  `user_id` int(11) NOT NULL,
  `status` varchar(25) NOT NULL DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Déchargement des données de la table `vac_rdv`
--

INSERT INTO `vac_rdv` (`id`, `date_rdv`, `description_rdv`, `user_id`, `status`) VALUES
(14, '2021-11-25 15:30:00', 'tst', 52, 'pending');

-- --------------------------------------------------------

--
-- Structure de la table `vac_users`
--

CREATE TABLE `vac_users` (
  `id` int(11) NOT NULL,
  `nom` varchar(30) NOT NULL,
  `prenom` varchar(30) NOT NULL,
  `age` int(3) DEFAULT NULL,
  `naissance` date DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `tel` varchar(10) DEFAULT NULL,
  `sexe` int(1) NOT NULL DEFAULT 2,
  `adresse` varchar(255) DEFAULT NULL,
  `postal` int(5) DEFAULT NULL,
  `ville` varchar(60) DEFAULT NULL,
  `mdp` varchar(200) NOT NULL,
  `token` varchar(100) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `token_time` datetime DEFAULT NULL,
  `validemail` int(1) NOT NULL DEFAULT 0,
  `role` varchar(25) NOT NULL DEFAULT 'normal'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Déchargement des données de la table `vac_users`
--

INSERT INTO `vac_users` (`id`, `nom`, `prenom`, `age`, `naissance`, `email`, `tel`, `sexe`, `adresse`, `postal`, `ville`, `mdp`, `token`, `created_at`, `token_time`, `validemail`, `role`) VALUES
(52, 'Jouen', 'Leonard', NULL, '2021-11-18', 'leonardjouen@gmail.com', '0601020504', 1, '12 rue test', 76240, 'Rouen', '$2y$10$hwWYdpG4zCFNZuJp0xtxb.AYbtp4xIoikVK3.AKkD5uaoZJO0yQUy', 'alZArAA5P8A22hM9Oil36nnCeFBYWheLC6uwf22RkoL7oBFkPereUIv1HmsyT4hCE01asTzZp20QKHqwuCYtvlGPAeSRpKrnLDhF', '2021-11-19 11:44:22', NULL, 1, 'banni'),
(53, 'qsdq', 'sqdqsd', NULL, NULL, 'qsdqs@test.fr', NULL, 2, NULL, NULL, NULL, '$2y$10$WBTgeFJMKQL43dOD.guq1.iD9rInwSJE3hFpvQ5r4z.okxNaWfyPm', 'OVVR4x6UDbeKM1oHe9HozagLEmbVAx7p5y8eIFenfVeRuAwnHMZPfxoC41ekF8PSxQMnN3x65OzURegeTY4LmvQFMRAzMloPV8NE', '2021-11-19 12:07:17', NULL, 1, 'normal'),
(54, 'Cherik', 'Louis', 19, '2002-10-09', 'moviemaster76100@gmail.com', '0782320099', 0, '6 rue Amédée Dormoy', 76000, 'Rouen', '$2y$10$TB2sans/RuuFoIBWggdmEes.ZLqNr6OcUeblnRzLtZcD8CY8W.HcW', 'N0Pi0bQV8fnnhpzvIEnevPrx90DLn1SKLkGJHTIZZfyYvTg5VIAbXjhDIKlg89EG0K7RdEZhvunZL6I2vTemTIGC4pXwGIyQ4UwB', '2021-11-22 14:56:04', NULL, 1, 'admin'),
(55, 'dfgs', 'sfgzsfv', 24, NULL, '', NULL, 0, NULL, NULL, NULL, 'fgetzfsvbzfs', NULL, '2021-11-22 16:07:41', '2021-11-22 16:07:41', 1, 'normal');

-- --------------------------------------------------------

--
-- Structure de la table `vac_usersvaccins`
--

CREATE TABLE `vac_usersvaccins` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `vaccin_id` int(11) NOT NULL,
  `date_rappel` date DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `date_vaccination` date NOT NULL,
  `note` text DEFAULT NULL,
  `documents` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Déchargement des données de la table `vac_usersvaccins`
--

INSERT INTO `vac_usersvaccins` (`id`, `user_id`, `vaccin_id`, `date_rappel`, `created_at`, `date_vaccination`, `note`, `documents`) VALUES
(13, 52, 316, '2021-11-25', '2021-11-22 14:29:45', '2021-11-22', '', ''),
(14, 54, 316, '0000-00-00', '2021-11-22 15:08:47', '2021-11-18', 'erzgtrerzgtrerzgtrerzgtrerzgtrerzgtrerz erzgtrerzgtrerzgtrerzgtrerzgtrerzgtr gtrerzgtrerzgtrerzgtrerzgtrerzgtr erzgtrerzg trerzgtrerzgtrerzgtrerzgtrerzgtr erzgtrerzgtrerzgtrerzgtrerzgtrerzgtrerzgtr zgtrerzgtrerzgtrerzgtr', 'BC1-projet1.pdf,BC1-projet1 - Copie (2).pdf,BC1-projet1 - Copie (4).pdf,BC1-projet1 - Copie (6).pdf,BC1-projet1 - Copie (5).pdf'),
(15, 52, 316, '2021-11-25', '2021-11-22 14:29:45', '2021-11-22', '', ''),
(16, 54, 323, '0000-00-00', '2021-11-23 11:20:57', '2021-11-10', '', 'fer.pdf');

-- --------------------------------------------------------

--
-- Structure de la table `vac_vaccins`
--

CREATE TABLE `vac_vaccins` (
  `id` int(11) NOT NULL,
  `nom_vaccin` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `temps_rappel` int(11) NOT NULL,
  `temps_rappel2` int(11) DEFAULT NULL,
  `createur` varchar(100) NOT NULL,
  `status` varchar(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Déchargement des données de la table `vac_vaccins`
--

INSERT INTO `vac_vaccins` (`id`, `nom_vaccin`, `description`, `temps_rappel`, `temps_rappel2`, `createur`, `status`) VALUES
(311, 'ACT-HIB', 'Vaccin conjugué contre Haemophilus influenzae type b - Act-HIB 10 microgrammes/0,5 mL, poudre et solvant pour solution injectable en seringue préremplie.Vaccin disponible.', 0, 0, 'Sanofi Pasteur', 'indisponible'),
(312, 'AVAXIM 160 U', 'Vaccin contre l&#39;hépatite A (inactivé, adsorbé).Vaccin disponible.', 0, 0, 'Sanofi Pasteur', 'disponible'),
(313, 'AVAXIM 80 U', 'Vaccin contre l&#39;hépatite A (inactivé, adsorbé).Vaccin disponible.', 0, 0, 'Sanofi Pasteur', 'disponible'),
(314, 'BEXSERO', 'Vaccin méningococcique groupe B (ADNr, composant, adsorbé).Vaccin disponible. La primovaccination des nourrissons âgés de 2 à 5 mois peut être réalisée avec deux doses espacées de deux mois au lieu de trois doses espacées d un mois. Le schéma à deux doses peut être utilisé dès l âge de 2 mois depuis mai 2020. Juin 2021 : la Haute Autorité de santé recommande la vaccination de tous les nourrissons contre la méningite B avec le vaccin Bexsero.', 5, 0, 'GSK Vaccines', 'disponible'),
(315, 'BOOSTRIXTETRA', 'Vaccin diphtérique, tétanique, coquelucheux (acellulaire, multicomposé) et poliomyélitique (inactivé), (adsorbé, à teneur réduite en antigènes).Vaccin disponible. Nouvelle indication : Boostrix Tetra peut être administré au 2ème et 3ème trimestre de la grossesse en vue d induire une immunisation maternelle pendant la grossesse permettant la protection du nouveau-né à naître contre la coqueluche (RCP 11/2020).', 3, 0, 'GSK Vaccines', 'disponible'),
(316, 'CERVARIX', 'Vaccin Papillomavirus Humain [Types 16, 18] (Recombinant, avec adjuvant, adsorbé).Vaccin disponible.', 0, 0, 'GSK Vaccines', 'disponible'),
(317, 'COMIRNATY Pfizer-BioNTech', 'Concentré pour dispersion injectable - Vaccin à ARN messager (à nucléoside modifié) contre la covid 19. Autres dénominations : Pfizer-BioNTech COVID-19 VACCINE ; Bnt162b2.Maintenant disponible dans les cabinets médicaux et les pharmacies d officine. Recommandation de la Haute Autorité de santé du 5 novembre 2021 : ce vaccin est recommandé préférentiellement au vaccin Spikevax (autre vaccin à ARN autorisé) avant l âge de 30 ans (que ce soit en primovaccination ou en rappel). Des recommandations complémentaires concernant la vaccination des personnes immunodéprimées ou ayant été infectées par le SARS-CoV-2 seront publiées prochainement. La FDA (agence des médicaments des Etats-Unis) a autorisé le 28 octobre 2021 l utilisation de ce vaccin chez les 5-11 ans (évaluation en cours par l Agence européenne des médicaments).', 0, 0, 'BioNTech-Pfizer', 'indisponible'),
(318, 'COVID-19 Vaccine Janssen', 'Vaccin anti-covid 19 à vecteur viral non réplicatif (adénovirus). Autres dénominations : Ad26COV2.S ; JMJ Vaccine, Janssen COVID-19 Vaccine ou J &amp; J COVID-19 Vaccine (J &amp; J : Johnson &amp; Johnson, laboratoire pharmaceutique dont Janssen est une filiale).La Haute Autorité de santé recommande un rappel vaccinal 4 semaines après la primovaccination par le vaccin de Janssen avec un vaccin à ARNm : une dose de Comirnaty à partir de l âge de 18 ans, ou une demi-dose de Spikevax à partir de l âge de 30 ans, pour réduire le risque de myocardite.', 0, 0, 'Janssen', 'indisponible'),
(319, 'DENGVAXIA', 'Vaccin dengue vivant atténué, chimérique recombinant basé sur le vaccin fièvre jaune 17D, quadrivalent.Ce vaccin n est pas recommandé pour les voyageurs d une zone non endémique vers une zone endémique. Il n est pas recommandé pour les résidents de La Réunion et de Mayotte. Pour les personnes qui vivent en Guadeloupe, en Martinique ou en Guyane, la vaccination n est pas recommandée mais peut toutefois être proposée à celles apportant une preuve documentée d’une infection confirmée virologiquement.', 0, 0, 'Sanofi Pasteur', 'indisponible'),
(320, 'DUKORAL', 'Vaccin cholérique recombinant.Le vaccin Dukoral est à nouveau disponible en pharmacie depuis le 2 mars 2020.', 0, 0, 'Valneva Sweden AB', 'indisponible'),
(321, 'EFLUELDA', 'Vaccin grippal inactivé à virion fragmenté quadrivalent haute dose contre la grippe saisonnière.Vaccin disponible. La campagne de vaccination a débuté le 22 octobre 2021. Ce vaccin peut être associé au rappel vaccinal contre la covid. Les résultats intermédiaires d une étude de co-administration de ce vaccin avec une dose de rappel du vaccin Spikevax montrent une efficacité et une sécurité similaires à celles de chaque vaccin administré individuellement.Depuis le 2 mars 2021, extension de l indication du vaccin EFLUELDA à partir de l âge de 60 ans au lieu de 65 ans. Toutefois, le coût du vaccin n est pris en charge par l assurance maladie qu à partir de l âge de 65 ans.Ce vaccin contient une quantité plus importante d antigène et suscite ainsi une meilleure réponse immunitaire que d autres vaccins grippaux.', 720, 780, 'Sanofi Pasteur', 'disponible'),
(322, 'ENCEPUR', 'Vaccin de l&#39;encéphalite à tiques (inactivé adsorbé).', 0, 0, 'GSK Vaccines', 'indisponible'),
(323, 'ENGERIX B 10 µg', 'ENGERIX B 10 microgrammes/0,5 mL, suspension injectable en seringue préremplie. Vaccin de l&#39;hépatite B (ADNr), (adsorbé) (VHB).Vaccin disponible.', 0, 0, 'GSK Vaccines', 'disponible'),
(324, 'ENGERIX B 20 µg', 'ENGERIX B 20 microgrammes/1 mL, suspension injectable en seringue préremplie. Vaccin de l&#39;hépatite B (ADNr), (adsorbé) (VHB).Vaccin disponible.', 0, 0, 'GSK Vaccines', 'disponible'),
(325, 'Ervebo', 'Vaccin contre Ebola Zaïre (rVSV&amp;#916;G-ZEBOV-GP, vivant).Vaccin autorisé en Europe depuis le 11 novembre 2019. La Haute autorité de santé estime que ce vaccin confère une amélioration du service médical rendu majeure (ASMR I) dans la stratégie de prévention de la maladie à virus Ebola due au virus Zaïre selon les recommandations nationales en vigueur.', 0, 0, 'MSD Vaccins', 'indisponible'),
(326, 'FLUENZ TETRA', 'Vaccin grippal vivant atténué quadrivalent, nasal.Ce vaccin n est plus commercialisé en France. Il peut toutefois être importé et mis à disposition de manière occasionnelle.Vaccin grippal vivant atténué administré par voie nasale. La nouvelle composition des vaccins grippaux a été publiée par l OMS en mars 2021 (deux souches différentes sur quatre). Le 29/07/2021, le RCP a été modifié pour prendre en compte les 2 nouvelles souches.', 0, 0, 'AstraZeneca AB', 'indisponible'),
(327, 'Fluzone High-Dose Quadrivalent', 'Vaccin contre les virus de la grippe A et B.Vaccin autorisé aux USA pour l immunisation active des personnes âgées de 65 ans et plus contre la grippe causée par les sous-types A et B du virus de la grippe. Il s agit d un équivalent du vaccin Efluelda, autorisé en France. Le vaccin Efluelda ne sera pas disponible en France durant la saison 2020-2021, mais il devrait y être remplacé par des doses importées de Fluzone High-Dose Quadrivalent.', 0, 0, 'Sanofi Pasteur', 'indisponible'),
(328, 'GARDASIL', 'Vaccin papillomavirus humain [types 6, 11, 16, 18] (recombinant, adsorbé).Arrêt de commercialisation le 31/12/2020. Remplacé par le Gardasil 9, qui confère une protection contre 5 types de papillomavirus supplémentaires par rapport à Gardasil.', 0, 0, 'MSD Vaccins', 'indisponible'),
(329, 'GARDASIL 9', 'Vaccin papillomavirus humain [types 6, 11, 16, 18, 31, 33, 45, 52, 58] (recombinant, adsorbé).Vaccin disponible. Remise à disposition normale depuis le 23 août 2021. Vaccin remboursé également chez les garçons selon les recommandations en vigueur (JO du 4 décembre 2020). La HAS a recommandé en décembre 2019 de vacciner tous les garçons avec le même schéma vaccinal que chez les filles : deux doses de vaccin Gardasil 9 (M0-M6) chez les 11-14 ans révolus et en rattrapage chez les 15-19 ans révolus avec 3 doses (M0-M2-M6). Cette recommandation est en vigueur depuis le 1er janvier 2021.', 168, 228, 'MSD Vaccins', 'disponible'),
(330, 'HAVRIX 1 440 U', 'Vaccin inactivé de l&#39;hépatite A adsorbé.Vaccin disponible.', 0, 0, 'GSK Vaccines', 'disponible'),
(331, 'HAVRIX 720 U', 'Vaccin inactivé de l&#39;hépatite A adsorbé.Vaccin disponible.', 0, 0, 'GSK Vaccines', 'disponible'),
(332, 'HBVAXPRO 10 µg', 'Vaccin contre l&#39;hépatite B (ADNr).Vaccin disponible.', 0, 0, 'MSD Vaccins', 'disponible'),
(333, 'HBVAXPRO 5 µg', 'Vaccin contre l&#39;hépatite B (ADNr).Vaccin disponible.', 0, 0, 'MSD Vaccins', 'disponible'),
(334, 'HEXYON', 'Vaccin diphtérique, tétanique, coquelucheux (acellulaire, multicomposé), de l&#39;hépatite B(ADNr), poliomyélitique (inactivé) et conjugué de l&#39;Haemophilus influenzae type b, adsorbé.Vaccin disponible. Le vaccin est prêt à l emploi et ne nécessite pas de reconstitution.', 0, 0, 'Sanofi Pasteur', 'disponible'),
(335, 'IMOVAX POLIO', 'Vaccin poliomyélitique inactivé.Vaccin disponible.', 0, 0, 'Sanofi Pasteur', 'disponible'),
(336, 'INFANRIX HEXA', 'Vaccin diphtérique (D), tétanique (T), coquelucheux (acellulaire, multicomposé) (Ca), de l&#39;hépatite B (ADNr) (HepB), poliomyélitique inactivé (P) et d&#39;Haemophilus influenzae type b (Hib) conjugué (adsorbé).Tensions d approvisionnement en ville jusque fin novembre 2021. Il est possible de substituer un autre vaccin hexavalent pour la deuxième ou la troisième dose.', 0, 0, 'GSK Vaccines', 'indisponible'),
(337, 'INFANRIXQUINTA', 'Vaccin diphtérique (D), tétanique (T), coquelucheux (acellulaire, multicomposé) (Ca), poliomyélitique (inactivé) (P) et conjugué de l’Haemophilus influenzae type b (Hib), adsorbé.Disponible dans les collectivités - Tensions sur le marché de ville : dépannages possibles en pharmacie de ville pour les patients chez lesquels un schéma vaccinal avec InfanrixQuinta a déjà été débuté.', 0, 0, 'GSK Vaccines', 'indisponible'),
(338, 'INFANRIXTETRA', 'Vaccin diphtérique, tétanique, coquelucheux acellulaire, poliomyélitique inactivé, adsorbé.Vaccin indisponible. Un produit équivalent est disponible : le vaccin TETRAVAC-ACELLULAIRE.', 0, 0, 'GSK Vaccines', 'indisponible'),
(339, 'INFLUSPLIT TETRA', 'Vaccin grippal inactivé à virion fragmenté quadrivalent contre la grippe saisonnière.Equivalent du vaccin Fluarix Tetra. La nouvelle composition des vaccins grippaux a été publiée par l OMS en mars 2021 (deux souches différentes sur quatre).', 0, 0, 'GSK Vaccines', 'indisponible'),
(340, 'INFLUVAC TETRA', 'Vaccin quadrivalent à antigènes de surface contre la grippe saisonnière.Ce vaccin est disponible et peut être associé au rappel vaccinal contre la covid. Il est autorisé pour toutes les personnes âgées de 6 mois et plus (mais le remboursement n est possible qu à partir de l âge de 3 ans). La nouvelle composition des vaccins grippaux a été publiée par l OMS en mars 2021 (deux souches différentes sur quatre). La campagne de vaccination a débuté le 22 octobre 2021.', 0, 0, 'MYLAN MEDICAL SAS', 'indisponible'),
(341, 'IXIARO', 'Vaccin contre l&#39;encéphalite japonaise (inactivé, adsorbé).Pour les personnes vaccinées antérieurement avec un schéma complet par Jevax et à nouveau en situation d exposition au virus, une dose de rappel par Ixiaro est considérée comme suffisante pour les adultes (recommandation faite hors autorisation de mise sur le marché).', 0, 0, 'Valneva Austria GmbH', 'indisponible'),
(342, 'M-M-RVAXPRO', 'Vaccin rougeoleux, des oreillons et rubéoleux (vivant).Vaccin disponible. Arrêt de la commercialisation de la forme flacon. Remise à disposition de la présentation flacon de poudre et seringue de solvant préremplie depuis le 17/05/2021. La forme flacon de poudre et flacon de solvant n’est plus disponible.', 0, 0, 'MSD Vaccins', 'disponible'),
(343, 'MENJUGATE 10 µg', 'Vaccin conjugué méningococcique du groupe C (adsorbé).Vaccin disponible.', 0, 0, 'GSK Vaccines', 'disponible'),
(344, 'MENVEO', 'Vaccin méningococcique des groupes A, C, W-135 et Y conjugué.Vaccin disponible.', 0, 0, 'GSK Vaccines', 'disponible'),
(345, 'NEISVAC', 'Vaccin méningococcique polyosidique du groupe C (conjugué adsorbé).Vaccin disponible. Calendrier vaccinal général : une dose à l âge de 5 mois, suivie d une dose de rappel à l âge de 12 mois. Modification du RCP le 6 août 2021 : 1) si possible, utiliser le même vaccin tout au long de la série de vaccination) ; 2) réponse immunitaire de 95,7 % si le vaccin Neisvac est administré un mois après le vaccin tétanique, comparé à 100 % en cas d administration simultanée des deux vaccins ; 3) remaniement de la rubrique \"Mises en garde et précautions d emploi\".', 5, 12, 'Pfizer', 'disponible'),
(346, 'NIMENRIX', 'Vaccin méningococcique conjugué des groupes A, C, W et Y.Vaccin disponible.', 0, 0, 'Pfizer', 'disponible'),
(347, 'PENTAVAC', 'Vaccin diphtérique, tétanique, coquelucheux (acellulaire, multicomposé), poliomyélitique (inactivé), et conjugué de l&#39;haemophilus type b, adsorbé.Vaccin disponible.', 0, 0, 'Sanofi Pasteur', 'disponible'),
(348, 'PNEUMOVAX', 'Vaccin pneumococcique polyosidique.Vaccin disponible.', 0, 0, 'MSD Vaccins', 'disponible'),
(349, 'PREVENAR 13', 'Vaccin pneumococcique polyosidique conjugué (13-valent, adsorbé).Vaccin disponible.', 0, 0, 'Pfizer', 'disponible'),
(350, 'PRIORIX', 'Vaccin rougeoleux, des oreillons et rubéoleux (vivant).Vaccin disponible.', 0, 0, 'GSK Vaccines', 'disponible'),
(351, 'RABIPUR', 'Vaccin rabique pour usage humain, préparé sur cultures cellulaires. Autres dénominations : KD-357 ; Purified chicken-embryo cell rabies vaccine ; RabAvert ; Rabivac ; Rasilvax.Vaccin disponible.', 0, 0, 'GSK Vaccines', 'disponible'),
(352, 'REPEVAX', 'Vaccin diphtérique (contenu réduit en antigène), tétanique, coquelucheux acellulaire et poliomyélitique (inactivé, adsorbé).Vaccin disponible. Le résumé des caractéristiques du produit a été mis à jour le 5 juillet 2021 (durée de conservation étendue de 3 à 4 ans) puis le 4 août 2021 (données d immunogénicité et d efficacité vaccinale contre la coqueluche chez les nourrissons et les jeunes enfants nés de femmes vaccinées pendant la grossesse).', 48, 0, 'Sanofi Pasteur', 'disponible'),
(353, 'REVAXIS', 'Vaccin diphtérique, tétanique et poliomyélitique (inactivé, adsorbé).Vaccin disponible.', 0, 0, 'Sanofi Pasteur', 'disponible'),
(354, 'ROTARIX', 'Vaccin oral à rotavirus (vivant).Vaccin disponible.', 0, 0, 'GSK Vaccines', 'disponible'),
(355, 'ROTATEQ', 'Vaccin rotavirus (vivant, oral).Vaccin disponible.', 0, 0, 'MSD Vaccins', 'disponible'),
(356, 'SPIKEVAX - COVID-19 Vaccine Moderna', 'Vaccin à ARNm (à nucléoside modifié) anti-covid 19, dispersion pour injection. Autre dénomination : Moderna mRNA-1273.Dernière mise à jour le 15 novembre 2021 (rubrique \"Effets indésirables\"). Recommandation de la Haute Autorité de santé du 5 novembre 2021 : ce vaccin n est pas recommandé avant l âge de 30 ans (que ce soit en primovaccination ou en rappel), et peut être utilisé pour le rappel vaccinal (demi-dose) 6 mois après la dernière dose de primovaccination. Des recommandations complémentaires concernant la vaccination des personnes immunodéprimées ou ayant été infectées par le SARS-CoV-2 seront publiées prochainement. Avant l âge de 30 ans, il faut préférer l utilisation du vaccin Comirnaty.', 0, 0, 'Moderna-NIAID', 'indisponible'),
(357, 'SPIROLEPT', 'Vaccin leptospires inactivé.Vaccin disponible.', 0, 0, 'IMAXIO', 'disponible'),
(358, 'STAMARIL', 'Vaccin contre la fièvre jaune (vivant) -  poudre et solvant pour suspension injectable en seringue préremplie.Cette vaccination ne peut être effectuée que dans des centres de vaccination habilités ou, en Guyane, par des médecins généralistes habilités.', 0, 0, 'Sanofi Pasteur', 'indisponible'),
(359, 'TETRAVAC-ACELLULAIRE', 'Vaccin diphtérique, tétanique, coquelucheux acellulaire, poliomyélitique (inactivé, adsorbé).Vaccin disponible. Vaccin pédiatrique à dosage normal en anatoxines, et donc utilisable pour réaliser une primovaccination. Il est également utilisé pour les rappels (notamment pour le rappel de 6 ans). Mise à jour le 9 juillet 2021 de la rubrique « Pharmacodynamie » : réponses immunitaires après injection de rappel chez les personnes âgées de 4 à 13 ans ; efficacité vaccinale et efficacité sur le terrain contre la coqueluche.', 72, 156, 'Sanofi Pasteur', 'disponible'),
(360, 'TICOVAC 0,25 ml ENFANTS', 'Vaccin de l&#39;encéphalite à tiques (virus entier inactivé).Vaccin disponible.', 0, 0, 'Pfizer', 'disponible'),
(361, 'TICOVAC 0,5 ml ADULTES', 'Vaccin de l&#39;encéphalite à tiques (virus entier inactivé).Vaccin disponible.', 0, 0, 'Pfizer', 'disponible'),
(362, 'TRUMENBA', 'Vaccin méningococcique groupe B (recombinant, adsorbé).Vaccin autorisé dans les pays de l Union Européenne depuis le 24 mai 2017. Ce vaccin n est pas disponible en France pour l instant. Nouvelle recommandation vaccinale de la HAS publiée le 2 juin 2021 (cf. Indications).', 0, 0, 'Pfizer', 'indisponible'),
(363, 'TWINRIX ADULTE', 'Vaccin de l&#39;hépatite A (inactivé) et de l&#39;hépatite B (ADNr) (HAB) (adsorbé).Vaccin disponible.', 0, 0, 'GSK Vaccines', 'disponible'),
(364, 'TYAVAX', 'Vaccin de l&#39;hépatite A (inactivé, adsorbé) et typhoïdique (polyosidique).Vaccin disponible.', 0, 0, 'Sanofi Pasteur', 'disponible'),
(365, 'TYPHIM VI', 'Vaccin typhoïdique polyosidique.Vaccin disponible.', 0, 0, 'Sanofi Pasteur', 'disponible'),
(366, 'VACCIN BCG AJVaccines', 'Vaccin contre la tuberculose.Vaccin disponible en collectivités et indisponible en ville. Seul vaccin contre la tuberculose actuellement commercialisé en France, le vaccin BCG AJVaccines est disponible dans les établissements suivants : centres de vaccination, services de protection maternelle et infantile (PMI), centres de lutte antituberculeuse (CLAT), hôpitaux, maternités.', 0, 0, 'AJ VACCINES A/S', 'disponible'),
(367, 'VACCIN BCG BIOMED-LUBLIN', 'Vaccin contre la tuberculose.Le laboratoire Sanofi Pasteur Europe a annoncé le 4 mars 2019 la fin de la mise à disposition en France de ce vaccin dans le courant du mois de mars 2019. Un autre vaccin disponible peut être utilisé (vaccin BCG AJVaccines).', 0, 0, 'Sanofi Pasteur', 'indisponible'),
(368, 'VACCIN RABIQUE PASTEUR', 'Poudre et solvant pour suspension injectable en seringue préremplie. Vaccin rabique, inactivé.Vaccin disponible.', 0, 0, 'Sanofi Pasteur', 'disponible'),
(369, 'VAQTA 50', 'Hépatite A.Vaccin disponible.', 0, 0, 'MSD Vaccins', 'disponible'),
(370, 'VARILRIX', 'Vaccin varicelleux vivant.Vaccin disponible. Mise à jour du RCP du 27 juillet 2021 : nombreuses modifications, notamment des indications. Ce vaccin peut maintenant être utilisé chez les nourrissons immunocompétents âgés de 9 à 11 mois, avec un schéma à 2 doses espacées d au moins 3 mois (au lieu d un intervalle minimum de 6 semaines lorsque le vaccin est administré à partir de l âge de 12 mois).', 11, 12, 'GSK Vaccines', 'disponible'),
(371, 'VARIVAX', 'Vaccin varicelleux vivant.Vaccin disponible.', 0, 0, 'MSD Vaccins', 'disponible'),
(372, 'VAXELIS', 'Vaccin diphtérique, tétanique, coquelucheux (acellulaire, multicomposé), de l&#39;hépatite B (ADNr), poliomyélitique (inactivé), et conjugué de l&#39;Haemophilus de type b (adsorbé).Vaccin disponible.', 0, 0, 'MSD Vaccins', 'disponible'),
(373, 'VAXIGRIPTETRA', 'Vaccin grippal quadrivalent (inactivé, à virion fragmenté).Vaccin disponible. La campagne de vaccination a débuté le 22 octobre 2021. Ce vaccin peut être associé au rappel vaccinal contre la covid.', 0, 0, 'Sanofi Pasteur', 'disponible'),
(374, 'VAXZEVRIA - COVID-19 Vaccine AstraZeneca', 'Vaccin anti-covid 19 à vecteur viral non réplicatif (adénovirus de chimpanzé). Autres dénominations : Oxford AZD1222 ; ChAdOx1-S ; COVID-19 Vaccine AstraZeneca.Un rappel avec un vaccin à ARNm (1 dose de Comirnaty à partir de l âge de 18 ans ou 1/2 dose de Spikevax à partir de l âge de 30 ans) est recommandée au moins 6 mois après la primovaccination par Vaxzevria, y compris en cas de schéma hétérologue, à toutes les personnes âgées de 65 ans et plus ainsi qu aux personnes à risque d une forme grave de covid 19 ou dans l entourage de personnes immunodéprimées (HAS, 05/11/2021). Egalement fabriqué sous licence en Russie par R-Pharma (R-COVI) et en Inde par le Serum Institute of India (COVISHIELD). Pour améliorer la protection contre le variant Delta, la HAS recommande i) d initier la primo-vaccination avec un vaccin à ARNm, en respectant un intervalle de 3 à 4 semaines entre les 2 doses et ii) pour les personnes ayant reçu une première dose de Vaxzevria, d utiliser pour la 2e dose un vaccin à ARNm 4 semaines après la 1ère injection (au lieu de 12 semaines auparavant). Modification RCP du 20/10/2021 : ajout des l effets indérirables thrombocytopénie immunitaire, paralysie faciale et spasmes musculaires.', 0, 0, 'AstraZeneca-University of Oxford', 'indisponible'),
(375, 'ZOSTAVAX', 'Vaccin zona (vivant atténué).Vaccin disponible. L information sur ce vaccin a été mise à jour par l agence européenne du médicament le 5 mars 2020 et le 27 janvier 2021. L’utilisation concomitante du vaccin Zostavax et d’un vaccin pneumococcique polyosidique comportant 23 valences a conduit à une diminution de l’immunogénicité du vaccin Zostavax au cours d une petite étude clinique. Toutefois, les données recueillies lors d’une vaste étude observationelle n’ont pas révélé de risque accru de zona après l’administration concomitante des deux vaccins. Ces deux vaccins peuvent donc être administrés le même jour sur deux sites différents.', 0, 0, 'MSD Vaccins', 'disponible');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `vac_rdv`
--
ALTER TABLE `vac_rdv`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `vac_users`
--
ALTER TABLE `vac_users`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `vac_usersvaccins`
--
ALTER TABLE `vac_usersvaccins`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `vac_vaccins`
--
ALTER TABLE `vac_vaccins`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `vac_rdv`
--
ALTER TABLE `vac_rdv`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT pour la table `vac_users`
--
ALTER TABLE `vac_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=56;

--
-- AUTO_INCREMENT pour la table `vac_usersvaccins`
--
ALTER TABLE `vac_usersvaccins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT pour la table `vac_vaccins`
--
ALTER TABLE `vac_vaccins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=376;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
