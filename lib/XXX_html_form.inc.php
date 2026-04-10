<?php

function html_form($item, $action, $method='post'){
	
	$html_form="<form action='$action' method='$method'>\n";
    for($i=0;$i<count($item);$i++){
    	switch ($item[$i]){
    		case 'input':
    			form_input();
    		break;



    	}

    }
	$html_form.="</form>\n";
}


function form_input($type){
 	switch($type){
 		case 'text':
 			input_text();
 		break;
 		case 'password':
 		case 'submit':
 	}
}

function input_text($required='n'){

  if(strtoupper($required)=='Y' || )


}

?>