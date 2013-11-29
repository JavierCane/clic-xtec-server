//versió antiga

CREATE TABLE
DROIDCLIC_clics 
(
id INT/* primary key*/,
titol varchar(255),
descripcio varchar(2048),
lang varchar(2),
autors varchar(255),
llicencia varchar(255),
nivell varchar(255),
area varchar(255),
logoUrl varchar(255),
urlBase varchar(255),
inst varchar(255),
clicPrincipal varchar(255),
clicsAdicionals varchar(1024),
tipusActivitats varchar(1024)
)DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci


//versió nova

CREATE TABLE `DROIDCLIC_clics1` (
`id` int(11) DEFAULT NULL,
`titol` varchar(255) DEFAULT NULL,
`descripcio` varchar(2048) DEFAULT NULL,
`lang` varchar(2) DEFAULT NULL,
`lang_de` bit(1) NOT NULL DEFAULT b'0',
`lang_en` bit(1) NOT NULL DEFAULT b'0',
`lang_ar` bit(1) NOT NULL DEFAULT b'0',
`lang_eu` bit(1) NOT NULL DEFAULT b'0',
`lang_rm` bit(1) NOT NULL DEFAULT b'0',
`lang_ca` bit(1) NOT NULL DEFAULT b'0',
`lang_es` bit(1) NOT NULL DEFAULT b'0',
`lang_eo` bit(1) NOT NULL DEFAULT b'0',
`lang_fr` bit(1) NOT NULL DEFAULT b'0',
`lang_gl` bit(1) NOT NULL DEFAULT b'0',
`lang_el` bit(1) NOT NULL DEFAULT b'0',
`lang_it` bit(1) NOT NULL DEFAULT b'0',
`lang_la` bit(1) NOT NULL DEFAULT b'0',
`lang_oc` bit(1) NOT NULL DEFAULT b'0',
`lang_pt` bit(1) NOT NULL DEFAULT b'0',
`lang_ro` bit(1) NOT NULL DEFAULT b'0',
`lang_sv` bit(1) NOT NULL DEFAULT b'0',
`lang_zh` bit(1) NOT NULL DEFAULT b'0',
`autors` varchar(255) DEFAULT NULL,
`llicencia` varchar(255) DEFAULT NULL,
`nivell` varchar(255) DEFAULT NULL,
`area` varchar(255) DEFAULT NULL,
`area_lleng` bit(1) NOT NULL DEFAULT b'0',
`area_mat` bit(1) NOT NULL DEFAULT b'0',
`area_soc` bit(1) NOT NULL DEFAULT b'0',
`area_exp` bit(1) NOT NULL DEFAULT b'0',
`area_mus` bit(1) NOT NULL DEFAULT b'0',
`area_vip` bit(1) NOT NULL DEFAULT b'0',
`area_ef` bit(1) NOT NULL DEFAULT b'0',
`area_tec` bit(1) NOT NULL DEFAULT b'0',
`area_div` bit(1) NOT NULL DEFAULT b'0',
`logoUrl` varchar(255) DEFAULT NULL,
`urlBase` varchar(255) DEFAULT NULL,
`inst` varchar(255) DEFAULT NULL,
`clicPrincipal` varchar(255) DEFAULT NULL,
`clicsAdicionals` varchar(1024) DEFAULT NULL,
`tipusActivitats` varchar(1024) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;