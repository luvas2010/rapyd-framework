<?php


$lang = array
(
  //dataedit
	'de.inserted'=> 'Záznam byl zadán správně.',
	'de.updated'=> 'Záznam byl úspěšně změněn.',
	'de.deleted'=> 'Záznam byl úspěšně smazán.',

	'de.err_read'	=> 'Došlo k chybě při čtení záznamu.',
	'de.err_insert'	=> 'Nastala chyba při vkládání záznamu.',
	'de.err_update'	=> 'Došlo k chybě při úpravě záznamu.',
	'de.err_delete'	=> 'Došlo k chybě v záznamu je odstraněn.',
	'de.err_unknown'=> 'Došlo k chybě, žádný záznam, na kterém chcete pracovat.',
	'de.err_dup_pk' => 'Došlo k chybě, non-jedinečný primární klíč.',
	'de.err_no_model'=> 'Došlo k chybě, chybějících datového modelu, použijte $edit->source("tablename")',
	'de.err_no_backurl'=> 'Došlo k chybě, je nutné nastavit vlastnost "back_url"',
	
	'de.confirm_delete'=> 'Opravdu si přejete smazat aktuální záznam?',
	'de.inserted'=> 'Chyba, nejsou jedinečný primární klíč.',

  //field text
	'fld.delete'=> 'Odstranit',
	'fld.browse'=> 'Vyberte soubor, který chcete odeslat:',
	'fld.modify'=> 'Upravit',

  //buttons
	'btn.add'		=> 'Přidat',
	'btn.reset'	=> 'Obnovit',
	'btn.search'=> 'Hledání',
	'btn.modify'=> 'Upravit',
	'btn.delete'=> 'Smazat',

	'btn.do_delete'	=> 'Smazat',
	'btn.save'		=> 'Uložit',
	'btn.undo'		=> 'Zrušit',
	'btn.back'		=> 'Zpět seznam',
	'btn.back_edit'	=> 'Show',
	'btn.back_error'=> 'Zpět',

	// validations
	'val.required'      => 'Pole %s je vyžadována.',
	'val.isset'         => 'Pole %s je vyžadována.',
	'val.min_length'    => 'Pole %s musí být alespoň %d znaků.',
	'val.max_length'    => 'Pole %s nesmí být vyšší než %d znaků.',
	'val.exact_length'  => 'Toto pole %s musí obsahovat přesně %d znaků.',
	'val.matches'       => 'V tomto poli by se mělo shodovat s %s - %s pole',
	'val.valid_email'   => 'Pole %s musí obsahovat platnou e-mailovou adresu.',
	'val.in_range'      => 'Pole %s musí být v uvedeném rozsahu.',
	'val.regex'         => 'Pole %s není v rozporu s přijatými daty.',
	'val.unique'        => 'Pole %s musí být unikátní, je s záznam se stejnou hodnotou',
	'val.captcha'       => 'Pole %s neodpovídá, zkuste to znovu s novou image.',
	'val.approve'       => 'Musíte schválit: %s.',
	'val.valid_type'    => 'Pole %s je %s obsahuje znaky',

	// field types
	'val.alpha'         => 'alpha',
	'val.alpha_dash'    => 'abecední, pomlčka, a zdůraznil',
	'val.numeric'       => 'číselné',

	// upload errors
	'val.user_aborted'  => 'Nahrání souboru %s byla zrušena.',
	'val.invalid_type'  => 'Soubor %s není povolen typ souboru.',
	'val.max_size'      => 'Soubor %s je příliš velký. Maximální povolená velikost je %s.',
	'val.max_width'     => 'Soubor %s musí mít maximální šířku %spx.',
	'val.max_height'    => 'Soubor %s musí být maximální výšky %spx.',
	'val.min_width'     => 'Soubor %s musí mít maximální šířku %spx.',
	'val.min_height'    => 'Soubor %s musí být maximální výšky %spx.',

	// pagination
	'pag.first'         => 'První',
	'pag.last'          => 'Poslední',

);
