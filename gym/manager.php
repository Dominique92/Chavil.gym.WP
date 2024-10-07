<?php
if(1) {
	error_reporting(E_ALL);
	ini_set('display_errors','on');
	ini_set('display_startup_errors', 'on');
}

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

	// Affichage des documents à charger
	global $bordereaux;
	$nos_bordereaux = array_keys ($bordereaux);
	foreach ($nos_bordereaux as $nob => $b) {
		if ($nob) {
			$date_bordereau = array_values ($bordereaux)[$nob];
			$nom_doc = "Bordereau No$nob du " .
				substr($date_bordereau, 4, 2) . '-' .
				substr($date_bordereau, 2, 2) . '-20' .
				substr($date_bordereau, 0, 2);
		}
		else
			$nom_doc = "Journal des inscriptions en date";

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
		$no_bord_arg = intval('0'.$args[1]);
		$nos_bordereaux = array_keys ($bordereaux); //TODO foreach

		if ($no_bord_arg) {
			$date_bordereau = array_values ($bordereaux)[$no_bord_arg];
			$nom_bordereau = "BORDEREAU No$no_bord_arg du " .
				substr($date_bordereau, 4, 2) . '-' .
				substr($date_bordereau, 2, 2) . '-20' .
				substr($date_bordereau, 0, 2);
		}
		else
			$nom_bordereau = "JOURNAL DES INSCRIPTIONS " . date("d/m/Y H\hi");

		$nom_fichier = str_replace ([' ','/'], ['_','-'], strtolower($nom_bordereau));
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
		if (!$no_bord_arg) {
			$titres[] = "Bordereau";
			$titres[] = "Solde";
		}
		$order_list = [
			["Chavil'GYM Stripe => Crédit Mutuel : $nom_bordereau"],
			[],
			$titres];

		// Get orders
		$orders = wc_get_orders([
		    'orderby' => 'date_modified',
		    'order' => 'ASC',
			'limit' => null,
		]);

		// Ajouter le numéro de bordereau
		$no_bord_ligne = [];
		foreach (array_keys($bordereaux) as $nl => $nb)
			while (count($no_bord_ligne) <= $nb)
				$no_bord_ligne[] = $nl;

		$ligne_journal = count($order_list);
		foreach ($orders as $odb) {
			$o = $odb->get_data();

			if (intval ($o["total"]) && $o['transaction_id']) {
				$ligne_excel = count($order_list) + 1;
				$ligne_journal++;
				$total = floatval($o["total"]);
				$com = $o["payment_method_title"] == "Link by Stripe" ? "1,2" : "1,5";
				$fees = floatval($odb->get_meta("_stripe_fee"));
				$no_bord = @$no_bord_ligne[$ligne_journal];
				$items = [
					$o["id"],
					$o["date_created"]->date_i18n(),
					$o["status"],
					$o["billing"]["first_name"],
					$o["billing"]["last_name"],
					number_format(floatval($o["total"]), 2, ",", ""),
					$fees
						? number_format($fees, 2, ",", "")
						: "=ARRONDI.SUP(F$ligne_excel*$com%+0,25;2)", // Pour les 2 premières
					"=F$ligne_excel-G$ligne_excel",
				];

				// Toutes les lignes pour journal
				if (!$no_bord_arg) {
					$items[] = $no_bord;

					if ($no_bord == @$numero_bordereau_precedent)
						$items[] = "=H$ligne_excel+J".($ligne_excel - 1);
					else
						$items[] = "=H$ligne_excel";

					$numero_bordereau_precedent = $no_bord;
				}

				// Add the line to the list
				if (!$no_bord_arg || $no_bord_arg === $no_bord)
					$order_list[] = $items;
			}
		}

		// Totaux
		$order_list[] = ["", "", "", "", "Total",
			"=SOMME(F4:F".count($order_list).")",
			"=SOMME(G4:G".count($order_list).")",
			"=SOMME(H4:H".count($order_list).")",
		];

		if (!$no_bord_arg)
			$order_list[] = [
				"", "", "", "", "", "Commission",
				"=CONCATENER(ARRONDI(G".count($order_list)."/F".count($order_list)."*100;2);\" %\")",
				"=NB.SI(F:F;\">10\")-1", "Inscriptions",
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
