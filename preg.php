<?php

/*

int 	preg_match 		( string $pattern , string $subject [, 	array &$matches [, int $flags = 0 [, int $offset = 0 ]]] )
int 	preg_match_all 	( string $pattern , string $subject [, 	array &$matches [, int $flags = PREG_PATTERN_ORDER [, int $offset = 0 ]]] )
array 	preg_split 		( string $pattern , string $subject [, 	int $limit = -1 [, int $flags = 0 ]] )
mixed 	preg_replace 	( mixed $pattern , 	mixed $replacement , mixed $subject [, int $limit = -1 [, int &$count ]] )
array 	preg_grep 		( string $pattern , array $input [, 	int $flags = 0 ] )
mixed 	preg_filter 	( mixed $pattern , 	mixed $replacement , mixed $subject [, int $limit = -1 [, int &$count ]] )
string 	preg_quote 		( string $str [, 	string $delimiter = NULL ] )

*/

class Preg {
	
	private $fulls = array(
		'm' => 'preg_match',
		'a' => 'preg_match_all',
		's' => 'preg_split',
		'r' => 'preg_replace',
		'f' => 'preg_filter',
		'q' => 'preg_quote'
	);
	
	private $abbrs = array(
		'preg_match' => 'm',
		'preg_match_all' => 'a',
		'preg_split' => 's',
		'preg_replace' => 'r',
		'preg_filter' => 'f',
		'preg_quote' => 'q'
	);
	
	private $constants = array(
		'p_m1' => PREG_OFFSET_CAPTURE,
		'p_a1' => PREG_PATTERN_ORDER,
		'p_a2' => PREG_SET_ORDER,
		'p_a3' => PREG_OFFSET_CAPTURE,
		'p_s1' => PREG_SPLIT_NO_EMPTY,
		'p_s2' => PREG_SPLIT_DELIM_CAPTURE,
		'p_s3' => PREG_SPLIT_OFFSET_CAPTURE,
		'p_g1' => PREG_GREP_INVERT
	);
	
	private $constants_reverse = array(
		'p_m1' => "PREG_OFFSET_CAPTURE",
		'p_a1' => "PREG_PATTERN_ORDER",
		'p_a2' => "PREG_SET_ORDER",
		'p_a3' => "PREG_OFFSET_CAPTURE",
		'p_s1' => "PREG_SPLIT_NO_EMPTY",
		'p_s2' => "PREG_SPLIT_DELIM_CAPTURE",
		'p_s3' => "PREG_SPLIT_OFFSET_CAPTURE",
		'p_g1' => "PREG_GREP_INVERT"
	);
	
	private $assocs = array(
		'preg_match' => array('p_m1'),
		'preg_match_all' => array('p_a1', 'p_a2', 'p_a3'),
		'preg_split' => array('p_s1', 'p_s2', 'p_s3'),
		'preg_replace' => array(),
		'preg_filter' => array(),
		'preg_quote' => array()
	);
	
	private $uses = array(
		'preg_match' => 'preg_match("%s", "%s", $matches, %s, %s);',
		'preg_match_all' => 'preg_match_all("%s", "%s", $matches, %s, %s);',
		'preg_split' => 'preg_split("%s", "%s", %s, %s);',
		'preg_replace' => 'preg_replace("%s", "%s", "%s", %s);',
		'preg_filter' => 'preg_filter("%s", "%s", "%s", %s);',
		'preg_quote' => 'preg_quote("%s", "%s");'
	);
	
	private $snippets = array(
		'preg_match' 		=> 'int 	preg_match 		( string $pattern , string $subject [, 	array &$matches [, int $flags = 0 [, int $offset = 0 ]]] )',
		'preg_match_all' 	=> 'int 	preg_match_all 	( string $pattern , string $subject [, 	array &$matches [, int $flags = PREG_PATTERN_ORDER [, int $offset = 0 ]]] )',
		'preg_split' 		=> 'array 	preg_split 		( string $pattern , string $subject [, 	int $limit = -1 [, int $flags = 0 ]] )',
		'preg_replace' 		=> 'mixed 	preg_replace 	( mixed $pattern , 	mixed $replacement , mixed $subject [, int $limit = -1 [, int &$count ]] )',
		'preg_filter' 		=> 'mixed 	preg_filter 	( mixed $pattern , 	mixed $replacement , mixed $subject [, int $limit = -1 [, int &$count ]] )',
		'preg_quote' 		=> 'mixed 	preg_quote 		( string $str 	[, 	string $delimiter = NULL ] )'
	);
	
