<?php
$annee = 2023;
$nom_jour = explode(
    " ",
    " lundi mardi mercredi jeudi vendredi samedi dimanche"
);
$nom_mois = explode(
    " ",
    " janvier fevrier mars avril mai juin" .
        " juillet aout septembre octobre novembre décembre" .
        " janvier fevrier mars avril mai juin"
);

// Load correctly syles.css files
add_action("wp_enqueue_scripts", "wp_enqueue_scripts_function");
function wp_enqueue_scripts_function()
{
    wp_register_style("style", get_stylesheet_uri());
    wp_enqueue_style("style");
}

// Réglage de l'opacité de l'éditeur
add_action("admin_head", "admin_head_function");
function admin_head_function()
{
    wp_enqueue_style(
        "admin_css",
        get_stylesheet_directory_uri() . "/style.css"
    );
}

// Use global urls in block templates (as defined in wp-includes/general-template.php)
add_shortcode("get_info", "get_info_function");
function get_info_function($args)
{
    return get_bloginfo($args[0]);
}

// Lien d'édition de la page
add_shortcode("edit_page", "edit_page_function");
function edit_page_function()
{
    global $post;
    return "<a class=\"crayon\" title=\"Modification de la page\" href=\"" .
        get_bloginfo("url") .
        "/wp-admin/post.php?&action=edit&post={$post->ID}\">&#9998;</a>";
}

