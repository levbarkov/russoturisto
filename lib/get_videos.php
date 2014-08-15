<?php

class get_videos {
    private $params;
    
    public function __construct($width = 1120, $height = 460, $wmode = 'opaque'){
        $this->params = array(
            'width'     => $width,
            'height'    => $height,
            'wmode'     => $wmode
        ); 
    }
    
    public function make($url){
        $url = parse_url($url);
        parse_str($url['query'], $params);
        
        $src = '';    
        if(preg_match('~youtube\.(com|ru)~ui', $url['host'])){
            $src = "http://www.youtube.com/v/{$params['v']}&hl=ru&fs=1";
        }
        elseif(strpos($url['host'], 'rutube.ru') !== false)
            $src = 'http://video.rutube.ru/' . $params['v'];
        elseif(strpos($url['host'], 'vimeo.com') !== false){
            $clip_id = str_replace('/', '', $url['path']);
            $src = "http://vimeo.com/moogaloop.swf?clip_id={$clip_id}&server=vimeo.com&show_title=1&show_byline=1&show_portrait=0&color=&fullscreen=1";
        }
        elseif(strpos($url['host'], 'smotri.com') !== false){
            $src = "http://pics.smotri.com/player.swf?file={$params['id']}&bufferTime=3&autoStart=false&str_lang=rus&xmlsource=http%3A%2F%2Fpics.smotri.com%2Fcskins%2Fblue%2Fskin_color.xml&xmldatasource=http%3A%2F%2Fpics.smotri.com%2Fskin_ng.xml";
        }
        
        if($src == '')
            return false;
        
        $add_params = strpos($url['host'], 'smotri.com') !== false ? '' : "<param name='movie' value='{$src}'></param>";
        
        $html = <<<HTML
            <object width="{$this->params['width']}" height="{$this->params['height']}">
                {$add_params}
                <param value="true" name="allowFullScreen"></param>
                <param name="allowscriptaccess" value="always"></param>
                <param name="wmode" value="{$this->params['wmode']}"></param>
                <embed src="{$src}" type="application/x-shockwave-flash" allowscriptaccess="always" allowfullscreen="true" width="{$this->params['width']}" height="{$this->params['height']}" wmode="{$this->params['wmode']}"></embed>
             </object>
HTML;

        return $html;
 
    }  
}
