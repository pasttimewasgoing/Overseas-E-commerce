lö¢i<?php exit; ?>a:6:{s:10:"last_error";s:0:"";s:10:"last_query";s:380:"SELECT DISTINCT t.term_id
			 FROM wp_terms AS t  LEFT JOIN wp_termmeta ON ( t.term_id = wp_termmeta.term_id AND wp_termmeta.meta_key='order') INNER JOIN wp_term_taxonomy AS tt ON t.term_id = tt.term_id
			 WHERE tt.taxonomy IN ('product_cat') AND ( 
  ( wp_termmeta.meta_key = 'order' OR wp_termmeta.meta_key IS NULL )
)
			 ORDER BY wp_termmeta.meta_value+0 ASC, t.name ASC
			 ";s:11:"last_result";a:7:{i:0;O:8:"stdClass":1:{s:7:"term_id";s:2:"16";}i:1;O:8:"stdClass":1:{s:7:"term_id";s:2:"23";}i:2;O:8:"stdClass":1:{s:7:"term_id";s:2:"17";}i:3;O:8:"stdClass":1:{s:7:"term_id";s:2:"24";}i:4;O:8:"stdClass":1:{s:7:"term_id";s:2:"29";}i:5;O:8:"stdClass":1:{s:7:"term_id";s:2:"28";}i:6;O:8:"stdClass":1:{s:7:"term_id";s:2:"27";}}s:8:"col_info";a:1:{i:0;O:8:"stdClass":13:{s:4:"name";s:7:"term_id";s:7:"orgname";s:7:"term_id";s:5:"table";s:1:"t";s:8:"orgtable";s:8:"wp_terms";s:3:"def";s:0:"";s:2:"db";s:9:"wordpress";s:7:"catalog";s:3:"def";s:10:"max_length";i:2;s:6:"length";i:20;s:9:"charsetnr";i:63;s:5:"flags";i:32801;s:4:"type";i:8;s:8:"decimals";i:0;}}s:8:"num_rows";i:7;s:10:"return_val";i:7;}