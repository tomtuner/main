<?php

class Inflector {
	public static function pluralize($word) {
		$plural_rules = array(
			'/^(ox)$/'              => '\1\2en',	    # ox
			'/([m|l])ouse$/'          => '\1ice',	    # mouse, louse
			'/(matr|vert|ind)ix|ex$/' =>  '\1ices',     # matrix, vertex, index
			'/(x|ch|ss|sh)$/'         =>  '\1es',       # search, switch, fix, box, process, address
			'/([^aeiouy]|qu)ies$/'    =>  '\1y',
			'/([^aeiouy]|qu)y$/'      =>  '\1ies',      # query, ability, agency
			'/(hive)$/'               =>  '\1s',        # archive, hive
			'/(?:([^f])fe|([lr])f)$/' =>  '\1\2ves',    # half, safe, wife
			'/sis$/'                  =>  'ses',        # basis, diagnosis
			'/([ti])um$/'             =>  '\1a',        # datum, medium
			'/(p)erson$/'             =>  '\1eople',    # person, salesperson
			'/(m)an$/'                =>  '\1en',       # man, woman, spokesman
			'/(c)hild$/'              =>  '\1hildren',  # child
			'/(buffal|tomat)o$/'      =>  '\1\2oes',    # buffalo, tomato
			'/(bu)s$/'                =>  '\1\2ses',    # bus
			'/(alias)/'               =>  '\1es',       # alias
			'/(octop|vir)us$/'        =>  '\1i',        # octopus, virus - virus has no defined plural (according to Latin/dictionary.com), but viri is better than viruses/viruss
			'/(ax|cri|test)is$/'      =>  '\1es',       # axis, crisis 
			'/s$/'                    =>  's',          # no change (compatibility)
			'/$/'                     => 's'
		);

		foreach($plural_rules as $rule => $replacement) 
		{
			if(preg_match($rule, $word)) 
			{
				return preg_replace($rule, $replacement, $word);
			}
		}

		return $word;
	}

	public static function singularize($word) {
		$singular_rules = array(
			'/(matr)ices$/'         =>'\1ix',
			'/(vert|ind)ices$/'     => '\1ex',
			'/^(ox)en/'             => '\1',
			'/(alias)es$/'          => '\1',
			'/([octop|vir])i$/'     => '\1us',
			'/(cris|ax|test)es$/'   => '\1is',
			'/(shoe)s$/'            => '\1',
			'/(o)es$/'              => '\1',
			'/(bus)es$/'            => '\1',
			'/([m|l])ice$/'         => '\1ouse',
			'/(x|ch|ss|sh)es$/'     => '\1',
			'/(m)ovies$/'           => '\1\2ovie',
			'/(s)eries$/'           => '\1\2eries',
			'/([^aeiouy]|qu)ies$/'  => '\1y',
			'/([lr])ves$/'          => '\1f',
			'/(tive)s$/'            => '\1',
			'/(hive)s$/'            => '\1',
			'/([^f])ves$/'          => '\1fe',
			'/(^analy)ses$/'        => '\1sis',
			'/((a)naly|(b)a|(d)iagno|(p)arenthe|(p)rogno|(s)ynop|(t)he)ses$/' => '\1\2sis',
			'/([ti])a$/'            => '\1um',
			'/(p)eople$/'           => '\1\2erson',
			'/(m)en$/'              => '\1an',
			'/(s)tatus$/'           => '\1\2tatus',
			'/(c)hildren$/'         => '\1\2hild',
			'/(n)ews$/'             => '\1\2ews',
			'/s$/'                  => ''
		);

		foreach($singular_rules as $rule => $replacement) 
		{
			if(preg_match($rule, $word)) 
			{
				return preg_replace($rule, $replacement, $word);
			}
		}
		// should not return false is not matched
		return $word;//false;
	}

	public static function camelize($lower_case_and_underscored_word) {
		return str_replace(" ","",ucwords(str_replace("_"," ",$lower_case_and_underscored_word)));
	}    

	public static function underscore($camel_cased_word) {
		$camel_cased_word = preg_replace('/([A-Z]+)([A-Z])/','\1_\2',$camel_cased_word);
		$camel_cased_word = preg_replace('/([a-z])([0-9])/','\1_\2',$camel_cased_word);
		return strtolower(preg_replace('/([a-z])([A-Z])/','\1_\2',$camel_cased_word));
	}

	public static function humanize($lower_case_and_underscored_word) {
		return ucwords(str_replace("_"," ",$lower_case_and_underscored_word));
	}    

	public static function tableize($class_name) {
		return Inflector::pluralize(Inflector::underscore($class_name));
	}

	public static function sequenceize($table_name) {
		if(strpos($table_name, '.') !== false) {
			$table_name = substr(strrchr($table_name, '.'), 1);
		}

		return Inflector::singularize($table_name) .'_id';
	}

	public static function classify($tableName) {
		return Inflector::camelize(Inflector::singularize($tableName));
	}

	public static function setize($column_name) {
		return 'set'. self::camelize($column_name);
	}
}

?>
