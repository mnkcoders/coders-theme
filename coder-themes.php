<?php defined('ABSPATH') or die;
/*******************************************************************************
 * Plugin Name: Coder Themes
 * Plugin URI: https://coderstheme.org
 * Description: Theme Helper Prototype
 * Version: 0.1.2
 * Author: Coder01
 * Author URI: 
 * License: GPLv2 or later
 * Text Domain: coder_themes
 * Domain Path: lang
 * Class: CoderThemes
 * 
 * @author Coder01 <mnkcoders@gmail.com>
 ******************************************************************************/
abstract class CoderThemes{
    
    const TYPE_SELECT = 'select';
    const TYPE_TEXT = 'text';
    const TYPE_NUMBER = 'number';
    const TYPE_CHECKBOX = 'checkbox';

    private $_priority = 8;
    
    private $_elements = array(
        //
    );
    
    /**
     * @var \CoderThemes
     */
    private static $_instance = null;
    
   
    /**
     *
     * @var Array
     */
    private $_contents = array(
        'theme_support' => array(),
        'sidebar' => array(),
        'menu' => array(),
        'style' => array(),
        'script' => array(),
        'localized' => array(),
        'customizer_section' => array(),
        'customizer_control' => array(),
        'settings' => array(),
    );
    /**
     * 
     */
    public function __construct() {
    
        $this->registerThemeCustomizers()
                ->registerThemeSupport()
                ->registerThemeScripts()
                ->registerThemeSidebars()
                ->registerThemeMenus()
                ->registerThemeSettings()
                ->registerThemeBlocks();
        
        if(is_null(self::$_instance)){
            $this->register()->setupCustomizer();
            self::$_instance = $this;
        }
    }
    
    /**
     * @param String $name
     * @return String
     */
    public final function __get( $name ){
        switch(TRUE){
            case preg_match('/^id_/', $name):
                //si es un id retorna booleano
                return $this->hasId($name);
            case preg_match('/^tag_/', $name):
                //si es un tag, retorna su nombre
                return $this->blockTag( $name );
            case preg_match('/^wrap_/', $name):
                //indica si contiene un wrapper
                return $this->hasWrapper($name) ? implode(' ', $this->wrap()) : '';
            case array_key_exists($name, $this->_elements):
                //var_dump($this->_elements[$name]['value']);
                return $this->_elements[$name]['value'];
            default:
                return $this->mod($name,'');
        }
    }
    
    /**
     * @param string $TAG
     * @param array $attributes
     * @param mixed $content
     * @return String|HTML
     */
    protected static final function __HTML( $TAG , $attributes = array() , $content = NULL ){
        if( isset( $attributes['class'])){
            if(is_array($attributes['class'])){
                $attributes['class'] = implode(' ', $attributes['class']);
            }
        }
        $serialized = array();
        foreach( $attributes as $var => $val ){
            $serialized[] = sprintf('%s="%s"',$var,$val);
        }
        if( !is_null($content) ){
            if(is_object($content)){
                $content = strval($content);
            }
            elseif(is_array($content)){
                $content = implode(' ', $content);
            }
            return sprintf('<%s %s>%s</%s>' , $TAG ,
                    implode(' ', $serialized) , strval( $content ) ,
                    $TAG);
        }
        return sprintf('<%s %s />' , $TAG , implode(' ', $serialized ) );
    }
    /**
     * @param string $input
     * @return boolean
     */
    protected static final function __matchUrl( $input ){
        return preg_match('/^(http|https):\/\//',$input) > 0;
    }
    /**
     * @param string $part
     */
    public static final function templatePart( $part ){
        get_template_part( 'html/' . preg_replace('/_/', '-', $part) );
    }
    /**
     * @param string $part
     */
    public static final function templatePath( $part ){
        return sprintf('%s/html/%s.php', get_stylesheet_directory(),$part);
    }
    
    
    /**
     * 
     * @return \CoderThemes
     */
    protected function registerThemeCustomizers() {
        
        return $this;
    }
    /**
     * 
     * @return \CoderThemes
     */
    protected function registerThemeSupport() {
        
        return $this;
    }
    /**
     * 
     * @return \CoderThemes
     */
    protected function registerThemeSidebars() {
        
        return $this;
    }
    /**
     * 
     * @return \CoderThemes
     */
    protected function registerThemeMenus() {
        
        return $this;
    }
    /**
     * 
     * @return \CoderThemes
     */
    protected function registerThemeScripts() {
        
        return $this;
    }
    /**
     * 
     * @return \CoderThemes
     */
    protected function registerThemeSettings() {
        
        return $this;
    }
    /**
     * 
     * @return \CoderThemes
     */
    protected function registerThemeBlocks() {
        
        return $this;
    }
    
    
    
