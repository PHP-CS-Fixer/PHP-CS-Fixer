<?php

namespace Symfony\CS\Util;

//custom token constants
define('T_NEW_LINE', -500);                   // \n
define('T_CURLY_BRACE_OPEN', -501);           // {
define('T_CURLY_BRACE_CLOSE', -502);          // }
define('T_PAREN_OPEN', -503);                 // (
define('T_PAREN_CLOSE', -504);                // )
define('T_SEMI_COLON', -505);                 // ;
define('T_COMMA', -506);                      // ,
define('T_EQUALS', -507);                     // =
define('T_AT', -508);                         // @
define('T_EXCLAMATION', -509);                // !
define('T_PERIOD', -510);                     // .
define('T_DOUBLE_QUOTE', -511);               // "
define('T_PLUS', -512);                       // +
define('T_FORWARD_SLASH', -513);              // /
define('T_BRACKET_OPEN', -514);               // [
define('T_BRACKET_CLOSE', -515);              // ]
define('T_ASTERISK', -516);                   // *
define('T_DASH', -517);                       // -
define('T_AMP', -518);                        // &
define('T_LT', -519);                         // <
define('T_GT', -520);                         // >
define('T_QUESTION', -521);                   // ?
define('T_COLON', -522);                      // :
define('T_PERCENT', -523);                    // %
define('T_PIPE', -524);                       // |


/**
 * The built-in tokenizer doesn't treat all tokens equally, which makes
 * processing tokens harder when you need to know about newlines and/or
 * accurate line numbers for braces.  This extends the built-in 'token_get_all'
 * implementation to treat new lines as tokens, as well as give accurate numbers
 * for braces, parentheses and semicolons.
 *
 * Some of this code was taken from the PHP.net comments, kudos
 * to [Dennis Robinson from basnetworks dot net] for sharing his solution.
 * http://us3.php.net/manual/en/function.token-get-all.php#91847
 *
 * The biggest change from the default `token_get_all` implementation is that T_WHITESPACE
 * is separated into multiple tokens if new lines "\n" characters are found.  New line
 * characters are then added as their own token called T_NEW_LINE.
 *
 * @author Evan Villemez <evillemez@gmail.com>
 */
class Tokenizer
{
    /**
     * Return array of tokens.  Each token is an array in the following format:
     * array(
     *    integer,   //int corresponding to token name constant defined by PHP or constants above
     *    string,    //the content of the token
     *    integer,   //the line number where the token occurs in the file
     * );
     *
     * @param  string $content
     * @return array
     */
    public function getTokens($content)
    {
        $tokens = array();
        $currentline = 0;
        foreach (token_get_all($content) as $token) {

            if (is_array($token)) {

                //keep track of line number
                $currentline = $token[2];

                //check for whitespace tokens
                if (T_WHITESPACE === $token[0]) {
                    //make sure any line endings are the proper format in the whitespace
                    $token[1] = str_replace("\r\n", "\n", $token[1]);

                    //if whitespace does NOT contain newline, leave it alone, but keep track of current line
                    if (false === strpos($token[1], "\n")) {
                        $tokens[] = $token;
                    } else {
                        //if it DOES contain newlines, treat them as separate tokens and increment the line number accordingly
                        $exp = explode("\n", $token[1]);
                        $count = count($exp);
                        for ($i = 0; $i < $count; $i++) {
                            //add whitespace token back in (without the newline characters, and only if actually has length)
                            if (strlen($exp[$i]) > 0) {
                                $tokens[] = array(T_WHITESPACE, $exp[$i], $currentline);
                            }

                            //if this isn't the last element, add a newline token to current line, THEN increment line #
                            if ($i < $count - 1) {
                                $tokens[] = array(T_NEW_LINE, "\n", $currentline);
                                $currentline++;
                            }
                        }
                    }
                } else {
                    //if not a whitespace token, leave it alone
                    $tokens[] = $token;
                }
            } else {
                //create custom tokens to keep a consistent format
                switch ($token) {
                    case '{' : $tokens[] = array(T_CURLY_BRACE_OPEN, $token, $currentline); break;
                    case '}' : $tokens[] = array(T_CURLY_BRACE_CLOSE, $token, $currentline); break;
                    case '(' : $tokens[] = array(T_PAREN_OPEN, $token, $currentline); break;
                    case ')' : $tokens[] = array(T_PAREN_CLOSE, $token, $currentline); break;
                    case ';' : $tokens[] = array(T_SEMI_COLON, $token, $currentline); break;
                    case ',' : $tokens[] = array(T_COMMA, $token, $currentline); break;
                    case '=' : $tokens[] = array(T_EQUALS, $token, $currentline); break;
                    case '@' : $tokens[] = array(T_AT, $token, $currentline); break;
                    case '!' : $tokens[] = array(T_EXCLAMATION, $token, $currentline); break;
                    case '.' : $tokens[] = array(T_PERIOD, $token, $currentline); break;
                    case '"' : $tokens[] = array(T_DOUBLE_QUOTE, $token, $currentline); break;
                    case '+' : $tokens[] = array(T_PLUS, $token, $currentline); break;
                    case '/' : $tokens[] = array(T_FORWARD_SLASH, $token, $currentline); break;
                    case '[' : $tokens[] = array(T_BRACKET_OPEN, $token, $currentline); break;
                    case ']' : $tokens[] = array(T_BRACKET_CLOSE, $token, $currentline); break;
                    case '*' : $tokens[] = array(T_ASTERISK, $token, $currentline); break;
                    case '-' : $tokens[] = array(T_DASH, $token, $currentline); break;
                    case '&' : $tokens[] = array(T_AMP, $token, $currentline); break;
                    case '<' : $tokens[] = array(T_LT, $token, $currentline); break;
                    case '>' : $tokens[] = array(T_GT, $token, $currentline); break;
                    case '?' : $tokens[] = array(T_QUESTION, $token, $currentline); break;
                    case ':' : $tokens[] = array(T_COLON, $token, $currentline); break;
                    case '%' : $tokens[] = array(T_PERCENT, $token, $currentline); break;
                    case '|' : $tokens[] = array(T_PIPE, $token, $currentline); break;
//                    default : echo(sprintf("Did not account for token '%s' in %s".PHP_EOL, $token, __METHOD__));
                    default : throw new \RuntimeException(sprintf("Did not account for token '%s' in %s!", $token, __METHOD__));
                }
            }
        }

        return $tokens;
    }
}
