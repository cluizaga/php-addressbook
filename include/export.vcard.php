<?php

function text2adr($address) {
   	
   	  preg_match_all('/[^\d](\d{4,6})[^\d]/', $address, $zips);
   	  preg_match_all('/[^\d](\d{1,3})[^\d]/', $address, $street_nrs);
      
      $address = str_replace("\r", "", $address);
      $addr_lines = explode("\n", $address);
      $cnt_lines  = count($addr_lines);
      
      // 
      // Find the city:
      //  a) On the line of the zip.
      //  b) On the last line if more than 2 letters
      //  c) ...
      //
      $zip  = "";
      $city = "";
      if(count($zips[0]) > 0) {
      	$zip = $zips[1][0];
      	for($i = 0; $i < $cnt_lines; $i++) {
        	if(FALSE !== strpos($addr_lines[$i], $zip)) {
        		$city_line = $i;
        		$city = trim(str_replace($zip, "", $addr_lines[$i]), ", ");
        	}
      	}
      } else {
      	if($cnt_lines >= 2) {
          $city_line = $cnt_lines-1;
     			$city = $addr_lines[$city_line];
      		if(strlen($city) <= 2) {
            $city_line = $cnt_lines-2;
      			$city = $addr_lines[$city_line];
      		}
      	} elseif($cnt_lines == 1) {
          $city_line = 0;
      		$city = $addr_lines[$city_line];
      	}
      }
      
      //--------------------------------------------------------
      // Find the name of the street:
      //
      $addr      = "";
      $street    = "";
      $street_nr = "";
      if(count($street_nrs[0]) > 0) {
      	$street_nr = $street_nrs[1][0];
        for($i = 0; $i < $cnt_lines; $i++) {
        	if(FALSE !== strpos($addr_lines[$i], $street_nr)) {
        		$addr_line = $i;
      	    $addr = $addr_lines[$addr_line];
        		$street = trim(str_replace($street_nr, "", $addr_lines[$i]), ", ");
        	}
        }        
      } elseif(isset($city_line) && $city_line >= 1) {
       		$addr_line = $city_line-1;
      	  $addr = $addr_lines[$addr_line];
          $street = $addr;
      }

      //--------------------------------------------------------
      // Find the extension:
      //
      $exta = "";
      if(isset($addr_line) && $addr_line >= 1) {
      	  $exta = $addr_lines[$addr_line-1];
      }

      //--------------------------------------------------------
      // Find the name of the country:
      //
      $country = "";
      if(isset($city_line) && $city_line < $cnt_lines-1) {
      	  $country = $addr_lines[$city_line+1];
      }

      $addr_struc['pbox']      = "";         // post office box
      $addr_struc['exta']      = $exta;      // the extended address; the street   
      $addr_struc['street']    = $street;
      $addr_struc['street_nr'] = $street_nr;
      $addr_struc['addr']      = $addr;      // address
      $addr_struc['city']      = $city;      // the locality (e.g., city)
      $addr_struc['region']    = "";         // the region (e.g., state or province)
      $addr_struc['zip']       = $zip;       // the postal code
      $addr_struc['country']   = $country;   // the country name
   	     	     	  
   	  return $addr_struc;
   	
}

function address2vcard($links) {
	
   $firstname  = utf8_to_latin1($links["firstname"]);
   $lastname   = utf8_to_latin1($links["lastname"]);
   $address    = utf8_to_latin1($links["address"]);
   $home       = utf8_to_latin1($links["home"]);
   $mobile     = utf8_to_latin1($links["mobile"]);
   $work       = utf8_to_latin1($links["work"]);
   $fax        = utf8_to_latin1($links["fax"]);
   $email      = utf8_to_latin1($links["email"]);
   $email2     = utf8_to_latin1($links["email2"]);
 
   $bday       = utf8_to_latin1($links["bday"]);
   $bmonth_num = utf8_to_latin1($links["bmonth_num"]);
   $byear      = utf8_to_latin1($links["byear"]);

   $phone2     = utf8_to_latin1($links["phone2"]);
   $address2   = utf8_to_latin1($links["address2"]);

	 $result  = "BEGIN:VCARD\n";
	 $result .= "VERSION:2.1\n";
	 $result .= "N:$lastname;$firstname;;;;\n";
	 $result .= "FN:$firstname $lastname\n";
	 $adr = text2adr($address);
	 $result .= "ADR;home:"
	               .$adr['pbox']
	           .";".$adr['exta']
	           .";".$adr['addr']
	           .";".$adr['city']
	           .";".$adr['region']
	           .";".$adr['zip']
	           .";".$adr['country']."\n";
	 $adr = text2adr($address2);
	 $result .= "ADR;work:"
	               .$adr['pbox']
	           .";".$adr['exta']
	           .";".$adr['addr']
	           .";".$adr['city']
	           .";".$adr['region']
	           .";".$adr['zip']
	           .";".$adr['country']."\n";
	 $result .= "TEL;HOME;VOICE:$home\n";
	 $result .= "TEL;cell;VOICE:$mobile\n";
	 $result .= "TEL;work;VOICE:$work\n";
	 $result .= "TEL;fax:$fax\n";
	 $result .= "TEL:$phone2\n";
	 $result .= "EMAIL;PREF;INTERNET:$email\n";
	 $result .= "EMAIL;PREF;INTERNET:$email2\n";
	 $result .= "BDAY:$byear-".(strlen($bmonth_num) == 1?"0":"")."$bmonth_num-$bday\n";
	 $result .= "END:VCARD\n";
	 
   return $result;
}

?>