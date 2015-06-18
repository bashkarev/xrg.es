<?php

require(dirname(__FILE__) ."/trace.php");
require(dirname(__FILE__) ."/preg.php");

session_start();

$preg = new Preg($_GET);

if ($_SESSION['key']) {
    $redis = new Redis();
    $redis->connect("localhost");
    
    if ($request = $redis->get("xrg.es:". $_SESSION['key'])) {
        $preg->restore_cache($request);
        trace("Cache RESTORED xrg.es:". $_SESSION['key']);
        $restored = true;
    }
    unset($_SESSION['key']);
}

?><!doctype html>
<html class="no-js" lang="en">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>Live RegExp - PHP Regular Expression online tester - xrg.es</title>
        <link rel="stylesheet" href="/css/foundation.css" />
        <link rel="stylesheet" href="/css/custom.css" />
        <script src="/js/modernizr.js"></script>
        <script type="text/javascript">var restored = <?=(int)$restored?>;</script>
    </head>

    <body>
        <nav class="top-bar" data-topbar>
            <ul class="title-area">
                <li class="name">
                    <h1><a href="http://<?=$_SERVER['HTTP_HOST']?>">PHP Regular Expressions tester</a></h1>
                </li>
            </ul>

            <section class="top-bar-section">
                <ul class="right">
                    <li><a href="/">Reset</a></li>
                    <li class="has-form"><a href="#" class="button" data-reveal-id="help">Help &darr;</a></li>
                </ul>
            </section>
        </nav>

        <form method="POST" action="/update.php" id="form">

            <div class="row">
                <div class="medium-12 column">
                    <dl class="tabs" data-tab>
                        <dd class="<?=$preg->active("preg_match")?>"     data-visible="pattern subject"><a href="#preg_match">preg_match</a></dd>
                        <dd class="<?=$preg->active("preg_match_all")?>" data-visible="pattern subject"><a href="#preg_match_all">preg_match_all</a></dd>
                        <dd class="<?=$preg->active("preg_split")?>"     data-visible="pattern subject"><a href="#preg_split">preg_split</a></dd>
                        <dd class="<?=$preg->active("preg_replace")?>"   data-visible="pattern subject replacement"><a href="#preg_replace">preg_replace</a></dd>
                        <dd class="<?=$preg->active("preg_filter")?>"    data-visible="pattern subject replacement"><a href="#preg_filter">preg_filter</a></dd>
                        <dd class="<?=$preg->active("preg_quote")?>"     data-visible="pattern"><a href="#preg_quote">preg_quote</a></dd>
                    </dl>
                </div>
            </div>

            <div class="row">
                <div class="medium-12 column">
                    <div id="pattern">
                        <h4>Regular Expression / Pattern</h4>
                        <input class="update" type="text" name="o[pattern]" tabindex="1" value="<?=h($preg->o["pattern"])?>" id="input_pattern" />
                    </div>
                    <div id="replacement" <?php if (!$restored) echo 'style="display: none;"'; ?>>
                        <h4>Replacement</h4>
                        <input class="update" type="text" name="o[replacement]" tabindex="3" value="<?=h($preg->o["replacement"])?>" />
                    </div>
                    <div id="subject">
                        <h4>String / Subject</h4>
                        <textarea class="update" name="o[subject]" tabindex="2"><?=h($preg->o["subject"])?></textarea>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="medium-12 column">
                    <h4>Options</h4>
                </div>
            </div>

            <div class="row tabs-content">
                <div class="medium-12 content column <?=$preg->active("preg_match")?>" id="preg_match">
                    <!--<p class="phpnethelp"><a href="http://php.net/preg_match"><span>php.net help</span></a></p>-->
                    <div class="row collapse">
                        <div class="medium-3 column">
                            <?=Preg::html_checkbox('m', 'p_m1', $preg->m)?>
                            <label class="inline" for="p_m1">PREG_OFFSET_CAPTURE</label>
                        </div>
                        <div class="medium-9 column">
                            <div class="medium-1 columns">
                                <span class="prefix">offset</span>
                            </div>
                            <div class="medium-2 columns">
                                <?=Preg::html_text('m', 'p_mo', $preg->m, 'offset')?>
                            </div>
                            <div class="medium-9 columns"></div>
                        </div>
                    </div>
                </div>

                <div class="medium-12 content column <?=$preg->active("preg_match_all")?>" id="preg_match_all">
                    <!--<span class="phpnethelp"><a href="http://php.net/preg_match_all"><span>php.net help</span></a></span>-->
                    <div class="row">
                        <div class="medium-12 column">
                            <?=Preg::html_checkbox('a', 'p_a1', $preg->a)?>
                            <label class="inline" for="p_a1">PREG_PATTERN_ORDER</label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="medium-12 column">
                            <?=Preg::html_checkbox('a', 'p_a2', $preg->a)?>
                            <label class="inline" for="p_a2">PREG_SET_ORDER</label>
                        </div>
                    </div>
                    <div class="row collapse">
                        <div class="medium-3 column">
                            <?=Preg::html_checkbox('a', 'p_a3', $preg->a)?>
                            <label class="inline" for="p_a3">PREG_OFFSET_CAPTURE</label>
                        </div>
                        <div class="medium-9 column">
                            <div class="medium-1 columns">
                                <span class="prefix">offset</span>
                            </div>
                            <div class="medium-2 columns">
                                <?=Preg::html_text('a', 'p_ao', $preg->a, 'offset')?>
                            </div>
                            <div class="medium-9 columns"></div>
                        </div>
                    </div>
                </div>

                <div class="medium-12 column content <?=$preg->active("preg_split")?>" id="preg_split">
                    <!--<span class="phpnethelp"><a href="http://php.net/preg_split"><span>php.net help</span></a></span>-->
                    <div class="row">
                        <div class="medium-12 column">
                            <?=Preg::html_checkbox('s', 'p_s1', $preg->s)?>
                            <label class="inline" for="p_s1">PREG_SPLIT_NO_EMPTY</label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="medium-12 column">
                            <?=Preg::html_checkbox('s', 'p_s2', $preg->s)?>
                            <label class="inline" for="p_s2">PREG_SPLIT_DELIM_CAPTURE</label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="medium-12 column">
                            <?=Preg::html_checkbox('s', 'p_s3', $preg->s)?>
                            <label class="inline" for="p_s3">PREG_SPLIT_OFFSET_CAPTURE</label></li>
                        </div>
                    </div>
                    <div class="row collapse">
                        <div class="medium-1 columns">
                            <span class="prefix">Limit</span>
                        </div>
                        <div class="medium-2 columns">
                            <?=Preg::html_text('s', 'p_sl', $preg->s, 'limit')?>
                        </div>
                        <div class="medium-9 columns"></div>
                    </div>
                </div>

                <div class="medium-12 column content <?=$preg->active("preg_replace")?>" id="preg_replace">
                    <!--<span class="phpnethelp"><a href="http://php.net/preg_replace"><span>php.net help</span></a></span>-->
                    <div class="row collapse">
                        <div class="medium-1 columns">
                            <span class="prefix">Limit</span>
                        </div>
                        <div class="medium-2 columns">
                            <?=Preg::html_text('r', 'p_rl', $preg->r, 'limit')?>
                        </div>
                        <div class="medium-9 columns"></div>
                    </div>
                </div>

                <div class="medium-12 column content <?=$preg->active("preg_filter")?>" id="preg_filter">
                    <!--<span class="phpnethelp"><a href="http://php.net/preg_filter"><span>php.net help</span></a></span>-->
                    <div class="row collapse">
                        <div class="medium-1 columns">
                            <span class="prefix">Limit</span>
                        </div>
                        <div class="medium-2 columns">
                            <?=Preg::html_text('f', 'p_fl', $preg->f, 'limit')?>
                        </div>
                        <div class="medium-9 columns"></div>
                    </div>
                </div>

                <div class="medium-12 column content <?=$preg->active("preg_quote")?>" id="preg_quote">
                    <!--<span class="phpnethelp"><a href="http://php.net/preg_quote"><span>php.net help</span></a></span>-->
                    <div class="row collapse">
                        <div class="medium-1 columns">
                            <span class="prefix">Delimiter</span>
                        </div>
                        <div class="medium-2 columns">
                            <?=Preg::html_text('q', 'p_qd', $preg->q, 'delimiter')?>
                        </div>
                        <div class="medium-9 columns"></div>
                    </div>
                </div>
            </div>
            <input type="hidden" name="o[preg]" value="<?=($preg->o['preg']? $preg->o['preg']: "preg_match")?>" id="preg_selection" />
        </form>

        <div class="row">
            <div class="medium-12 column">
                <h3 id="return">Results</h3>
            </div>
        </div>

        <div class="results">
            <div class="row">
                <div class="medium-12 column">
                    <tt id="code"></tt>
                    <div id="dump">no results until you use the form</div>
                </div>
            </div>
        </div>

        <div class="footer">
            <div class="row">
                <div class="medium-12 column text-right">
                    <p>RegExp / Regular Expressions / Expresiones Regulares / Evaluator / Tester 
                        -- Written by <a href="mailto:xergio@gmail.com">Sergio Álvarez</a> (<a href="https://twitter.com/xergio">@xergio</a>) 
                        and <a href="mailto:godsea@gmail.com">Jordi Rivero</a>. 
                        Using <a href="http://dbug.ospinto.com/">dBug</a>. 
                        Permalinks stored for 1 year. 
			<code><a href="https://github.com/xergio/xrg.es">Source code <img src="https://github.com/favicon.ico" width="12" height="12" /></a></code></p>
                </div>
            </div>
        </div>


        <div id="help" class="reveal-modal expand" data-reveal>
            <div class="row">
                <div class="medium-3 column">
                    <h4>Pattern Modifiers</h4>
                    <ul>
                        <li><code>i</code> PCRE_CASELESS</li>
                        <li><code>m</code> PCRE_MULTILINE</li>
                        <li><code>s</code> PCRE_DOTALL</li>
                        <li><code>x</code> PCRE_EXTENDED</li>
                        <li><code>e</code> (Disabled for this tool)</li>
                        <li><code>A</code> PCRE_ANCHORED</li>
                        <li><code>D</code> PCRE_DOLLAR_ENDONLY</li>
                        <li><code>S</code> Extra analysis</li>
                        <li><code>U</code> PCRE_UNGREEDY</li>
                        <li><code>X</code> PCRE_EXTRA</li>
                        <li><code>J</code> PCRE_INFO_JCHANGED</li>
                        <li><code>u</code> PCRE_UTF8</li>
                    </ul>
                </div>

                <div class="medium-5 column">
                    <h4>Meta-characters outside <code>[ ]</code></h4>
                    <ul>
                        <li><code>\</code> general escape character</li>
                        <li><code>^</code> assert start of subject (or line, in multiline mode)</li>
                        <li><code>$</code> assert end of subject (or line, in multiline mode)</li>
                        <li><code>.</code> match any character except newline (by default)</li>
                        <li><code>[ ]</code> character class definition</li>
                        <li><code>|</code> start of alternative branch</li>
                        <li><code>( )</code> subpattern</li>
                        <li><code>?</code> extends the meaning of '(', also 0 or 1 quantifier</li>
                        <li><code>*</code> 0 or more quantifier</li>
                        <li><code>+</code> 1 or more quantifier</li>
                        <li><code>{ }</code> min/max quantifier, {n[,n]}</li>
                    </ul>

                    <h4>Meta-characters inside <code>[ ]</code></h4>
                    <ul>
                        <li><code>\</code> general escape character</li>
                        <li><code>^</code> negate the class, but only if the first character</li>
                        <li><code>-</code> indicates character range</li>
                    </ul>

                    <h4>Others</h4>
                    <ul>
                        <li><code>\1-9</code> in-group back references</li>
                        <li><code>(?P&lt;lbl&gt;...)</code> labelize subpatterns</li>
                        <li><code>(?:...)</code> non-capture group</li>
                        <li><code>(?&gt;...)</code> Atomic group</li>
                        <li><code>(?=...)</code> Positive lookahead</li>
                        <li><code>(?!...)</code> Negative lookahead</li>
                        <li><code>(?&lt;=..)</code> Positive lookbehind</li>
                        <li><code>(?&lt;!..)</code> Negative lookbehind</li>
                        <li><code>(?(?=.).|.)</code> if . then . else .</li>
                        <li><code>(?#...)</code> Comment</li>
                    </ul>
                </div>

                <div class="medium-4 column">
                    <h4>Scape sequences</h4>
                    <ul>
                        <li><code>\a</code> alarm, that is, the BEL character (hex 07)</li>
                        <li><code>\cx</code> "control-x", where x is any character</li>
                        <li><code>\e</code> escape (hex 1B)</li>
                        <li><code>\f</code> formfeed (hex 0C)</li>
                        <li><code>\n</code> newline (hex 0A)</li>
                        <li><code>\r</code> carriage return (hex 0D)</li>
                        <li><code>\t</code> tab (hex 09)</li>
                        <li><code>\p{xx}</code> a character with the xx <a href="http://www.php.net/manual/en/regexp.reference.unicode.php">property</a></li>
                        <li><code>\P{xx}</code> a character without the xx <a href="http://www.php.net/manual/en/regexp.reference.unicode.php">property</a></li>
                        <li><code>\xhh</code> character with hex code hh</li>
                        <li><code>\ddd</code> character with octal code ddd, or backreference</li>
                        <li><code>\d</code> any decimal digit</li>
                        <li><code>\D</code> any character that is not a decimal digit</li>
                        <li><code>\s</code> any whitespace character</li>
                        <li><code>\S</code> any character that is not a whitespace character</li>
                        <li><code>\h</code> any horizontal whitespace character</li>
                        <li><code>\H</code> any character that is not a horizontal whitespace</li>
                        <li><code>\v</code> any vertical whitespace character</li>
                        <li><code>\V</code> any character that is not a vertical whitespace character</li>
                        <li><code>\w</code> any "word" character</li>
                        <li><code>\W</code> any "non-word" character</li>
                        <li><code>\b</code> word boundary</li>
                        <li><code>\B</code> not a word boundary</li>
                        <li><code>\A</code> start of subject (independent of multiline mode)</li>
                        <li><code>\Z</code> end of subject or newline at end (independent of multiline mode)</li>
                        <li><code>\z</code> end of subject (independent of multiline mode)</li>
                        <li><code>\G</code> first matching position in subject</li>
                    </ul>
                </div>
            </div>
            <a class="close-reveal-modal">&#215;</a>
        </div>

        <script src="/js/vendor/jquery.js"></script>
        <script src="/js/foundation/foundation.js"></script>
        <script src="/js/foundation/foundation.tab.js"></script>
        <script src="/js/foundation/foundation.reveal.js"></script>
        <script src="/js/custom.js"></script>

<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-70198-2', 'xrg.es');
  ga('send', 'pageview');

</script>
    </body>
</html>
