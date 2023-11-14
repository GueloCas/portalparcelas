<?php
	use uListing\Classes\StmListingSettings;
   use uListing\Classes\StmListingAttribute;

	wp_enqueue_script('tolips-mortgage-calc');
	
	$price = $args['model']->getAttributeValue(StmListingAttribute::TYPE_PRICE);
   $price = (isset($price["price"])) ? $price["price"] : 0;

	wp_localize_script('tolips-mortgage-calc', 'calc_data',  array(
      'homeValue'     => $price,
  	));
?>
<div <?php echo \uListing\Classes\Builder\UListingBuilder::generation_html_attribute($element) ?>>
	<div id="app_mortgage_calc">
		<h3 class="ulisting-el-heading"><?php echo esc_html__('Calculadora de hipoteca', 'tolips') ?></h3>
		<div class="form-mortgage_calc-content">

			<div class="form-group calc-results">
				<div class="item">
					<label><?php echo esc_html__('Costo total del préstamo:', 'tolips') ?></label>
					<span class="">{{formatAsCurrency(totalCostOfMortgage)}}</span>
				</div>
				<div class="item">
					<label><?php echo esc_html__('Interés total pagado:', 'tolips') ?></label>
					<span class="">{{formatAsCurrency(interestPayed)}}</span>
				</div> 
				<div class="item">
					<label>{{paymentSelection}} <?php echo esc_html__('Pago:', 'tolips') ?></label>
					<span class="out">{{formatAsCurrency(payment, 2)}}</span>
				</div> 
			</div> 

			<div class="form-group">
				<label><?php echo esc_html__('Valor de la vivienda:', 'tolips') ?></label>
				<custominput v-model="homeValue" :step="1000" value="222"/>
			</div>

			<div class="form-group">
				<label><?php echo esc_html__('Depósito:', 'tolips') ?></label>
				<custominput v-model="downpayment" :step="1000"/>
			</div>

			<div class="form-group">
				<label><?php echo esc_html__('Periodo de amortización:', 'tolips') ?></label>
				<custominput v-model="amortization" :step="1" type="years"/>
			</div>

			<div class="form-group">
				<label><?php echo esc_html__('Tasa de interés:', 'tolips') ?></label>
				<custominput v-model="interestRate" :step="0.001" type="percent" :decimals="3"/>
			</div>

			<div class="form-group">
				<label><?php echo esc_html__('Frecuencia de pago:', 'tolips') ?></label>
				<select class="form-control" v-model="paymentSelection">
				  <option v-for="payment, key in paymentPeriod" :value="key">{{key}}</option>
				</select>
			</div>
	  </div> 
	</div>
	<template id="custominput">
	  <input type="text" class="form-control" @keydown.up.prevent="increment" @keydown.down.prevent="decrement" :value="active?val:formatted" @blur="update" @keyup.enter.stop="update" @focus="active = true">
	</template>
</div>
