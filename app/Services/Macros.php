<?php namespace App\Services;

use App\Services\Aio\Facades\Aio;
use Illuminate\Html\FormBuilder;
use Storage;

/**
 * Class Macros
 * @package App\Http
 */
class Macros extends FormBuilder {

	/**
	 * @param $name
	 * @param null $selected
	 * @param array $options
	 * @return string
	 */
	public function selectState($name, $selected = null, $options = array())
	{
		$list = [
			'' => 'Select One...',
			'AL' => 'Alabama',
			'AK' => 'Alaska'
		];

		return $this->select($name, $list, $selected, $options);
	}

	/**
	 * @param $name
	 * @param null $selected
	 * @param array $options
	 * @return string
	 */
	public function selectCountry($name, $selected = 'MM', $options = array())
	{
		/**
                $countries_file = Storage::get('countries.csv');
                
                $countries_lines = array_values(explode(PHP_EOL, $countries_file));
                
                while (list($key, $val) = each($countries_lines)) {
                    $line = explode(',', $val);
                    $countries_list[$line[0]] = $line[1];
                }   
                Storage::put('countries.json',json_encode($countries_list));
                 * 
                 */
                $countries_file = Storage::get('countries.json');
                $countries_list = json_decode($countries_file);
		return $this->select($name, $countries_list, $selected, $options);
	}
        