    /**
     * Override to define the theme structure
     * 
     * @return array
     */
    protected function themeLayout( ){
        return array('header','content','footer',);
    }
    /**
     * body classes
     * @return array
     */
    protected function themeClasses(){ return array('container'); }
    /**
     * @return array
     */
    protected function themeTags( ){ return array('header'=>'header','footer'=>'footer'); }
    /**
     * Theme block wrappers
     * @return array
     */
    protected function themeWrappers(){ return array('header','content','footer'); }
    /**
     * Theme block wrappers
     * @return array
     */
    protected function wrap(){ return array('wrap'); }
    /**
     * Theme element IDs
     * @return array
     */
    protected function themeIds(){ return array('header','content','footer'); }
    /**
     * @param string $container
     * @return boolean
     */
    private final function hasId( $container ){
        $ids = $this->themeIds();
        return in_array( $container , $ids );
    }
    /**
     * @param string $block
     * @return boolean
     */
    protected final function hasWrapper( $block ){
        $wrappers = $this->themeWrappers();
        $container = strtolower($block);
        return in_array($container, $wrappers);
    }
    /**
     * @param string $menu
     * @return boolean
     */
    private final function hasMenu( $menu ){
        $name = preg_match(  '/-menu$/' , $menu ) > 0 ? substr($menu, 0, strlen($menu)-5) : '';
        return strlen($name) && isset( $this->_contents['menu'][$name]);
    }
    /**
     * @param string $sidebar
     * @return boolean
     */
    private final function hasSidebar( $sidebar ){
        $name = preg_match(  '/-sidebar$/' , $sidebar ) > 0 ? substr($sidebar, 0, strlen($sidebar)-8) : '';
        return strlen($name) && isset( $this->_contents['sidebar'][$name]);
    }
    /**
     * @param string $menu
     * @return \CoderThemes
     */
    private final function showNavMenu($menu,$class = '') {
        if( has_nav_menu( $menu ) ){
            print wp_nav_menu(array(
                'theme_location' => $menu,
                'menu_class' => 'menu ' . (is_array($class) ? implode(' ', $class) : $class),
                'container' => FALSE,
                'echo' => FALSE));
        }
        return $this;
    }
    /**
     * @param string $sidebar
     * @return \CoderThemes
     */
    protected final function showSidebar( $sidebar ){
        dynamic_sidebar($sidebar);
        return $this;
    }
    /**
     * Logo por defecto del tema
     * 
     * https://codex.wordpress.org/Theme_Logo
     * 
     * @param boolean $display Muestra el logo por defecto
     * @return String|HTML
     */
    protected function showLogo( $display = true ){

        $logo = function_exists( 'get_custom_logo' ) ?
                get_custom_logo() :
                self::__HTML('a', array(
                    'class' => 'theme-logo',
                    'href' => get_site_url(),
                    'target' => '_self'
                ), get_bloginfo('name'));
        
        if( $display ){
            print $logo;
            return '';
        }
        return $logo;
    }
    
