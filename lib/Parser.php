<?php
namespace Ems;

use SimpleExcel\SimpleExcel;
use SimpleExcel\Writer\CSVWriter;

/**
 * SimpleExcel class for parsing CSV Spreadsheet for EMS School
 *
 * @author  Ben Younes Ousama
 * @package Ems/Parser
 */
class Parser
{
	/**
	 * Holds the current $excel object
	 *
	 * @access   private
	 * @var      SimpleExcel
	 */
	protected $excel;

	/**
	 * Holds the current $line value
	 *
	 * @access   private
	 * @var      int
	 */
	protected $currentLine;

	const OLD_ID = 1;
	const DATE_RDV = 2;
	const HEURE_RDV = 3;
	const ETUDIANT1 = 4;
	const ETUDIANT2 = 5;
	const ETUDIANT3 = 6;
	const ETUDIANT4 = 7;
	const ETUDIANT5 = 8;
	const CIVILITE = 9;
	const NOM = 10;
	const PRENOM = 11;
	const ADRESSE = 12;
	const CODE_POSTAL = 13;
	const VILLE = 14;
	const EMAIL = 15;
	const TEL = 16;
	const TEL2 = 17;
	const ORIGINE = 18;
	const DATE_CREATION = 19;
	const DATE_MODIFICATION = 20;
	const OPTIN = 21;
	const SOURCE = 22;
	const IP = 23;

	public function __construct()
	{
		// Create the main parsing object
		$excel = new SimpleExcel('CSV');
		$excel->parser->setDelimiter(';');
		$excel->writer->setDelimiter(';');
		$this->setExcel($excel);

		$this->setCurrentLine(2);
	}

	/**
	 * Get document content as string
	 *
	 * @return  string  Content of document
	 */
	public function parseFIle(){
		$excel = $this->getExcel();
		$excel->parser->loadFile('export_2016-05-02-06-18-46.csv');
		$this->createHeader($excel->writer);
		while ($excel->parser->isRowExists($this->getCurrentLine()))
		{
			$this->nextLine($excel);
		}
		$excel->writer->saveFile('titi.csv', 'tmp/ouput.csv');
	}


	/**
	 * @param CSVWriter $writer
	 */
	private function createHeader(CSVWriter $writer)
	{
		$writer->addRow([
					'ID',
					'ID FAMILLE',
					'Date RDV',
					'Heure RDV',
					'Nom',
					'Prénom',
					'Nom / Prénom',
					'Genre',
					'Date de naissance',
					'Ref. Nom',
					'Ref. Prénom',
					'Adresse',
					'Code Postal',
					'Ville',
					'E-mail',
					'TEL1',
					'TEL2',
					'Origine',
					'Date création',
					'Optin',
					'Source',
					'IP'
		]);
	}

	/**
	 * @param SimpleExcel $excel
	 */
	private function  nextLine(SimpleExcel $excel)
	{
		$currentLine = $this->getCurrentLine();
		$this->setCurrentLine($currentLine + 1);
		if (!empty($excel->parser->getCell($currentLine, self::ETUDIANT1))) {
			$this->createLine($excel, $currentLine, self::ETUDIANT1);
		}
		if (!empty($excel->parser->getCell($currentLine, self::ETUDIANT2))) {
			$this->createLine($excel, $currentLine, self::ETUDIANT2);
		}
		if (!empty($excel->parser->getCell($currentLine, self::ETUDIANT3))) {
			$this->createLine($excel, $currentLine, self::ETUDIANT3);
		}
		if (!empty($excel->parser->getCell($currentLine, self::ETUDIANT4))) {
			$this->createLine($excel, $currentLine, self::ETUDIANT4);
		}
		if (!empty($excel->parser->getCell($currentLine, self::ETUDIANT5))) {
			$this->createLine($excel, $currentLine, self::ETUDIANT5);
		}
	}


	/**
	 * @param SimpleExcel $excel
	 * @param $currentLine
	 */
	private function createLine(SimpleExcel $excel, $currentLine, $etudiantCase) {
		$parser = $excel->parser;

		//Abdelkamal HADDADI | 25/06/1979 (Homme)
		$infos = explode(' - ', $parser->getCell($currentLine, $etudiantCase));
		$firstnameLastname = $infos[0];
		$nomPrenomConf = explode(' ', $infos[0]);
		$nom = $nomPrenomConf[0];
		$prenom = isset($nomPrenomConf[1]) ? $nomPrenomConf[1] : '';
		if (sizeof($infos) === 2) {
			$infos2 = explode(' ', trim($infos[1]));
			$birthDate = $infos2[0];
			$genderString = substr($infos[1], 12);
			$gender = str_replace(['(',')'], '', $genderString);
		} else {
			$birthDate = 'ERREUR';
			$gender = 'ERREUR';
		}
		$lineContent = [
					$parser->getCell($currentLine, self::OLD_ID),
					'FAMILLE' . $currentLine,
					$parser->getCell($currentLine, self::DATE_RDV),
					$parser->getCell($currentLine, self::HEURE_RDV),
					$nom,
					$prenom,
					$firstnameLastname,
					$gender,
					$birthDate,
					$parser->getCell($currentLine, self::NOM),
					$parser->getCell($currentLine, self::PRENOM),
					$parser->getCell($currentLine, self::ADRESSE),
					$parser->getCell($currentLine, self::CODE_POSTAL),
					$parser->getCell($currentLine, self::VILLE),
					$parser->getCell($currentLine, self::EMAIL),
					$parser->getCell($currentLine, self::TEL),
					$parser->getCell($currentLine, self::TEL2),
					$parser->getCell($currentLine, self::ORIGINE),
					$parser->getCell($currentLine, self::DATE_CREATION),
					$parser->getCell($currentLine, self::DATE_MODIFICATION),
					$parser->getCell($currentLine, self::OPTIN),
					$parser->getCell($currentLine, self::SOURCE),
					$parser->getCell($currentLine, self::IP),
			];
			$excel->writer->addRow($lineContent);

	}

	/**
	 * @return SimpleExcel
	 */
	public function getExcel()
	{
		return $this->excel;
	}

	/**
	 * @param SimpleExcel $excel
	 */
	public function setExcel($excel)
	{
		$this->excel = $excel;
	}


	/**
	 * @return int
	 */
	public function getCurrentLine()
	{
		return $this->currentLine;
	}

	/**
	 * @param int $currentLine
	 */
	public function setCurrentLine($currentLine)
	{
		$this->currentLine = $currentLine;
	}

}