        /**
         * 
         * @param type $answer
         * @param type $results
         * @param type $options
         * @param type $form
         * @return string
         */
        public function makeInput( $answer, $results = null ,$options = [], $form = '' ){
            
            $html = '';
            $type = $answer->type;
            $name = $answer->akey;
            $text = (!empty($answer->text))?_t(ucfirst($answer->text)):'';
            $ansval = $answer->value;
            $section = $answer->question->section;
            $qslug = $answer->question->slug;
            $qid = $answer->question->id;
            if(empty($answer->slug)){
                $answer->update(['slug' => snake_case($answer->akey)]);
            }
            $ans_slug = $answer->slug;
            
            $options['id'] = $ans_slug;
            $options['data-logic'] = json_encode($answer->logic);
            $value = null;
            if(!is_null($results)){
                $rs = $results->where('section_id', $section)->first();
                if(!is_null($rs)) {
                foreach($rs->answers as $values) {
                    if(in_array($ans_slug, $values->toArray())) { 
                        if($type == 'radio') {
                            $value = true;   
                        }else{
                            $value = $values->value; 
                        }
                        break;
                    } else {
                        $value = null;
                    }
                }
                }
            } else {
                $value = null;
            }
            
            if($type == 'radio'){
                $inputname = "answer[$section][$qslug][radio]";
            }else{
                $inputname = "answer[$section][$qslug][$ans_slug]";
            }
            
            if(array_key_exists('class', $options) ) {
                $cssClass = $options['class'];
            } else {
                $cssClass = '';
            }
            
            switch($type) {
                case 'radio':
                    $options['data-value'] = $ansval;
                    $html .= "<div class=\"radio\">";
                    $html .= "<label class='control-label'>";
                    $html .= $this->radio($inputname, $ans_slug, $value, $options);
                    $html .= "<span class='badge'>$ansval</span> ";
                    $html .= $text;
                    $html .= "</label>";
                    $html .= "</div>";
                    
                    break;
                case 'checkbox':
                    $html .= "<div class=\"checkbox\">";
                    $html .= "<label class='control-label'>";
                    $html .= $this->checkbox($inputname, $ansval, $value, $options);
                    
                    $html .= $text;
                    $html .= "</label>";
                    $html .= "</div>";
                    break;
                case 'textarea':
                    $options['class'] = $cssClass . ' form-control';
                    $html .=  "<label for=\"$inputname\" class='col-xs-2 text-normal'>$text</label>";
                    $html .=  "<div class='col-xs-10' style='padding-left:0px'>";
                    $html .=  $this->textarea($inputname, $value, $options);
                    $html .=  "</div>";
                    break;
                case 'option':
                    
                    break;
                case 'question':
                    
                    break;
                default:
                    $options['class'] = $cssClass . ' form-control';
                    $html .=  "<label for=\"$inputname\" class='col-xs-2 text-normal'>$text</label>";
                    $html .=  "<div class='col-xs-10' style='padding-left:0px'>";
                    $html .=  $this->input($type, $inputname, $value, $options);
                    $html .=  "</div>";
                    break;
            }
            return $html;
        }
        
        
        public function answerField($question, $answer, $qnumber, $answer_key, $results, $options=array(), $wrapper = array())
        {            
            //dd($answer_key);
            
            $name = $answer->text;
            $display_value = $answer->value;
            $type = $answer->type;
            $akey = isset($answer->akey) ? $answer->akey:$answer_key;
            $css = $answer->css;
            $qnum = $question->qnum;
            $section = $question->section;
            
            if(empty($answer->slug)){
                $answer->update(['slug' => str_slug($answer->akey)]);
            }
            if(in_array($type, ['radio'])){
                $default = isset($answer->akey) ? $answer->akey:null;
            }else{
                $default = $answer->value;
            }
            if($type == 'radio'){
                $radioname = strtolower($qnum).'_radio';
                $answer_index = 'radio';
                $inputname = "answer[$section][$qnum][radio]";
            }else{
                $answer_index = $akey;
                $inputname = "answer[$section][$qnum][$akey]";
            }
            
            if(is_array($results)){
                $result = $results['results'];
                $section_id = $results['section'];
                $project_id = $results['project'];
                $resultable = $results['validator'];
                $incident = $results['incident'];//dd($incident);
                $value = $result->getResultBySection($section_id, $project_id, $resultable, $qnum, $akey, $incident);
            }else{
                $value = null;
            }
            //dd($answer_key);
            $cssId = $akey;//dd($cssId);
            //$cssId = $answer_key;
            /**
            if(property_exists($answer, "css")) {
               $css = $answer->css;dd($css);
            } else {
               $css = ''; 
            }
             * 
             */
            if(!empty($answer->require)){
                $pattern = preg_quote('+-*/<>=() ');
                $formula_str = $answer->require;
                //$formula_str = 'q1_a0 > (q1_a1 + q1_a2)';
                preg_match_all("|[$pattern]+|", $formula_str, $symbols, PREG_PATTERN_ORDER);
                $new_str = preg_replace("|[$pattern]|",'__', $formula_str);
                $arguments = array_values(array_filter(explode('__', $new_str)));
                
                $forFormula = '';
                for($i=0; $i < count($symbols[0]); $i++){
                    if($i != count($arguments)){
                        if(is_numeric($arguments[$i])){
                            $forFormula .= $arguments[$i];
                        }else{
                            //$forJS .= '$("#'.$arguments[$i].'").on("click",function(){'.
                            //         '$(self).addClass("alert alert-danger");});';
                            $forFormula .= $arguments[$i].'var';
                        }
                    }
                    
                    $forFormula .= $symbols[0][$i];
                    
                }
                
                if(count($symbols[0]) < count($arguments)){
                        if(is_numeric($arguments[count($symbols[0])])){
                            $forFormula .= $arguments[count($symbols[0])];
                        }else{
                            $forFormula .= $arguments[count($symbols[0])].'var'; 
                        }
                    }
                if(count($symbols[0]) === 0){
                    $forFormula .= '=='.$arguments[count($symbols[0])].'this';
                }
                $forJS = '';
                for($i=0; $i < count($arguments); $i++){
                    if(!is_numeric($arguments[$i])){
                    $forJS .= $arguments[$i].'var = $("#'.$cssId.'").val();';
                    $forJS .= '$("input[name=\''.$inputname.'\']").on("change",function(){'.$arguments[$i].'this = $(this).is(":checked")? $(this).val():false;'.
                                     'if('.$forFormula.'){$("#'.$arguments[$i].'").addClass("alert alert-danger");}else{$("#'.$arguments[$i].'").removeClass("alert alert-danger");}});';
                    $forJS .= '$("#'.$arguments[$i].'").on("click",function(){$(this).removeClass("alert alert-danger");});';
                    $forJS .= '$("#'.$arguments[$i].'").on("focusout",function(){'.$arguments[$i].'this = $("input[name=\''.$inputname.'\']:checked").val();if($(this).val() == "" && '.$forFormula.'){$(this).addClass("alert alert-danger");}else{$(this).removeClass("alert alert-danger");}});';
                    $forJS .= '$( "form" ).submit(function(e) {'.$arguments[$i].'this = $("input[name=\''.$inputname.'\']:checked").val();if($("#'.$arguments[$i].'").val() == "" && '.$forFormula.'){$("#'.$arguments[$i].'").addClass("alert alert-danger");e.preventDefault();}else{$("#'.$arguments[$i].'").removeClass("alert alert-danger");}});';
                    }
                }
                
               
            $options['data-expression'] = $forJS;
            }
            
            if(array_key_exists('style', $wrapper)){
                $wrap_style = $wrapper['style'];
            }else{
                $wrap_style = '';
            }
            
            if(array_key_exists('class', $wrapper)){
                $wrap_class = $wrapper['class'];
            }else{
                $wrap_class = '';
            }
            $wrap = [];
            if(array_key_exists('wrapper', $wrapper)){
                $wrap['start'] = "<$wrapper[wrapper] class='$wrap_class' style='$wrap_style'>";
                
                $wrap['end'] = "</$wrapper[wrapper]>";
            }
            
            if(array_key_exists('class', $options)){
                if($type == 'radio' || $type == 'checkbox'){
                    //remove form-control class if input is radio or checkbox. add space on both end.
                   $css .= ' '.str_replace('form-control', '', $options['class']).' ';
                }
                
            }
            $options['class'] = $css;
            $options['id'] = $answer->slug; //dd($options);
            $options['data-logic'] = json_encode($answer->logic);
            /*
             * To Do: need to fix select box answer
             */
            if($type == 'select') {
                $default_array = explode('|', $default);
                $selecthtml = "<div class='form-group'>";
                if($wrap){
                    $selecthtml .= $wrap['start'];
                }
                $selecthtml .= "<label for='$answer->text'>"._t(ucfirst($answer->text))." </label>";
                $selecthtml .= $this->{$type}($inputname, $default_array, $value, $options);
                if($wrap){
                    $selecthtml .= $wrap['end'];
                }
                $selecthtml .= "</div>";
                return $selecthtml;
            }
            if(in_array($type,['text','textarea'])){
                $options['class'] = $options['class'].' form-control '.$type;
                $texthtml = "<div class='form-group' style='min-height:35px'>";
                if($wrap){
                    $texthtml .= $wrap['start'];
                }
                if(!empty($answer->text)){
                    $texthtml .= "<label for='$answer->text' class='col-xs-2'>"._t(ucfirst($answer->text))." </label>";
                    $texthtml .= "<div class='col-xs-10'>";
                }else{
                    //$texthtml .= "<label for='$inputname' class='col-xs-1'> &nbsp; </label>";
                    $texthtml .= "<div class=''>";
                }
                $texthtml .= $this->{$type}($inputname, $value, $options);
                $texthtml .= "</div>";
                if($wrap){
                    $texthtml .= $wrap['end'];
                }
                $texthtml .= "</div>";
                return $texthtml;
            }
            if(in_array($type, ['date', 'datetime', 'time', 'week', 'month', 'year', 'number'])){
                $options['class'] = $options['class'].' form-control '.$type;
                $texthtml = "<div class='form-group'>";
                if($wrap){
                    $texthtml .= $wrap['start'];
                }
                if(!empty($answer->text)){
                $texthtml .= "<label for='$answer->text' class='col-xs-2 text-right'>"._t(ucfirst($answer->text))." </label>";
                $texthtml .= "<div class='col-xs-10'>";
                }else{
                    //$texthtml .= "<label for='$inputname' class='col-xs-1 text-right'> &nbsp; </label>";
                    $texthtml .= "<div class=''>";
                }
                $texthtml .= $this->input($type,$inputname, $value, $options);
                $texthtml .= "</div>";
                if($wrap){
                    $texthtml .= $wrap['end'];
                }
                $texthtml .= "</div>";
                return $texthtml;
            }
            if($type == 'checkbox'){
                if($default == $value){
                    $cbvalue = true;
                }else{
                    $cbvalue = null;
                }
                $cbhtml = "<div class='form-group'>";
                if($wrap){
                    $cbhtml .= $wrap['start'];
                }
                $cbhtml .= '<div class="checkbox"><label>';
                $cbhtml .= $this->{$type}($inputname, $default, $cbvalue, $options).'<span class="badge">'._t($display_value).'</span> '._t(ucfirst($answer->text)).' </label></div>';
                if($wrap){
                    $cbhtml .= $wrap['end'];
                }
                $cbhtml .= "</div>";
                return $cbhtml;
            }
            if($type == 'radio'){
                if($default == $value){
                    $radiovalue = true;
                }else{
                    $radiovalue = null;
                }
                $radioname = strtolower($qnum).'_radio';
                $rdhtml = "<div class='form-group'>";
                if($wrap){
                    $rdhtml .= $wrap['start'];
                }
                $rdhtml .= '<div class="radio"><label>';
                $rdhtml .= $this->radio($inputname, $default, $value, $options).'<span class="badge">'._t($display_value).'</span> '._t(ucfirst($answer->text)).' </label></div>';
                if($wrap){
                    $rdhtml .= $wrap['end'];
                }
                $rdhtml .= "</div>";
                return $rdhtml;
            }
            
            if($type == 'question'){
                return $this->getChildren($question, $options, $answer_style);
            }
        }
        
        private function getChildren($question, $options = [], $wrapper = []) {
            if(array_key_exists('style', $wrapper)){
                $ans_style = $wrapper['style'];
            }else{
                $ans_style = '';
            }
            foreach($question->children as $child){ 
                echo "<label class='col-lg-1 control-label'>$child->qnum </label><div class='col-lg-11'>
                            <div class='form-control-static'>$child->question</div>";
                echo "<div class='col-lg-12'>";
                foreach($child->answers as $answer){
                 if($child->answer_view == 'horizontal'){
                     echo "<div class='col-xs-".Aio()->getColNum(count($child->answers))."'>";
                     echo $this->answerField($child, $answer, $child->qnum, null);
                     echo "</div>";
                 } else  {  
                    echo $this->answerField($child, $answer, $child->qnum, null);
                 }
                }
                echo "</div></div>";
            }
        }
        
        
}