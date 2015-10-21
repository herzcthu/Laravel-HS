<?php namespace App\Services;

use Illuminate\Html\FormBuilder;

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
	public function selectCountry($name, $selected = null, $options = array())
	{
		$list = [
			'' => 'Select One...',
			'AF' => 'Afghanistan',
			'AX' => 'Aland Islands'
		];

		return $this->select($name, $list, $selected, $options);
	}
        
        
        public function answerField($question, $answer, $qnumber, $answer_key, $results, $options=array(), $wrapper = array())
        {            
            if(!isset($answer->text)){
                dd($answer);
            }
            
            
            $name = $answer->text;
            $display_value = $answer->value;
            $type = $answer->type;
            $akey = $answer->akey; 
            $qnum = $question->qnum;
            if(in_array($type, ['radio'])){
                $default = $answer->akey;
            }else{
                $default = $answer->value;
            }
            if($type == 'radio'){
                $radioname = strtolower($qnum).'_radio';
                $answer_index = 'radio';
                $inputname = "answer[$qnum][radio]";
            }else{
                $answer_index = $akey;
                $inputname = "answer[$qnum][$akey]";
            }
            if(is_array($results)){
                $result = $results['results'];
                $section_id = $results['section'];
                $project_id = $results['project'];
                $resultable = $results['validator'];
                $value = $result->getResultBySection($section_id, $project_id, $resultable, $qnum, $answer_index);
            }else{
                $value = null;
            }
            
            $cssId = $question->qnum.'_a'.$answer_key;
            
            if(property_exists($answer, "css")) {
               $css = $answer->css;
            } else {
               $css = ''; 
            }
            $class_array[] = $css;
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
                    $forJS .= '$("input[name=\''.$inputname.'\'").on("change",function(){'.$arguments[$i].'this = $(this).is(":checked")? $(this).val():false;'.
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
                $class_array = explode(' ', $options['class']);
                if($type == 'radio' || $type == 'checkbox'){
                   $key = array_search('form-control', $class_array);
                   $class_array[$key] = '';
                }
                
            }
            $options['class'] = implode(' ', $class_array);
            $options['id'] = $cssId; //dd($options);
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
            if($type == 'text'){
                
                $texthtml = "<div class='form-group'>";
                if($wrap){
                    $texthtml .= $wrap['start'];
                }
                $texthtml .= "<label for='$answer->text' class='col-xs-2 text-right'>"._t(ucfirst($answer->text))." </label>";
                $texthtml .= "<div class='col-xs-10'>";
                $texthtml .= $this->{$type}($inputname, $value, $options);
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
                $rdhtml .= $this->radio($inputname, $default, $radiovalue, $options).'<span class="badge">'._t($display_value).'</span> '._t(ucfirst($answer->text)).' </label></div>';
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