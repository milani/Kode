<?php

/**
 * Show some easter eggs in the BO
 *
 * @category   App
 * @package    App_View
 * @subpackage Helper
 * @copyright  Copyright (c) 2011, Morteza Milani
 */
/**
 * Translation view helper (just a shortcut for translate)
 */
class App_View_Helper_EasterEgg extends Zend_View_Helper_Abstract {

    private $_emoticons = array(
        
    array(
        'emoticon' => '(.V.)', 'label' => 'Alien', 
    'url' => 'http://www.sharpened.net/glossary/emoticon.php?alien'
    ), 
    array(
        'emoticon' => 'O:-)', 'label' => 'Angel', 
    'url' => 'http://www.sharpened.net/glossary/emoticon.php?angel'
    ), 
    array(
        'emoticon' => 'X-(', 'label' => 'Angry', 
    'url' => 'http://www.sharpened.net/glossary/emoticon.php?angry'
    ), 
    array(
        'emoticon' => '~:0', 'label' => 'Baby', 
    'url' => 'http://www.sharpened.net/glossary/emoticon.php?baby'
    ), 
    array(
        'emoticon' => '(*v*)', 'label' => 'Bird', 
    'url' => 'http://www.sharpened.net/glossary/emoticon.php?bird'
    ), 
    array(
        'emoticon' => ':-#', 'label' => 'Braces', 
    'url' => 'http://www.sharpened.net/glossary/emoticon.php?braces'
    ), 
    array(
        'emoticon' => '</3', 'label' => 'Broken Heart', 
    'url' => 'http://www.sharpened.net/glossary/emoticon.php?broken_heart'
    ), 
    array(
        'emoticon' => '=^.^=', 'label' => 'Cat', 
    'url' => 'http://www.sharpened.net/glossary/emoticon.php?cat'
    ), 
    array(
        'emoticon' => '*<:o)', 'label' => 'Clown', 
    'url' => 'http://www.sharpened.net/glossary/emoticon.php?clown'
    ), 
    array(
        'emoticon' => 'O.o', 'label' => 'Confused', 
    'url' => 'http://www.sharpened.net/glossary/emoticon.php?confused'
    ), 
    array(
        'emoticon' => ':-S', 'label' => 'Confused', 
    'url' => 'http://www.sharpened.net/glossary/emoticon.php?confused'
    ), 
    array(
        'emoticon' => 'B-)', 'label' => 'Cool', 
    'url' => 'http://www.sharpened.net/glossary/emoticon.php?cool'
    ), 
    array(
        'emoticon' => ':_(', 'label' => 'Crying', 
    'url' => 'http://www.sharpened.net/glossary/emoticon.php?crying'
    ), 
    array(
        'emoticon' => ':\'(', 'label' => 'Crying', 
    'url' => 'http://www.sharpened.net/glossary/emoticon.php?crying'
    ), 
    array(
        'emoticon' => '|_P', 'label' => 'Cup of Coffee', 
    'url' => 'http://www.sharpened.net/glossary/emoticon.php?cup_of_coffee'
    ), 
    array(
        'emoticon' => '*-*', 'label' => 'Dazed', 
    'url' => 'http://www.sharpened.net/glossary/emoticon.php?dazed'
    ), 
    array(
        'emoticon' => ':o3', 'label' => 'Dog', 
    'url' => 'http://www.sharpened.net/glossary/emoticon.php?dog'
    ), 
    array(
        'emoticon' => '#-o', 'label' => 'Doh!', 
    'url' => 'http://www.sharpened.net/glossary/emoticon.php?doh'
    ), 
    array(
        'emoticon' => ':*)', 'label' => 'Drunk', 
    'url' => 'http://www.sharpened.net/glossary/emoticon.php?drunk'
    ), 
    array(
        'emoticon' => '//_^', 'label' => 'Emo', 
    'url' => 'http://www.sharpened.net/glossary/emoticon.php?emo'
    ), 
    array(
        'emoticon' => '-@--@-', 'label' => 'Eyeglasses', 
    'url' => 'http://www.sharpened.net/glossary/emoticon.php?eyeglasses'
    ), 
    array(
        'emoticon' => '<><', 'label' => 'Fish', 
    'url' => 'http://www.sharpened.net/glossary/emoticon.php?fish'
    ), 
    array(
        'emoticon' => '()', 'label' => 'Football', 
    'url' => 'http://www.sharpened.net/glossary/emoticon.php?football'
    ), 
    array(
        'emoticon' => ':-(', 'label' => 'Frown', 
    'url' => 'http://www.sharpened.net/glossary/emoticon.php?frown'
    ), 
    array(
        'emoticon' => ':(', 'label' => 'Frown', 
    'url' => 'http://www.sharpened.net/glossary/emoticon.php?frown'
    ), 
    array(
        'emoticon' => ':-(', 'label' => 'Frowning', 
    'url' => 'http://www.sharpened.net/glossary/emoticon.php?frowning'
    ), 
    array(
        'emoticon' => '=P', 'label' => 'Frustrated', 
    'url' => 'http://www.sharpened.net/glossary/emoticon.php?frustrated'
    ), 
    array(
        'emoticon' => ':-P', 'label' => 'Frustrated', 
    'url' => 'http://www.sharpened.net/glossary/emoticon.php?frustrated'
    ), 
    array(
        'emoticon' => '8-)', 'label' => 'Glasses', 
    'url' => 'http://www.sharpened.net/glossary/emoticon.php?glasses'
    ), 
    array(
        'emoticon' => '$_$', 'label' => 'Greedy', 
    'url' => 'http://www.sharpened.net/glossary/emoticon.php?greedy'
    ), 
    array(
        'emoticon' => ':->', 'label' => 'Grin', 
    'url' => 'http://www.sharpened.net/glossary/emoticon.php?grin'
    ), 
    array(
        'emoticon' => '=)', 'label' => 'Happy', 
    'url' => 'http://www.sharpened.net/glossary/emoticon.php?happy'
    ), 
    array(
        'emoticon' => ':-)', 'label' => 'Happy', 
    'url' => 'http://www.sharpened.net/glossary/emoticon.php?happy'
    ), 
    array(
        'emoticon' => '<3', 'label' => 'Heart', 
    'url' => 'http://www.sharpened.net/glossary/emoticon.php?heart'
    ), 
    array(
        'emoticon' => '{ }', 'label' => 'Hug', 
    'url' => 'http://www.sharpened.net/glossary/emoticon.php?hug'
    ), 
    array(
        'emoticon' => ':-|', 'label' => 'Indifferent', 
    'url' => 'http://www.sharpened.net/glossary/emoticon.php?indifferent'
    ), 
    array(
        'emoticon' => 'X-p', 'label' => 'Joking', 
    'url' => 'http://www.sharpened.net/glossary/emoticon.php?joking'
    ), 
    array(
        'emoticon' => '\VVV/', 'label' => 'King', 
    'url' => 'http://www.sharpened.net/glossary/emoticon.php?king'
    ), 
    array(
        'emoticon' => ':-)*', 'label' => 'Kiss', 
    'url' => 'http://www.sharpened.net/glossary/emoticon.php?kiss'
    ), 
    array(
        'emoticon' => ':-*', 'label' => 'Kiss', 
    'url' => 'http://www.sharpened.net/glossary/emoticon.php?kiss'
    ), 
    array(
        'emoticon' => '(-}{-)', 'label' => 'Kissing', 
    'url' => 'http://www.sharpened.net/glossary/emoticon.php?kissing'
    ), 
    array(
        'emoticon' => '=D', 'label' => 'Laughing Out Loud', 
    'url' => 'http://www.sharpened.net/glossary/emoticon.php?laughing_out_loud'
    ), 
    array(
        'emoticon' => ')-:', 'label' => 'Left-handed Sad Face', 
    'url' => 'http://www.sharpened.net/glossary/emoticon.php?left-handed_sad_face'
    ), 
    array(
        'emoticon' => '(-:', 'label' => 'Left-handed Smiley Face', 
    'url' => 'http://www.sharpened.net/glossary/emoticon.php?left-handed_smiley_face'
    ), 
    array(
        'emoticon' => '<3', 'label' => 'Love', 
    'url' => 'http://www.sharpened.net/glossary/emoticon.php?love'
    ), 
    array(
        'emoticon' => '=/', 'label' => 'Mad', 
    'url' => 'http://www.sharpened.net/glossary/emoticon.php?mad'
    ), 
    array(
        'emoticon' => ':-)(-:', 'label' => 'Married', 
    'url' => 'http://www.sharpened.net/glossary/emoticon.php?married'
    ), 
    array(
        'emoticon' => '<:3 )~', 'label' => 'Mouse', 
    'url' => 'http://www.sharpened.net/glossary/emoticon.php?mouse'
    ), 
    array(
        'emoticon' => '<:3)~', 'label' => 'Mouse', 
    'url' => 'http://www.sharpened.net/glossary/emoticon.php?mouse'
    ), 
    array(
        'emoticon' => '~,~', 'label' => 'Napping', 
    'url' => 'http://www.sharpened.net/glossary/emoticon.php?napping'
    ), 
    array(
        'emoticon' => ':-B', 'label' => 'Nerd', 
    'url' => 'http://www.sharpened.net/glossary/emoticon.php?nerd'
    ), 
    array(
        'emoticon' => '^_^', 'label' => 'Overjoyed', 
    'url' => 'http://www.sharpened.net/glossary/emoticon.php?overjoyed'
    ), 
    array(
        'emoticon' => '<l:0', 'label' => 'Partying', 
    'url' => 'http://www.sharpened.net/glossary/emoticon.php?partying'
    ), 
    array(
        'emoticon' => ':-/', 'label' => 'Perplexed', 
    'url' => 'http://www.sharpened.net/glossary/emoticon.php?perplexed'
    ), 
    array(
        'emoticon' => '=8)', 'label' => 'Pig', 
    'url' => 'http://www.sharpened.net/glossary/emoticon.php?pig'
    ), 
    array(
        'emoticon' => '\&&&/', 'label' => 'Princess', 
    'url' => 'http://www.sharpened.net/glossary/emoticon.php?princess'
    ), 
    array(
        'emoticon' => '\%%%/', 'label' => 'Queen', 
    'url' => 'http://www.sharpened.net/glossary/emoticon.php?queen'
    ), 
    array(
        'emoticon' => '@~)~~~~', 'label' => 'Rose', 
    'url' => 'http://www.sharpened.net/glossary/emoticon.php?rose'
    ), 
    array(
        'emoticon' => '=(', 'label' => 'Sad', 
    'url' => 'http://www.sharpened.net/glossary/emoticon.php?sad'
    ), 
    array(
        'emoticon' => ':-(', 'label' => 'Sad', 
    'url' => 'http://www.sharpened.net/glossary/emoticon.php?sad'
    ), 
    array(
        'emoticon' => ':(', 'label' => 'Sad', 
    'url' => 'http://www.sharpened.net/glossary/emoticon.php?sad'
    ), 
    array(
        'emoticon' => ':-7', 'label' => 'Sarcastic', 
    'url' => 'http://www.sharpened.net/glossary/emoticon.php?sarcastic'
    ), 
    array(
        'emoticon' => ':-@', 'label' => 'Screaming', 
    'url' => 'http://www.sharpened.net/glossary/emoticon.php?screaming'
    ), 
    array(
        'emoticon' => '=O', 'label' => 'Shocked', 
    'url' => 'http://www.sharpened.net/glossary/emoticon.php?shocked'
    ), 
    array(
        'emoticon' => ':-o', 'label' => 'Shocked', 
    'url' => 'http://www.sharpened.net/glossary/emoticon.php?shocked'
    ), 
    array(
        'emoticon' => 'O[-<]:', 'label' => 'Skateboarder', 
    'url' => 'http://www.sharpened.net/glossary/emoticon.php?skateboarder'
    ), 
    array(
        'emoticon' => ':-)', 'label' => 'Smile', 
    'url' => 'http://www.sharpened.net/glossary/emoticon.php?smile'
    ), 
    array(
        'emoticon' => ':-Q', 'label' => 'Smoking', 
    'url' => 'http://www.sharpened.net/glossary/emoticon.php?smoking'
    ), 
    array(
        'emoticon' => ':>', 'label' => 'Smug', 
    'url' => 'http://www.sharpened.net/glossary/emoticon.php?smug'
    ), 
    array(
        'emoticon' => ':P', 'label' => 'Sticking Tongue Out', 
    'url' => 'http://www.sharpened.net/glossary/emoticon.php?sticking_tongue_out'
    ), 
    array(
        'emoticon' => ':o', 'label' => 'Surprised', 
    'url' => 'http://www.sharpened.net/glossary/emoticon.php?surprised'
    ), 
    array(
        'emoticon' => '(:|', 'label' => 'Tired', 
    'url' => 'http://www.sharpened.net/glossary/emoticon.php?tired'
    ), 
    array(
        'emoticon' => ':-J', 'label' => 'Tongue in Cheek', 
    'url' => 'http://www.sharpened.net/glossary/emoticon.php?tongue_in_cheek'
    ), 
    array(
        'emoticon' => ':-&', 'label' => 'Tongue Tied', 
    'url' => 'http://www.sharpened.net/glossary/emoticon.php?tongue_tied'
    ), 
    array(
        'emoticon' => 'oh =-O', 'label' => 'Uh-oh', 
    'url' => 'http://www.sharpened.net/glossary/emoticon.php?uh'
    ), 
    array(
        'emoticon' => ':-\\', 'label' => 'Undecided', 
    'url' => 'http://www.sharpened.net/glossary/emoticon.php?undecided'
    ), 
    array(
        'emoticon' => '**==', 'label' => 'United States Flag', 
    'url' => 'http://www.sharpened.net/glossary/emoticon.php?united_states_flag'
    ), 
    array(
        'emoticon' => ':-E', 'label' => 'Vampire', 
    'url' => 'http://www.sharpened.net/glossary/emoticon.php?vampire'
    ), 
    array(
        'emoticon' => '=D', 'label' => 'Very Happy', 
    'url' => 'http://www.sharpened.net/glossary/emoticon.php?very_happy'
    ), 
    array(
        'emoticon' => ';-)', 'label' => 'Winking', 
    'url' => 'http://www.sharpened.net/glossary/emoticon.php?winking'
    ), 
    array(
        'emoticon' => ';)', 'label' => 'Winking', 
    'url' => 'http://www.sharpened.net/glossary/emoticon.php?winking'
    ), 
    array(
        'emoticon' => '|-O', 'label' => 'Yawn', 
    'url' => 'http://www.sharpened.net/glossary/emoticon.php?yawn'
    ), 
    array(
        'emoticon' => 'Z', 'label' => 'Zorro', 
    'url' => 'http://www.sharpened.net/glossary/emoticon.php?zorro'
    )
    );

    private $_linkTemplate = '<a href="%1$s" target="_blank" title="%2$s" class="emoticon-easter-egg">%3$s</a>';

    /**
     * Return a random emoticon
     *
     * @return void
     */
    public function emoticon(){

        $item = array_rand($this->_emoticons);
        return sprintf($this->_linkTemplate, $this->_emoticons[$item]['url'], 
        $this->_emoticons[$item]['label'], 
        htmlentities($this->_emoticons[$item]['emoticon']));
    }

    /**
     * Implements the "fluent" interface. In the view, it it's called 
     * directly, it will return the current object, so all the other methods
     * can be called without the need of explicitly instantiating the helper
     *
     * Ex:
     * $this->EasterEgg()->can('add');
     *
     * @access public                   
     * @return App_View_Helper_EasterEgg
     */
    public function EasterEgg(){

        return $this;
    }
}