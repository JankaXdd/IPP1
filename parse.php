<?php
    ini_set('display_errors', 'stderr');

    function XML_Header(){
        return "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
    }

    function XML_SpecialChars($string){
        $string = str_replace("&", "&amp;", $string);
        $string = str_replace("<", "&lt;", $string);
        $string = str_replace(">", "&gt;", $string);
        return $string;
    }

    function PrintArg($arg, $argType, $argCounter){
        $arg = XML_SpecialChars($arg);
        return "\t\t<arg$argCounter type=\"$argType\">$arg</arg$argCounter>\n";

    }

    function PrintInstruction($instruction, $op_counter){
        $instruction = strtoupper($instruction);
        return "\t<instruction order=\"$op_counter\" opcode=\"$instruction\">\n";
    }

    function IsLabel($label){
        if(preg_match("/\A[a-zA-Z$\-_&%\*!?][a-zA-Z\-_$&%\*!?]*\z/", $label)){
            return true;
        }
        return false;
    }

    function IsSymb($symb){
        if($symb == "nil@nil"){
            return true;
        }
        if($symb == "bool@true" || $symb == "bool@false"){
            return true;
        }
        if(preg_match("/^int@(-|\+)?0[oO]?[0-7]+(_[0-7]+)*$/", $symb)){ //oktalova
            return true;
        }
        if(preg_match("/^int@(-|\+)?0[xX][0-9a-fA-F]+(_[0-9a-fA-F]+)*$/", $symb)){ //hexadecimalni
            return true;
        }
        if(preg_match("/^int@(-|\+)?[1-9][0-9]*(_[0-9]+)*$/", $symb)){ //dekadicka
            return true;
        }
        if(preg_match("/^int@(-|\+)?0$/", $symb)){ //"kladna a zaporna" nula
            return true;
        }
        if(preg_match("/^string@([^\\\\#\\s]|\\\\\\d{3})*\z/", $symb)){
            return true;
        }
        return false;
    }

    function IsVar($variable){
        if(preg_match("/^(LF|GF|TF)@[a-zA-Z\-_$&%*!?][a-zA-Z0-9_\-$&%*!?]*\z/", $variable)){
            return true;
        }
        return false;
    }

    function IsType($type){
        if(preg_match("/^(bool|int|string)\z/", $type)){
            return true;
        }
        return false;
    }
    
    function NoArgs($line){
        if(sizeof($line)-1 != 0){
            exit(23);
        }
        return;
    }

    function LabelArg($arguments){
        if(sizeof($arguments) != 2){
            exit(23);
        }
        if(!IsLabel($arguments[1])){
            exit(23);            
        }
        echo (PrintArg($arguments[1], "label", 1));

        return;

    }

    function VarArg($arguments){
        if(sizeof($arguments) != 2){
            exit(23);
        }

        if(!IsVar($arguments[1])){
            exit(23);            
        }
        echo (PrintArg($arguments[1], "var", 1));

        return;
    }

    function SymbArg($arguments){

        if(sizeof($arguments) != 2){
            exit(23);
        }

        if(IsSymb($arguments[1])){
            $symb = explode("@", $arguments[1]);
            $symbType = $symb[0];
            array_shift($symb);
            $symb = implode("@", $symb);
            echo (PrintArg($symb, $symbType, 1));
        }
        else if(IsVar($arguments[1])){
            echo (PrintArg($arguments[1], "var", 1));
        }
        else{
            exit(23);
        }
        return;
    }

    function VarSymbArgs($arguments){
        if(sizeof($arguments) != 3){
            exit(23);
        }

        if(!IsVar($arguments[1])){
            exit(23);            
        }

        echo (PrintArg($arguments[1], "var", 1));
        
        if(IsSymb($arguments[2])){
            $symb = explode("@", $arguments[2]);
            $symbType = $symb[0];
            array_shift($symb);
            $symb = implode("@", $symb);
            echo (PrintArg($symb, $symbType, 2));
        }
        else if(IsVar($arguments[2])){
            echo (PrintArg($arguments[2], "var", 2));
        }
        else{
            exit(23);
        }
        return;
    }

    function VarSymbSymbArgs($arguments){
        if(sizeof($arguments) != 4){
            exit(23);
        }

        if(!IsVar($arguments[1])){
            exit(23);            
        }
        echo (PrintArg($arguments[1], "var", 1)); 

        if(IsSymb($arguments[2])){
            $symb = explode("@", $arguments[2]);
            $symbType = $symb[0];
            array_shift($symb);
            $symb = implode("@", $symb);
            echo (PrintArg($symb, $symbType, 2));
        }
        else if(IsVar($arguments[2])){
            echo (PrintArg($arguments[2], "var", 2));
        }
        else{
            exit(23);
        }

        if(IsSymb($arguments[3])){
            $symb = explode("@", $arguments[3]);
            $symbType = $symb[0];
            array_shift($symb);
            $symb = implode("@", $symb);
            echo (PrintArg($symb, $symbType, 3));
        }
        else if(IsVar($arguments[3])){
            echo (PrintArg($arguments[3], "var", 3));
        }
        else{
            exit(23);
        }

        return;
    }

    function VarTypeArgs($arguments){
        if(sizeof($arguments) != 3){
            exit(23);
        }
        if(!IsVar($arguments[1])){
            exit(23);            
        }
        echo (PrintArg($arguments[1], "var", 1));

        if(!IsType($arguments[2])){
            exit(23);
        }
        echo (PrintArg($arguments[2], "type", 2));

        return;
    }


    function LabelSymbSymbArgs($arguments){
        if(sizeof($arguments) != 4){
            exit(23);
        }

        if(!IsLabel($arguments[1])){
            exit(23);
        }
        echo (PrintArg($arguments[1], "label", 1));

        if(IsSymb($arguments[2])){
            $symb = explode("@", $arguments[2]);
            $symbType = $symb[0];
            array_shift($symb);
            $symb = implode("@", $symb);
            echo (PrintArg($symb, $symbType, 2));
        }
        else if(IsVar($arguments[2])){
            echo (PrintArg($arguments[2], "var", 2));
        }
        else{
            exit(23);
        }

        if(IsSymb($arguments[3])){
            $symb = explode("@", $arguments[3]);
            $symbType = $symb[0];
            array_shift($symb);
            $symb = implode("@", $symb);
            echo (PrintArg($symb, $symbType, 3));
        }
        else if(IsVar($arguments[3])){
            echo (PrintArg($arguments[3], "var", 3));
        }
        else{
            exit(23);
        }
        
        return;
    }

    function parse(&$output)
    {
        $stdin = fopen('php://stdin', 'r');
        $header = false;
        $op_counter = 0;

        $line = fgets($stdin);
        
        while($line){
            $line = trim($line);
            $line = explode("#", $line)[0];
            if(!$header){
                $line = strtolower($line);
                $line = preg_replace('/\s+/', '', $line);
                if(!$line){
                    $line = fgets($stdin);
                    continue;
                }
                if($line === ".ippcode23"){
                    echo ("<program language=\"IPPcode23\">\n");
                    $header = true;
                }
                else if($line){
                    exit(21);
                }
            }
            else{  
                if($line){
                    $line = preg_replace('!\s+!', ' ', $line);
                    $splitLine = explode(" ", trim($line));
                    switch(strtolower($splitLine[0])){
                        #instrukce bez argumentu
                        case "createframe":
                        case "pushframe":
                        case "popframe":
                        case "return":
                        case "break":
                            echo (PrintInstruction($splitLine[0], ++$op_counter));
                            NoArgs($splitLine);
                            echo ("\t</instruction>\n");
                            break;
                        
                        
                        #instrukce s jedním "label" argumentem
                        case "label":
                        case "jump":
                        case "call":
                            echo (PrintInstruction($splitLine[0], ++$op_counter));
                            LabelArg($splitLine);
                            echo ("\t</instruction>\n");
                            break;

                        #instrukce s jedním "symb" argumentem
                        case "pushs":
                        case "write":
                        case "exit":
                        case "dprint":
                            echo (PrintInstruction($splitLine[0], ++$op_counter));
                            SymbArg($splitLine);
                            echo ("\t</instruction>\n");
                            break;

                        #instrukce s jedním "var" argumentem
                        case "defvar":
                        case "pops":
                            echo (PrintInstruction($splitLine[0], ++$op_counter));
                            VarArg($splitLine);
                            echo ("\t</instruction>\n");
                            break;
                                    
                        
                        #instrukce s jedním "var" a s jedním "symb" argumentem
                        case "move":
                        case "int2char":
                        case "strlen":
                        case "type":
                        case "not":
                            echo (PrintInstruction($splitLine[0], ++$op_counter));
                            VarSymbArgs($splitLine);
                            echo ("\t</instruction>\n");
                            break;

                        #instrukce s jedním "var" argumentem a dvěma "symb" argumenty
                        case "add":
                        case "sub":
                        case "mul":
                        case "idiv":
                        case "lt":
                        case "gt":
                        case "eq":
                        case "and":
                        case "or":
                        case "stri2int":
                        case "concat":
                        case "getchar":
                        case "setchar":
                            echo (PrintInstruction($splitLine[0], ++$op_counter));
                            VarSymbSymbArgs($splitLine);
                            echo ("\t</instruction>\n");
                            break;

                        #instrukce s jedním "var" a s jedním "type" argumentem
                        case "read":
                            echo (PrintInstruction($splitLine[0], ++$op_counter));
                            VarTypeArgs($splitLine);
                            echo ("\t</instruction>\n");
                            break;

                        #instrukce s jedním "label" a dvěma "symb" argumenty
                        case "jumpifeq":
                        case "jumpifneq":
                            echo (PrintInstruction($splitLine[0], ++$op_counter));
                            LabelSymbSymbArgs($splitLine);
                            echo ("\t</instruction>\n");
                            break;
                        default:
                            exit(22);
                    }
                }   
            }
            $line = fgets($stdin);
        }
        if($header){
            echo ("</program>\n");
        }
        return 0;

    }

    if($argc != 1){
        if($argv[1] == "--help"){
            echo "Skript typu filtr (parse.php v jazyce PHP 8.1) nacte ze standardniho vstupu zdrojovy kod v IPP-code23, zkontroluje lexikalni a syntaktickou spravnost kodu a vypise na standardni vystup XML reprezentaci programu dle specifikace\n";
            exit(0);
        }
        else{
            exit(10);
        }
    }

    echo (XML_Header());

    $result = parse($output);
    
    exit($result);
?>