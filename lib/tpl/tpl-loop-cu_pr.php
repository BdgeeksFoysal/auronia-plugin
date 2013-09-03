<?php
function cu_pr_print_fantasia_header($tpl){
	echo '<div class="fantasia-header">';

	if($tpl == 'voyage'):
	?>
		<p>
			Dolcemente accomodata su un letto di farfalle. 
			Assapora tutta la loro libertà in una esplosione di colore. 
			I contorni del tuo viso sono scolpiti, vividi, per una forte 
			dichiarazione di indipendenza.
		</p>
		Ecco la tua Voyage nel colore rosa, creata apposta per te. A te 
		la scelta!
	<?php

	elseif($tpl == 'rose'):
	?>
		<p>
			Un tocco così delicatamente deciso. 
			La mano spazia senza confini e fonde i diversi elementi 
			creando una nuova dimensione in cui tu sei l’assoluta protagonista. 
			L’essenzialità del bianco e nero con lampi di vivacità che infiammano.
		</p>
		Ecco la tua Perpetual Rose con applicazioni fluo lime, creata apposta 
		per te. A te la scelta!
	<?php

	elseif($tpl == 'mystique'):
	?>
		<p>
			Magicamente spuntano dettagli accattivanti del tuo viso.  
			Un intrigante mistero cela il desiderio di saperne di più. 
			I colori sono pieni, vividi, ricchi, a piene emozioni.
		</p>
		Ecco la tua Mystique nella combinazione di colori viola/verde, 
		creata apposta per te. A te la scelta!
	<?php

	elseif($tpl == 'graffiti'):
	?>
		<p>
			Magicamente spuntano dettagli accattivanti del tuo viso.  
			Un intrigante mistero cela il desiderio di saperne di più.
		</p> 
		I colori sono pieni, vividi, ricchi, a piene emozioni. 
		A te la scelta!
	<?php
	endif;

	echo '</div><div class="clear"></div>';
}

function cu_pr_print_fantasia_footer($tpl){
	echo '<div class="fantasia-footer">';

	if($tpl == 'voyage'):
	?>
		La proposta Soft presenta un trattamento della tua immagine più 
		delicato; nella proposta Cool invece i graphic designer hanno dato 
		potenza solo ad alcuni dettagli del tuo viso e garantendo un risultato 
		marcato ed impattante, artistico. Entrambe le proposte hanno una 
		variante “plus” con una fantasia molto ricca, colma, quasi opulenta.
		L’applicazione di strass e borchie qui rappresentata è indicativa 
		in quanto il loro posizionamento sarà realizzato a mano, uno alla 
		volta, nell’ultima fase di produzione della t-shirt. 
	<?php

	elseif($tpl == 'rose'):
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

	elseif($tpl == 'mystique'):
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

	elseif($tpl == 'graffiti'):
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