    /**
     * @param string $container
     * @return string
     */
    private final function blockTag( $container ){
        $tags = $this->themeTags();
        return isset( $tags[$container] ) ? $tags[$container] : 'div';
    }
    /**
     * @param string $block
     * @return String
     */
    private final function blockClass( $block ){
        return implode(' ', $this->themeClasses()) . ' ' . $block;
    }
    /**
     * Sidebar structure
     * @return array
     */
    protected function defineSidebarContainer( $header = 'h2' ){
        return array(
            'before_widget' => '<div id="%1$s" class="widget %2$s">',
            'after_widget' => '</div>',
            'before_title'  => sprintf('<%s class="widget-title">',$header),
            'after_title'   => sprintf('</%s>',$header),
        );
    }
    
    /**
     * @param string $element
     */
    protected function showNotFound( $element ){
        printf('<!-- [ %s ] %s -->', $element , __('not found','coders_themes'));
    }
    /**
     * @return string Título
     */
    protected function showTitle(){

        return is_front_page( /*inicio*/ ) || is_home( /*inicio o pagina de entradas*/) ?
                get_bloginfo( 'name' ) :    //solo titulo web
                get_bloginfo( 'name' ) . ' - ' . get_the_title( ); //titulo web + titulo  pagina
    }
    /**
     * @return \CODERS\Theme
     */
    protected function openTheme(){
        printf('<!DOCTYPE html><html %s>', get_language_attributes());
        print('<head>');
        printf('<title>%s</title>',$this->showTitle());
        wp_head();
        print('</head>');
        printf('<body class="%s" >', implode(' ',  get_body_class( ) ) );
        return $this;
    }
    /**
     * @return \CoderThemes
     */
    protected function closeTheme(){
        wp_footer();
        print '</html>';
        return $this;
    }
    /**
     * @param string $block
     * @param string $class
     * @return \CODERS\Theme
     */
    private final function openBlock( $block , $class = '' ){
        $show_id = $this->hasId($block) ? sprintf('id="%s"',$block) : '';
        //open block with ID
        printf('<%s %s class="%s">',
                $this->blockTag( $block ),
                $show_id, 
                $this->blockClass( $show_id ? $class : $block . ' ' . $class ) );
        
        if( $this->hasWrapper($block) ){
            //apertura del wrapper
            printf( '<div class="%s">' , implode(' ', $this->wrap()) );
        }
        return $this;
    }
    /**
     * Finalize current block render
     * @return \CODERS\Theme
     */
    private final function closeBlock($block) {
        printf('%s</%s>',  $this->hasWrapper($block) ? '</div>' : '', $this->blockTag($block));
        return $this;
    }
    /**
     * Show a block's content
     * @param string $block
     * @return \CODERS\Theme
     */
    protected final function renderBlock( $block ){
        $blockMethod = sprintf('render%sBlock', preg_replace('/\s/', '', ucfirst(  preg_replace('/-/', ' ', $block ) ) ) );
        if( method_exists($this, $blockMethod)){
            //printf('<!-- %s -->',$blockMethod);
            $this->$blockMethod( );
        }
        elseif( $block === 'site-logo' ){
            //printf('<!-- logo:%s -->',$block);
            $this->openBlock( $block );
            $this->showLogo( TRUE );
            $this->closeBlock( $block );
        }
        elseif( $this->hasMenu( $block ) ){
            //printf('<!-- menu:%s -->',$block);
            $this->showNavMenu( preg_replace('/-menu$/','',$block  ) );
        }
        elseif( $this->hasSidebar( $block ) ){
            //printf('<!-- sidebar:%s -->',$block);
            $this->showSidebar( preg_replace('/-sidebar$/','',$block  ) );
        }
        else{
            $template_part = $this->templatePath($block);
            //printf('<!-- part:%s -->',$block);
            $this->openBlock( $block );
            if(file_exists($template_part)){
                require $template_part;
            }
            else{
                print $this->showNotFound($block);
            }
            $this->closeBlock( $block );
        }
        return $this;
    }
    /**
     * Muestra el contenido de la página
     */
    protected function renderContentBlock(){
        $content_type = $this->contentType();
        $template = implode( '-', $content_type );
        $path = $this->templatePath( $template );
        printf('<div class="content %s">', implode(' ', $content_type ) );
        $contentWrap = $this->hasWrapper('content');
        if( $contentWrap ){
            printf('<div class="%s">', implode(' ', $this->wrap()));
        }
        if(file_exists($path)){
            require $path;
        }
        else{
            $this->showNotFound($template);
        }
        print $contentWrap ? '</div></div>' : '</div>';
    }
    /**
     * Dive in and show the theme layout hierarchy
     * @param mixed $block_id
     * @param mixed $content
     * @return \CODERS\Theme
     */
    private final function renderTheme( $block_id , $content ){
        //for named arrays
        if(is_string($block_id) && $block_id !== '__class' ){
            $class = is_array($content) && array_key_exists('__class', $content) ? $content['__class'] : '';
            $this->openBlock( $block_id , $class );
            if( is_string( $content ) ){
                $this->renderBlock( $content );
            }
            elseif( is_array( $content ) ){
                foreach ( $content as $child_block => $sub_content ) {
                    $this->renderTheme( $child_block, $sub_content );
                }
            }
            $this->closeBlock( $block_id ); 
        }
        elseif(is_numeric($block_id)){
            //for numeric arrays (simple elements)
            $this->renderBlock($content);
        }
        return $this;
    }
    /**
     * @param string $root Plantilla wordpress a mostrar (experimental)
     * @return \CODERS\Theme
     */
    public final function display( $root = 'site-main' ){

        return $this->openTheme()
                ->renderTheme( $root , $this->themeLayout() )
                ->closeTheme();
    }
    
