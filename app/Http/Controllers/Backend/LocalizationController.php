<?php namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Locale;
use App\Translation as LocaleTranslation;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Maatwebsite\Excel\Facades\Excel;
use Stevebauman\Translation\Facades\Translation;
use Symfony\Component\HttpFoundation\Response;

class LocalizationController extends Controller {

	public function __construct(Locale $locale, LocaleTranslation $translation)
	{
		$this->middleware('auth');
		$this->current_user_id = Auth::id();
		$this->auth_user = User::find($this->current_user_id);
		$this->locale = $locale;
		$this->translation = $translation;
	}
	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$search = Input::get('lang');
                
		//return print_r($this->translation->all());
		//return dd($this->locale->translations());
		$locale_list = $this->locale->all();
                if($search){
                    $translation_list = $this->translation->where('translation', 'like', '%'.$search.'%')->with('translated')->paginate('100');
                }else{
                    $translation_list = $this->translation->with('translated')->paginate('100'); 
                }   //dd($translation_list);

		$current_locale = Translation::getLocale();//dd($current_locale);
		$default_locale = Translation::getAppLocale();
		//dd($default_locale);

		//print($locale_list[$default_locale]);
		//return;
		return view('backend.languages.index', compact('locale_list', 'current_locale', 'default_locale', 'translation_list'));
	}
        
        /**
         * 
         */
        public function import(Request $request) {
            Excel::filter('chunk')->load($request->only('file')['file'], 'UTF-8')->chunk(100, function($language){
                            // Loop through all rows
                            $language->each(function($row) {
                                
                                $default = Locale::find(1);
                                $locales = Locale::lists('id','name');
                                $langs = [];
                                foreach($row as $lang => $translation) {
                                    if(array_key_exists(ucfirst($lang), $locales->toArray())) {
                                        // if imported lang exist
                                        $ilang_id = $locales[ucfirst($lang)];
                                        //
                                        if($ilang_id == $default->id) {
                                            $langs[ucfirst($lang)]['translation'] = $translation;
                                            $langs[ucfirst($lang)]['id'] = $ilang_id;
                                        } else {
                                            $langs['child'][ucfirst($lang)]['translation'] = $translation;
                                            $langs['child'][ucfirst($lang)]['id'] = $ilang_id;
                                        }
                                    }
                                }
                                $new_trans = LocaleTranslation::firstOrNew(['locale_id' => $langs[$default->name]['id'],'translation' => $langs[$default->name]['translation']]);
                                $new_trans->save();
                                foreach($langs['child'] as $cland => $ctrans) {
                                    $new_ctrans = LocaleTranslation::firstOrNew(['locale_id' => $ctrans['id'],'translation' => $ctrans['translation'],'translation_id' => $new_trans->id]);
                                    $new_ctrans->save();
                                }
                                        
                                
                            });
                            
                        });
                        return redirect()->route('admin.language.index')->withFlashSuccess('Imported');
		
        }

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		//


	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		//
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		//
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		//
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update()
	{
		//
            /**
		$lang_id = Input::get('lang_id');
		foreach($lang_id as $childid => $translation){
			$locale_id = LocaleTranslation::where('translation_id', '=', $childid)->pluck('id');
			$locale = LocaleTranslation::find($locale_id);
			
			$locale->translation = $translation;
                        $locale->parent()->dissociate();
                        //$locale->parent()->associate()
			$locale->update();
		}
		$json['status'] = true;
		$json['message'] = 'Translation updated!';
		return json_encode($json);
             * 
             */
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
	}

}
