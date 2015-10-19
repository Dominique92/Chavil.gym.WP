<?php

/**
 * Affichage du contenu de la catégorie "Accueil" dans une page
 */
add_shortcode ('accueil', 'gym_affiche_categorie_accueil');
function gym_affiche_categorie_accueil () {
	$results = $GLOBALS['wpdb']->get_results ('SELECT term_id FROM wp_terms WHERE name = "Accueil"', OBJECT);
	query_posts ('cat='.$results[0]->term_id.'&orderby=ID');
	return '';
}

/**
 * Affichage des horaires des cours
 * Remplace [horaires] par le tableau coloré des cours correspondant au titre de la page
 * Utilise les données entre les bbcode [horaires][/horaires] dans la fiche Horaires
 */
add_shortcode ('horaires', 'gym_affiche_horaires');
function gym_affiche_horaires () {
	$titre = get_the_title(); // Titre de la page (pour les filtres)
	$results = $GLOBALS['wpdb']->get_results ('SELECT ID, post_title, post_content FROM wp_posts WHERE post_status = "publish"', OBJECT);
	foreach ($results AS $p) {
		$gym_posts [$p->post_title] = $p->ID; // Mémorise l'id de chaque post en fonction de son titre
		$c = str_replace (array("<tr>\r\n\t\t\t<td>\r\n\t\t\t\t","\r\n\t\t\t</td>\r\n\t\t\t<td>\r\n\t\t\t\t","\r\n\t\t\t</td>\r\n\t\t</tr>"), array('[','|',']'), $p->post_content);
		preg_match_all('/\[(.*)\]/', $c, $h);
		foreach ($h[1] AS $hv) {
			$hvs = explode ('|', $hv);
			if (strpos ('|'.$hv.'|Horaires|', '|'.$titre.'|')
				&& count ($hvs) > 1)
				$gym_cours [array_shift ($hvs)] [] = $hvs;
		}
	}
	$r = "<div id=\"horaires\"><b>Séances</b>";
	if (isset ($gym_cours))
		foreach ($gym_cours AS $j => $c) {
			$r .= "<div id=\"".strtolower($j)."\">$j<table>";
				foreach ($c AS $cv) {
					$r .= "<tr>";
						foreach ($cv AS $cvk => $cvv)
							if (trim ($cvv) != $titre)
								$r .= ($n = @$gym_posts [trim ($cvv)])
									? "<td class=\"hc$cvk\"><a href=\"?p=$n\">$cvv</a></td>"
									: "<td class=\"hc$cvk\">$cvv</td>";
					$r .= "</tr>";
				}
			$r .= "</table></div>";
		}
	return $r."</div>";
}

/**
 * Affichage d'images a droite de la page.
 */
add_shortcode ('droite', 'gym_affiche_images_droite');
function gym_affiche_images_droite ($atts = array(), $content = NULL) {
	$img = array ();
	preg_match_all ('/(src|mp4)="([^"]+)\.([[:alnum:]]+)"/', urldecode ($content), $match);
	foreach ($match[3] AS $k=>$v)
		switch ($v) {
			case 'jpg':
			case 'jpeg':
			case 'gif':
				$img [] = '<img src="'.$match[2][$k].'.'.$match[3][$k].'" />';
				break;
//			case 'avi':
//				$img [] = '<embed src="'.$match[2][$k].'.'.$match[3][$k].'"></embed>';
//				break;
			case 'mp4':
				$img [] =
					'<video autoplay="true" loop="true"'.($iteration++ ? ' muted="true"' : '').'>'.
						'<source src="'.$match[2][$k].'.'.$match[3][$k].'" type="video/mp4">'.
							'Your browser does not support the &lt;video&gt; tag.'.
						'</source>'.
					'</video>';
				break;
			default:
				$img [] = '<p>Format '.$match[0][$k].'" inconnu.</p>';
		}
	return '<div class="droite">'.implode('',$img).'</div>';
}

/**
 * Menu de gestion (Connexion, ...).
 */
add_action ('widgets_init', create_function ('', 'return register_widget("gym_gestion");'));
class gym_gestion extends WP_Widget {
    function gym_gestion () {
        parent::WP_Widget (false, $name = 'Menu de gestion GYM');	
    }
 
	public function widget ($args, $instance) {
		echo $args['before_widget'];
		echo $args['before_title'] . 'Gestion' . $args['after_title'];
		echo '<ul>';
			echo '<li>'; wp_loginout(); echo '</li>';
			echo wp_register();
			if (is_user_logged_in ('users_can_register')) {
				$r = $GLOBALS['wpdb']->get_results ('SELECT ID FROM wp_posts WHERE post_status LIKE "publish" AND post_title = "Mode d\'emploi"', OBJECT);
				echo '<li><a target="mode_emploi" href="?p='.$r[0]->ID.'">Mode d\'emploi</a></li>';
				echo '<li><a target="gym_v2" href="http://chaville.gym.free.fr/v2">Prècèdent site</a></li>';
			}
		echo '</ul>';
		echo $args['after_widget'];
    }
}

/**
 * Pied de page.
 */
add_action( 'twentyfifteen_credits', 'gym_credits');
function gym_credits ()
{
	echo "
<p>Chavil'Gym est un club de la <a href=\"http://www.ffepgv.org/\">Fédération Française d'Education Physique et de Gymnastique Volontaire</a></p>
<p>Site réalisé par <a href=\"mailto:webmestre@cavailhez.fr\">Dominique Cavailhez</a></p>
";
}
/*
	Validation:
	<a href=\"https://validator.w3.org/check?uri=referer\">HTML5</a> -
	<a href=\"http://jigsaw.w3.org/css-validator/check/referer\">CSS3</a> -
	<a href=\"https://validator.w3.org/mobile/check/referer\">Mobile</a>
*/

/**
 * Redirection après login/logout.
 */
add_filter('login_redirect', 'login_logout_redirect');
add_filter('logout_redirect', 'login_logout_redirect');
function login_logout_redirect() {
	return '.';
}

/////////////////////////////////////////////////////////////
// TODO DELETE A PARTIR D'ICI QUAND LE SITE EST INITIALISE //
/////////////////////////////////////////////////////////////

