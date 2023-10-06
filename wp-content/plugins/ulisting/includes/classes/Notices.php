<?php

namespace uListing\Classes;

class Notices {

	public $type;
	public  $message;

	const TYPE_SUCCESS = 'success';
	const TYPE_ERROR   = 'danger';
	const TYPE_WARNING = 'warning';
	const TYPE_INFO    = 'info';
	const TYPE_DEFAULT = 'default';

	const TYPE_ADMIN_NOTICES_ERROR = 'error';
	const TYPE_ADMIN_NOTICES_INFO = 'info';
	const TYPE_ADMIN_NOTICES_WARNING = 'warning';

	/**
	 * @param $type string
	 * @param array $messages array
     * @param $show
     * @return mixed
	 */

	public static function notice($type, $messages = array(), $show = true) {
		$html = '<div class="ulisting-main">';
		$html.='<div class="alert alert-'.$type.'" role="alert">';
				foreach ($messages as $message)
					$html.='<p>'.$message.'</p>';
		$html.='</div>';
		$html.='</div>';
		if(!$show)
			return $html;
		echo apply_filters('stm_sanitize_html', $html);
	}

    /**
     * @param $type string
     * @param array $messages array
     * @param $show
     * @return mixed
     */
	public static function pricing_notice($type, $messages = array(), $show = true) {
	    $html = '';
	    if ( count($messages) > 0 ) {
            $html = '<div class="col-3">';
            $html.='<div class="alert alert-'.$type.'" role="alert">';
            foreach ($messages as $message)
                $html.='<p>'.$message.'</p>';
            $html.='</div>';
            $html.='</div>';
        }

        if (!$show)
            return $html;
        echo apply_filters('stm_sanitize_html', $html);
    }

	/**
	 * @param $type
	 * @param $message
	 */
	public static function add_admin_notices($type, $message){
		$notices = new Notices();
		$notices->admin_notices( $type, $message);
	}

	/**
	 * @param $type
	 * @param $message
	 */
	public  function admin_notices($type, $message){
		$this->type = $type;
		$this->message = $message;
		add_action( 'admin_notices', [ $this, 'admin_notices_render' ]);
	}

	public function admin_notices_render(){
		$output = "<div class='notice notice-".$this->type."'> <p>".$this->message."</p>  </div>";
		echo apply_filters('uListing-sanitize-data', $output);
	}

}
