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
$boutique = true;

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
    if ($args[0] == "current_user_id") {
        return get_current_user_id(); //TODO use is_user_logged_in()
    } else {
        return get_bloginfo($args[0]);
    }
}

// Lien d'édition de la page
add_shortcode("edit_page", "edit_page_function");
function edit_page_function()
{
    global $post;

    if ($post) {
        return "<a class=\"crayon\" title=\"Modification de la page\" href=\"" .
            get_bloginfo("url") .
            "/wp-admin/post.php?&action=edit&post={$post->ID}\">&#9998;</a>";
    }
}

// Menu haut de page
add_shortcode("menu", "menu_function");
function menu_function($args)
{
    global $wpdb, $table_prefix, $post;

    $pages = $wpdb->get_results("
SELECT child.post_title, child.post_name, child.post_parent,
  parent.post_title AS parent_title, parent.post_name AS parent_name
FROM {$table_prefix}posts AS parent
JOIN {$table_prefix}posts AS child ON parent.ID = child.post_parent
WHERE parent.post_type = 'page'
  AND parent.post_status = 'publish' AND child.post_status = 'publish'
ORDER BY parent.menu_order, parent.post_title, child.menu_order, child.post_title
");

    $sous_menu = "";
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
        if ($post && $sous_menu == $post->ID) {
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
function horaires_function($arg = "")
{
    global $wpdb, $table_prefix, $nom_jour, $boutique;
    preg_match('|/[^/]+/$|', $_SERVER["REQUEST_URI"], $page_url);

    // Listage des produits
    $liste_produits = $wpdb->get_results("
SELECT ID, posts.post_title
FROM {$table_prefix}wc_product_meta_lookup AS products
JOIN {$table_prefix}posts AS posts ON products.product_id = posts.ID
");
    foreach ($liste_produits as $p) {
        $produits[$p->post_title] = $p;
    }

    $pages_publiees = $wpdb->get_results("
SELECT ID, post_title, post_name, post_content
FROM {$table_prefix}posts
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
            if (count($colonnes[1]) == 4) {
                $date = explode(" ", $colonnes[1][1], 2);
                $no_jour = array_search(strtolower(trim($date[0])), $nom_jour);

                $edit = isset(
                    wp_get_current_user()->allcaps["edit_others_pages"]
                )
                    ? "<a class=\"crayon\" title=\"Modification de la séance\" href=\"" .
                        get_bloginfo("url") .
                        "/wp-admin/post.php?&action=edit&post={$p->ID}\">&#9998;</a>"
                    : "";

                // Lien vers la page de commande
                $product_name = str_replace(
                    "<br>",
                    " ",
                    implode(", ", $colonnes[1])
                );
                $panier =
                    $boutique && isset($produits[$product_name])
                        ? ' <a href="' .
                            get_bloginfo("url") .
                            "?add-to-cart=" .
                            $produits[$product_name]->ID .
                            '" title="S\'inscrire"">&#128722;</a> '
                        : "";

                $lieu = isset($post_names[$colonnes[1][2]])
                    ? '<a title="Voir le lieu" href="' .
                        get_bloginfo("url") .
                        "/{$post_names[$colonnes[1][2]]}/\">{$colonnes[1][2]}</a>"
                    : $colonnes[1][2];

                $anim = isset($post_names[$colonnes[1][3]])
                    ? '<a title="Voir l\'animateur-ice" href="' .
                        get_bloginfo("url") .
                        "/{$post_names[$colonnes[1][3]]}/\">{$colonnes[1][3]}</a>"
                    : $colonnes[1][3];

                $ligne_horaire = [
                    '<a title="Voir l\'activité" href="' .
                    get_bloginfo("url") .
                    "/{$p->post_name}/\">{$colonnes[1][0]}</a>$edit",

                    $date[1] . $panier,
                    $lieu,
                    $anim,
                    $product_name,
                ];

                if (
                    $no_jour &&
                    $page_url &&
                    str_contains(
                        implode("/horaires/" . $arg, $ligne_horaire),
                        $page_url[0]
                    )
                ) {
                    $horaires[$no_jour][$date[1]][] = $ligne_horaire;
                }
            }
        }
    }

    if (isset($horaires)) {
        $cal = ["\n<div class=\"horaires\">"];
        $pnames = [];

        ksort($horaires);
        foreach ($horaires as $no_jour => $jour) {
            $rj = ["\t<table>", "\t\t<caption>{$nom_jour[$no_jour]}</caption>"];
            ksort($jour);
            foreach ($jour as $heure) {
                foreach ($heure as $ligne) {
                    $pnames[] = $ligne[4];
                    unset($ligne[4]);
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
        return $args ? $pnames : implode(PHP_EOL, $cal);
    }
}

// Calendrier
add_shortcode("calendrier", "calendrier_function");
function calendrier_function()
{
    global $post, $annee, $nom_jour, $nom_mois;
    date_default_timezone_set("Europe/Paris");
    $calendrier = [];

    if (!$post) {
        return;
    }

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

add_shortcode("csv", "csv_function");
function csv_function($args)
{
    global $wpdb, $table_prefix;

    // Access verification
    if (
        !array_intersect(
            ["administrator", "shop_manager"],
            wp_get_current_user()->roles
        )
    ) {
        return 'Vous devez être connecté comme gestionnaire de commandes pour accéder à cette page.<br/><a href="' .
            get_bloginfo("url") .
            "/wp-login.php?redirect_to=" .
            get_bloginfo("url") .
            '/csv">Connexion</a>';
    } elseif (!$_SERVER["QUERY_STRING"]) {
        return '<a href="' .
            get_bloginfo("url") .
            '/csv?csv">Télécharger le fichier inscriptions.csv</a>';
    }

    // Informations adhérent
    $customer_data = [
        "Titre" => "_billing_wooccm15",
        "Nom" => "_billing_last_name",
        "Prénom" => "_billing_first_name",
        "Date de naisance" => "_billing_wooccm11",
        "Adresse" => "_billing_address_1",
        "Code postal" => "_billing_postcode",
        "Ville" => "_billing_city",
        "Mail" => "_billing_email",
        "Téléphone" => "_billing_phone",
        "Prévenir" => "_billing_wooccm13",
        "Prévenir (tel)" => "_billing_wooccm14",
        "Demande attestation" => "_billing_wooccm17",
        "Droit à l'image" => "_billing_wooccm16",
        //"Certificat médical" => "_billing_wooccm12",
        "Montant commande" => "_order_total",
        "N° commande" => "order_id",
    ];
    $customer_keys = array_flip(array_values($customer_data));
    $inscriptions = [array_keys($customer_data)];

    $sql = "
SELECT *
FROM {$table_prefix}wc_order_product_lookup AS o
LEFT JOIN {$table_prefix}postmeta AS m ON m.post_id = o.order_id
";
    $commandes = $wpdb->get_results($sql);
    foreach ($commandes as $c) {
        $inscriptions[$c->order_id][$customer_keys["order_id"]] = $c->order_id;
        if (array_key_exists($c->meta_key, $customer_keys)) {
            $inscriptions[$c->order_id][$customer_keys[$c->meta_key]] =
                $c->meta_value;
        }
    }

    // Inscriptions aux cours
    $nom_cours = horaires_function("csv/");
    foreach ($nom_cours as $c) {
        $inscriptions[0][] = $c;
    }

    $sql = "
SELECT *
FROM {$table_prefix}wc_order_product_lookup AS o
LEFT JOIN {$table_prefix}postmeta AS m ON o.product_id = m.post_id
LEFT JOIN {$table_prefix}woocommerce_order_items USING (order_item_id)
WHERE meta_key = '_wp_old_slug'
";
    $commandes = $wpdb->get_results($sql);
    foreach ($commandes as $c) {
        $inscriptions[$c->order_id][$customer_keys["order_id"]] = $c->order_id;
        $indice_cours = array_search($c->order_item_name, $inscriptions[0]);
        $inscriptions[$c->order_id][$indice_cours] = 1;
    }

    // Complétude et tri des lignes
    foreach ($inscriptions as $k => $v) {
        for ($i = 0; $i < count($inscriptions[0]); $i++) {
            if (!isset($inscriptions[$k][$i])) {
                $inscriptions[$k][$i] = $i < count($customer_data) ? "" : 0;
            }
        }
        ksort($inscriptions[$k]);
    }
    ksort($inscriptions);

    //*DCMM*/echo"<pre style='background:white;color:black;font-size:16px'> = ".var_export($inscriptions,true).'</pre>'.PHP_EOL;exit();

    // Ecriture du fichier
    header("Content-Description: File Transfer");
    header("Content-Type: application/octet-stream");
    header(
        "Content-Disposition: attachment; filename=inscriptions 2023-2024.csv"
    );
    header("Content-Transfer-Encoding: binary");
    header("Expires: 0");
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    header("Pragma: public");
    echo "\xEF\xBB\xBF"; // UTF-8 BOM

    ob_start();
    $out = fopen("php://output", "w");
    foreach ($inscriptions as $i) {
        fputcsv($out, $i, ";");
    }
    fclose($out);
    echo ob_get_clean();

    exit();

    return get_current_user_id();
}
?>
