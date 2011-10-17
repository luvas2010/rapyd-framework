<?php if (!defined('RAPYD_PATH')) exit('No direct script access allowed');

/**
 * bbcode helper
 **/

class rpd_bbcode_helper {

	public static $smilies = array(':)' => 'smile.png', 
					 '=)' => 'smile.png',
					 ':|' => 'neutral.png', 
					 '=|' => 'neutral.png', 
					 ':(' => 'sad.png', 
					 '=(' => 'sad.png', 
					 ':D' => 'big_smile.png',
					 '=D' => 'big_smile.png', 
					 ':o' => 'yikes.png',
					 ':O' => 'yikes.png', 
					 ';)' => 'wink.png',
					 ':/' => 'hmm.png', 
					 ':P' => 'tongue.png',
					 ':p' => 'tongue.png',
					 ':lol:' => 'lol.png',
					 ':mad:' => 'mad.png',
					 ':rolleyes:' => 'roll.png',
					 ':cool:' => 'cool.png');
	
	public static function parse($text)
	{
		$bbcode = array(
		"'\[center\](.*?)\[/center\]'is" => "<center>\\1</center>",
		"'\[left\](.*?)\[/left\]'is" => "<div style='text-align: left;'>\\1</div>",
		"'\[right\](.*?)\[/right\]'is" => "<div style='text-align: right;'>\\1</div>",
		"'\[pre\](.*?)\[/pre\]'is" => "<pre>\\1</pre>",
		"'\[b\](.*?)\[/b\]'is" => "<b>\\1</b>",
		"'\[quote\](.*?)\[/quote\]'is" => "<blockquote><b>Quote:</b>\\1</blockquote>",
		"'\[quote=(.*?)\](.*?)\[/quote\]'is" => "<blockquote><b>Quote: \\1</b>\\2</blockquote>",
		"'\[code\](.*?)\[/code\]'is" => "<div class='top'>\\1</div>",
		"'\[i\](.*?)\[/i\]'is" => "<i>\\1</i>",
		"'\[u\](.*?)\[/u\]'is" => "<u>\\1</u>",
		"'\[url\](.*?)\[/url\]'is" => "<a href='\\1'>\\1</a>",
		"'\[url=(.*?)\](.*?)\[/url\]'is" => "<a href=\"\\1\">\\2</a>",
		"'\[anchor name=(.*?)\](.*?)\[/anchor\]'is" => "<a name='\\1'>\\2</a>",
		"'\[email\](.*?)\[/email\]'is" => "<a href='mailto: \\1'>\\1</a>",
		"'\[size=(.*?)\](.*?)\[/size\]'is" => "<span style='font-size: \\1;'>\\2</span>",
		"'\[font=(.*?)\](.*?)\[/font\]'is" => "<span style='font-family: \\1;'>\\2</span>",
		"'\[color=(.*?)\](.*?)\[/color\]'is" => "<font color= \\1;'>\\2</font>",
		//"'\[img\](.*?)\[/img\]'is" => "<img border=\"0\" src=\"\\1\">",
		//"'\[img=(.*?)\]'" => "<img border=\"0\" src=\"\\1\">",
		"'\[img=(.*?) size=(.*?)\]'" => "<img border=\"0\" width=\\2 src=\"\\1\">",
		);
		

		$text = htmlentities($text, ENT_QUOTES , "UTF-8");
		foreach (self::$smilies as $smiley_text => $smiley_img)
		{
			if (strpos($text, $smiley_text) !== false)
				$text = preg_replace("#(?<=[>\s])".preg_quote($smiley_text, '#')."(?=\W)#m", rpd_html_helper::image('smilies/'.$smiley_img), $text);
		}
		$text = preg_replace(array_keys($bbcode), array_values($bbcode), $text);
		return $text;

	}
	

}
