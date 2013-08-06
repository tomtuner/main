<?php

class Xml extends Model {
	
	private $file;
	protected $parser;
	protected $data;
	
	private $parentNode;
	private $counter;
	private $isTagOpen;

	public function __construct() {
		$parentNode = null;
		$counter = 0;
		$isTagOpen = false;
		$data = array();
	}
	
	public function setFile($_file) {
		$this->file = $_file;
	}
	
	public function parse() {
		$parser = xml_parser_create();
		xml_set_object($parser,$this);
		xml_set_element_handler($parser, "startElement", "endElement");
		xml_set_character_data_handler($parser, "characterData");

		$fp = fopen($this->file, "r");

		while ($data = fread($fp, 4096)) {
			if (!xml_parse($parser, $data, feof($fp))) {
			$errors[] = array(
							"line"=>xml_get_current_line_number($parser),
							"error"=>xml_error_string(xml_get_error_code($parser))
							);
			}
		}
		
		xml_parser_free($parser);
	}
	
	protected function startElement($parser, $name, $attrs) {
		$this->location = strtoupper($name);
	}
	
	protected function characterData($parser,$info) {
		$info = trim($info);
		
		if (strlen($info) > 0) {			
			// Is a child
			$previous = null;
			if (isset($this->parentNode)) {
				if (isset($this->data[$this->parentNode][$this->counter][$this->location])) {
					$previous = $this->data[$this->parentNode][$this->counter][$this->location];
				}
				
				$this->data[$this->parentNode][$this->counter][$this->location] = (isset($previous))?$previous.$info:$info;
			}
		} else {
			// Has Children
			if (isset($this->location)) {
				$this->parentNode = $this->location;
				$this->counter++;
			}
		}
	}
	
	protected function endElement($parser, $name) {
		$this->location = trim($this->location);
		
		if (!isset($this->location)) $this->parentNode = null;
		
		$this->location = null;
	}
	
	public function getResults() {
		return $this->data;
	}
	
	/* Search feature for events nodes */
	public function getEventKeyLink($title) {	
		foreach ($this->data as $node) {
			foreach ($node as $child) {
				if (isset($child["TITLE"])) {
					if ($child["TITLE"] == $title) return $child["LINK"];
				}
			}
		}
	}
}

?>