	private $errors = array(
		PREG_NO_ERROR => "",
		PREG_INTERNAL_ERROR => "there was an internal PCRE error",
		PREG_BACKTRACK_LIMIT_ERROR => "backtrack limit was exhausted",
		PREG_RECURSION_LIMIT_ERROR => "recursion limit was exhausted",
		PREG_BAD_UTF8_ERROR => "malformed UTF-8 data",
		PREG_BAD_UTF8_OFFSET_ERROR => "the offset didn't correspond to the begin of a valid UTF-8 code point"
	);

	public static $placeholders = array(
		"offset" => "0",
		"limit" => "-1",
		"delimiter" => ""
	);

	public static $input_type = array(
		"offset" => "number",
		"limit" => "number",
		"delimiter" => "text"
	);
	
	private $flags = 0;
	private $offset = 0;
	private $limit = -1;
	private $delimiter = null;
	
	public $preg = "";
	public $status = 'ok';
	public $return = false;
	public $reason = '';
	public $snippet = "";
	public $use = "";
	public $content = "";
	
	
	public function __construct($request=array()) {
		$this->o = $request['o'];
		$this->m = $request['m'];
		$this->a = $request['a'];
		$this->s = $request['s'];
		$this->r = $request['r'];
		$this->f = $request['f'];
		$this->q = $request['q'];
	}
	
	
	public function error($reason) {
		$this->return = false;
		$this->status = 'error';
		$this->reason = $reason;
		$this->content = "";
		throw new Exception($this->reason);
	}
	
	
	public function last_error() {
		if ($e = $this->errors[preg_last_error()]) return $e;
		
		$e = error_get_last();
		if (preg_match("#^[^:]+: (.*)#", $e['message'], $matchs))
			return $matchs[1];
		
		return "Unknown error"; 
	}
	
	
	public function validate() {
		$regexp = preg_replace("/([a-zA-Z0-9]+)$/e", "str_replace('e', '', '$1')", trim($this->o['pattern']));
		if ($regexp != trim($this->o['pattern']))
			$this->error("'e' modifier is not allowed here");
		
		if (!in_array($this->o['preg'], $this->fulls))
			$this->error("Invalid params");
			
		if (strlen($this->o['pattern']) === 0)
			$this->error("empty regular expression!");
			
		if (strlen($this->o['subject']) === 0 and $this->o['preg'] != "preg_quote")
			$this->error("empty text!");
	}
	
	
	public function prepare() {
		$this->o['pattern'] = $pattern = preg_replace("/([a-zA-Z0-9]+)$/e", "str_replace('e', '', '$1')", trim($this->o['pattern']));
		$this->preg = $this->o['preg'];
		$this->abbr = $this->abbrs[$this->preg];
		$this->assoc = $this->assocs[$this->preg];
		$this->group = $this->{$this->abbr};
	}
	
	
	public function flags() {
		foreach ($this->assoc as $id)
			if (in_array($id, $this->group))
				$this->flags |= $this->constants[$id];
		return $this->flags;
	}
	
	
	public function flags_reverse() {
		$r = array();
		
		foreach ($this->assoc as $id)
			if (in_array($id, $this->group))
				$r[] = $this->constants_reverse[$id];
				
		return (count($r)? implode(" | ", $r): "null");
	}
	
	
	public function offset() {
		if ($this->group['offset'])
			$this->offset = (int)$this->group['offset'];
	}
	
	
	public function limit() {
		if ($this->group['limit'])
			$this->limit = (int)$this->group['limit'];
	}
	
	
	public function delimiter() {
		if ($this->group['delimiter'])
			$this->delimiter = $this->group['delimiter'];
	}
	
	
	public function exec() {
		$this->snippet = $this->snippets[$this->preg];
		//$this->use = str_replace(array('?&gt;', '&lt;?php&nbsp;'), '', highlight_string('<?php '. $this->uses[$this->preg] .' ?'.'>', true) );
		
		$function = $this->preg;
		$this->$function();
	}
	
	
	private function syntax($sprintf) {
		$this->use = str_replace(array('?&gt;', '&lt;?php&nbsp;', '&nbsp;'), array('', '', ' '), highlight_string('<?php '. $sprintf .' ?'.'>', true) );
	}
	
	
	private function preg_match() {
		$this->syntax(sprintf($this->uses[$this->preg], $this->o['pattern'], Preg::crop_text($this->o['subject']), $this->flags_reverse(), $this->offset));
		
		$return = preg_match($this->o['pattern'], $this->o['subject'], $matchs, $this->flags, $this->offset);
		
		if ($return === false) return $this->error($this->last_error());
		$this->return = $return;
		$this->content = $matchs?: "no matches";
	}
	
	
	private function preg_match_all() {
		$this->syntax(sprintf($this->uses[$this->preg], $this->o['pattern'], Preg::crop_text($this->o['subject']), $this->flags_reverse(), $this->offset));
		
		$return = preg_match_all($this->o['pattern'], $this->o['subject'], $matchs, $this->flags, $this->offset);
		
		if ($return === false) return $this->error($this->last_error());
		$this->return = $return;
		$this->content = $matchs?: "no matches";
	}
	
	
	private function preg_split() {
		$this->syntax(sprintf($this->uses[$this->preg], $this->o['pattern'], Preg::crop_text($this->o['subject']), $this->limit, $this->flags_reverse()));
		
		$return = preg_split($this->o['pattern'], $this->o['subject'], $this->limit, $this->flags);
		
		$this->return = true;
		$this->content = $return;
	}
	
	
	private function preg_replace() {
		$this->syntax(sprintf($this->uses[$this->preg], $this->o['pattern'], $this->o['replacement'], Preg::crop_text($this->o['subject']), $this->limit));
		
		$return = preg_replace($this->o['pattern'], $this->o['replacement'], $this->o['subject'], $this->limit);
		
		if ($return === null) return $this->error($this->last_error());
		$this->return = true;
		$this->content = $return;
	}
	
	
	private function preg_filter() {
		$this->syntax(sprintf($this->uses[$this->preg], $this->o['pattern'], $this->o['replacement'], Preg::crop_text($this->o['subject']), $this->limit));
		
		$return = preg_filter($this->o['pattern'], $this->o['replacement'], $this->o['subject'], $this->limit);
		
		if ($return === null) return $this->error($this->last_error());
		$this->return = true;
		$this->content = $return;
	}
	
	
	private function preg_quote() {
		$this->syntax(sprintf($this->uses[$this->preg], $this->o['pattern'], $this->delimiter));
		
		$return = preg_quote($this->o['pattern'], $this->delimiter);
		
		if ($return === null) return $this->error($this->last_error());
		$this->return = true;
		$this->content = $return;
	}
	
	
	public static function html_checkbox($type, $id, $options) {
		return '<input class="update" type="checkbox" name="'. $type .'[]" id="'. $id .'" value="'. h($id) .'"'. (in_array($id, (array)$options)? ' checked="checked"': '') .' />';
	}
	
	
	public static function html_text($type, $id, $options, $field='offset') {
		return '<input class="update" type="'. Preg::$input_type[$field] .'" name="'. $type .'['. $field .']" id="'. $id .'" value="'. h($options[$field]) .'" placeholder="'. Preg::$placeholders[$field] .'" />';
	}
	
	
	public function active($function) {
		return ($this->o['preg'] == $function or (!$this->o and $function == 'preg_match'))? 'active': '';
	}
	
	
	public function key_cache() {
		return base_convert(crc32(sha1(
			serialize($this->o).
			serialize($this->m).
			serialize($this->a).
			serialize($this->s).
			serialize($this->r).
			serialize($this->f).
			serialize($this->q)
		)), 10, 36);
	}
	
	
	public function value_cache() {
		return serialize(array(
			/*'preg' => $this->preg,
			'status' => $this->status,
			'reason' => $this->reason,
			'return' => $this->return,
			'snippet' => $this->snippet,
			'use' => $this->use,
			'content' => $this->content,*/
			'o' => $this->o,
			'm' => $this->m,
			'a' => $this->a,
			's' => $this->s,
			'r' => $this->r,
			'f' => $this->f,
			'q' => $this->q
		));
	}
	
	
	public function restore_cache($serialized) {
		$data = unserialize($serialized);
		/*
		$this->preg = $data['preg'];
		$this->status = $data['status'];
		$this->return = $data['return'];
		$this->reason = $data['reason'];
		$this->snippet = $data['snippet'];
		$this->use = $data['use'];
		$this->snippet = $data['snippet'];
		$this->content = $data['content'];
		*/
		$this->o = $data['o'];
		$this->m = $data['m'];
		$this->a = $data['a'];
		$this->s = $data['s'];
		$this->r = $data['r'];
		$this->f = $data['f'];
		$this->q = $data['q'];
	}
	
	
	static public function crop_text($str, $len=50) {
        if (mb_strlen($str, "utf-8") < $len) return $str;
		return str_replace(array("\r", "\n"), array('\r', '\n'), addslashes(mb_substr($str, 0, $len, "utf-8")). "...");
	}
}