    /**
     * @param String $type
     * @return Number
     */
    protected final function countElements( $type ){
        return array_key_exists($type, $this->_contents) ? count( $this->_contents[ $type ] ) : 0;
    }
    /**
     * @param String $resource
     * @return String|URÑ
     */
    protected final function themeUri( $resource = '' ){
        
        $uri = get_template_directory_uri();
        
        return strlen($resource) > 0 ? sprintf('%s/%s',$uri,$resource) : $uri;
    }
    /**
     * @param string $resouce
     * @return string
     */
    protected final function  themePath( $resouce = '' ){
        $path = preg_replace('/\\\\/', '/', get_template_directory());
        return strlen($resouce) ? $path . '/' . $resouce : $path;
    }

    /**
     * @param string $name
     * @param string $type
     * @return mixed
     */
    protected final function content( $name , $type ){
        return array_key_exists($type, $this->_contents) && array_key_exists($name, $this->_contents[$type]) ?
                $this->_contents[$type][$name] :
                    '';
    }
    /**
     * Define el tipo de post
     * @return array
     */
    private function contentType( ){
        $post_type = get_post_type();
        switch( TRUE ){
            case $post_type === false ||is_404():
                return  array('error','404');
            case $post_type === 'page':
                return array('page','single');
            case $post_type === 'post':
                return array('post',is_single() ? 'single' : 'loop' );
        }
    }
    /**
     * @param string $name
     * @param string $type
     * @param mixed $value
     * @return CoderThemes
     */
    protected final function element( $name , $type , $value = FALSE ){
        if( !array_key_exists($name, $this->_elements)){
            $element = array(
                'name' => $name,
                'type' => $type,
                //'mod' => $mod,
                'value' => $value
            );
            switch( $type ){
                case self::TYPE_CHECKBOX:
                    $element['value'] = boolval($value);
                    break;
                case self::TYPE_NUMBER:
                    $element['value'] = intval($value);
                    break;
                case self::TYPE_SELECT:
                case self::TYPE_TEXT:
                    $element['value'] = $value;
                    break;
            }
            $this->_elements[$name] = $element;
        }
        return $this;
    }
    /**
     * @param string $feature
     * @param array $settings
     * @return \CoderThemes
     */
    protected function themeSupport( $feature , array $settings = array( ) ){
        $this->_contents['theme_support'][$feature] = $settings;
        return $this;
    }
    /**
     * @param string $sidebar
     * @param string $name
     * @param array $settings
     * @return \CoderThemes
     */
    protected function sidebar( $sidebar , $name = '' , array $settings = array( ) ){
        if( !array_key_exists('id', $settings)){
            $settings['id'] = $sidebar;
        }
        $settings['name'] = strlen( $name ) ? $name : $sidebar;
        if( !array_key_exists('before_widget', $settings)){
            $settings['before_widget'] = '<div class="widget">';
            $settings['after_widget'] = '</div>';
        }
        if( !array_key_exists('before_title', $settings)){
            $settings['before_title'] = '<h2 class="widget-title">';
            $settings['after_title'] = '</h2>';
        }
        $this->_contents['sidebar'][$sidebar] = $settings;
        return $this;
    }
    /**
     * @param string $menu
     * @param string $location
     * @return \CoderThemes
     */
    protected function menu( $menu , $location ){
        $this->_contents['menu'][$menu] = $location;
        return $this;
    }
    /**
     * @param string $style_id
     * @param array $url
     * @return \CoderThemes
     */
    protected function style( $style_id , $url ){
        $this->_contents['style'][$style_id] = $url;
        return $this;
    }
    /**
     * @param string $script_id
     * @param array $url
     * @return \CoderThemes
     */
    protected function script( $script_id , $url = '' ){
        $this->_contents['script'][$script_id] = $url;
        return $this;
    }
    /**
     * @param string $script_id
     * @param array $data
     * @return \CoderThemes
     */
    protected function localize( $script_id , $data = array( ) ){
        if(array_key_exists($script_id, $this->_contents['script'])){
            $this->_contents['localized'][$script_id] = $data;
        }
        return $this;
    }
    /**
     * @param string $script_id
     * @return array
     */
    protected function createLocalized( $script_id ){
        
        $localized = $this->content($script_id, 'localized');
        
        return array(
            'name' => preg_replace('/\s/', '', ucwords(preg_replace('/[\-_]/', ' ', $script_id ) )  ),
            'content' => $localized,
        );
    }
    /**
     * @param string $script_id
     * @return boolean
     */
    protected function isLocalized( $script_id ){
        return array_key_exists($script_id, $this->_contents['localized']);
    }

