<?php
function check_fields($fields, $update=false) {
    $message = "";
    // loop over fields to check for errors
    foreach ($fields as $field => $value) {
        if ($field == "strasse" OR $field == "stadt" OR $field == "bundesland") {
            if (!ctype_alpha($field)) {
                $message .= "Falsche Eingabe für " . $field .". Bitte geben Sie nur Buchstaben ein.";
                break;
            }
            elseif (strlen($field) < 2) {
                $message .= "Falsche Eingabe für " . $field .". Bitte geben Sie mindestens 2 Buchstaben ein.";
                break;
            }
            elseif (strlen($field) > 50) {
                $message .= "Falsche Eingabe für " . $field .". Bitte geben Sie maximal 50 Buchstaben ein.";
                break;
            }
            else {
                if ($field == "bundesland") {
                    //bundesland check
                    $check = file_get_contents("https://openplzapi.org/de/FederalStates");
                    $check = json_decode($check, true);
                    $check = array_column($check, "name");
                    if (!in_array($value, $check)) {
                        $message .= "Falsche Eingabe für " . $field .".<br> <em>Bitte geben Sie eines der gültigen Bundesländer ein:</em><br> " . implode(", ", $check) . ".";
                        break;
                    }
                }
                elseif ($field == "stadt" AND isset($fields['plz']) AND $fields["plz"] != ""){
                    //stadt check
                    $check = file_get_contents("https://openplzapi.org/de/Localities?postalCode=" . $fields["plz"]);
                    $check = json_decode($check, true);
                    $check = array_column($check, "name");
                    if (!in_array($value, $check)) {
                        if (!count($check)) {
                            $message .= "Falsche Postleitzahl. Bitte eine gültige Postleitzahl eingeben.";
                            break;
                        }
                        else {
                            $message .= "Postleitzahl und Stadt passen nicht zusammen.<br><em> Bitte die gültige Stadt für die Postleitzahl eingeben:</em><br> " . implode(", ", $check) . ".";
                            break;
                        }
                    }
                }
            }
        }
        
        elseif ($field == "plz") {
            if (!is_numeric($value) OR strlen($value) != 5) {
                $message .= "Falsche Postleitzahl. Bitte eine 5-stellige Zahl eingeben.";
                break;
            }
            else {
                //auto fill bundesland and stadt
                $check = file_get_contents("https://openplzapi.org/de/Localities?postalCode=" . $value);
                if (!count(json_decode($check, true))) {
                    $message .= "Falsche Postleitzahl. Bitte eine gültige Postleitzahl eingeben.";
                    break;
                }
                else {
                    $check = json_decode($check, true);
                    // if not matching, update fields
                    if (($fields["stadt"] != $check[0]["name"] OR $fields["bundesland"] != $check[0]["federalState"]["name"])) {
                        $fields["stadt"] = $check[0]["name"];
                        $fields["bundesland"] = $check[0]["federalState"]["name"];
                        $_SESSION['fields'] = $fields;
                        if ($update) {
                            header('Location: update_wohnung.html?id='.$_GET['id']);
                            exit();
                        }
                        else {
                            header('Location: add_wohnung.html');
                        }
                        exit();
                    }
                } 
            }
        }
        elseif ($field == "etage") {
            if ($value == "") {
                $value = 0;
            }
            if (!is_numeric($value) OR $value < 0) {
                $message .= "Falsche Etage. Bitte eine positive Zahl eingeben oder für's Erdgeschoß leer lassen. " .$value;
                break;
            }
        }
    }
    return $message;
}
?>