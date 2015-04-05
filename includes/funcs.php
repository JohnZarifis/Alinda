<?php

function redirect_to($location = NULL){
	if($location!=NULL){
		header("Location:{$location}");
		exit;
	}
}

function output_message($message=""){
	if(!empty($message)){
		return "<p class=\message\">{$message}</p>";
	} else{
		return "";
	}
}

// function number_format_clean($number,$precision=2,$dec_point=',',$thousands_sep='.')
    // {
    	// $number =str_replace("," , ".", $number);
		// $result = trim(number_format($number,$precision,$dec_point,$thousands_sep), $dec_point.'00');
		// if(!$result){
			// RETURN 0;
		// }
		// else{RETURN $result;}
//         
    // }
function number_format_clean($number,$precision=2,$dec_point=',',$thousands_sep='.')
    {
    	$number =str_replace("," , ".", $number);
		$result = number_format($number,$precision,$dec_point,$thousands_sep);
		if(!$result){
			RETURN 0;
		}
		else{RETURN $result;}
        
    }
function number_format_lastprice($number,$precision=4,$dec_point=',',$thousands_sep='.')
    {
    	$number =str_replace("," , ".", $number);
		$result = number_format($number,$precision,$dec_point,$thousands_sep);
		if(!$result){
			RETURN 0;
		}
		else{RETURN $result;}
        
    }

?>