// Textes du forum initial
$gym_init_posts = array (
	'Accueil' => array (
		'Contacts' => "
<p>&nbsp;</p>
<p><span style=\"color:#008000;\"><strong>Renseignements sur les cours :</strong></span></p>
<p><span style=\"color:#008000;\">* Françoise Thélot 01 47 50 57 35</span></p>
<p><span style=\"color:#008000;\">* Geneviève Daël 01 47 50 37 48</span></p>
<p><span style=\"color:#008000;\">* Contact mail: [email=antoine.dael@wanadoo.fr]Geneviéve Daël[/email] ou tel: 06 70 77 42 90</span></p>
<p>&nbsp;</p>
<p><span style=\"color:#008000;\"><strong>Autres informations :</strong></span></p>
<p><span style=\"color:#008000;\">Fédération Française d'Education Physique et de Gymnastique Volontaire : <a href=\"http://www.ffepgv.org/\">http://www.ffepgv.org/</a></span></p>
<p><span style=\"color:#008000;\">Ville de Chaville : <a href=\"http://ville-chaville.fr/\">http://ville-chaville.fr/</a></span></p>
<p>&nbsp;</p>
<p><span style=\"color:#008000;\"><strong>Webmestre</strong></span></p>
<p><span style=\"color:#008000;\">Pour toute remarque sur le site web envoyez un message au [email=webmestre@cavailhez.fr]webmestre[/email]</span></p>
",
		'Concept – Objectifs' => "
<p class=\"carre_vert_titre\">Concept:</span></p>
<p class=\"carre_vert_texte\">Le Sport-Santé est un concept basé sur la gestion du capital santé de l'individu par l'activité physique.</span></p>
<p class=\"carre_vert_titre\">Objectif:</span></p>
<p class=\"carre_vert_texte\">Améliorer sa forme physique tout en se faisant plaisir et en découvrant de multiples activités.</span></p>
<p class=\"carre_vert_texte\">Bref, tout sauf l'ennui pour s'assurer un avenir en pleine forme et en toute autonomie. A tous les âges la vitalité, telle est notre promesse.</span></p>
<p class=\"carre_vert_titre\">Essayez:</span></p>
<p class=\"carre_vert_texte\">Les deux premières séances sont gratuites et les inscriptions se font sur les lieux de cours.</span></p>
<p class=\"carre_vert_texte\">Les inscriptions sont possibles toute l'année. N'hésitez pas à nous rejoindre.</span></p>
",
		'Inscriptions' => "
<p class=\"ecxMsoNormal\"><span style=\"font-size: 11pt; color: blue; font-family: Arial;\">&nbsp;&nbsp; <span>Assurance complémentaire &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&nbsp;<span>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;11€</span></p>
<p class=\"ecxMsoNormal\"><span style=\"font-size: 11pt; color: blue; font-family: Arial;\"><br /></span></p>
<p><span style=\"color: #0000ff; font-family: 'Comic Sans MS'; font-size: 15px;\">Certificat médical obligatoire pour les plus de 60 ans et les nouveaux inscrits<br /></span></p>
<p class=\"BodyText2\" style=\"color: #0000ff; font-size: 15px; font-family: Arial;\"><span style=\"font-family: 'Comic Sans MS'; font-size: small;\">Remarque&nbsp;: Un minimum de 12 inscrits est nécessaire pour le maintien d’un cours</span></p>
<p class=\"BodyText2\" style=\"color: #0000ff; font-size: 15px; font-family: Arial;\"><span style=\"font-size: small;\"><strong style=\"mso-bidi-font-weight: normal;\"><span style=\"font-family: 'Comic Sans MS'; color: red;\">Inscriptions</span></strong></span></p>
<p class=\"MsoNormal\" style=\"color: #0000ff; font-size: 15px; font-family: Arial;\"><span style=\"font-size: small;\"><span style=\"font-family: Wingdings;\"><span style=\"mso-char-type: symbol; mso-symbol-font-family: Wingdings;\">Ø</span><span style=\"font-family: 'Comic Sans MS';\"> </span><strong style=\"mso-bidi-font-weight: normal;\"><span style=\"font-family: 'Comic Sans MS';\">toute l’année</span></strong></span></p>
<p class=\"MsoNormal\" style=\"color: #0000ff; font-size: 15px; font-family: Arial;\"><span style=\"font-size: small;\"><span style=\"font-family: Wingdings;\"><span style=\"mso-char-type: symbol; mso-symbol-font-family: Wingdings;\">Ø</span><span style=\"font-family: 'Comic Sans MS';\"> </span><strong style=\"mso-bidi-font-weight: normal;\"><span style=\"font-family: 'Comic Sans MS';\">sur les lieux de cours</span></strong></span></p>
<p class=\"BodyText2\" style=\"color: #0000ff; font-size: 15px; font-family: Arial;\"><span style=\"font-family: 'Comic Sans MS'; font-size: small;\">Documents disponibles auprès des correspondant(e)s de cours et des animateurs</span></p>
<p>&nbsp;</p>
<p class=\"carre_vert_texte\" style=\"margin-top: 0px; margin-bottom: 0px; font-size: 12px; color: #329932; background-color: #f7fff7; text-indent: 10px;\">Téléchargez la <a href=\"wp-content/uploads/plaquette_2015_2016.pdf\" title=\"Téléchargez la plaquette 2015-2016\" style=\"text-decoration: none; font-weight: bold; color: #3399cc;\">plaquette 2015-2016</a> pour avoir tous les détails.</span></p>
<p class=\"carre_vert_texte\" style=\"margin-top: 0px; margin-bottom: 0px; font-size: 12px; color: #329932; background-color: #f7fff7; text-indent: 10px;\">Téléchargez la <a href=\"wp-content/uploads/fiche_inscription_2015.pdf\" title=\"Téléchargez la fiche d'inscription\" style=\"text-decoration: none; font-weight: bold; color: #3399cc;\">fiche d'inscription</a></span></p>
",
		'Saison 2015-2016' => "
<p>Voir les nouveaux cours sur notre <a href=\"wp-content/uploads/plaquette_2015_2016.pdf\" title=\"Téléchargez la plaquette 2015-2016\" style=\"text-decoration: none; font-weight: bold; color: #3399cc;\">plaquette 2015-2016</a> :</span></p>
<p>- Rose pour un cours Gym zen mardi à 12h15 à l'atrium.</span></p>
<p>- Armelle pour un cours supplémentaire de Yoga le mercredi à 10h00 au gymnase Halimi.</span></p>
<p>- Emilie pour un cours gym cardio le mercredi à 20H45 à Paul Bert.</span></p>
<p>&nbsp;</p>
<p>L'association accepte les bons du CCAS et les bons ANCV.</span></p>
",
		'Nouveau cours de GYM ZEN' => "
<p>Venez essayer le nouveau cours de \"<strong>GYM ZEN</strong>\" .</p><p>Ce cours a&nbsp;<span style=\"font-size: 12.16px;\">lieu le mardi à 12H15 à l'Atrium, salle Mautice Béjart.</span></p><p><span style=\"font-family: Calibri, sans-serif; font-size: 16px;\">Il s'agit d'un cours zen mais ce n’est pas un cours de gym douce. Il comporte des séquences de renforcement musculaire mais dans une atmosphère très zen.</span></p><p style=\"text-align: justify;\"><span style=\"font-family: Calibri, sans-serif; font-size: 12pt;\">Il est ouvert à tous les adhérents et n’est pas réservé aux seules personnes travaillant en entreprise.</span></p>
",
		'Fête de l\'association' => "La fête de l'association aura lieu le samedi 30 Janvier 2016.",
	),
	'Les cours' => array (
		'Horaires' => str_replace (array('{','|','}'), array("<tr>\r\n\t\t\t<td>\r\n\t\t\t\t","\r\n\t\t\t</td>\r\n\t\t\t<td>\r\n\t\t\t\t","\r\n\t\t\t</td>\r\n\t\t</tr>"), "
<p>&diams; Tous les cours durent une heure. Pour chaque cours, on indique</p>
<p>=&gt; les horaires</p>
<p>=&gt; le lieu</p>
<p>=&gt; l&#39;animateur/animatrice</p>

[horaires]
<span style=\"color:#0000FF;\">La saisie des horaires est centralisée.
Pour supprimer un cours: cliquer sur une case et faire \"click droit\" -> Ligne -> Supprimer les lignes
Pour ajouter un cours: cliquer sur une case et faire \"click droit\" -> Ligne -> Insérer une ligne
ATTENTION: Le texte des cases doit être exactement celui des titres des pages pour activer les liens.
Conserver l'ordre par tranche horaire.</span>
<table border=\"1\" cellpadding=\"1\" cellspacing=\"1\" style=\"width:500px;\"><tbody>
{Lundi|Yoga|09:00 - 10:00|(07/09)|Gymnase Halimi|Rose}
{Lundi|Gym équilibre|14:30 - 15:30|(07/09)|Atrium / Maurice Béjart|Marie-Thérèse}
{Lundi|Multi-gym moyenne|18:30 - 19:30|(07/09)|Paul Bert|Claire}
{Lundi|Multi-gym moyenne|19:30 - 20:30|(07/09)|Paul Bert|Claire}
{Lundi|Gym cardio|20:40 - 21:40|(07/09)|Atrium Salle Tchaïkovski|Nathalie}
{Mardi|Pilates, renforcement musculaire profond|09:30 - 10:30|(08/09)|Atrium / Maurice Béjart|Nathalie}
{Mardi|Gym modérée|11:00 - 12:00|(08/09)|Atrium / Maurice Béjart|Florina}
{Mardi|Gym Zen|12:15 - 13:15|(08/09)|Atrium / Maurice Béjart|Rose}
{Mardi|Gym tonique|18:30 - 19:30|(08/09)|Ferdinand Buisson|Nathalie}
{Mardi|Pilates, renforcement musculaire, stretching|20:00 - 21:00|(08/09)|Gymnase Halimi|Nathalie}
{Mercredi|Yoga|08:45 - 09:45|(09/09)|Gymnase Halimi|Armelle}
{Mercredi|Yoga|10:00 - 11:00|(09/09)|Gymnase Halimi|Armelle}
{Mercredi|Multi-gym|18:30 - 19:30|(09/09)|Paul Bert|Marie-Thérèse}
{Mercredi|Multi-gym|19:30 - 20:30|(09/09)|Paul Bert|Marie-Thérèse}
{Mercredi|Gym cardio|20:45 - 21:45|(09/09)|Paul Bert|Émilie}
"/* {Mercredi|Danse-cardio-boxing|18:30 - 19:30|(09/09)|Doisu|Osvaldo} */."
{Jeudi|Gym nordique|09:30 - 10:30|(10/09)|Atrium / Maurice Béjart|Florina}
{Jeudi|Gym modérée|10:45 - 11:45|(10/09)|Atrium / Maurice Béjart|Émilie}
{Jeudi|Multi-gym|18:30 - 19:30|(10/09)|Ferdinand Buisson|Marie-Thérèse}
{Vendredi|Gym bien- être, Qi-gong|09:15 - 10:15|(11/09)|Atrium Salle Tchaïkovski|Arnaud}
{Vendredi|Multi-gym|10:30 - 11:30|(11/09)|Atrium Salle Tchaïkovski|Émilie}
{Vendredi|Acti’March|19:00 - 20:00|(11/09)|Jean Jaurès / Piste d’Athlétisme|Rose}
{Samedi|Gym tonique|09:00 - 10:00|(12/09)|Léo Lagrange|Sandrine}
{Samedi|Gym tonique|10:00 - 11:00|(12/09)|Léo Lagrange|Sandrine}
</tbody></table>
[/horaires]
"),
		'Multi-gym' => "
<p><span style=\"color:#000000;\"><strong>Les cours:</strong></span></p>
<p><span style=\"color:#000000;\">Multi-gym, travail du positionnement, travail du dos et des abdos</span></p>
<p><span style=\"color:#000000;\">Multi-gym : renforcement musculaire stretching, fitball</span></p>
<p><span style=\"color:#000000;\">Multi-gym, renforcement musculaire, stretching</span></p>
<p>&nbsp;</p>
<p><span style=\"color:#008000;\"><strong>La cible:</strong></span></p>
<p><span style=\"color:#008000;\">Les adultes.</span></p>
<p>&nbsp;</p>
<p><span style=\"color:#008000;\"><strong>Les besoins du public:</strong></span></p>
<p><span style=\"color:#008000;\">* Rechercher le bien-être,</span></p>
<p><span style=\"color:#008000;\">* Optimiser la condition physique et le capital santé,</span></p>
<p><span style=\"color:#008000;\">* Découvrir de nouvelles activités physiques et sportives.</span></p>
<p>&nbsp;</p>
<p><span style=\"color:#008000;\"><strong>Les activités physiques proposées:</strong></span></p>
<p><span style=\"color:#008000;\">* Echauffements articulaires et cardio-respiratoires</span></p>
<p><span style=\"color:#008000;\">* Fitness, enchaînements dansés, musculation</span></p>
<p><span style=\"color:#008000;\">* Travail des abdominaux</span></p>
<p><span style=\"color:#008000;\">* Etirements</span></p>
<p>&nbsp;</p>
<p><span style=\"color:#008000;\"><strong>Les objectifs à atteindre:</strong></span></p>
<p><span style=\"color:#008000;\">* Développer les potentialités physiques,</span></p>
<p><span style=\"color:#008000;\">* Apprendre à gérer l'effort,</span></p>
<p><span style=\"color:#008000;\">* Maintenir durablement son intégrité physique et psychique dans un environnement évolutif.</span></p>
[horaires]
",
		'Multi-gym moyenne' => "
<p><span style=\"color:#B22222;\">Multi-gym, intensité moyenne</span></p>
<p>&nbsp;</p>
<p><span style=\"color:#008000;\"><strong>La cible:</strong></span></p>
<p><span style=\"color:#008000;\">Jeunes et adultes.</span></p>
<p>&nbsp;</p>
<p><span style=\"color:#008000;\"><strong>Les besoins du public:</strong></span></p>
<p><span style=\"color:#008000;\">* Rechercher le bien-être,</span></p>
<p><span style=\"color:#008000;\">* Optimiser la condition physique et le capital santé,</span></p>
<p><span style=\"color:#008000;\">* Découvrir de nouvelles activités physiques et sportives.</span></p>
<p>&nbsp;</p>
<p><span style=\"color:#008000;\"><strong>Les activités physiques proposées:</strong></span></p>
<p><span style=\"color:#008000;\">* Echauffements</span></p>
<p><span style=\"color:#008000;\">* Fitness, enchaînements dansés,</span></p>
<p><span style=\"color:#008000;\">* Activités cardio (intensité moyenne), renforcement musculaire(abdos, fessiers), stretching,</span></p>
<p><span style=\"color:#008000;\">* Etirements</span></p>
<p>&nbsp;</p>
<p><span style=\"color:#008000;\"><strong>Les objectifs à atteindre:</strong></span></p>
<p><span style=\"color:#008000;\">* Développer les potentialités physiques,</span></p>
<p><span style=\"color:#008000;\">* Reprendre ou conserver le goût pour l'effort physique,</span></p>
<p><span style=\"color:#008000;\">* Améliorer son maintien</span></p>
<p><span style=\"color:#008000;\">* Maintenir durablement son intégrité physique et psychique dans un environnement évolutif.</span></p>
[horaires]
",
		'Gym cardio' => "
<p><span style=\"color:#B22222;\">Body Sculpt, Gym cardio</span></p>
<p>&nbsp;</p>
<p><span style=\"color:#008000;\"><strong>La cible:</strong></span></p>
<p><span style=\"color:#008000;\">Jeunes et adultes désirant faire une heure de grande dépense physique pour se muscler rapidement.</span></p>
<p>&nbsp;</p>
<p><span style=\"color:#008000;\"><strong>Activités proposées:</strong></span></p>
<p><span style=\"color:#008000;\">Des enchaînements rapides pour l'échauffement sur une musique entraînante</span></p>
<p><span style=\"color:#008000;\">Exercices de renforcement musculaire, en particulier des abdominaux.</span></p>
<p><span style=\"color:#008000;\">Etirements</span></p>
[horaires]
",
		'Gym équilibre' => "
<p>&nbsp;</p>
<p><span style=\"color:#008000;\"><strong>La cible:</strong></span></p>
<p><span style=\"color:#008000;\">Personnes seniors ne pratiquant plus d'activités physiques.</span></p>
<p><span style=\"color:#008000;\">Personnes ayant des problèmes d'équilibre.</span></p>
<p>&nbsp;</p>
<p><span style=\"color:#008000;\"><strong>Les besoins du public:</strong></span></p>
<p><span style=\"color:#008000;\">* Retrouver le plaisir de bouger</span></p>
<p><span style=\"color:#008000;\">* Perdre l'appréhension de la chute par une meilleure sensation d'équilibre</span></p>
<p><span style=\"color:#008000;\">* Reprendre un travail de musculation en douceur</span></p>
<p>&nbsp;</p>
<p><span style=\"color:#008000;\"><strong>Les activités physiques proposées:</strong></span></p>
<p><span style=\"color:#008000;\">* Echauffements des articulations et travail respiratoire</span></p>
<p><span style=\"color:#008000;\">* Exercices renforçant les capacités d'équilibre: travail des pieds, marche en ligne...</span></p>
<p><span style=\"color:#008000;\">* Exercices pour aller au sol et apprendre à se relever dans toutes les circonstances</span></p>
<p><span style=\"color:#008000;\">* Exercices variés de musculation du dos, des abdominaux, des fessiers</span></p>
<p>&nbsp;</p>
<p><span style=\"color:#008000;\"><strong>Les objectifs à atteindre:</strong></span></p>
<p><span style=\"color:#008000;\">* Retrouver le plaisir de se mouvoir sans appréhension de la chute</span></p>
<p><span style=\"color:#008000;\">* Remobiliser en douceur son corps pour se sentir plus en forme.</span></p>
<p><span style=\"color:#008000;\">* Maintenir durablement son activité physique</span></p>
<p>&nbsp;</p>
<p><span style=\"color:#008000;\"><strong>Pour tous renseignements, prendre contact avec :</strong></span></p>
<p><span style=\"color:#008000;\">Marie-Thérèse DUCLOS: 01 47 50 54 65</span></p>
[horaires]
",
		'Gym modérée' => "
<p><span style=\"color:#B22222;\">Gym modérée</span></p>
<p>&nbsp;</p>
<p><span style=\"color:#008000;\"><strong>La cible:</strong></span></p>
<p><span style=\"color:#008000;\">Les seniors désirant garder la forme</span></p>
<p>&nbsp;</p>
<p><span style=\"color:#008000;\"><strong>Les besoins du public:</strong></span></p>
<p><span style=\"color:#008000;\">* Rechercher le bien-être à travers une activité physique encadrée par une professionnelle</span></p>
<p><span style=\"color:#008000;\">* Passer une heure ludique et conviviale</span></p>
<p><span style=\"color:#008000;\">* Maintenir ses liens sociaux</span></p>
<p><span style=\"color:#008000;\">* Découvrir des activités physiques variées</span></p>
<p>&nbsp;</p>
<p><span style=\"color:#008000;\"><strong>Les activités physiques proposées:</strong></span></p>
<p><span style=\"color:#008000;\">* Travail en douceur des articulations</span></p>
<p><span style=\"color:#008000;\">* Séquences d'exercice de la mémoire</span></p>
<p><span style=\"color:#008000;\">* Musculation en profondeur sans à-coups</span></p>
<p><span style=\"color:#008000;\">* Travail de la posture</span></p>
<p><span style=\"color:#008000;\">* Etirements, auto-massages</span></p>
<p>&nbsp;</p>
<p><span style=\"color:#008000;\"><strong>Les objectifs à atteindre:</strong></span></p>
<p><span style=\"color:#008000;\">* Développer les potentialités physiques,</span></p>
<p><span style=\"color:#008000;\">* Apprendre à gérer l'effort,</span></p>
<p><span style=\"color:#008000;\">* Maintenir durablement son intégrité physique et psychique dans un environnement évolutif.</span></p>
[horaires]
",
		'Gym nordique' => "
<p><span style=\"color:#B22222;\">Gym nordique – intensité moyenne</span></p>
<p>&nbsp;</p>
<p><span style=\"color:#008000;\"><strong>La cible:</strong></span></p>
<p><span style=\"color:#008000;\">Les adultes et seniors en bonne forme</span></p>
<p>&nbsp;</p>
<p><span style=\"color:#008000;\"><strong>Les besoins du public:</strong></span></p>
<p><span style=\"color:#008000;\">* Rechercher le bien-être,</span></p>
<p><span style=\"color:#008000;\">* Conserver ou retrouver le plaisir de se bouger pour la forme</span></p>
<p><span style=\"color:#008000;\">* Découvrir de nouvelles façons de pratiquer des exercices physiques</span></p>
<p>&nbsp;</p>
<p><span style=\"color:#008000;\"><strong>Les activités physiques proposées:</strong></span></p>
<p><span style=\"color:#008000;\">* Echauffements variés pour les articulations et le renforcement cardio-vasculaire</span></p>
<p><span style=\"color:#008000;\">* Musculation avec ou sans charges,</span></p>
<p><span style=\"color:#008000;\">* Abdominaux, fessiers avec ballons, elasti-band&#8230;</span></p>
<p><span style=\"color:#008000;\">* Massages, étirements</span></p>
<p>&nbsp;</p>
<p><span style=\"color:#008000;\"><strong>Les objectifs à atteindre:</strong></span></p>
<p><span style=\"color:#008000;\">* Se sentir en forme et bien dans sa peau</span></p>
<p><span style=\"color:#008000;\">* Retrouver ou conserver la possibilité de faire des efforts</span></p>
<p><span style=\"color:#008000;\">* Faire du sport dans une ambiance très détendue et gaie</span></p>
[horaires]
",
		'Gym tonique' => "
<p>Gym tonique</span></p>
<p>&nbsp;</p>
<p><span style=\"color:#008000;\"><strong>La cible:</strong></span></p>
<p><span style=\"color:#008000;\">* adultes</span></p>
<p>&nbsp;</p>
<p><span style=\"color:#008000;\"><strong>Les besoins du public:</strong></span></p>
<p><span style=\"color:#008000;\">* Rechercher le bien-être,</span></p>
<p><span style=\"color:#008000;\">* Faire une heure d'exercices physique de façon conviviale</span></p>
<p>&nbsp;</p>
<p><span style=\"color:#008000;\"><strong>Les activités physiques proposées:</strong></span></p>
<p><span style=\"color:#008000;\">* Renforcement musculaire, stretching</span></p>
<p>&nbsp;</p>
<p><span style=\"color:#008000;\"><strong>Les objectifs à atteindre:</strong></span></p>
<p><span style=\"color:#008000;\">* Améliorer sa musculation et ses capacités cardiaques et respiratoires</span></p>
<p><span style=\"color:#008000;\">* Se bouger dans une ambiance détendue</span></p>
[horaires]
",
		'Gym Zen' => "
<p><span style=\"color:#B22222;\">Gym zen: travail postural, anti-stress</span></p>
<p>&nbsp;</p>
<p><span style=\"color:#008000;\"><strong>La cible:</strong></span></p>
<p><span style=\"color:#008000;\">Les adultes disponibles à l'heure du déjeuner.</span></p>
<p>&nbsp;</p>
<p><span style=\"color:#008000;\"><strong>Les besoins du public:</strong></span></p>
<p><span style=\"color:#008000;\">* Introduire une coupure dans la journée</strong></span></p>
<p><span style=\"color:#008000;\">* Travailler sur les postures du poste de travail</span></p>
<p><span style=\"color:#008000;\">* Se détendre et améliorer sa forme</span></p>
<p>&nbsp;</p>
<p><span style=\"color:#008000;\"><strong>Les activités physiques proposées:</strong></span></p>
<p><span style=\"color:#008000;\">* Echauffements variés pour fluidifier la mobilité et faire travailler le système cardio-vasculaire</span></p>
<p><span style=\"color:#008000;\">* Travail du dos, des abdominaux et des fessiers</span></p>
<p><span style=\"color:#008000;\">* Etirements, relaxation</span></p>
<p>&nbsp;</p>
<p><span style=\"color:#008000;\"><strong>Les objectifs à atteindre:</strong></span></p>
<p><span style=\"color:#008000;\">* Passer une heure de ressourcement physique</span></p>
<p><span style=\"color:#008000;\">* Améliorer son tonus et sa forme</span></p>
<p><span style=\"color:#008000;\">* Apprendre à gérer l'effort</span></p>
[horaires]
",
		'Gym bien- être, Qi-gong' => "
<p><span style=\"color:#B22222;\">Gym bien- être, Qi gong et Tai chi, détente, travail postural et respiratoire</span></p>
<p>&nbsp;</p>
<p><span style=\"color:#008000;\"><strong>La cible:</strong></span></p>
<p><span style=\"color:#008000;\">Adultes et seniors.</span></p>
<p>&nbsp;</p>
<p><span style=\"color:#008000;\"><strong>Les besoins du public:</strong></span></p>
<p><span style=\"color:#008000;\">* Rechercher le bien-être, l'harmonie du corps</span></p>
<p><span style=\"color:#008000;\">* Utiliser son énergie interne pour du mieux être</span></p>
<p>&nbsp;</p>
<p><span style=\"color:#008000;\"><strong>Les activités physiques proposées:</strong></span></p>
<p><span style=\"color:#008000;\">* Exercices issus de la gymnastique chinoise: Tai Chi, Qi Gong, DO IN</span></p>
<p><span style=\"color:#008000;\">* Exercices de respiration, relaxation, détente</span></p>
<p>&nbsp;</p>
<p><span style=\"color:#008000;\"><strong>Les objectifs à atteindre:</strong></span></p>
<p><span style=\"color:#008000;\">* Renforcement de la santé, de l'équilibre physique et émotionnel</span></p>
<p><span style=\"color:#008000;\">* Amélioration de la souplesse et de l'utilisation du souffle</span></p>
<p><span style=\"color:#008000;\">* Meilleur confort articulaire, détente corporelle globale</span></p>
[horaires]
",
		'Pilates, renforcement musculaire profond' => "
<p><span style=\"color:#B22222;\">Cette séance s'appuie essentiellement sur des séquences de Pilates.</span></p>
<p><span style=\"color:#B22222;\">Cette méthode entraîne des constractions des muscles profonds avec peu de mouvements apparents.</span></p>
[horaires]
",
		'Pilates, renforcement musculaire, stretching' => "
<p><span style=\"color:#B22222;\">Ce cours s'adresse aux personnes qui  désirent renforcer leur musculation mais veulent terminer la séance par un temps de relâchement permettant de terminer la soirée sereinement.</span></p>
[horaires]
",
		'Yoga' => "
<p>&nbsp;</p>
<p><span style=\"color:#008000;\"><strong>La cible:</strong></span></p>
<p><span style=\"color:#008000;\">* adultes et seniors</span></p>
<p>&nbsp;</p>
<p><span style=\"color:#008000;\"><strong>Les besoins du public:</strong></span></p>
<p><span style=\"color:#008000;\">* Rechercher le bien-être, l'harmonie du corps</span></p>
<p><span style=\"color:#008000;\">* Découvrir de nouvelles méthodes pour y parvenir</span></p>
<p>&nbsp;</p>
<p><span style=\"color:#008000;\"><strong>Les activités physiques proposées:</strong></span></p>
<p><span style=\"color:#008000;\">* Les exercices revigorent et rafraîchissent à la fois le corps et l'esprit et apprennent à se détendre avec la conscience de la respiration et des sensations.</span></p>
<p><span style=\"color:#008000;\">Les séances comprennent une partie relaxation amenant la détente complète des muscles, de la peau, des organes, du système nerveux.</span></p>
<p><span style=\"color:#008000;\">* Une concentration permanente est demandée sur les sensations et la respiration.</span></p>
<p>&nbsp;</p>
<p><span style=\"color:#008000;\"><strong>Les objectifs à atteindre:</strong></span></p>
<p><span style=\"color:#008000;\">* renforcement de la santé, de l'équilibre physique et psychique</span></p>
<p><span style=\"color:#008000;\">* amélioration de la souplesse et de l'utilisation du souffle</span></p>
[horaires]
",
		'Acti’March' => "
<p>&nbsp;</p>
<p><span style=\"color:#008000;\"><strong>La cible:</strong></span></p>
<p><span style=\"color:#008000;\">Les adultes. seniors actifs</span></p>
<p>&nbsp;</p>
<p><span style=\"color:#008000;\"><strong>Les besoins du public:</strong></span></p>
<p><span style=\"color:#008000;\">* Renforcer ses activités cardio-respiratoires</span></p>
<p><span style=\"color:#008000;\">* Améliorer sa résistance à l'effort soutenu</span></p>
<p><span style=\"color:#008000;\">* Perdre du poids (à condition de faire une séance libre supplémentaire)</span></p>
<p>&nbsp;</p>
<p><span style=\"color:#008000;\"><strong>Les activités physiques proposées:</strong></span></p>
<p><span style=\"color:#008000;\">* Echauffements</span></p>
<p><span style=\"color:#008000;\">* Marche rapide à un rythme conseillé par l'animatrice, personnalisé pour chacun selon sa forme. Cette marche est faite avec un cardio-fréquencemètre. L'adhérent surveille ainsi l'impact de son effort.</span></p>
<p><span style=\"color:#008000;\">* Etirements</span></p>
<p>&nbsp;</p>
<p><span style=\"color:#008000;\"><strong>! Attention:</strong></span></p>
<p><span style=\"color:#008000;\"><u>Cette activité est proposée selon un calendrier (25 séances) car elle se déroule en extérieur et il y a donc peu de séances en hiver.</u></span></p>
<p><span style=\"color:#008000;\">Les adhérents passent un test avec l'animatrice pour déterminer la fréquence cardiaque à atteindre.</span></p>
<p>&nbsp;</p>
<p><span style=\"color:#008000;\"><strong>Les objectifs à atteindre:</strong></span></p>
<p><span style=\"color:#008000;\">* Etre capable d'efforts soutenus sans essoufflement</span></p>
<p><span style=\"color:#008000;\">* Apprendre à gérer l'effort</span></p>
<p><span style=\"color:#008000;\">Pour plus de renseignements, contacter [email=simon.rose@neuf.fr]Rose[/email]</span></p>
<p><span style=\"color:#008000;\">Téléchargez la <a href=\"wp-content/uploads/acti_march.pdf\">plaquette acti-march</a> pour la distribuer autour de vous.</span></p>
<p><span style=\"color:#008000;\">Téléchargez le <a href=\"wp-content/uploads/calendrier_actimarch_2014_20151.pdf\">Calendrier Acti March 2014-2015</a>.</span></p>
[horaires]
",
	),
	'Les salles' => array (
		'Atrium / Maurice Béjart' => "
<p>&nbsp;</p>
<p><span style=\"color:#008000;\"><strong>Adresse:</strong></span></p>
<p><span style=\"color:#008000;\">Atrium de Chaville, Salle Maurice Béjart</span></p>
<p><span style=\"color:#008000;\">12, rue de la Fontaine Henri IV</span></p>
<p>&nbsp;</p>
<p><span style=\"color:#008000;\"><strong>Accès:</strong></span></p>
<p><span style=\"color:#008000;\">Accès par la porte \"entrée des artistes\" située 12, rue de la Fontaine Henri IV, ascenseur jusqu'au 5° étage, redescendre par escalier (fléchage) jusqu'au 4° où se trouve la salle Maurice Béjart.</span></p>
[flexiblemap address=\"12, rue de la Fontaine Henri IV, Chaville fr\"]
[horaires]
",
		'Atrium Salle Tchaïkovski' => "
<p>&nbsp;</p>
<p><span style=\"color:#008000;\"><strong>Adresse:</strong></span></p>
<p><span style=\"color:#008000;\">Atrium de Chaville, Salle Tchaïkovski</span></p>
<p><span style=\"color:#008000;\">3, Parvis Robert Schuman ou 885, Avenue Roger Salengro</span></p>
<p>&nbsp;</p>
<p><span style=\"color:#008000;\"><strong>Accès:</strong></span></p>
<p><span style=\"color:#008000;\">Accès par le Parvis</span></p>
<p><span style=\"color:#008000;\">Chaussons obligatoires.</span></p>
[flexiblemap address=\"885, Avenue Roger Salengro, Chaville fr\"]
[horaires]
",
		'Ferdinand Buisson' => "
<p>&nbsp;</p>
<p><span style=\"color:#008000;\"><strong>Adresse:</strong></span></p>
<p><span style=\"color:#008000;\">273, avenue Roger Salengro</span></p>
<p>&nbsp;</p>
<p><span style=\"color:#008000;\"><strong>Accès:</strong></span></p>
<p><span style=\"color:#008000;\">Ecole Primaire Ferdinand Buisson.</span></p>
<p><span style=\"color:#008000;\">Entrée par l'arrière de l'école, en la contournant par la gauche.</span></p>
[flexiblemap address=\"273, avenue Roger Salengro, Chaville fr\"]
[horaires]
",
		'Gymnase Halimi' => "
Dojo du gymnase Halimi.
23 rue de la Fontaine Henri IV à l'angle de la rue du Gros Chêne.
[flexiblemap address=\"23 rue de la Fontaine Henri IV, Chaville fr\"]
[horaires]
",
		'Jean Jaurès / Piste d’Athlétisme' => "
Sur le stade Jean Jaurès. Le RDV est près de la loge des gardiens, au milieu du grand bâtiment, ou s'il pleut, sur les gradins.
[flexiblemap address=\"Rue Marcel Rebart, Chaville fr\"]
[horaires]
",
		'Léo Lagrange' => "
Accéder en montant au niveau du terrain de foot. Le gymnase Léo Lagrange est à ce niveau, au bout (vers la rue Albert Perdreaux).
[flexiblemap address=\"Rue Jean Jaurès, Chaville fr\"]
",
		'Paul Bert' => "
<p>&nbsp;</p>
<p><span style=\"color:#008000;\"><strong>Adresse:</strong></span></p>
<p><span style=\"color:#008000;\">3/5 rue de la Bataille de Stalingrad,(anciennement rue de Stalingrad) (derrière Monoprix)</span></p>
<p>&nbsp;</p>
<p><span style=\"color:#008000;\"><strong>Accès:</strong></span></p>
<p><span style=\"color:#008000;\">Ecole élémentaire Paul Bert.</span></p>
<p><span style=\"color:#008000;\">L'entrée se trouve sur le parvis derrière Monoprix. La salle se trouve au rez-de-chaussée sur la droite.</span></p>
<p><span style=\"color:#008000;\">Parking facile.</span></p>
[flexiblemap address=\"Rue de Stalingrad, Chaville fr\"]
[horaires]
",
	),
	'Les animateur[trice]s' => array (
		'Armelle' => '[horaires]',
		'Arnaud' => '[horaires]',
		'Claire' => '[horaires]',
		'Émilie' => '[horaires]',
		'Florina' => '[horaires]',
		'Marie-Thérèse' => '[horaires]',
		'Nathalie' => '[horaires]',
//        	'Osvaldo' => '[horaires]',
		'Rose' => '[horaires]',
		'Sandrine' => '[horaires]',
	),
	'Informations pratiques' => array (
		'Association' => "
<p>&nbsp;</p>
<p><span style=\"color:#008000;\"><strong>Informations légales:</strong></span></p>
<p><span style=\"color:#008000;\">Chavil’ Gymnastique Volontaire</span></p>
<p><span style=\"color:#008000;\">Association régie par la loi 1901 – déclarée à la Sous-préfecture de Boulogne-Billancourt (92), le 4 mai 2001</span></p>
<p><span style=\"color:#008000;\">N° enregistrement: 22013568</span></p>
<p><span style=\"color:#008000;\">Publication au Journal officiel sous le numéro 1691 le 2 juin 2001</span></p>
<p><span style=\"color:#008000;\">Siège social: 2, rue Jean Jaurès – 92370 Chaville</span></p>
<p><span style=\"color:#008000;\">Adresse postale: 15 rue Jean Jaurès - 92370 Chaville</span></p>
<p><span style=\"color:#008000;\">Tel: 01 47 50 37 48</span></p>

<p>&nbsp;</p>
<p><span style=\"color:#008000;\"><strong>Assemblée générale 2014:</strong></span></p>
<p><span style=\"color:#008000;\"><a href=\"wp-content/uploads/ag_2014.pdf\" title=\"Téléchargez le compte-rendu\">Compte-rendu de l'assemblée générale 2014</a></span></p>
<p>&nbsp;</p>
<p><span style=\"color:#008000;\"><strong>Assemblée générale 2013:</strong></span></p>
<p><span style=\"color:#008000;\"><a href=\"wp-content/uploads/ag_2013.pdf\" title=\"Téléchargez le compte-rendu\">Compte-rendu de l'assemblée générale 2013</a></span></p>
<p>&nbsp;</p>
<p><span style=\"color:#008000;\"><strong>Assemblée générale 2012:</strong></span></p>
<p><span style=\"color:#008000;\"><a href=\"wp-content/uploads/ag_2012.pdf\" title=\"Téléchargez le compte-rendu\">Compte-rendu de l'assemblée générale 2012</a></span></p>
<p>&nbsp;</p>
<p><span style=\"color:#008000;\"><strong>Assemblée générale 2011:</strong></span></p>
<p><span style=\"color:#008000;\"><a href=\"wp-content/uploads/ag_2011.pdf\" title=\"Téléchargez le compte-rendu\">Compte-rendu de l'assemblée générale 2011</a></span></p>
<p>&nbsp;</p>
<p><span style=\"color:#008000;\"><strong>Assemblée générale 2010:</strong></span></p>
<p><span style=\"color:#008000;\"><a href=\"wp-content/uploads/ag_2010.pdf\" title=\"Téléchargez le compte-rendu\">Compte-rendu de l'assemblée générale 2010</a></span></p>
<p>&nbsp;</p>
<p><span style=\"color:#008000;\"><strong>Assemblée générale 2009:</strong></span></p>
<p><span style=\"color:#008000;\"><a href=\"wp-content/uploads/ag_2009.pdf\" title=\"Téléchargez le compte-rendu\">Compte-rendu de l'assemblée générale 2009</a></span></p>
<p>&nbsp;</p>
<p><span style=\"color:#008000;\"><strong>Assemblée générale 2008:</strong></span></p>
<p><span style=\"color:#008000;\"><a href=\"wp-content/uploads/ag_2008.pdf\" title=\"Téléchargez le compte-rendu\">Compte-rendu de l'assemblée générale 2008</a></span></p>
<p>&nbsp;</p>
<p><span style=\"color:#008000;\"><strong>Assemblée générale 2007:</strong></span></p>
<p><span style=\"color:#008000;\"><a href=\"wp-content/uploads/ag_2007.pdf\" title=\"Téléchargez le compte-rendu\">Compte-rendu de l'assemblée générale 2007</a></span></p>
<p>&nbsp;</p>
<p><span style=\"color:#008000;\"><strong>Assemblée générale 2006:</strong></span></p>
<p><span style=\"color:#008000;\"><a href=\"wp-content/uploads/ag_2006.pdf\" title=\"Téléchargez le compte-rendu\">Compte-rendu de l'assemblée générale 2006</a></span></p>
",
	),
	'Caché' => array (
		'Mode d\'emploi' => str_replace (array ('{','|','}'), array ('<p><strong>','</strong></span></p>
<p style=\"margin: 0 0 10px 20px;\">','</p>'), "
{Créer une fiche| Créer (bandeau noir du haut) -> Article (ET NON PAS PAGE !)
-> saisir titre et texte -> sélectionner une catégorie (en bas à droite)
<i>pour que cette fiche apparaissent dans la page d'accueil, sélectionner \"Accueil\"</i>
-> Publier (en bleu à droite) -> Afficher l'article (en dessous du titre).}
{Modifier une fiche|Aller sur la fiche -> Modifier (en jaune en dessous du texte) -> Onglet visuel (en haut à droite des boutons d'édition) -> entrer les modifications
-> Mettre à jour (en bleu à droite) -> Afficher l'article (en dessous du titre).}
{Faire apparaitre une fiche en page d'accueil|Aller sur la fiche -> Modifier
-> dans \"Catégories\" (en bas à droite) cocher \"Accueil\"
-> Mettre à jour -> Afficher l'article.}
{Supprimer une fiche|Aller sur la fiche -> Modifier -> Déplacer dans la Corbeille (en rouge en haut à droite).}
{Modifier les horaires|Horaires -> Modifier -> faire glisser vers le bas l'ascenseur à droite du texte
-> modifier le tableau suivant le mode d'emploi (texte en bleu)
-> Mettre à jour -> Afficher l'article.}
{Ajouter une image ou un lien vers un document dans le texte d'une fiche|Aller sur la fiche -> Modifier -> positionner le curseur dans le texte
<i>Pour qu'une image apparaisse à droite du texte, il faut l'insérer entre les repères &#91;droite&#93; et &#91;/droite&#93; (si nécessaire, ajouter ces repères au dessus du texte)</i>
-> Ajouter un média (au dessus des boutons de l'éditeur) -> Envoyer des fichiers (onglet en haut à gauche) -> Choisir des fichiers
-> cliquez sur un fichier -> Ouvrir -> Insérer dans l'article (en bleu en bas à droite) -> Mettre à jour -> Afficher l'article.}
"),
	),
	'Archives' => array (
		'Randonnée pédestre' => "
<p><span style=\"color:#008000;\"><strong>Randonnée pédestre</strong></span></p>
<p><span style=\"color:#008000;\">Randonnées de plusieurs jours. <span style=\"color:red\">ACTIVITE SUSPENDUE</span></p>
<table width=\"100%\"><tbody>

<tr><td valign=\"top\"><a href=\"http://diapo.chemineur.fr/viewtopic.php?view=diapo&amp;t=3\" title=\"Voir le diaporama des plages du nord\"><img src=\"wp-content/uploads/berck.jpg\" /></a></td> <td valign=\"top\">
<p>&nbsp;</p>
<p><span style=\"color:#008000;\"><strong><a href=\"http://diapo.chemineur.fr/viewtopic.php?view=diapo&amp;t=3\" title=\"Voir le diaporama des plages du nord\">Photos sortie mai 2013</a></strong></span></p>
<p><span style=\"color:#008000;\">Randonnée pédestre du côté de Berck du 9 au 11 mai 2013. Les plages et les dunes du nord.</span></p>
<p><a href=\"wp-content/uploads/berck.zip\" title=\"Cliquer pour télécharger les photos\">Télécharger les photos</a>
</p></td></tr>

<tr><td valign=\"top\">
<p>&nbsp;</p>
<p><span style=\"color:#008000;\"><strong><a href=\"http://diapo.chemineur.fr/viewtopic.php?view=diapo&amp;t=2\" title=\"Voir le diaporama de la sortie en Champagne\">Photos sortie mai 2012</a></strong></span></p>
<p><span style=\"color:#008000;\">Randonnée pédestre du côté d'Épernay du 13 au 15 mai 2012. Le vignoble champenois</p></td>
<td valign=\"top\"><a href=\"http://diapo.chemineur.fr/viewtopic.php?view=diapo&amp;t=2\" title=\"Voir le diaporama de la sortie en Champagne\"><img src=\"wp-content/uploads/epernay.jpg\" /></a></td></tr>

<tr><td valign=\"top\"><a href=\"http://chemineur.fr/diapos/20110613211654\" title=\"Voir les photos de la Suisse Normande\"><img src=\"wp-content/uploads/suissenormande.jpg\" /></a></td> <td valign=\"top\">
<p>&nbsp;</p>
<p><span style=\"color:#008000;\"><strong><a href=\"http://chemineur.fr/diapos/20110613211654\" title=\"Voir les photos de la Suisse Normande\">Photos sortie juin 2011</a></strong></span></p>
<p><span style=\"color:#008000;\">Randonnée pédestre en Suisse Normande du 2 au 4 juin 2011</p></td></tr>
<tr><td valign=\"top\">
<p>&nbsp;</p>
<p><span style=\"color:#008000;\"><strong><a href=\"http://chemineur.fr/diapos/20100524112116\" title=\"Voir les photos de Bayeux\">Photos sortie mai 2010</a></strong></span></p>
<p><span style=\"color:#008000;\">Randonnée pédestre du côté de Bayeux du 13 au 15 mai 2010. Les plages du débarquement et la campagne normande</p></td> <td valign=\"top\"><a href=\"http://chemineur.fr/diapos/20100524112116\" title=\"Voir les photos de Bayeux\"><img src=\"wp-content/uploads/bayeux.jpg\" /></a></td></tr>

<tr><td valign=\"top\"><a href=\"http://chemineur.fr/diapos/20090523165343\" title=\"Voir les photos de la Sologne\"><img src=\"wp-content/uploads/sologne.jpg\" /></a></td> <td valign=\"top\">
<p>&nbsp;</p>
<p><span style=\"color:#008000;\"><strong><a href=\"http://chemineur.fr/diapos/20090523165343\" title=\"Voir les photos de la Sologne\">Photos sortie mai 2008</a></strong></span></p>
<p><span style=\"color:#008000;\">Photos sortie mai 2009.</p></td></tr>
<tr><td valign=\"top\">
<p>&nbsp;</p>
<p><span style=\"color:#008000;\"><strong><a href=\"http://cavailhez.fr/5.4rc/bin/diapo.php?site=saga&amp;cle=cotentin\" title=\"Voir les photos du Cotentin\">Randonnée en Sologne</a></strong></span></p>
<p><span style=\"color:#008000;\">Randonnée pédestre sur la côte du cotentin les 1er, 2 et 3 mai 2008. Sentiers côtiers.</p></td> <td valign=\"top\"><a href=\"http://chaville.gym.free.fr/5.4rc/bin/diapo.php?site=saga&amp;cle=cotentin\" title=\"Voir les photos du Cotentin\"><img src=\"wp-content/uploads/agon.jpg\" /></a></td></tr>
<tr><td valign=\"top\"><a href=\"http://chavil.gym.free.fr/ardennes\" title=\"Voir les photos des Ardennes\"><img src=\"wp-content/uploads/meuse.jpg\" /></a></td> <td valign=\"top\">
<p>&nbsp;</p>
<p><span style=\"color:#008000;\"><strong><a href=\"http://chavil.gym.free.fr/ardennes\" title=\"Voir les photos des Ardennes\">Photos sortie mai 2007</a></strong></span></p>
<p><span style=\"color:#008000;\">Les boucles de la Meuse</span></p>
<p><span style=\"color:#008000;\">Hébergement au campanile de Charleville-Mézières.</p></td></tr>
<tr><td valign=\"top\">
<p>&nbsp;</p>
<p><span style=\"color:#008000;\"><strong><a href=\"http://chavil.gym.free.fr/dinard/index.htm\" title=\"Voir les photos de Dinard\">Photos sortie mai 2006</a></strong></span></p>
<p><span style=\"color:#008000;\">Randonnée pédestre sur la côte d'Emeraude autour de Dinard les 25, 26 et 27 mai 2006.</span></p>
<p><span style=\"color:#008000;\">Sentiers côtiers - GR34 - vallée de la Rance.</span></p>
<p><span style=\"color:#008000;\">Hébergement au Village Vacances Le Manoir de la Vicomté.</p></td> <td valign=\"top\"><a href=\"http://chavil.gym.free.fr/dinard/index.htm\" title=\"Voir les photos de Dinard\"><img src=\"wp-content/uploads/dinard.jpg\" /></a></td></tr></tbody></table>
",
	),
);

$drupal_node = array (
	array (0, 0, 'Accueil', 'Accueil', 'Accueil', 1, 1, 1276445524, 1406984709, 0, 0, 0, -100, 0, 0),
	array (0, 0, 'Concept – Objectifs', 'Concept – Objectifs', 'Concept – Objectifs', 1, 1, 1276445524, 1406984709, 0, 0, 0, -100, 0, 0),
	array (3, 3, 'page', 'fr', 'Randonnée pédestre', 1, 1, 1276445524, 1406984709, 0, 0, 0, -100, 0, 0),
	array (132, 132, 'seance', 'fr', '', 3, 0, 1405704481, 1439296673, 0, 0, 0, -100, 0, 0),
	array (94, 94, 'new', 'fr', 'Photos de la randonnée en Suisse Normande du 2 au 4 2011', 1, 1, 1301600463, 1312135752, 0, 0, 0, -100, 0, 0),
	array (52, 52, 'salle', 'fr', 'Jean Jaurès / Piste d’Athlétisme', 1, 1, 1279913801, 1409824521, 0, 0, 0, -100, 0, 0),
	array (9, 9, 'page', 'fr', 'Contacts', 1, 1, 1276445526, 1409858424, 0, 0, 0, -100, 0, 0),
	array (10, 10, 'page', 'fr', 'Association', 1, 1, 1276445526, 1417296694, 0, 0, 0, -100, 0, 0),
	array (11, 11, 'animateur', 'fr', 'Marie-Thérèse', 1, 1, 1276445527, 1379274237, 0, 0, 0, -100, 0, 0),
	array (12, 12, 'animateur', 'fr', 'Florina', 1, 1, 1276445527, 1276460634, 0, 0, 0, 0, 0, 0),
	array (161, 161, 'new', 'fr', 'Dates à retenir', 3, 1, 1438762755, 1442141641, 0, 1, 0, -100, 0, 0),
	array (163, 163, 'salle', 'fr', 'Doisu', 3, 1, 1439277077, 1440505461, 0, 0, 0, -100, 0, 0),
	array (16, 16, 'animateur', 'fr', 'Rose', 1, 1, 1276445529, 1276461345, 0, 0, 0, 0, 0, 0),
	array (17, 17, 'cours', 'fr', 'Multi-gym', 1, 1, 1276445530, 1329688405, 0, 0, 0, -100, 0, 0),
	array (19, 19, 'cours', 'fr', 'Gym modérée', 1, 1, 1276445530, 1406735259, 0, 0, 0, -100, 0, 0),
	array (20, 20, 'cours', 'fr', 'Gym équilibre', 1, 1, 1276445531, 1282292302, 0, 0, 0, -100, 0, 0),
	array (21, 21, 'salle', 'fr', 'Atrium / Maurice Béjart', 1, 1, 1276445531, 1379274473, 0, 0, 0, -100, 0, 0),
	array (110, 110, 'seance', 'fr', '', 3, 0, 1343222737, 1439280823, 0, 0, 0, -100, 0, 0),
	array (23, 23, 'salle', 'fr', 'Ferdinand Buisson', 1, 1, 1276445531, 1319383675, 0, 0, 0, -100, 0, 0),
	array (24, 24, 'salle', 'fr', 'Paul Bert', 1, 1, 1276445532, 1289330363, 0, 0, 0, -100, 0, 0),
	array (25, 25, 'salle', 'fr', 'Léo Lagrange', 1, 1, 1276445532, 1409823726, 0, 0, 0, -100, 0, 0),
	array (26, 26, 'seance', 'fr', 'Séance', 1, 1, 1276445532, 1439280534, 0, 0, 0, -100, 0, 0),
	array (165, 165, 'animateur', 'fr', 'Osvaldo', 3, 1, 1439277943, 1439277943, 0, 0, 0, -100, 0, 0),
	array (30, 30, 'seance', 'fr', 'Séance', 1, 1, 1276445534, 1439280778, 0, 0, 0, -100, 0, 0),
	array (31, 31, 'seance', 'fr', 'Séance', 1, 1, 1276445534, 1439297080, 0, 0, 0, -100, 0, 0),
	array (33, 33, 'seance', 'fr', 'Séance', 1, 1, 1276445535, 1439280912, 0, 0, 0, -100, 0, 0),
	array (34, 34, 'seance', 'fr', 'Séance', 1, 1, 1276445535, 1439280938, 0, 0, 0, -100, 0, 0),
	array (35, 35, 'seance', 'fr', 'Séance', 1, 1, 1276445536, 1439280994, 0, 0, 0, -100, 0, 0),
	array (36, 36, 'seance', 'fr', 'Séance', 1, 1, 1276445536, 1439282010, 0, 0, 0, -100, 0, 0),
	array (37, 37, 'seance', 'fr', 'Séance', 1, 1, 1276445537, 1439281925, 0, 0, 0, -100, 0, 0),
	array (177, 177, 'seance', 'fr', '', 1, 0, 1441531234, 1441531262, 0, 0, 0, -100, 0, 0),
	array (114, 114, 'animateur', 'fr', 'Nathalie', 3, 1, 1375101560, 1375101560, 0, 0, 0, -100, 0, 0),
	array (115, 115, 'animateur', 'fr', 'Émilie', 3, 1, 1375101591, 1376938116, 0, 0, 0, -100, 0, 0),
	array (42, 42, 'page', 'fr', 'Horaires', 1, 1, 1276447838, 1439280473, 0, 0, 0, -100, 0, 0),
	array (42, 42, 'page', 'fr', 'Horaires', 1, 1, 1276447838, 1439280473, 0, 0, 0, -100, 0, 0),
	array (44, 44, 'new', 'fr', 'Chavil\'Gym', 1, 1, 1277065503, 1396559736, 0, 1, 0, -100, 0, 0),
	array (47, 47, 'animateur', 'fr', 'Arnaud', 1, 1, 1279912478, 1379273202, 0, 0, 0, -100, 0, 0),
	array (172, 172, 'animateur', 'fr', 'Claire', 3, 1, 1439296610, 1439296626, 0, 0, 0, -100, 0, 0),
	array (49, 49, 'cours', 'fr', 'Gym bien- être, Qi-gong', 1, 1, 1279912972, 1379273418, 0, 0, 0, -100, 0, 0),
	array (50, 50, 'seance', 'fr', '', 1, 0, 1279913189, 1439281072, 0, 0, 0, -100, 0, 0),
	array (51, 51, 'cours', 'fr', 'Acti’March', 1, 1, 1279913381, 1441811736, 0, 0, 0, -100, 0, 0),
	array (53, 53, 'seance', 'fr', '', 1, 0, 1279914097, 1439281096, 0, 0, 0, -100, 0, 0),
	array (54, 54, 'salle', 'fr', 'Atrium Salle Tchaïkovski', 1, 1, 1279972631, 1375102336, 0, 0, 0, -100, 0, 0),
	array (113, 113, 'animateur', 'fr', 'Armelle', 3, 1, 1375100916, 1375100916, 0, 0, 0, -100, 0, 0),
	array (107, 107, 'seance', 'fr', '', 3, 0, 1343221842, 1439298271, 0, 0, 0, -100, 0, 0),
	array (173, 173, 'cours', 'fr', 'Yoga', 3, 1, 1439297695, 1439297695, 0, 0, 0, -100, 0, 0),
	array (159, 159, 'new', 'fr', 'SAISON 2015-2016', 3, 1, 1438761811, 1442141621, 0, 1, 0, -100, 0, 0),
	array (105, 105, 'new', 'fr', 'et aussi....', 3, 1, 1343221065, 1422607219, 0, 1, 0, -100, 0, 0),
	array (174, 174, 'cours', 'fr', 'yoga 2 heure', 3, 1, 1439298310, 1439298678, 0, 0, 0, -100, 0, 0),
	array (59, 59, 'cours', 'fr', 'Gym nordique', 1, 1, 1279987629, 1282295581, 0, 0, 0, -100, 0, 0),
	array (61, 61, 'cours', 'fr', 'Multi-gym moyenne', 1, 1, 1279987860, 1282294564, 0, 0, 0, -100, 0, 0),
	array (95, 95, 'animateur', 'fr', 'Sandrine', 1, 1, 1312128806, 1312128901, 0, 0, 0, -100, 0, 0),
	array (64, 64, 'salle', 'fr', 'Gymnase Halimi', 1, 1, 1289329599, 1379273395, 0, 0, 0, -100, 0, 0),
	array (65, 65, 'new', 'fr', 'Plaquette 2015-2016', 3, 1, 1292943525, 1441722955, 0, 1, 0, -100, 0, 0),
	array (133, 133, 'seance', 'fr', '', 3, 0, 1405704539, 1439281352, 0, 0, 0, -100, 0, 0),
	array (142, 142, 'animateur', 'fr', 'Bénédicte', 1, 1, 1409859932, 1409859932, 0, 0, 0, -100, 0, 0),
	array (134, 134, 'cours', 'fr', 'Danse-cardio-boxing', 3, 1, 1405704636, 1439280141, 0, 0, 0, -100, 0, 0),
	array (178, 178, 'seance', 'fr', '', 1, 0, 1441810250, 1441810250, 0, 0, 0, -100, 0, 0),
	array (138, 138, 'seance', 'fr', '', 3, 0, 1405713723, 1439281419, 0, 0, 0, -100, 0, 0),
	array (139, 139, 'cours', 'fr', 'Pilates, renforcement musculaire profond', 3, 1, 1405713967, 1441810533, 0, 0, 0, -100, 0, 0),
	array (140, 140, 'seance', 'fr', '', 3, 0, 1405714021, 1439280715, 0, 0, 0, -100, 0, 0),
	array (96, 96, 'cours', 'fr', 'Gym tonique', 1, 1, 1312129718, 1406735093, 0, 0, 0, -100, 0, 0),
	array (97, 97, 'seance', 'fr', '', 1, 0, 1312129893, 1439281131, 0, 0, 0, -100, 0, 0),
	array (98, 98, 'seance', 'fr', '', 1, 0, 1312129952, 1439281163, 0, 0, 0, -100, 0, 0),
	array (100, 100, 'cours', 'fr', 'Yoga', 1, 1, 1312130521, 1320660912, 0, 0, 0, -100, 0, 0),
	array (101, 101, 'seance', 'fr', '', 1, 0, 1312130588, 1439278601, 0, 0, 0, -100, 0, 0),
	array (137, 137, 'cours', 'fr', 'Pilates, renforcement musculaire, stretching', 3, 1, 1405713673, 1405713673, 0, 0, 0, -100, 0, 0),
	array (117, 117, 'seance', 'fr', '', 3, 0, 1375101761, 1439279760, 0, 0, 0, -100, 0, 0),
	array (118, 118, 'cours', 'fr', 'Gym cardio', 3, 1, 1375102583, 1376936970, 0, 0, 0, -100, 0, 0),
	array (171, 171, 'cours', 'fr', 'Gym Zen', 3, 1, 1439290217, 1439297226, 0, 0, 0, -100, 0, 0),
	array (131, 131, 'seance', 'fr', '', 3, 0, 1405704420, 1439296654, 0, 0, 0, -100, 0, 0),
);

require_once(ABSPATH . 'wp-admin/includes/taxonomy.php');
function gym_cree_posts ($textes, $ecrase = true) {
	// Crée les catégories
	foreach ($textes AS $categorie => $posts)
		if (!category_exists ($categorie, 0))
			wp_insert_category (array (
				'cat_name' => $categorie,
				'category_parent' => 0,
				'category_nicename' => sanitize_title (++$indice.'0'.$categorie), // Sert à ordonner les posts d'une catégorie
			));

	// Liste les catégoties
	$results = $GLOBALS['wpdb']->get_results( 'SELECT term_id,name FROM wp_terms JOIN wp_term_taxonomy USING (term_id) WHERE taxonomy = "category"', OBJECT );
	foreach ($results AS $r)
		$categories [$r->name] = $r->term_id;

	// Liste des posts
	$results = $GLOBALS['wpdb']->get_results ('SELECT ID, post_title, post_content FROM wp_posts WHERE post_status = "publish"', OBJECT);
	foreach ($results AS $r)
		$gym_posts [$r->post_title] = $r->ID; // Mémorise l'id de chaque post en fonction de son titre

	// Créer ou mettre à jour les posts
	foreach ($textes AS $categorie => $posts)
		foreach ($posts AS $titre => $texte) {
			$img = array ();
			global $drupal_node;
			foreach ($drupal_node AS $d)
				if ($d[4] == $titre) {
					$results = $GLOBALS['wpdb']->get_results ('SELECT * FROM wp_posts WHERE guid LIKE "%-'.$d[0].'-%"', OBJECT);
					foreach ($results AS $r) {
						switch ($r->post_mime_type) {
							case 'image/jpg':
							case 'image/jpeg':
							case 'image/gif':
								$img [$r->ID] = '<p><img src="'.$r->guid.'" /></p>';
								break;
							case 'image/avi':
								$img [$r->ID] = '<p><embed src="'.$r->guid.'"></embed></p>';
								break;
							case 'video/mp4':
								$img [$r->ID] = '<p>[video mp4="'.$r->guid.'"]</p>';
								break;
							default:
								echo"<pre style='background-color:white;color:black;font-size:14px;'>TYPE INCONNU = ".var_export($r,true).'</pre>';
						}
					}
				}
			if (count ($img))
				$texte = "[droite]\n".implode("\n",$img)."\n[/droite]\n".$texte;

			$post = array (
				'ID'            => @$gym_posts[$titre],
				'post_category' => array ($categories[$categorie]),
				'post_title'    => $titre,
				'post_content'  => $texte,
				'post_status'   => 'publish',
			);
			if ($ecrase || !$post['ID']) {
				// https://codex.wordpress.org/Function_Reference/wp_insert_post
				$id = wp_insert_post ( $post, $wp_error );
				if ($wp_error)
					echo"<pre style='background-color:white;color:black;font-size:14px;'> = ".var_export($wp_error,true).'</pre>';
				elseif ($id)
					echo"<pre style='background-color:white;color:black;font-size:14px;'>POST_CREE = ".var_export(array ($id, $titre),true).'</pre>';
			}
		}
}

add_shortcode ('init', 'gym_init');
function gym_init () {
	global $gym_init_posts, $drupal_node;
	gym_cree_posts ($gym_init_posts);
}