    /**
     * @param string $section_id
     * @param array $settings
     * @return \CoderThemes
     */
    protected function customSection( $section_id , array $settings = array( ) ){
        $this->_contents['customizer_section'][$section_id] = $settings;
        if( !array_key_exists('title', $this->_contents['customizer_section'][$section_id])){
            $this->_contents['customizer_section'][$section_id]['title'] = __($section_id, 'coder_themes');
        }
        if( !array_key_exists('priority', $this->_contents['customizer_section'][$section_id])){
            $this->_contents['customizer_section'][$section_id]['priority'] = $this->_priority + $this->countElements('customizer_section');
        }
        return $this;
    }
    /**
     * @param string $control_id
     * @param array $contents
     * @return \CoderThemes
     */
    protected function customControl( $control_id , $section , $setting , $type = 'text', array $contents = array( ) ){
        $contents['settings'] = $setting;
        $contents['section'] = $section;
        if( !array_key_exists('type', $contents)){
            $contents['type'] = $type;
        }
        if( !array_key_exists('label', $contents)){
            $contents['label'] = __($control_id,'coder_themes');
        }
        if( !array_key_exists('description', $contents)){
            $contents['description'] = __('A custom theme setting','coder_themes');
        }
        if( !array_key_exists('priority', $contents)){
            $contents['priority'] = $this->countElements('customizer_control') + $this->_priority;
        }
        if( $contents['type'] === 'select' ){
            $contents['choices'] = $this->setting($setting);
        }
        $this->_contents['customizer_control'][$control_id] = $contents;
        return $this;
    }
    /**
     * @param string $setting_id
     * @param array $contents
     * @return \CoderThemes
     */
    protected function customSetting( $setting_id , array $contents = array( ) ){
        $this->_contents['settings'][$setting_id] = $contents;
        return $this;
    }
    /**
     * 
     * @return \CoderThemes
     */
    private final function setupCustomizer(){

        add_action('customize_register', function(WP_Customize_Manager $wp_customize) {
            
            foreach( CoderThemes::instance()->list('settings') as $id => $contents ){
                $wp_customize->add_setting($id, $contents);
            }
            
            foreach( CoderThemes::instance()->list('customizer_section') as $section_id => $settings ){
                $wp_customize->add_section($section_id,$settings);
            }
            
            foreach( CoderThemes::instance()->list('customizer_control') as $control_id => $settings ){
                
                if(array_key_exists('type', $settings) && $settings['type'] === 'select' ){
                    $settings['choices'] = $this->setting($control_id);
                }
                
                $wp_customize->add_control(new WP_Customize_Control($wp_customize,$control_id,$settings));
            }
        });
        
        return $this;
    }
    /**
     * @return \CoderThemes
     */
    private final function register(){
        
        $theme = $this;
        
        add_action( 'init' , function() use($theme){

            foreach( $theme->list('theme_support') as $feature => $settings ){
                add_theme_support($feature,$settings);
            }

            add_action( 'wp_enqueue_scripts' , function() use($theme){
                foreach( $theme->list('style') as $handle => $url ){
                    wp_enqueue_style($handle,$url);
                }
                foreach( $theme->list('script') as $handle => $url ){
                    if(strlen($url)){
                        wp_enqueue_script($handle,$url,array(),false,true);
                    }
                    else{
                        wp_enqueue_script($handle);
                    }
                    if($theme->isLocalized($handle)){
                        $localized = $theme->createLocalized($handle);
                        wp_localize_script( $handle, $localized['name'], $localized['content']);
                    }
                }
            });
            
            register_nav_menus( CoderThemes::instance()->list('menu') );
            
            foreach( CoderThemes::instance()->list('sidebar') as $settings ){
                register_sidebar($settings);
            }
        });
        
        
        return $this;
    }
    /**
     * @return array
     */
    public final function setting( $setting_id ){
        $settings = $this->list('settings');
        return array_key_exists($setting_id, $settings) ? $settings[$setting_id] : array();
    }
    /**
     * @param string $content_type
     * @return array
     */
    public final function list( $content_type ){
        return array_key_exists($content_type, $this->_contents ) ?
                $this->_contents[$content_type] :
                        array();
    }
    
