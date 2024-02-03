<style>
	body {
		margin: 20px;
	}
	p,
	.product	{
		font-size: 16px;
		line-height: 20px;
	}
</style>

<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>

<?php do_action( 'wpo_wcpdf_before_document', $this->get_type(), $this->order ); ?>

<?php do_action( 'wpo_wcpdf_before_document_label', $this->get_type(), $this->order ); ?>
<h1 class="document-type-label">FICHE D'INSCRIPTION</h1>
<?php do_action( 'wpo_wcpdf_after_document_label', $this->get_type(), $this->order ); ?>

<table class="order-data-addresses">
	<tr>
		<td class="address shipping-address">
			<p><b>Commande n°</b>: <?=$this->order_number()?> du <?=$this->order_date()?></p>
			<p><?=$this->order->get_meta('_billing_wooccm11')?> <?=$this->shipping_address()?></p>
			<p><b>Né(e) le</b>: <?=$this->order->get_meta('_billing_wooccm12')?></p>
			<p><b>Représentant légal</b>: <?=$this->order->get_meta('_billing_wooccm21')?></p>
			<p><b>Téléphone</b>: <?=$this->billing_phone()?></p>
			<p><b>Mail</b>: <?=$this->billing_email()?></p>
			<p><b>Personne à prévenir</b>: <?=$this->order->get_meta('_billing_wooccm13')?> / Tel:
			<?=$this->order->get_meta('_billing_wooccm18')?></p>
			<p><b>Questionnaire de santé</b>: <?=$this->order->get_meta('_billing_wooccm14')?'NON':'OUI'?></p>
			<p><b>Certificat médical</b>: <?php
				$cm_id = $this->order->get_meta('_billing_wooccm16');
				if ($cm_id)
					echo '<a target="_bliank" href="'.wp_get_attachment_url($cm_id).'">Télécharger</a>';
			?></p>
			<p><b>Publication de l'image</b>: <?=$this->order->get_meta('_billing_wooccm20')?'NON':'OUI'?>
			<p><b>Payé</b>: <?=$this->get_woocommerce_totals()['order_total']['value']?>
		</td>
	</tr>
</table>

<?php do_action( 'wpo_wcpdf_before_order_details', $this->get_type(), $this->order ); ?>

<table class="order-details">
	<tbody>
		<?php foreach ( $this->get_order_items() as $item_id => $item ) : ?>
			<tr class="<?php echo apply_filters( 'wpo_wcpdf_item_row_class', 'item-'.$item_id, esc_attr( $this->get_type() ), $this->order, $item_id ); ?>">
				<td class="product">
					<?php $description_label = __( 'Description', 'woocommerce-pdf-invoices-packing-slips' ); // registering alternate label translation ?>
					<span class="item-name"><?php echo $item['name']; ?></span>
					<?php do_action( 'wpo_wcpdf_before_item_meta', $this->get_type(), $item, $this->order  ); ?>
					<span class="item-meta"><?php echo $item['meta']; ?></span>
					<dl class="meta">
						<?php $description_label = __( 'SKU', 'woocommerce-pdf-invoices-packing-slips' ); // registering alternate label translation ?>
						<?php if ( ! empty( $item['sku'] ) ) : ?><dt class="sku"><?php _e( 'SKU:', 'woocommerce-pdf-invoices-packing-slips' ); ?></dt><dd class="sku"><?php echo esc_attr( $item['sku'] ); ?></dd><?php endif; ?>
						<?php if ( ! empty( $item['weight'] ) ) : ?><dt class="weight"><?php _e( 'Weight:', 'woocommerce-pdf-invoices-packing-slips' ); ?></dt><dd class="weight"><?php echo esc_attr( $item['weight'] ); ?><?php echo esc_attr( get_option( 'woocommerce_weight_unit' ) ); ?></dd><?php endif; ?>
					</dl>
					<?php do_action( 'wpo_wcpdf_after_item_meta', $this->get_type(), $item, $this->order  ); ?>
				</td>
			</tr>
		<?php endforeach; ?>
	</tbody>
</table>

<?php do_action( 'wpo_wcpdf_after_document', $this->get_type(), $this->order ); ?>