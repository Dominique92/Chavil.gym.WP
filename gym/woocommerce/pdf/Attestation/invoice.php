<style>
.logo img {
  float: left;
  margin: 1cm;
  width: 3cm;
}
.entete {
  margin: 1cm;
  text-align: right;
}
.entete p {
  margin-top: 5px;
  font-size: 14px;
}
.titre {
  clear: both;
  margin-top: 2.5cm;
  text-align: center;
}
.corps {
  margin: 0 2cm 2cm 4cm;
  text-indent: 1cm;
  font-size: 16px;
  line-height: 1.3em;
  text-align: justify;
  text-justify: inter-word;
}
.corps p {
  margin: 0.5em;
}
.pour {
  float: left;
  margin-left: 4cm;
  line-height: 1.3em;
  font-size: 14px;
}
.date {
  margin: 0 2cm;
  text-align: right;
  font-size: 14px;
}
.signatures {
  float: left;
  padding: 1.5cm 0;
}
.signatures img {
  width: 6cm;
}
</style>

<div class="logo">
	<?php
	if ($this->has_header_logo()) {
		do_action('wpo_wcpdf_before_shop_logo', $this->get_type(), $this->order);
		$this->header_logo();
		do_action('wpo_wcpdf_after_shop_logo', $this->get_type(), $this->order);
	} else {
		$this->title();
	}
	?>
</div>

<div class="entete">
	<p>CHAVIL’ GYMNASTIQUE VOLONTAIRE</p>
	<p>2 rue Jean-Jaurès, 92370 Chaville</p>
	<p>Association loi 1901, JO n° 1691 du 2 juin 2001</p>
	<p>Siret 43 888 2003 000 17</p>
	<p>https://chaville.gym.c92.fr/</p>
</div>

<h1 class="titre">ATTESTATION D’ADHESION</h1>

<div class="corps">
	<p>L'association Chavil’ Gymnastique Volontaire
	certifie avoir fait une adhésion avec licence
	pour la pratique de la gymnastique volontaire à :</p>
	<p>Madame / Monsieur : 
	<?=$this->order->data['shipping']['first_name']?>
	<?=$this->order->data['shipping']['last_name']?></p>
	<p>Adresse : 
	<?=$this->order->data['shipping']['address_1']?>
	<?=$this->order->data['shipping']['address_2']?></p>
	<p>Ville :
	<?=$this->order->data['shipping']['postcode']?>
	<?=$this->order->data['shipping']['city']?></p>
	<p>Pour un montant de :
	<?=$this->get_woocommerce_totals()['order_total']['value']?></p>
	<p>Pour l’adhésion à la saison sportive : 2024-2025</p>
</div>

<p class="pour">Pour la présidente<br/>Geneviève daël</p>
<p class="date">Chaville, le <?=$this->date($this->get_type())?></p>

<div class="signatures">
	<img class="tampon" src="<?=site_url()?>/fichiers/tampon.jpg" />
</div>