    public static final function mod( $setting , $default = FALSE ){
        return get_theme_mod($setting,$default);
    }
    /**
     * @return array
     */
    public final function dump(){
        return $this->_contents;
    }
    /**
     * @param string $feature
     * @return string
     */
    //public static final function feature( $feature ){
    //    return self::$_instance->$feature;
    //}
    /**
     * @param string $feature
     * @return string
     */
    public final function feature( $feature ){
        return $this->$feature;
    }
    /**
     * @param string $uri
     * @return CoderThemes
     */
    private static final function create( $uri = '' ){
            $root = explode('/', $uri );
            $name = $root[count($root)-1];
            $path = sprintf('%s/%s.theme.php',$uri,$name);
            $class = sprintf('\%sTheme', ucfirst( $name) );
            if(file_exists($path)){
                require_once $path;
                if(class_exists($class) && is_subclass_of($class, \CoderThemes::class,true)){
                    return new $class();
                }
                else{
                    printf('<p>Invalid Theme Instance %s</p>',$class);
                }
            }
            else{
                printf('<p>Invalid Theme Path %s</p>',$path);
            }

            return null;
    }
    /**
     * Theme loaded and ready
     * @return Boolean
     */
    public static final function ready(){
        return !is_null(self::$_instance);
    }
    /**
     * @param string $uri
     * @return \CoderThemes
     */
    public static final function instance( $uri = '' ){
        return is_null( self::$_instance ) && strlen($uri) ? self::create($uri) : self::$_instance;
    }
}


