<?php
// Affichage supplémentaire sur la page "mon compte"
add_shortcode ("doc_admin", function() {
	$page = [];

	// Inclure la page "doc_admin"
	$doc_admin = get_page_by_path("doc_admin");
	if ($doc_admin)
		$page += [
			'<h1>' . $doc_admin->post_title .
				' <a title="Modifier le texte ci dessous" class="crayon" href="' .
				get_admin_url() .
				'post.php?action=edit&post=' . $doc_admin->ID .
				'">&#9998;</a></h1>',
			$doc_admin->post_content,
		];

	// Affichage des documents comptables
	$page += [
		"<h1>Documents comptables</h1>",
		"<p>Cliquez pour charger les fichiers excel :</p>",
	];

	global $bordereaux;
	$no_bordereaux = array_keys ($bordereaux);
	for ($nob = 0; $nob <= count ($no_bordereaux); $nob++) {
		$nom_doc = "Journal des inscriptions en date";
		if ($nob) {
			$date_bordereau = array_values ($bordereaux)[$nob - 1];
			$nom_doc = "Bordereau No$nob du " .
				substr($date_bordereau, 4, 2) . '-' .
				substr($date_bordereau, 2, 2) . '-' .
				substr($date_bordereau, 0, 2);
		}
		$page[] = "<p><a href=\"?compta=$nob\">$nom_doc</a></p>";
	}

	return implode (PHP_EOL, $page);
});

// Téléchargement d'un document comptable
preg_match('/^\/mon-compte\/\?compta\=?([0-9]*)$/', $_SERVER['REQUEST_URI'], $args);
if (count ($args))
	add_action ("init", function() {
		global $args, $bordereaux;
		date_default_timezone_set('Europe/Paris');

		// Filtrage d'un bordereau
		$no_bord = intval('0'.$args[1]);
		$no_bordereaux = array_keys ($bordereaux);
		$premiere_cmd = @$no_bordereaux[$no_bord - 2] ?: 0;
		$dernière_cmd = @$no_bordereaux[$no_bord - 1] ?: 9999;

		$nom_bordereau = "JOURNAL DES INSCRIPTIONS " . date("y-m-d H\hi");
		if ($no_bord == count ($no_bordereaux) + 1)
			$nom_bordereau = "RESTE A TRANSFERER " . date("y-m-d H\hi");
		elseif ($no_bord) {
			$date_bordereau = array_values ($bordereaux)[$no_bord - 1];
			$nom_bordereau = "BORDEREAU No$no_bord du " .
				substr($date_bordereau, 4, 2) . '-' .
				substr($date_bordereau, 2, 2) . '-' .
				substr($date_bordereau, 0, 2);
		}
		$nom_fichier = str_replace (' ', '_', strtolower($nom_bordereau));

		$order_db = [];

		$titres = [
			"Commande",
			"Date",
			"Statut",
			"Prénom",
			"Nom",
			"Payé",
			"Commission",
			"Transfert",
		];
		if (!$no_bord)
			$titres[] = "Bordereau";
		$order_list = [
			["Chavil'GYM Stripe => Crédit Mutuel : $nom_bordereau"],
			[],
			$titres];

		$orders = wc_get_orders([
		    'orderby' => 'ids',
			'limit' => null,
		]);
		foreach ($orders as $odb) {
			$o = $odb->get_data();
			$ligne = count($order_list) + 1;
			$total = floatval($o["total"]);
			$com = $o["payment_method_title"] == "Link by Stripe" ? "1,2" : "1,5";
			$days_old = intval(time () / 24 / 3600) - intval(strtotime ($o["date_created"]) / 24 / 3600);
			$fees = floatval($odb->get_meta("_stripe_fee"));

			$numero_bordereau = '';
			rsort ($no_bordereaux);
			foreach ($no_bordereaux as $i => $b) {
				if ($o["id"] <= $b)
					$numero_bordereau = count ($no_bordereaux)- $i;
			}
			if (!$numero_bordereau)
				$numero_bordereau = $days_old < 6 ? "Attente" : "Dispo";

			if (intval ($o["total"])) {
				$items = [
					$o["id"],
					$o["date_created"]->date_i18n(),
					$o["status"],
					$o["billing"]["first_name"],
					$o["billing"]["last_name"],
					number_format(floatval($o["total"]), 2, ",", ""),
					$fees
						? number_format($fees, 2, ",", "")
						: "=ARRONDI.SUP(F$ligne*$com%+0,25;2)",
					"=F$ligne-G$ligne",
				];
				if (!$no_bord)
					$items[] = $numero_bordereau;

			if ((!$no_bord || ($premiere_cmd < $o['id'] && $o['id'] <= $dernière_cmd)) &&
				$o["status"] != 'cancelled' && $o["status"] != 'pending')
				$order_list[] = $items;
			}
		}

		// Totaux
		$order_list[] = ["", "", "", "", "Total",
			"=SOMME(F4:F".count($order_list).")",
			"=SOMME(G4:G".count($order_list).")",
			"=SOMME(H4:H".count($order_list).")",
		];

		// Ecriture du fichier
		header("Content-Description: File Transfer");
		header("Content-Type: application/octet-stream");
		header("Content-Disposition: attachment; filename=$nom_fichier.csv");
		header("Content-Transfer-Encoding: binary");
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Pragma: public");
		echo "\xEF\xBB\xBF"; // UTF-8 BOM

		ob_start();
		$out = fopen("php://output", "w");
		foreach ($order_list as $i) {
			fputcsv($out, $i, ";");
		}
		fclose($out);
		echo ob_get_clean();

		exit();
	});
