<?php
include("PARSECREATORS.php");
include("PARSEENTRIES.php");
include("PARSEMONTH.php");
include("PARSEPAGE.php");

// some samle usage

$parse = NEW PARSEENTRIES();
$parse->expandMacro = TRUE;
//	$array = array("RMP" =>"Rev., Mod. Phys.");
//	$parse->loadStringMacro($array);
//	$parse->removeDelimit = FALSE;
//	$parse->fieldExtract = FALSE;
$parse->openBib("bib.bib");
$parse->extractEntries();
$parse->closeBib();
list($preamble, $strings, $entries) = $parse->returnArrays();
print_r($preamble);
print "\n";
print_r($strings);
print "\n";
print_r($entries);
print "\n\n";



$authors = "Mark N. Grimshaw and Bush III, G.W. & M. C. Hammer Jr. and von Frankenstein, Ferdinand Cecil, P.H. & Charles Louis Xavier Joseph de la Vallee Poussin";
$creator = new PARSECREATORS();
$creatorArray = $creator->parse($authors);
print_r($creatorArray);