<?php
function cu_pr_print_fantasia_header($tpl, $order){
	echo '<div class="fantasia-header">';
	$nome = $order->billing_first_name;
	$cognome = $order->billing_last_name;
	if($tpl == 'capricci'):
	?>
	<h2>
		Cara 
		<?php echo $nome ." ". $cognome; ?> , per il tuo ordine 
		<?php echo $order->id; ?>
	</h2>
	<h1>ecco la TUA tshirt AURONIA</h1>
	<?php

	elseif($tpl == 'kiss-me'):
	?>
		<p>
			Un invito, un modo di essere, per chi non vuole stare ad aspettare.<br />
			Qui sei trattata da vera diva, immortalata con il tuo sorriso migliore e indiscussa protagonista di un poster di sapore pop-art da indossare e mostrare con orgoglio.
		</p>
		Ecco la tua Kiss Me, creata apposta per te. A te la scelta!
	<?php

	elseif($tpl == 'fusion'):
	?>
		<p>
			Imperativo: liberarsi di tutto quello che non ci va più!<br />
			Un’esplosione di carattere, di gioia e di determinazione invade tutto lo spazio visibile.
		</p>
		Ecco la tua Fusion, creata apposta per te. A te la scelta!
	<?php

	elseif($tpl == 'cromoterapia'):
	?>
		<p>
			I nostri lineamenti sono l’impronta con cui ci presentiamo al mondo.<br />
			Da loro passano tutte le nostre emozioni; tutto il resto serve a poco.
			Fluttuano liberi nello spazio circostante.
		</p>
		Ecco la tua Cromoterapia, creata apposta per te. A te la scelta!
	<?php


	elseif($tpl == 'perpetual-rose'):
	?>
		<p>
			Un tocco così deciso, impresso con un delicato vigore, tutto d’un fiato.<br />
			La mano spazia senza confini e fonde i diversi elementi creando una nuova dimensione in cui tu sei l’assoluta protagonista.
		</p> 
		Ecco la tua Perpetual Rose, creata apposta per te. A te la scelta!
	<?php
	endif;

	echo '</div><div class="clear"></div>';
}

function cu_pr_print_fantasia_footer($tpl){
	echo '<div class="fantasia-footer">';

	if($tpl == 'capricci'):
	?>
	    Scegliendo la proposta creativa che preferisci darai il via alla produzione della tua t-shirt personalizzata. Mani esperte si metteranno all’opera per te, stampandola e impreziosendola con applicazioni e strass Swarovski® posizionati a mano seguendo i dettagli del tuo viso. 10 giorni e sarà tua!
	<?php

	elseif($tpl == 'kiss-me'):
	?>
		La proposta Soft presenta un trattamento della tua immagine più 
		delicato, nei toni del grigio; nella proposta Cool invece i graphic 
		designer hanno marcato la mano attingendo pesantemente al nero e 
		garantendo un risultato decisamente rock. Entrambe le proposte hanno 
		una variante “plus” con una fantasia molto ricca, colma, quasi opulenta.
		L’applicazione di strass e borchie qui rappresentata è indicativa 
		in quanto il loro posizionamento sarà realizzato a mano, uno alla 
		volta, nell’ultima fase di produzione della t-shirt. 
	<?php

	elseif($tpl == 'fusion'):
	?>
		Nella proposta Soft la tua immagine appare con delicatezza, completamente 
		avvolta dai colori; nella proposta Cool invece i graphic designer hanno 
		studiato un effetto pop con colori saturi e pennellate decise dall’effetto 
		striato, di forte impatto visivo, di cui sei piena protagonista. Entrambe 
		le proposte hanno una variante Plus con una fantasia molto ricca, colma, 
		quasi opulenta.
		L’applicazione di strass e borchie qui rappresentata è indicativa in quanto 
		il loro posizionamento sarà realizzato a mano, uno alla volta, nell’ultima 
		fase di produzione della t-shirt. 

	<?php

	elseif($tpl == 'cromoterapia'):
	?>
		Nella proposta Soft la tua immagine appare con delicatezza, completamente 
		avvolta dai colori; nella proposta Cool invece i graphic designer hanno 
		studiato un effetto pop con colori saturi e pennellate decise dall’effetto 
		striato, di forte impatto visivo, di cui sei piena protagonista. Entrambe 
		le proposte hanno una variante Plus con una fantasia molto ricca, colma, 
		quasi opulenta.
		L’applicazione di strass e borchie qui rappresentata è indicativa in quanto 
		il loro posizionamento sarà realizzato a mano, uno alla volta, nell’ultima 
		fase di produzione della t-shirt. 

	<?php

	elseif($tpl == 'perpetual-rose'):
	?>
		La proposta Soft presenta la tua immagine ha un trattamento più misterioso, 
		appare con delicatezza, completamente avvolta dai colori; nella proposta Cool 
		invece i graphic designer hanno studiato un effetto pop con colori saturi e 
		pennellate decise con un effetto striato, di forte impatto visivo di cui sei 
		piena protagonista. Entrambe le proposte hanno una variante “plus” con una 
		fantasia molto ricca, colma, quasi opulenta.
		L’applicazione di strass e borchie qui rappresentata è indicativa in quanto 
		il loro posizionamento sarà realizzato a mano, uno alla volta, nell’ultima 
		fase di produzione della t-shirt. 
	<?php
	endif;

	echo '</div><div class="clear"></div>';
}