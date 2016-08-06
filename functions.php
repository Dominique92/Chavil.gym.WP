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
	$titre = get_the_title (); // Titre de la page (pour les filtres)
	$results = $GLOBALS['wpdb']->get_results ('SELECT ID, post_title, post_content FROM wp_posts WHERE post_status = "publish"', OBJECT);
	foreach ($results AS $p) {
		$gym_posts [cnv ($p->post_title)] = $p->ID; // Mémorise l'id de chaque post en fonction de son titre
		$c = str_replace (array("<tr>\r\n\t\t\t<td>\r\n\t\t\t\t","\r\n\t\t\t</td>\r\n\t\t\t<td>\r\n\t\t\t\t","\r\n\t\t\t</td>\r\n\t\t</tr>"), array('[','|',']'), $p->post_content);
		preg_match_all('/\[(.*)\]/', $c, $h);
		foreach ($h[1] AS $hv) {
			$hvs = explode ('|', $hv);
			if (stripos ('|'.cnv($hv).'|horaires|', '|'.cnv($titre).'|')
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
								$r .= ($n = @$gym_posts [cnv ($cvv)])
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

function cnv ($c) {
	$c = html_entity_decode ($c);
	$c = strtolower ($c);
	$c = preg_replace ('/[^a-z\|]/', '', $c);
	return $c;
}
