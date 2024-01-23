<style>
.logo {
  float: left;
  margin : 1cm;
  width: 15%;
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
  margin-top: 3cm;
  text-align: center;
}
.corps {
  margin: 1cm 2cm 2cm 4cm;
  text-indent: 1cm;
  font-size: 16px;
  line-height: 1.3em;
  text-align: justify;
  text-justify: inter-word;
}
.date {
  margin: 0 2cm;
  text-align: right;
  font-size: 14px;
}
.signatures {
  text-align: right;
  padding: 1cm 2cm;
  display: flex;
  justify-content: space-around;
}
.signatures img {
  width: 6cm;
}
</style>

<img class="logo" src="<?=get_theme_file_uri()?>/woocommerce/pdf/Attestation/logo_club.jpg" />

<div class="entete">
<p>CHAVIL’ GYMNASTIQUE VOLONTAIRE</p>
<p>2 rue Jean-Jaurès, 92370 Chaville</p>
<p>Association loi 1901, JO n° 1691 du 2 juin 2001</p>
<p>Siret 43 888 2003 000 17</p>
<p>https://chaville.gym.c92.fr/</p>
</div>

<h1 class="titre">ATTESTATION D’ADHESION</h1>

<?php
$shipping = $this->order->data['shipping'];
?>
<div class="corps">
<p>Je, soussignée Françoise THELOT, secrétaire de Chavil' Gymnastique Volontaire, certifie que
 <?=$this->order->data['shipping']['first_name']?>
 <?=$this->order->data['shipping']['last_name']?>, résidant
 <?=$this->order->data['shipping']['address_1']?>
 <?=$this->order->data['shipping']['address_2']?>
 <?=$this->order->data['shipping']['postcode']?>
 <?=$this->order->data['shipping']['city']?>,
a versé la somme de
<?=$this->get_woocommerce_totals()['order_total']['value']?>
 pour l’adhésion à la saison sportive 2024-2025.</p>
</div>

<p class="date">Chaville, le <?=$this->date($this->get_type())?></p>

<div class="signatures">
<img class="signature" src="<?=get_theme_file_uri()?>/woocommerce/pdf/Attestation/signature.jpg" />
<img class="tampon" src="<?=get_theme_file_uri()?>/woocommerce/pdf/Attestation/tampon.jpg" />
</div>