// Menu haut de page
add_shortcode("menu", "menu_function");
function menu_function($args)
{
    global $wpdb, $post;

    $pages = $wpdb->get_results("
SELECT child.post_title, child.post_name, child.post_parent,
  parent.post_title AS parent_title, parent.post_name AS parent_name
FROM wpgym_posts AS parent
JOIN wpgym_posts AS child ON parent.ID = child.post_parent
WHERE parent.post_status = 'publish' AND child.post_status = 'publish'
ORDER BY parent.menu_order, parent.post_title, child.menu_order, child.post_title
");

    $menu[] = '<ul class="menu">';
    $liste[] = '<ul class="sous_pages">';
    foreach ($pages as $p) {
        // Au changement de sous-menu
        if ($sous_menu != $p->post_parent) {
            if ($sous_menu) {
                $menu[] = "\t\t</ul>\n\t</li>";
            }
            $menu[] = "\t<li>\n\t\t<a onclick=\"return clickMenu(event,this)\" href='/$p->parent_name/'>$p->parent_title</a>\n\t\t<ul>";

            $sous_menu = $p->post_parent;
        }

        // Pour toutes les lignes
        $menu[] = "\t\t\t<li><a href='/$p->post_name/' title='Voir la page'>$p->post_title</a></li>";

        // Affichage de sous-catégories
        if ($sous_menu == $post->ID) {
            $liste[] =
                "<li><a href=\"" .
                get_bloginfo("url") .
                "/$p->post_name/\">$p->post_title</a></li>";
        }
    }
    $menu[] = "\t\t</ul>\n\t</li>\n</ul>";
    $liste[] = "</ul>";

    return implode(PHP_EOL, $args ? $liste : $menu);
}

// Horaires
add_shortcode("horaires", "horaires_function");
function horaires_function()
{
    global $wpdb, $nom_jour;

    preg_match('|/[^/]+/$|', $_SERVER["REQUEST_URI"], $page_url);

    $pages_publiees = $wpdb->get_results("
SELECT post_title, post_name, post_content, ID
FROM wpgym_posts
WHERE post_status = 'publish'
");

    foreach ($pages_publiees as $p) {
        $post_names[$p->post_title] = $p->post_name;
        $post_titles[$p->post_name] = $p->post_title;
    }

    foreach ($pages_publiees as $p) {
        preg_match_all("|<tr>.*</tr>|U", $p->post_content, $lignes);
        foreach ($lignes[0] as $l) {
            preg_match_all("|<td>(.*)</td>|U", $l, $colonnes);
            $date = explode(" ", $colonnes[1][1], 2);
            $no_jour = array_search(strtolower(trim($date[0])), $nom_jour);
            $edit = wp_get_current_user()->allcaps["edit_others_pages"]
                ? "<a class=\"crayon\" title=\"Modification de la séance\" href=\"" .
                    get_bloginfo("url") .
                    "/wp-admin/post.php?&action=edit&post={$p->ID}\">&#9998;</a>"
                : "";

            $ligne_horaire = [
                '<a title="Voir l\'activité" href="' .
                get_bloginfo("url") .
                "/{$p->post_name}/\">{$colonnes[1][0]}</a>$edit",

                $date[1],

                '<a title="Voir le lieu" href="' .
                get_bloginfo("url") .
                "/{$post_names[$colonnes[1][2]]}/\">{$colonnes[1][2]}</a>",

                '<a title="Voir l\'animateur-ice" href="' .
                get_bloginfo("url") .
                "/{$post_names[$colonnes[1][3]]}/\">{$colonnes[1][3]}</a>",
            ];

            if (
                $no_jour &&
                str_contains(
                    implode("/horaires/", $ligne_horaire),
                    $page_url[0]
                )
            ) {
                $horaires[$no_jour][$date[1]][] = $ligne_horaire;
            }
        }
    }

    if (count($horaires)) {
        $cal[] = "\n<div class=\"horaires\">";

        ksort($horaires);
        foreach ($horaires as $no_jour => $jour) {
            $rj = ["\t<table>", "\t\t<caption>{$nom_jour[$no_jour]}</caption>"];
            ksort($jour);
            foreach ($jour as $heure) {
                foreach ($heure as $ligne) {
                    $rj[] =
                        "\t\t<tr>\n\t\t\t<td>" .
                        implode("</td>\n\t\t\t<td>", $ligne) .
                        "</td>\n\t\t</tr>";
                }
            }
            $rj[] = "\t</table>";
            $cal[] = implode(PHP_EOL, $rj);
        }
        $cal[] = "</div>";
        return implode(PHP_EOL, $cal);
    }
}

// Calendrier
add_shortcode("calendrier", "calendrier_function");
function calendrier_function()
{
    global $post, $annee, $nom_jour, $nom_mois;
    date_default_timezone_set("Europe/Paris");

    // Remplir des cases actives
    preg_match_all("|<tr>.*</tr>|U", $post->post_content, $lignes);
    foreach ($lignes[0] as $l) {
        preg_match_all("|<td>(.*)</td>|U", $l, $colonnes);
        if (count($colonnes) == 2 && is_numeric($colonnes[1][0])) {
            foreach (explode(",", $colonnes[1][1]) as $jour) {
                remplir_calendrier(
                    $calendrier,
                    $annee + ($colonnes[1][0] < 8 ? 1 : 0),
                    $colonnes[1][0],
                    $jour,
                    "date_active"
                );
            }
        }
    }
    // Remplir les autres cases
    for ($j = 1; $j < 310; $j++) {
        remplir_calendrier($calendrier, $annee, 9, $j, "");
    }
    // Afficher le calendrier
    $cal = [];
    ksort($calendrier);
    foreach ($calendrier as $k => $v) {
        $edit = wp_get_current_user()->allcaps["edit_others_pages"]
            ? "<a class=\"crayon\" title=\"Modification du calendrier\" href=\"" .
                get_bloginfo("url") .
                "/wp-admin/post.php?&action=edit&post={$post->ID}\">&#9998;</a>"
            : "";
        $cal[] = "<table class=\"calendrier\">";
        $cal[] = "<tr><td colspan=\"6\">Les {$nom_jour[$k]}s $edit</td></tr>";
        ksort($v);
        foreach ($v as $kv => $vv) {
            if ($kv < 19) {
                // N'affiche pas juillet
                $cal[] = "<tr><td>{$nom_mois[$kv]}</td>";
                ksort($vv);
                foreach ($vv as $kvv => $vvv) {
                    $cal[] = "<td class=\"$vvv\">$kvv</td>";
                }
                $cal[] = "</tr>";
            }
        }
        $cal[] = "</table>";
    }
    $cal[] = "</div>";
    return implode(PHP_EOL, $cal);
}

function remplir_calendrier(&$calendrier, $an, $mois, $jour, $set)
{
    global $annee;

    $dateTime = new DateTime();
    $dateTime->setDate($an, $mois, $jour);
    $dt = explode(" ", $dateTime->format("Y N n j"));
    $noj = $dt[1];
    $nom = $dt[2] + ($dt[0] - $annee) * 12;
    if (isset($calendrier[$noj]) || $set) {
        $calendrier[$noj][$nom][$dt[3]] .= $set;
    }
}
?>
